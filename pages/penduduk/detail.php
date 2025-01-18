<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'];
$query = "SELECT p.*, kk.nomor_kk, kk.alamat, w.nama_wilayah
          FROM penduduk p
          LEFT JOIN kartu_keluarga kk ON p.id_kk = kk.id
          LEFT JOIN wilayah w ON kk.id_wilayah = w.id
          WHERE p.id = ?";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Penduduk - SINDAGA</title>
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
            <div class="max-w-5xl mx-auto">
                <!-- Page Header -->
                <div class="mb-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-white">Detail Penduduk</h1>
                            <p class="text-base text-gray-400 mt-1">Informasi lengkap data penduduk</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="index.php"
                                class="px-4 py-2 bg-dark-200 text-gray-300 rounded-lg hover:bg-dark-300 transition-colors duration-200">
                                Kembali
                            </a>
                            <a href="edit.php?id=<?= $data['id'] ?>"
                                class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors duration-200">
                                Edit Data
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Data Card -->
                <div class="bg-dark-200 rounded-xl border border-dark-300 shadow-lg">
                    <div class="border-b border-dark-300 p-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-white"><?= $data['nama_lengkap'] ?></h2>
                                <p class="text-gray-400 mt-1">NIK: <?= $data['nik'] ?></p>
                            </div>
                            <span class="px-4 py-2 rounded-full text-sm font-medium
                                <?php
                                switch ($data['status_keluarga']) {
                                    case 'KEPALA KELUARGA':
                                        echo 'bg-emerald-500/10 text-emerald-500';
                                        break;
                                    case 'ISTRI':
                                        echo 'bg-blue-500/10 text-blue-500';
                                        break;
                                    case 'ANAK':
                                        echo 'bg-violet-500/10 text-violet-500';
                                        break;
                                    default:
                                        echo 'bg-gray-500/10 text-gray-500';
                                }
                                ?>">
                                <?= $data['status_keluarga'] ?>
                            </span>
                        </div>
                    </div>

                    <div class="p-8 space-y-12">
                        <!-- Informasi KK -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">
                                Informasi Kartu Keluarga
                            </h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <p class="text-sm font-medium text-gray-400 mb-2">Nomor KK</p>
                                    <p class="text-base text-white"><?= $data['nomor_kk'] ?? '-' ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-400 mb-2">Wilayah</p>
                                    <p class="text-base text-white"><?= $data['nama_wilayah'] ?? '-' ?></p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-sm font-medium text-gray-400 mb-2">Alamat</p>
                                    <p class="text-base text-white"><?= $data['alamat'] ?? '-' ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Data Pribadi -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">
                                Data Pribadi
                            </h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <p class="text-sm font-medium text-gray-400 mb-2">Tempat Lahir</p>
                                    <p class="text-base text-white"><?= $data['tempat_lahir'] ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-400 mb-2">Tanggal Lahir</p>
                                    <p class="text-base text-white"><?= date('d F Y', strtotime($data['tanggal_lahir'])) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-400 mb-2">Jenis Kelamin</p>
                                    <p class="text-base text-white">
                                        <?= $data['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-400 mb-2">Agama</p>
                                    <p class="text-base text-white"><?= ucfirst(strtolower($data['agama'])) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Status & Pekerjaan -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">
                                Status & Pekerjaan
                            </h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <p class="text-sm font-medium text-gray-400 mb-2">Status Perkawinan</p>
                                    <p class="text-base text-white"><?= $data['status_kawin'] ?></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-400 mb-2">Pekerjaan</p>
                                    <p class="text-base text-white"><?= $data['pekerjaan'] ?? '-' ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Tambahan -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">
                                Informasi Tambahan
                            </h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <p class="text-sm font-medium text-gray-400 mb-2">Terdaftar Pada</p>
                                    <p class="text-base text-white">
                                        <?= date('d F Y H:i', strtotime($data['dibuat_pada'])) ?>
                                    </p>
                                </div>
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