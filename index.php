<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: pages/auth/login.php");
    exit();
}

// Get total counts
$query_penduduk = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penduduk");
$total_penduduk = mysqli_fetch_assoc($query_penduduk)['total'];

$query_kk = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kartu_keluarga");
$total_kk = mysqli_fetch_assoc($query_kk)['total'];

$query_laki = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penduduk WHERE jenis_kelamin = 'L'");
$total_laki = mysqli_fetch_assoc($query_laki)['total'];

$query_perempuan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penduduk WHERE jenis_kelamin = 'P'");
$total_perempuan = mysqli_fetch_assoc($query_perempuan)['total'];

// Get recent data
$query_penduduk_baru = mysqli_query($koneksi, "
    SELECT p.*, kk.nomor_kk, w.nama_wilayah 
    FROM penduduk p 
    LEFT JOIN kartu_keluarga kk ON p.id_kk = kk.id
    LEFT JOIN wilayah w ON kk.id_wilayah = w.id 
    ORDER BY p.dibuat_pada DESC 
    LIMIT 5
");

$query_kk_baru = mysqli_query($koneksi, "
    SELECT kk.*, w.nama_wilayah,
           (SELECT COUNT(*) FROM penduduk WHERE id_kk = kk.id) as jumlah_anggota
    FROM kartu_keluarga kk
    LEFT JOIN wilayah w ON kk.id_wilayah = w.id
    ORDER BY kk.dibuat_pada DESC 
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SINDAGA</title>
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
        }
    </style>
</head>

<body class="bg-dark-100 text-gray-100">
    <?php include 'includes/header.php'; ?>

    <div class="flex min-h-screen">
        <?php include 'includes/sidebar.php'; ?>

        <main class="flex-1 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Welcome Section -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-white">Selamat Datang, <?= $_SESSION['nama_lengkap'] ?>! 👋</h1>
                    <p class="mt-2 text-gray-400">Berikut ringkasan data kependudukan terkini</p>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Penduduk -->
                    <div class="bg-dark-200 rounded-xl p-6 border border-dark-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-500/10">
                                <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Total Penduduk</p>
                                <p class="text-2xl font-bold text-white"><?= number_format($total_penduduk) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Total KK -->
                    <div class="bg-dark-200 rounded-xl p-6 border border-dark-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-emerald-500/10">
                                <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Total Kartu Keluarga</p>
                                <p class="text-2xl font-bold text-white"><?= number_format($total_kk) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Laki-laki -->
                    <div class="bg-dark-200 rounded-xl p-6 border border-dark-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500/10">
                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Penduduk Laki-laki</p>
                                <p class="text-2xl font-bold text-white"><?= number_format($total_laki) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Perempuan -->
                    <div class="bg-dark-200 rounded-xl p-6 border border-dark-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-rose-500/10">
                                <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-400">Penduduk Perempuan</p>
                                <p class="text-2xl font-bold text-white"><?= number_format($total_perempuan) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Data Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Penduduk -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300">
                        <div class="p-6 border-b border-dark-300">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-white">Data Penduduk Terbaru</h2>
                                <a href="pages/penduduk/index.php" class="text-sm text-indigo-400 hover:text-indigo-300">Lihat Semua</a>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                            <th class="pb-3">NIK</th>
                                            <th class="pb-3">Nama</th>
                                            <th class="pb-3">Wilayah</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-dark-300">
                                        <?php while ($row = mysqli_fetch_assoc($query_penduduk_baru)): ?>
                                            <tr class="text-sm">
                                                <td class="py-3 text-gray-300"><?= $row['nik'] ?></td>
                                                <td class="py-3 font-medium text-white"><?= $row['nama_lengkap'] ?></td>
                                                <td class="py-3 text-gray-400"><?= $row['nama_wilayah'] ?? '-' ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Recent KK -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300">
                        <div class="p-6 border-b border-dark-300">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-white">Data KK Terbaru</h2>
                                <a href="pages/kartu-keluarga/index.php" class="text-sm text-indigo-400 hover:text-indigo-300">Lihat Semua</a>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                            <th class="pb-3">No. KK</th>
                                            <th class="pb-3">Wilayah</th>
                                            <th class="pb-3">Anggota</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-dark-300">
                                        <?php while ($row = mysqli_fetch_assoc($query_kk_baru)): ?>
                                            <tr class="text-sm">
                                                <td class="py-3 text-gray-300"><?= $row['nomor_kk'] ?></td>
                                                <td class="py-3 text-gray-300"><?= $row['nama_wilayah'] ?? '-' ?></td>
                                                <td class="py-3 text-gray-300"><?= $row['jumlah_anggota'] ?> orang</td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>