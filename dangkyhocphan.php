<?php
// Chỉ gọi session_start() một lần ở đây
session_start();

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Include các file cần thiết
include 'header.php'; // Đảm bảo header.php KHÔNG gọi session_start()
include 'config.php';

// Giả định mã sinh viên (có thể lấy từ session sau khi đăng nhập)
$ma_sv = '0123456789'; // Thay bằng MaSV thực tế, ví dụ: $_SESSION['ma_sv']

// Xử lý thêm học phần vào giỏ hàng
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['mahp'])) {
    $ma_hp = trim($_GET['mahp']);

    try {
        // Kiểm tra học phần có tồn tại và còn chỗ không
        $stmt = $conn->prepare("SELECT MaHP, SoLuongConLai FROM HocPhan WHERE MaHP = ?");
        $stmt->execute([$ma_hp]);
        $hocphan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$hocphan) {
            throw new Exception("Học phần không tồn tại!");
        }

        if ($hocphan['SoLuongConLai'] <= 0) {
            throw new Exception("Học phần đã hết chỗ!");
        }

        // Kiểm tra xem học phần đã có trong giỏ hàng chưa
        if (in_array($ma_hp, $_SESSION['cart'])) {
            throw new Exception("Học phần đã có trong giỏ hàng!");
        }

        // Thêm học phần vào giỏ hàng
        $_SESSION['cart'][] = $ma_hp;
        echo "<script>alert('Đã thêm học phần $ma_hp vào giỏ hàng!'); window.location='dangkyhocphan.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('" . htmlspecialchars($e->getMessage(), ENT_QUOTES) . "'); window.location='dangkyhocphan.php';</script>";
    }
}

