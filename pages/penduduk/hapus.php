<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'];

// Check if person is Kepala Keluarga
$query_check = "SELECT status_keluarga FROM penduduk WHERE id = ?";
$stmt_check = mysqli_prepare($koneksi, $query_check);
mysqli_stmt_bind_param($stmt_check, "i", $id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$data_check = mysqli_fetch_assoc($result_check);

if ($data_check['status_keluarga'] == 'KEPALA KELUARGA') {
    header("Location: index.php?error=1");
    exit();
}

// Delete the resident
$query = "DELETE FROM penduduk WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: index.php?success=3");
} else {
    header("Location: index.php?error=2");
}
exit();
