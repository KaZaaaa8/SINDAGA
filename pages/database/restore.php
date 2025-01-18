<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['backup_file'])) {
    $file = $_FILES['backup_file'];

    if ($file['error'] === UPLOAD_ERR_OK && $file['type'] === 'application/sql' || pathinfo($file['name'], PATHINFO_EXTENSION) === 'sql') {
        $sql_content = file_get_contents($file['tmp_name']);
        $commands = array_filter(
            explode(";\n", $sql_content),
            function ($cmd) {
                return trim($cmd) != '';
            }
        );

        mysqli_begin_transaction($koneksi);

        try {
            // Disable foreign key checks
            mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS=0");

            foreach ($commands as $command) {
                $result = mysqli_query($koneksi, $command);
                if (!$result) {
                    throw new Exception(mysqli_error($koneksi));
                }
            }

            // Re-enable foreign key checks
            mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS=1");

            mysqli_commit($koneksi);
            header("Location: index.php?success=restore");
            exit();
        } catch (Exception $e) {
            mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS=1"); // Re-enable even on error
            mysqli_rollback($koneksi);
            header("Location: index.php?error=" . urlencode($e->getMessage()));
            exit();
        }
    } else {
        header("Location: index.php?error=invalid_file");
        exit();
    }
}

header("Location: index.php");
exit();
