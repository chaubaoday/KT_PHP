<?php
require 'config.php'; // Kết nối database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Nhận dữ liệu từ form
        $masv = $_POST['masv'];
        $hoten = $_POST['hoten'];
        $gioitinh = $_POST['gioitinh'];
        $ngaysinh = $_POST['ngaysinh'];
        $manganh = $_POST['manganh'];

        // Kiểm tra các trường có bị trống không
        if (empty($masv) || empty($hoten) || empty($gioitinh) || empty($ngaysinh) || empty($manganh)) {
            throw new Exception("Vui lòng nhập đầy đủ thông tin!");
        }

        // Xử lý upload ảnh
        $target_dir = "uploads/"; // Thư mục lưu ảnh
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Tạo thư mục nếu chưa tồn tại
        }

        $hinh = ""; // Đường dẫn ảnh lưu vào database
        if (!empty($_FILES["hinh"]["name"])) {
            $file_name = basename($_FILES["hinh"]["name"]);
            $target_file = $target_dir . time() . "_" . $file_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Kiểm tra định dạng ảnh hợp lệ
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowed_types)) {
                throw new Exception("Chỉ chấp nhận file JPG, JPEG, PNG, GIF.");
            }

            // Kiểm tra upload file
            if (!move_uploaded_file($_FILES["hinh"]["tmp_name"], $target_file)) {
                throw new Exception("Lỗi khi tải ảnh lên.");
            }
            $hinh = $target_file; // Lưu đường dẫn vào database
        }

        // Câu lệnh SQL INSERT
        $sql = "INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh) 
                VALUES (:masv, :hoten, :gioitinh, :ngaysinh, :hinh, :manganh)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':masv' => $masv,
            ':hoten' => $hoten,
            ':gioitinh' => $gioitinh,
            ':ngaysinh' => $ngaysinh,
            ':hinh' => $hinh,
            ':manganh' => $manganh
        ]);

        echo "<script>alert('Thêm sinh viên thành công!'); window.location.href='them_sinhvien.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sinh Viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f5f5f5;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }
        label {
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<h2 style="text-align: center;">Thêm Sinh Viên</h2>

<form action="" method="POST" enctype="multipart/form-data">
    <label for="masv">Mã Sinh Viên:</label>
    <input type="text" name="masv" id="masv" required>

    <label for="hoten">Họ và Tên:</label>
    <input type="text" name="hoten" id="hoten" required>

    <label for="gioitinh">Giới Tính:</label>
    <select name="gioitinh" id="gioitinh">
        <option value="Nam">Nam</option>
        <option value="Nữ">Nữ</option>
    </select>

    <label for="ngaysinh">Ngày Sinh:</label>
    <input type="date" name="ngaysinh" id="ngaysinh" required>

    <label for="hinh">Hình Ảnh:</label>
    <input type="file" name="hinh" id="hinh" accept="image/*">

    <label for="manganh">Ngành Học:</label>
    <select name="manganh" id="manganh">
        <option value="CNTT">Công nghệ thông tin</option>
        <option value="QTKD">Quản trị kinh doanh</option>
    </select>

    <button type="submit">Thêm Sinh Viên</button>
</form>

</body>
</html>

