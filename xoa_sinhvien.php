<?php
include 'config.php';

// Kiểm tra nếu có MaSV được truyền vào
if (isset($_GET['MaSV'])) {
    $ma_sv = $_GET['MaSV'];

    try {
        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        $conn->beginTransaction();

        // Lấy danh sách MaDK của sinh viên
        $stmt = $conn->prepare("SELECT MaDK FROM DangKy WHERE MaSV = ?");
        $stmt->execute([$ma_sv]);
        $dangky_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Nếu có dữ liệu trong ChiTietDangKy thì xóa
        if (!empty($dangky_ids)) {
            $dangky_ids_str = implode(',', array_map('intval', $dangky_ids));

            // Xóa trong ChiTietDangKy
            $stmt = $conn->prepare("DELETE FROM ChiTietDangKy WHERE MaDK IN ($dangky_ids_str)");
            $stmt->execute();
            
            // Cập nhật lại SoLuongDuKien trong HocPhan
            $stmt = $conn->prepare("
                UPDATE HocPhan 
                SET SoLuongDuKien = SoLuongDuKien + 1 
                WHERE MaHP IN (
                    SELECT MaHP FROM ChiTietDangKy WHERE MaDK IN ($dangky_ids_str)
                )
            ");
            $stmt->execute();
        }

        // Xóa trong DangKy
        $stmt = $conn->prepare("DELETE FROM DangKy WHERE MaSV = ?");
        $stmt->execute([$ma_sv]);

        // Xóa trong SinhVien
        $stmt = $conn->prepare("DELETE FROM SinhVien WHERE MaSV = ?");
        $stmt->execute([$ma_sv]);

        // Commit transaction
        $conn->commit();

        echo "<script>alert('Xóa sinh viên thành công!'); window.location='danhsachsinhvien.php';</script>";
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollBack();
        echo "<script>alert('Xóa sinh viên thất bại: " . addslashes($e->getMessage()) . "'); window.location='danhsachsinhvien.php';</script>";
    }
} else {
    echo "<script>alert('Mã sinh viên không hợp lệ!'); window.location='danhsachsinhvien.php';</script>";
}

// Đóng kết nối
$conn = null;
?>
