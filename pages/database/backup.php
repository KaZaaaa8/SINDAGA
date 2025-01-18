<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Set headers for file download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="backup_' . date('Y-m-d_H-i-s') . '.sql"');

// Get all tables
$tables_query = mysqli_query($koneksi, "SHOW TABLES");
$tables = [];
while ($row = mysqli_fetch_array($tables_query)) {
    $tables[] = $row[0];
}

// Start output buffering
ob_start();

// Add DROP TABLE statements
foreach ($tables as $table) {
    echo "DROP TABLE IF EXISTS `$table`;\n";
}

// Get CREATE TABLE statements and data
foreach ($tables as $table) {
    // Get table creation SQL
    $create_query = mysqli_query($koneksi, "SHOW CREATE TABLE `$table`");
    $row = mysqli_fetch_array($create_query);
    echo "\n" . $row[1] . ";\n\n";

    // Get table data
    $data_query = mysqli_query($koneksi, "SELECT * FROM `$table`");
    $num_fields = mysqli_num_fields($data_query);

    while ($row = mysqli_fetch_array($data_query)) {
        echo "INSERT INTO `$table` VALUES(";
        for ($i = 0; $i < $num_fields; $i++) {
            if (is_null($row[$i])) {
                echo 'NULL';
            } else {
                echo "'" . mysqli_real_escape_string($koneksi, $row[$i]) . "'";
            }
            if ($i < ($num_fields - 1)) {
                echo ',';
            }
        }
        echo ");\n";
    }
    echo "\n";
}

// Get the buffer contents and clean the buffer
$output = ob_get_clean();

// Output the SQL
echo $output;
exit;
