<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: $base_url/pages/auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Penggunaan - SINDAGA</title>
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
            <div class="max-w-4xl mx-auto">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-white">Panduan Penggunaan SINDAGA</h1>
                    <p class="mt-2 text-gray-400">Panduan lengkap penggunaan sistem informasi data warga</p>
                </div>

                <!-- Guide Content -->
                <div class="space-y-8">
                    <!-- Getting Started -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300 overflow-hidden">
                        <div class="p-6 border-b border-dark-300">
                            <h2 class="text-xl font-semibold text-white">Memulai Penggunaan</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-indigo-400 mb-2">Login Sistem</h3>
                                <p class="text-gray-300">Untuk mengakses SINDAGA:</p>
                                <ul class="list-disc list-inside mt-2 space-y-1 text-gray-400">
                                    <li>Masukkan username dan password yang telah diberikan</li>
                                    <li>Pilih "Masuk" untuk mengakses dashboard</li>
                                    <li>Pastikan logout setelah selesai menggunakan sistem</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Data Management -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300 overflow-hidden">
                        <div class="p-6 border-b border-dark-300">
                            <h2 class="text-xl font-semibold text-white">Pengelolaan Data</h2>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Data Penduduk -->
                            <div>
                                <h3 class="text-lg font-medium text-indigo-400 mb-2">Data Penduduk</h3>
                                <p class="text-gray-300">Cara mengelola data penduduk:</p>
                                <ul class="list-disc list-inside mt-2 space-y-1 text-gray-400">
                                    <li>Klik menu "Data Penduduk" di sidebar</li>
                                    <li>Gunakan tombol "Tambah Penduduk" untuk data baru</li>
                                    <li>Edit data dengan klik tombol "Edit"</li>
                                    <li>Hapus data dengan klik tombol "Hapus"</li>
                                </ul>
                            </div>

                            <!-- Kartu Keluarga -->
                            <div>
                                <h3 class="text-lg font-medium text-indigo-400 mb-2">Kartu Keluarga</h3>
                                <p class="text-gray-300">Pengelolaan kartu keluarga:</p>
                                <ul class="list-disc list-inside mt-2 space-y-1 text-gray-400">
                                    <li>Akses menu "Kartu Keluarga"</li>
                                    <li>Tambah KK baru dengan tombol "Tambah KK"</li>
                                    <li>Tambah anggota keluarga di halaman detail KK</li>
                                    <li>Update informasi KK melalui tombol "Edit"</li>
                                </ul>
                            </div>

                            <!-- Wilayah -->
                            <div>
                                <h3 class="text-lg font-medium text-indigo-400 mb-2">Data Wilayah</h3>
                                <p class="text-gray-300">Manajemen data wilayah:</p>
                                <ul class="list-disc list-inside mt-2 space-y-1 text-gray-400">
                                    <li>Buka menu "Data RT"</li>
                                    <li>Tambah wilayah baru dengan form yang tersedia</li>
                                    <li>Atur hierarki wilayah sesuai kebutuhan</li>
                                    <li>Update informasi wilayah jika diperlukan</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Reports -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300 overflow-hidden">
                        <div class="p-6 border-b border-dark-300">
                            <h2 class="text-xl font-semibold text-white">Laporan</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-indigo-400 mb-2">Mengakses Laporan</h3>
                                <p class="text-gray-300">Cara menggunakan fitur laporan:</p>
                                <ul class="list-disc list-inside mt-2 space-y-1 text-gray-400">
                                    <li>Pilih jenis laporan yang diinginkan</li>
                                    <li>Atur filter sesuai kebutuhan</li>
                                    <li>Klik "Export" untuk mengunduh laporan</li>
                                    <li>Format laporan tersedia dalam Excel</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Database Management -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300 overflow-hidden">
                        <div class="p-6 border-b border-dark-300">
                            <h2 class="text-xl font-semibold text-white">Manajemen Database</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-indigo-400 mb-2">Backup & Restore</h3>
                                <p class="text-gray-300">Panduan backup dan restore database:</p>
                                <ul class="list-disc list-inside mt-2 space-y-1 text-gray-400">
                                    <li>Akses menu "Database" (khusus admin)</li>
                                    <li>Klik "Backup" untuk mengunduh database</li>
                                    <li>Gunakan "Restore" untuk mengembalikan data</li>
                                    <li>Simpan file backup di tempat yang aman</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>

</html>