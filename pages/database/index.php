<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get database info
$tables_query = mysqli_query($koneksi, "SHOW TABLES");
$total_tables = mysqli_num_rows($tables_query);

// Get total records
$total_records = 0;
while ($table = mysqli_fetch_array($tables_query)) {
    $count_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM " . $table[0]);
    $count = mysqli_fetch_assoc($count_query);
    $total_records += $count['total'];
}

$db_name_query = mysqli_query($koneksi, "SELECT DATABASE()");
$db_name = mysqli_fetch_array($db_name_query)[0];

// Get database size
$size_query = mysqli_query($koneksi, "
    SELECT table_schema AS 'database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size' 
    FROM information_schema.tables 
    WHERE table_schema = '" . $db_name . "'
    GROUP BY table_schema
");
$size_info = mysqli_fetch_assoc($size_query);
$database_size = $size_info['size'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Management - SINDAGA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            100: '#1E293B',
                            200: '#334155',
                            300: '#475569',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #1E293B;
        }
    </style>
</head>

<body class="bg-dark-100 text-gray-100">
    <?php include '../../includes/header.php'; ?>

    <div class="flex min-h-screen">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-white">Database Management</h1>
                    <p class="text-base text-gray-400 mt-1">Backup dan restore database sistem</p>
                </div>

                <!-- Messages -->
                <?php if (isset($_GET['success']) && $_GET['success'] == 'restore'): ?>
                    <div class="mb-6 rounded-lg bg-green-50 p-4 border border-green-200">
                        <div class="flex">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="ml-3 text-sm text-green-700">Database berhasil direstore</p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="mb-6 rounded-lg bg-red-50 p-4 border border-red-200">
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="ml-3 text-sm text-red-700">
                                <?php
                                switch ($_GET['error']) {
                                    case 'invalid_file':
                                        echo "File tidak valid. Harap upload file SQL.";
                                        break;
                                    default:
                                        echo htmlspecialchars($_GET['error']);
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-3 gap-6 mb-8">
                    <div class="bg-dark-200 rounded-xl p-6 border border-dark-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-500/10">
                                <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Total Tabel</p>
                                <p class="text-2xl font-bold text-white"><?= number_format($total_tables) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-dark-200 rounded-xl p-6 border border-dark-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-emerald-500/10">
                                <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Total Records</p>
                                <p class="text-2xl font-bold text-white"><?= number_format($total_records) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-dark-200 rounded-xl p-6 border border-dark-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-amber-500/10">
                                <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Ukuran Database</p>
                                <p class="text-2xl font-bold text-white"><?= number_format($database_size, 2) ?> MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Cards -->
                <div class="grid grid-cols-2 gap-6">
                    <!-- Backup Card -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-white mb-4">Backup Database</h2>
                            <p class="text-gray-400 mb-6">Download backup database dalam format SQL untuk menyimpan data sistem</p>
                            <button onclick="window.location.href='backup.php'"
                                class="w-full px-4 py-3 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 
                                       transition-colors duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Download Backup
                            </button>
                        </div>
                    </div>

                    <!-- Restore Card -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-white mb-4">Restore Database</h2>
                            <p class="text-gray-400 mb-6">Upload file backup SQL untuk mengembalikan data sistem</p>
                            <form action="restore.php" method="POST" enctype="multipart/form-data">
                                <div class="flex items-center space-x-4">
                                    <input type="file" name="backup_file" accept=".sql" required
                                        class="block w-full text-sm text-gray-400
                                               file:mr-4 file:py-2.5 file:px-4
                                               file:rounded-lg file:border-0
                                               file:text-sm file:font-medium
                                               file:bg-dark-300 file:text-gray-300
                                               hover:file:bg-dark-100">
                                    <button type="submit"
                                        class="px-4 py-2.5 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 
                                               transition-colors duration-200 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                        </svg>
                                        Restore
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>

</html>