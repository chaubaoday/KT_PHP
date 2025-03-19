<?php
include 'config.php';

$sql = "SELECT * FROM SinhVien";
$stmt = $conn->prepare($sql);
$stmt->execute();
$sinhviens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách sinh viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <h2 class="mb-4">Danh sách sinh viên</h2>
    <a href="them_sinhvien.php" class="btn btn-primary mb-3">Thêm Sinh Viên</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mã SV</th>
                <th>Họ Tên</th>
                <th>Giới Tính</th>
                <th>Ngày Sinh</th>
                <th>Hình</th>
                <th>Ngành Học</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sinhviens as $sv) : ?>
            <tr>
                <td><?= $sv['MaSV']; ?></td>
                <td><?= $sv['HoTen']; ?></td>
                <td><?= $sv['GioiTinh']; ?></td>
                <td><?= $sv['NgaySinh']; ?></td>
                <td><img src="<?= $sv['Hinh']; ?>" width="50"></td>
                <td><?= $sv['MaNganh']; ?></td>
                <td>
                    <a href="chitiet_sinhvien.php?MaSV=<?= $sv['MaSV']; ?>" class="btn btn-info btn-sm">Xem</a>
                    <a href="sua_sinhvien.php?MaSV=<?= $sv['MaSV']; ?>" class="btn btn-warning btn-sm">Sửa</a>
                    <a href="xoa_sinhvien.php?MaSV=<?= $sv['MaSV']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa sinh viên này?')">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>

