<?php
session_start();
require_once '../../config/config.php';
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Surat - SINDAGA</title>
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
            <div class="max-w-6xl mx-auto">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-white">Layanan Surat</h1>
                    <p class="text-base text-gray-400 mt-1">Pilih jenis surat yang ingin dibuat</p>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-emerald-800">Surat berhasil dibuat!</h3>
                                <div class="mt-4 flex space-x-4">
                                    <a href="../../Surat/output/<?= $_GET['file'] ?>" download
                                        class="inline-flex items-center px-3 py-2 border border-emerald-300 text-sm leading-4 font-medium rounded-md text-emerald-700 bg-white hover:bg-emerald-50">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download Surat
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Surat Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Surat Keterangan Belum Memiliki Rumah -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300 overflow-hidden hover:border-indigo-500/50 transition-colors duration-300">
                        <div class="p-6">
                            <div class="w-12 h-12 bg-indigo-500/10 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white mb-2">Surat Keterangan Belum Memiliki Rumah</h3>
                            <p class="text-sm text-gray-400 mb-4">Surat keterangan untuk menyatakan bahwa seseorang belum memiliki rumah</p>
                            <a href="belum-memiliki-rumah.php"
                                class="inline-flex items-center text-sm font-medium text-indigo-400 hover:text-indigo-300">
                                Buat Surat
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Surat Kematian -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300 overflow-hidden">
                        <div class="p-6">
                            <div class="w-12 h-12 bg-rose-500/10 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white mb-2">Surat Keterangan Kematian</h3>
                            <p class="text-sm text-gray-400 mb-4">Surat keterangan untuk mencatat kematian penduduk</p>
                            <a href="kematian.php"
                                class="inline-flex items-center text-sm font-medium text-rose-400 hover:text-rose-300">
                                Buat Surat
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Surat Keterangan Tidak Mampu -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300 overflow-hidden">
                        <div class="p-6">
                            <div class="w-12 h-12 bg-amber-500/10 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white mb-2">Surat Keterangan Tidak Mampu</h3>
                            <p class="text-sm text-gray-400 mb-4">Surat keterangan untuk keluarga tidak mampu (SKTM)</p>
                            <a href="sktm.php"
                                class="inline-flex items-center text-sm font-medium text-amber-400 hover:text-amber-300">
                                Buat Surat
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Template for additional letter types -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300 overflow-hidden opacity-50">
                        <div class="p-6">
                            <div class="w-12 h-12 bg-gray-500/10 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white mb-2">Jenis Surat Lainnya</h3>
                            <p class="text-sm text-gray-400 mb-4">Template untuk jenis surat yang akan datang</p>
                            <span class="inline-flex items-center text-sm font-medium text-gray-400">
                                Segera Hadir
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Information Section -->
                <div class="mt-8 bg-dark-200 rounded-lg p-6 border border-dark-300">
                    <h3 class="text-lg font-semibold text-white mb-4">Informasi Layanan Surat</h3>
                    <div class="space-y-3 text-gray-400">
                        <p class="text-sm">
                            <span class="font-medium text-white">•</span> Pilih jenis surat yang ingin dibuat dari pilihan di atas
                        </p>
                        <p class="text-sm">
                            <span class="font-medium text-white">•</span> Isi formulir dengan data yang diperlukan
                        </p>
                        <p class="text-sm">
                            <span class="font-medium text-white">•</span> Setelah surat dibuat, Anda dapat langsung mengunduh atau mencetak surat
                        </p>
                        <p class="text-sm">
                            <span class="font-medium text-white">•</span> Pastikan data yang dimasukkan sudah benar sebelum membuat surat
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>

</html>