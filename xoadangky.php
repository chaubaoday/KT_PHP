<?php
include 'config.php';
session_start();

// Kiểm tra nếu sinh viên đã đăng nhập
if (!isset($_SESSION['maSV'])) {
    header("Location: dangnhap.php");
    exit();
}

$maSV = $_SESSION['maSV'];

// Xóa tất cả học phần đã đăng ký của sinh viên
$sql = "DELETE ctdk, dk
        FROM ChiTietDangKy ctdk
        JOIN DangKy dk ON ctdk.MaDK = dk.MaDK
        WHERE dk.MaSV = :maSV";
$stmt = $conn->prepare($sql);
$stmt->execute(['maSV' => $maSV]);

header("Location: dangkyhocphan.php");
?>