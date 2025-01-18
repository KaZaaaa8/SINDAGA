<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle search
$where_clause = "1=1";
if (isset($_GET['search']) && $_GET['search'] != '') {
    $search = mysqli_real_escape_string($koneksi, $_GET['search']);
    $where_clause .= " AND (username LIKE '%$search%' OR nama_lengkap LIKE '%$search%')";
}

$query = "SELECT * FROM pengguna WHERE $where_clause ORDER BY dibuat_pada DESC";
$result = mysqli_query($koneksi, $query);

// Get statistics
$query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengguna");
$total_pengguna = mysqli_fetch_assoc($query_total)['total'];

$query_admin = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengguna WHERE level = 'admin'");
$total_admin = mysqli_fetch_assoc($query_admin)['total'];

$query_petugas = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengguna WHERE level = 'petugas'");
$total_petugas = mysqli_fetch_assoc($query_petugas)['total'];
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna - SINDAGA</title>
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

        <main class="flex-1 p-4">
            <div class="max-w-6xl mx-auto">
                <!-- Page Header -->
                <div class="mb-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-xl font-bold text-white">Data Pengguna</h1>
                            <p class="text-sm text-gray-400">Kelola data pengguna sistem</p>
                        </div>

                        <button onclick="window.location.href='tambah.php'"
                            class="inline-flex items-center px-3 py-1.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Pengguna
                        </button>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-3 gap-3 mt-3">
                        <div class="bg-dark-200 rounded-lg p-3 border border-dark-300">
                            <p class="text-xs text-gray-400">Total Pengguna</p>
                            <p class="text-lg font-bold text-white"><?= number_format($total_pengguna) ?></p>
                        </div>
                        <div class="bg-dark-200 rounded-lg p-3 border border-dark-300">
                            <p class="text-xs text-gray-400">Admin</p>
                            <p class="text-lg font-bold text-white"><?= number_format($total_admin) ?></p>
                        </div>
                        <div class="bg-dark-200 rounded-lg p-3 border border-dark-300">
                            <p class="text-xs text-gray-400">Petugas</p>
                            <p class="text-lg font-bold text-white"><?= number_format($total_petugas) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Search Section -->
                <div class="bg-dark-200 rounded-lg border border-dark-300 mb-4">
                    <div class="p-4">
                        <form method="GET" class="flex items-end gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Cari</label>
                                <div class="relative">
                                    <input type="text"
                                        name="search"
                                        value="<?= $_GET['search'] ?? '' ?>"
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 focus:ring-indigo-500 focus:border-indigo-500 pl-10 pr-3 py-2 text-sm"
                                        placeholder="Cari username atau nama...">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="flex space-x-2">
                                <button type="submit"
                                    class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Cari
                                </button>
                                <?php if (isset($_GET['search'])): ?>
                                    <button type="button"
                                        onclick="window.location.href='index.php'"
                                        class="px-4 py-2 bg-dark-300 hover:bg-dark-100 text-gray-300 text-sm font-medium rounded-lg transition-colors duration-200 flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Reset
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="bg-dark-200 rounded-lg border border-dark-300">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-dark-300">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase">No</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase">Username</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase">Nama Lengkap</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase">Level</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-300">
                                <?php if (mysqli_num_rows($result) == 0): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                </svg>
                                                <p>Belum ada data pengguna</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)):
                                    ?>
                                        <tr class="hover:bg-dark-300/50 transition-colors duration-150">
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-400"><?= $no++ ?></td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-white"><?= $row['username'] ?></td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-300"><?= $row['nama_lengkap'] ?></td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    <?= $row['level'] == 'admin' ? 'bg-indigo-500/10 text-indigo-400' : 'bg-emerald-500/10 text-emerald-400' ?>">
                                                    <?= ucfirst($row['level']) ?>
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="edit.php?id=<?= $row['id'] ?>"
                                                        class="text-amber-400 hover:text-amber-300 transition-colors duration-200">Edit</a>
                                                    <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                                        <button onclick="confirmDelete(<?= $row['id'] ?>)"
                                                            class="text-rose-400 hover:text-rose-300 transition-colors duration-200">Hapus</button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include '../../includes/footer.php'; ?>

    <script>
        function confirmDelete(id) {
            if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
                window.location.href = 'hapus.php?id=' + id;
            }
        }
    </script>
</body>

</html>