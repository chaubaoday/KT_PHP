<?php
include 'config.php';

$sql = "SELECT * FROM hocphan";
$stmt = $conn->prepare($sql);
$stmt->execute();
$hocphans = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Học Phần</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">DANH SÁCH HỌC PHẦN</h2>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Mã Học Phần</th>
                <th>Tên Học Phần</th>
                <th>Số Tín Chỉ</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($hocphans)) {
                foreach ($hocphans as $row) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row['ma_hoc_phan'] ?? 'N/A') . "</td>
                        <td>" . htmlspecialchars($row['ten_hoc_phan'] ?? 'N/A') . "</td>
                        <td>" . htmlspecialchars($row['so_tin_chi'] ?? 'N/A') . "</td>
                        <td><a href='dangkyhocphan.php?id=" . htmlspecialchars($row['id'] ?? 0) . "' class='btn btn-success'>Đăng Kí</a></td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>Không có học phần nào</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
