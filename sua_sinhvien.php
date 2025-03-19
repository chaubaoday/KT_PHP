<?php
include 'config.php';

if (isset($_GET['MaSV'])) {
    $MaSV = $_GET['MaSV'];
    $sql = "SELECT * FROM SinhVien WHERE MaSV = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$MaSV]);
    $sinhvien = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $HoTen = $_POST['HoTen'];
    $GioiTinh = $_POST['GioiTinh'];
    $NgaySinh = $_POST['NgaySinh'];
    $MaNganh = $_POST['MaNganh'];

    // Xử lý cập nhật ảnh
    $Hinh = $sinhvien['Hinh']; // Mặc định giữ nguyên ảnh cũ

    if (!empty($_FILES["Hinh"]["name"])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = basename($_FILES["Hinh"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra định dạng ảnh hợp lệ
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            echo "<script>alert('Chỉ chấp nhận file JPG, JPEG, PNG, GIF.');</script>";
        } else {
            // Xóa ảnh cũ nếu có
            if (!empty($sinhvien['Hinh']) && file_exists($sinhvien['Hinh'])) {
                unlink($sinhvien['Hinh']);
            }

            // Lưu ảnh mới
            if (move_uploaded_file($_FILES["Hinh"]["tmp_name"], $target_file)) {
                $Hinh = $target_file; // Cập nhật đường dẫn ảnh mới
            } else {
                echo "<script>alert('Lỗi khi tải ảnh lên.');</script>";
            }
        }
    }

    // Cập nhật dữ liệu
    $sql = "UPDATE SinhVien SET HoTen=?, GioiTinh=?, NgaySinh=?, Hinh=?, MaNganh=? WHERE MaSV=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$HoTen, $GioiTinh, $NgaySinh, $Hinh, $MaNganh, $MaSV]);

    echo "<script>alert('Cập nhật thành công!'); window.location.href='index.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <h2 class="mb-4">Sửa Thông Tin Sinh Viên</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Họ Tên</label>
            <input type="text" name="HoTen" class="form-control" value="<?= htmlspecialchars($sinhvien['HoTen']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Giới Tính</label>
            <select name="GioiTinh" class="form-control">
                <option value="Nam" <?= ($sinhvien['GioiTinh'] == 'Nam') ? 'selected' : '' ?>>Nam</option>
                <option value="Nữ" <?= ($sinhvien['GioiTinh'] == 'Nữ') ? 'selected' : '' ?>>Nữ</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Ngày Sinh</label>
            <input type="date" name="NgaySinh" class="form-control" value="<?= htmlspecialchars($sinhvien['NgaySinh']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Hình Ảnh</label>
            <input type="file" name="Hinh" class="form-control" accept="image/*">
            <br>
            <?php if (!empty($sinhvien['Hinh'])): ?>
                <img src="<?= $sinhvien['Hinh'] ?>" alt="Ảnh Sinh Viên" width="150">
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label>Ngành Học</label>
            <input type="text" name="MaNganh" class="form-control" value="<?= htmlspecialchars($sinhvien['MaNganh']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
</div>
</body>
</html>
<?php
 // Thêm dòng này
