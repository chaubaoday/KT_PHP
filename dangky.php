<?php
include 'config.php';
session_start();

// Kiểm tra nếu sinh viên đã đăng nhập
if (!isset($_SESSION['maSV'])) {
    header("Location: dangnhap.php");
    exit();
}

$maSV = $_SESSION['maSV'];
$maHP = $_GET['maHP'];

// Kiểm tra số lượng dự kiến
$sql_check = "SELECT SoLuongDuKien FROM HocPhan WHERE MaHP = :maHP";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->execute(['maHP' => $maHP]);
$row = $stmt_check->fetch();

if ($row['SoLuongDuKien'] <= 0) {
    // Nếu số lượng dự kiến đã hết, thông báo lỗi
    header("Location: hocphan.php?error=Số lượng dự kiến đã hết!");
    exit();
}

// Giảm số lượng dự kiến
$sql_update = "UPDATE HocPhan SET SoLuongDuKien = SoLuongDuKien - 1 WHERE MaHP = :maHP";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->execute(['maHP' => $maHP]);

// Thêm vào bảng DangKy
$sql_dangky = "INSERT INTO DangKy (NgayDK, MaSV) VALUES (NOW(), :maSV)";
$stmt_dangky = $conn->prepare($sql_dangky);
$stmt_dangky->execute(['maSV' => $maSV]);

// Lấy MaDK vừa tạo
$maDK = $conn->lastInsertId();

// Thêm vào bảng ChiTietDangKy
$sql_chitiet = "INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES (:maDK, :maHP)";
$stmt_chitiet = $conn->prepare($sql_chitiet);
$stmt_chitiet->execute(['maDK' => $maDK, 'maHP' => $maHP]);

header("Location: hocphan.php?success=Đăng ký thành công!");
?>