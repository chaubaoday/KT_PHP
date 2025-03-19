<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $maSV = $_POST['maSV'];

    // Kiểm tra MaSV có tồn tại trong bảng SinhVien không
    $sql = "SELECT * FROM SinhVien WHERE MaSV = :maSV";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['maSV' => $maSV]);
    $row = $stmt->fetch();

    if ($row) {
        $_SESSION['maSV'] = $maSV;
        header("Location: hocphan.php");
    } else {
        $error = "Mã sinh viên không tồn tại!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>ĐĂNG NHẬP</h2>
    <form method="POST" action="">
        <label>MaSV:</label>
        <input type="text" name="maSV" required><br>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <button type="submit">Đăng Nhập</button>
        <a href="index.php">Back to List</a>
    </form>
</body>
</html>