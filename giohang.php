<?php
include 'header.php';
include 'connect.php';

if (!isset($_SESSION['dangky'])) {
    $_SESSION['dangky'] = [];
}

// Xóa từng học phần
if (isset($_GET['xoa'])) {
    $ma_hp = $_GET['xoa'];
    if (($key = array_search($ma_hp, $_SESSION['dangky'])) !== false) {
        unset($_SESSION['dangky'][$key]);

        // Tăng lại số lượng học phần
        $sql_update = "UPDATE hocphan SET SoLuong = SoLuong + 1 WHERE MaHP = '$ma_hp'";
        $conn->query($sql_update);

        echo "<script>alert('Đã xóa học phần!');</script>";
    }
}

// Xóa toàn bộ học phần
if (isset($_GET['xoahet'])) {
    foreach ($_SESSION['dangky'] as $ma_hp) {
        $sql_update = "UPDATE hocphan SET SoLuong = SoLuong + 1 WHERE MaHP = '$ma_hp'";
        $conn->query($sql_update);
    }
    $_SESSION['dangky'] = [];
    echo "<script>alert('Đã xóa tất cả học phần!');</script>";
}
?>

<h2>Giỏ Hàng Học Phần</h2>
<table border="1">
    <tr>
        <th>Mã HP</th>
        <th>Tên Học Phần</th>
        <th>Hành Động</th>
    </tr>
    <?php foreach ($_SESSION['dangky'] as $ma_hp) {
        $sql = "SELECT * FROM hocphan WHERE MaHP = '$ma_hp'";
        $result = $conn->query($sql);
        if ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['MaHP'] ?></td>
                <td><?= $row['TenHP'] ?></td>
                <td><a href="giohang.php?xoa=<?= $row['MaHP'] ?>">Xóa</a></td>
            </tr>
    <?php }} ?>
</table>
<a href="giohang.php?xoahet=true">Xóa Tất Cả</a>
