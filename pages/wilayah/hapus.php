<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'];

// Cek apakah wilayah memiliki child
$query_check = "SELECT COUNT(*) as total FROM wilayah WHERE id_parent = ?";
$stmt_check = mysqli_prepare($koneksi, $query_check);
mysqli_stmt_bind_param($stmt_check, "i", $id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$data_check = mysqli_fetch_assoc($result_check);

if ($data_check['total'] > 0) {
    header("Location: index.php?error=1");
    exit();
}

// Hapus wilayah
$query = "DELETE FROM wilayah WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: index.php?success=3");
} else {
    header("Location: index.php?error=2");
}
exit();
?>