// Xử lý xóa học phần khỏi giỏ hàng
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['mahp'])) {
    $ma_hp = trim($_GET['mahp']);
    if (($key = array_search($ma_hp, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Sắp xếp lại mảng
        echo "<script>alert('Đã xóa học phần $ma_hp khỏi giỏ hàng!'); window.location='dangkyhocphan.php';</script>";
    }
}

// Xử lý xóa toàn bộ giỏ hàng
if (isset($_GET['action']) && $_GET['action'] == 'clear') {
    $_SESSION['cart'] = [];
    echo "<script>alert('Đã xóa toàn bộ giỏ hàng!'); window.location='dangkyhocphan.php';</script>";
}

// Xử lý lưu đăng ký
if (isset($_POST['save_registration'])) {
    if (empty($_SESSION['cart'])) {
        echo "<script>alert('Giỏ hàng trống! Vui lòng chọn học phần để đăng ký.'); window.location='dangkyhocphan.php';</script>";
        exit();
    }

    try {
        // Bắt đầu transaction
        $conn->beginTransaction();

        // Thêm bản ghi vào bảng DangKy
        $stmt = $conn->prepare("INSERT INTO DangKy (NgayDK, MaSV) VALUES (NOW(), ?)");
        $stmt->execute([$ma_sv]);
        $ma_dk = $conn->lastInsertId();

        // Thêm từng học phần vào ChiTietDangKy và cập nhật SoLuongConLai
        foreach ($_SESSION['cart'] as $ma_hp) {
            // Kiểm tra lại số lượng
            $stmt = $conn->prepare("SELECT SoLuongConLai FROM HocPhan WHERE MaHP = ?");
            $stmt->execute([$ma_hp]);
            $hocphan = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($hocphan['SoLuongConLai'] <= 0) {
                throw new Exception("Học phần $ma_hp đã hết chỗ!");
            }

            // Kiểm tra xem sinh viên đã đăng ký học phần này chưa
            $stmt = $conn->prepare("
                SELECT COUNT(*) 
                FROM DangKy dk 
                JOIN ChiTietDangKy ctdk ON dk.MaDK = ctdk.MaDK 
                WHERE dk.MaSV = ? AND ctdk.MaHP = ?
            ");
            $stmt->execute([$ma_sv, $ma_hp]);
            $already_registered = $stmt->fetchColumn();

            if ($already_registered > 0) {
                throw new Exception("Bạn đã đăng ký học phần $ma_hp trước đó!");
            }

            // Thêm vào ChiTietDangKy
            $stmt = $conn->prepare("INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES (?, ?)");
            $stmt->execute([$ma_dk, $ma_hp]);

            // Cập nhật SoLuongConLai
            $stmt = $conn->prepare("UPDATE HocPhan SET SoLuongConLai = SoLuongConLai - 1 WHERE MaHP = ?");
            $stmt->execute([$ma_hp]);
        }

        // Commit transaction
        $conn->commit();

        // Xóa giỏ hàng sau khi lưu
        $_SESSION['cart'] = [];
        echo "<script>alert('Đăng ký thành công!'); window.location='dangkyhocphan.php';</script>";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "<script>alert('Đăng ký thất bại: " . htmlspecialchars($e->getMessage(), ENT_QUOTES) . "'); window.location='dangkyhocphan.php';</script>";
    }
}

// Truy vấn danh sách học phần
$sql = "SELECT MaHP, TenHP, SoTinChi, SoLuongConLai FROM HocPhan WHERE SoLuongConLai > 0";
$stmt = $conn->prepare($sql);
$stmt->execute();
$hocphans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Truy vấn giỏ hàng (lấy thông tin học phần trong giỏ)
$cart_items = [];
if (!empty($_SESSION['cart'])) {
    // Sử dụng prepared statement để truy vấn giỏ hàng
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $sql = "SELECT MaHP, TenHP, SoTinChi FROM HocPhan WHERE MaHP IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($_SESSION['cart']);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng Ký Học Phần</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        a, button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
        }
        a:hover, button:hover {
            background-color: #45a049;
        }
        .remove {
            background-color: #f44336;
        }
        .remove:hover {
            background-color: #da190b;
        }
        .clear {
            background-color: #ff9800;
        }
        .clear:hover {
            background-color: #e68a00;
        }
    </style>
</head>
<body>
    <!-- Danh sách học phần -->
    <h2>Danh Sách Học Phần</h2>
    <table>
        <tr>
            <th>Mã HP</th>
            <th>Tên Học Phần</th>
            <th>Số Tín Chỉ</th>
            <th>Số Lượng Còn Lại</th>
            <th>Action</th>
        </tr>
        <?php if (count($hocphans) > 0): ?>
            <?php foreach ($hocphans as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['MaHP']) ?></td>
                    <td><?= htmlspecialchars($row['TenHP']) ?></td>
                    <td><?= htmlspecialchars($row['SoTinChi']) ?></td>
                    <td><?= htmlspecialchars($row['SoLuongConLai']) ?></td>
                    <td>
                        <a href="dangkyhocphan.php?action=add&mahp=<?= urlencode($row['MaHP']) ?>">Đăng Ký</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center;">Không có học phần nào để đăng ký.</td>
            </tr>
        <?php endif; ?>
    </table>

    <!-- Giỏ hàng -->
    <h2>Giỏ Hàng Học Phần</h2>
    <table>
        <tr>
            <th>Mã HP</th>
            <th>Tên Học Phần</th>
            <th>Số Tín Chỉ</th>
            <th>Hành động</th>
        </tr>
        <?php if (count($cart_items) > 0): ?>
            <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['MaHP']) ?></td>
                    <td><?= htmlspecialchars($item['TenHP']) ?></td>
                    <td><?= htmlspecialchars($item['SoTinChi']) ?></td>
                    <td>
                        <a href="dangkyhocphan.php?action=remove&mahp=<?= urlencode($item['MaHP']) ?>" class="remove">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align: center;">Giỏ hàng trống.</td>
            </tr>
        <?php endif; ?>
    </table>

    <!-- Nút điều khiển giỏ hàng -->
    <div style="text-align: center; margin-top: 20px;">
        <a href="dangkyhocphan.php?action=clear" class="clear">Xóa Hết</a>
        <form method="POST" style="display: inline;">
            <button type="submit" name="save_registration">Lưu Đăng Ký</button>
        </form>
    </div>

    <!-- Liên kết đến trang học phần đã đăng ký -->
    <div style="text-align: center; margin-top: 20px;">
        <a href="registered_courses.php">Xem Học Phần Đã Đăng Ký</a>
    </div>
</body>
</html>

<?php
// Đóng kết nối
$conn = null;
?>