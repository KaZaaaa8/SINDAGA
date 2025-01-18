<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Begin transaction
    mysqli_begin_transaction($koneksi);

    try {
        // Check if wilayah is used in kartu_keluarga
        $check_kk = "SELECT COUNT(*) as total FROM kartu_keluarga WHERE id_wilayah = ?";
        $stmt = mysqli_prepare($koneksi, $check_kk);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $count = mysqli_fetch_assoc($result)['total'];

        if ($count > 0) {
            throw new Exception("Tidak dapat menghapus wilayah karena masih digunakan dalam data kartu keluarga");
        }

        // Delete the wilayah
        $query = "DELETE FROM wilayah WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_commit($koneksi);
            header("Location: index.php?success=3");
            exit();
        } else {
            throw new Exception(mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        header("Location: index.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}

header("Location: index.php");
exit();
