<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Begin transaction
    mysqli_begin_transaction($koneksi);

    try {
        // Prevent deleting own account
        if ($id == $_SESSION['user_id']) {
            throw new Exception("Tidak dapat menghapus akun yang sedang digunakan");
        }

        // Delete the user
        $query = "DELETE FROM pengguna WHERE id = ?";
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
