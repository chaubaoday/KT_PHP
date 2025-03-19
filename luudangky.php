<?php
include 'config.php';
session_start();

// Kiểm tra nếu sinh viên đã đăng nhập
if (!isset($_SESSION['maSV'])) {
    header("Location: dangnhap.php");
    exit();
}

$maSV = $_SESSION['maSV'];

// Lấy danh sách học phần đã chọn (tạm thời từ session hoặc form, giả sử đã chọn trước đó)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
    $ngayDK = date('Y-m-d'); // Ngày đăng ký hiện tại
    $selectedCourses = $_POST['courses']; // Danh sách MaHP được chọn từ form

    // Thêm vào bảng DangKy
    $sql_dangky = "INSERT INTO DangKy (NgayDK, MaSV) VALUES (:ngayDK, :maSV)";
    $stmt_dangky = $conn->prepare($sql_dangky);
    $stmt_dangky->execute(['ngayDK' => $ngayDK, 'maSV' => $maSV]);
    $maDK = $conn->lastInsertId();

    // Thêm vào bảng ChiTietDangKy
    $sql_chitiet = "INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES (:maDK, :maHP)";
    $stmt_chitiet = $conn->prepare($sql_chitiet);
    foreach ($selectedCourses as $maHP) {
        $stmt_chitiet->execute(['maDK' => $maDK, 'maHP' => $maHP]);
    }

    header("Location: luudangky.php?success=1");
    exit();
}

// Lấy thông tin đăng ký hiện tại
$sql = "SELECT hp.MaHP, hp.TenHP, hp.SoTinChi, dk.MaDK, dk.NgayDK
        FROM HocPhan hp
        JOIN ChiTietDangKy ctdk ON hp.MaHP = ctdk.MaHP
        JOIN DangKy dk ON ctdk.MaDK = dk.MaDK
        WHERE dk.MaSV = :maSV";
$stmt = $conn->prepare($sql);
$stmt->execute(['maSV' => $maSV]);
$registeredCourses = $stmt->fetchAll();

// Tính tổng số học phần và tín chỉ
$totalCourses = count($registeredCourses);
$totalCredits = 0;
foreach ($registeredCourses as $course) {
    $totalCredits += $course['SoTinChi'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Ký Học Phần</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Đăng Ký Học Phần</h2>
    <table border="1">
        <tr>
            <th>MaHP</th>
            <th>Tên Học Phần</th>
            <th>Số Tín Chỉ</th>
            <th></th>
        </tr>
        <?php
        if ($totalCourses > 0) {
            foreach ($registeredCourses as $row) {
                echo "<tr>";
                echo "<td>" . $row['MaHP'] . "</td>";
                echo "<td>" . $row['TenHP'] . "</td>";
                echo "<td>" . $row['SoTinChi'] . "</td>";
                echo "<td></td>"; // Cột trống theo hình
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Chưa đăng ký học phần nào.</td></tr>";
        }
        ?>
    </table>
    <p>Số học phần: <?php echo $totalCourses; ?></p>
    <p>Tổng số tín chỉ: <?php echo $totalCredits; ?></p>
    <p>Tổng số học phần: <?php echo $totalCourses; ?></p>

    <h3>Thông tin Đăng ký</h3>
    <?php
    if ($totalCourses > 0) {
        $row = $registeredCourses[0]; // Lấy thông tin từ bản ghi đầu tiên
        echo "<p>Ma sinh viên: " . $maSV . "</p>";
        echo "<p>Ho ten: Nguyễn Văn A</p>"; // Giả sử lấy từ SinhVien, cần join bảng để lấy đúng
        echo "<p>Ngay sinh: 22/2000 12:00 AM</p>"; // Giả sử
        echo "<p>Ma hoc: CNTT</p>"; // Giả sử
        echo "<p>Ngay Dang Ky: " . $row['NgayDK'] . "</p>";
    }
    ?>

    <form method="POST" action="">
        <input type="submit" name="save" value="Lưu thông tin" class="btn-luu">
        <a href="xoa_thongtin.php" class="btn-xoa">Xóa thông tin</a>
    </form>

    <h3>Thông báo đăng ký thành công:</h3>
    <?php
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        echo "<p>Đăng ký thành công!</p>";
    }
    ?>

    <h3>Thông tin Học Phần Đã Lưu</h3>
    <table border="1">
        <tr>
            <th>MaDK</th>
            <th>NgayDK</th>
            <th>MaSV</th>
        </tr>
        <?php
        if ($totalCourses > 0) {
            $row = $registeredCourses[0];
            echo "<tr>";
            echo "<td>" . $row['MaDK'] . "</td>";
            echo "<td>" . $row['NgayDK'] . "</td>";
            echo "<td>" . $maSV . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    <table border="1">
        <tr>
            <th>MaDK</th>
            <th>MaHP</th>
        </tr>
        <?php
        if ($totalCourses > 0) {
            foreach ($registeredCourses as $row) {
                echo "<tr>";
                echo "<td>" . $row['MaDK'] . "</td>";
                echo "<td>" . $row['MaHP'] . "</td>";
                echo "</tr>";
            }
        }
        ?>
    </table>
</body>
</html>