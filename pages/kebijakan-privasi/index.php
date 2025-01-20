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
    <title>Kebijakan Privasi - SINDAGA</title>
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
                    <h1 class="text-2xl font-bold text-white">Kebijakan Privasi</h1>
                    <p class="mt-2 text-gray-400">Informasi tentang pengelolaan dan perlindungan data di SINDAGA</p>
                </div>

                <!-- Privacy Policy Content -->
                <div class="space-y-8">
                    <!-- Introduction -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300 overflow-hidden">
                        <div class="p-6 border-b border-dark-300">
                            <h2 class="text-xl font-semibold text-white">Pendahuluan</h2>
                        </div>
                        <div class="p-6 space-y-4 text-gray-300">
                            <p>
                                SINDAGA berkomitmen untuk melindungi privasi dan data pribadi pengguna. Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi yang kami terima.
                            </p>
                        </div>
                    </div>

                    <!-- Data Collection -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300 overflow-hidden">
                        <div class="p-6 border-b border-dark-300">
                            <h2 class="text-xl font-semibold text-white">Pengumpulan Data</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-indigo-400 mb-2">Data yang Kami Kumpulkan</h3>
                                <ul class="list-disc list-inside space-y-2 text-gray-300">
                                    <li>Data pribadi penduduk (nama, NIK, dll)</li>
                                    <li>Informasi kartu keluarga</li>
                                    <li>Data alamat dan wilayah</li>
                                    <li>Informasi pengguna sistem</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Data Usage -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300 overflow-hidden">
                        <div class="p-6 border-b border-dark-300">
                            <h2 class="text-xl font-semibold text-white">Penggunaan Data</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-indigo-400 mb-2">Tujuan Penggunaan Data</h3>
                                <ul class="list-disc list-inside space-y-2 text-gray-300">
                                    <li>Pengelolaan data kependudukan</li>
                                    <li>Pembuatan laporan statistik</li>
                                    <li>Pelayanan administrasi</li>
                                    <li>Pengembangan layanan</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Data Protection -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300 overflow-hidden">
                        <div class="p-6 border-b border-dark-300">
                            <h2 class="text-xl font-semibold text-white">Perlindungan Data</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-indigo-400 mb-2">Keamanan Data</h3>
                                <ul class="list-disc list-inside space-y-2 text-gray-300">
                                    <li>Enkripsi data sensitif</li>
                                    <li>Pembatasan akses pengguna</li>
                                    <li>Backup data berkala</li>
                                    <li>Monitoring aktivitas sistem</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- User Rights -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300 overflow-hidden">
                        <div class="p-6 border-b border-dark-300">
                            <h2 class="text-xl font-semibold text-white">Hak Pengguna</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-indigo-400 mb-2">Hak-hak Pengguna</h3>
                                <ul class="list-disc list-inside space-y-2 text-gray-300">
                                    <li>Akses ke data pribadi</li>
                                    <li>Pembetulan data yang tidak akurat</li>
                                    <li>Penghapusan data sesuai ketentuan</li>
                                    <li>Pengajuan keluhan terkait privasi</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-dark-200 rounded-xl border border-dark-300 overflow-hidden">
                        <div class="p-6 border-b border-dark-300">
                            <h2 class="text-xl font-semibold text-white">Kontak</h2>
                        </div>
                        <div class="p-6 space-y-4 text-gray-300">
                            <p>
                                Untuk pertanyaan atau keluhan terkait privasi data, silakan hubungi:
                            </p>
                            <ul class="list-disc list-inside space-y-2">
                                <li>Email: privacy@sindaga.com</li>
                                <li>Telepon: (021) 1234567</li>
                                <li>Alamat: Jl. Contoh No. 123, Kota</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>

</html>
