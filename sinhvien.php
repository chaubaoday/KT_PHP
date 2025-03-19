<?php
require "config.php";

$stmt = $conn->prepare("SELECT * FROM sinhvien");
$stmt->execute();
$sinhviens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Danh sách sinh viên</title>
</head>
<body>
<?php include 'header.php'; ?>
    <h2>Danh sách sinh viên</h2>
    <a href="them_sinhvien.php">Thêm sinh viên</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Mã SV</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Ngày sinh</th>
            <th>Giới tính</th>
            <th>Hành động</th>
        </tr>
        <?php foreach ($sinhviens as $sv) : ?>
        <tr>
            <td><?= $sv['id'] ?></td>
            <td><?= $sv['masv'] ?></td>
            <td><?= $sv['hoten'] ?></td>
            <td><?= $sv['email'] ?></td>
            <td><?= $sv['ngaysinh'] ?></td>
            <td><?= $sv['gioitinh'] ?></td>
            <td>
                <a href="sua_sinhvien.php?id=<?= $sv['id'] ?>">Sửa</a> |
                <a href="xoa_sinhvien.php?id=<?= $sv['id'] ?>" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

