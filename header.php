<?php
session_start(); // Nếu cần sử dụng session
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản Lý Sinh Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Quản Lý Sinh Viên</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Trang Chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="them_sinhvien.php">Thêm Sinh Viên</a></li>
                <li class="nav-item"><a class="nav-link" href="dangkyhocphan.php"> Đk Học Phần </a></li>
                <li class="nav-item"><a class="nav-link" href="hocphan.php"> QL Học Phần </a></li>
                <li class="nav-item"><a class="nav-link" href="dangnhap.php"> Đăng nhập </a></li>
                <li class="nav-item"><a class="nav-link" href="luudangky.php"> Đăng ký </a></li>
               
             
                
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">
