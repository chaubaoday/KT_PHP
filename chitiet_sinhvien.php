<?php
include 'config.php';

if (isset($_GET['MaSV'])) {
    $MaSV = $_GET['MaSV'];
    $sql = "SELECT * FROM SinhVien WHERE MaSV = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$MaSV]);
    $sinhvien = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    die("Không tìm thấy sinh viên!");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông Tin Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <h2 class="mb-4">Thông Tin Sinh Viên</h2>
    <table class="table table-bordered">
        <tr>
            <th>Mã SV</th>
            <td><?= $sinhvien['MaSV'] ?></td>
        </tr>
        <tr>
            <th>Họ Tên</th>
            <td><?= $sinhvien['HoTen'] ?></td>
        </tr>
        <tr>
            <th>Giới Tính</th>
            <td><?= $sinhvien['GioiTinh'] ?></td>
        </tr>
        <tr>
            <th>Ngày Sinh</th>
            <td><?= $sinhvien['NgaySinh'] ?></td>
        </tr>
        <tr>
            <th>Hình Ảnh</th>
            <td><img src="<?= $sinhvien['Hinh'] ?>" width="100"></td>
        </tr>
        <tr>
            <th>Ngành Học</th>
            <td><?= $sinhvien['MaNganh'] ?></td>
        </tr>
    </table>
    <a href="index.php" class="btn btn-secondary">Quay lại</a>
</div>
</body>
</html>

