<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'];

// Get KK data with wilayah
$query = "SELECT kk.*, w.nama_wilayah 
          FROM kartu_keluarga kk
          LEFT JOIN wilayah w ON kk.id_wilayah = w.id 
          WHERE kk.id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$kk = mysqli_fetch_assoc($result);

// Get family members
$query_anggota = "SELECT * FROM penduduk WHERE id_kk = ? ORDER BY FIELD(status_keluarga, 'KEPALA KELUARGA', 'ISTRI', 'ANAK', 'LAINNYA')";
$stmt = mysqli_prepare($koneksi, $query_anggota);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result_anggota = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kartu Keluarga - SINDAGA</title>
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
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-white">Detail Kartu Keluarga</h1>
                            <p class="text-base text-gray-400 mt-1">Detail informasi kartu keluarga dan anggota</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="index.php"
                                class="px-4 py-2 bg-dark-200 text-gray-300 rounded-lg hover:bg-dark-300 transition-colors duration-200">
                                Kembali
                            </a>
                            <a href="edit.php?id=<?= $kk['id'] ?>"
                                class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors duration-200">
                                Edit KK
                            </a>
                        </div>
                    </div>
                </div>

                <!-- KK Information -->
                <div class="bg-dark-200 rounded-xl border border-dark-300 shadow-lg mb-8">
                    <div class="p-6 border-b border-dark-300">
                        <h2 class="text-lg font-semibold text-white">Informasi Kartu Keluarga</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <p class="text-sm font-medium text-gray-400 mb-1">Nomor KK</p>
                                <p class="text-base text-white"><?= $kk['nomor_kk'] ?></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-400 mb-1">Wilayah</p>
                                <p class="text-base text-white"><?= $kk['nama_wilayah'] ?? '-' ?></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-400 mb-1">RT/RW</p>
                                <p class="text-base text-white"><?= $kk['rt_rw'] ?></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-400 mb-1">Tanggal Dibuat</p>
                                <p class="text-base text-white"><?= date('d/m/Y', strtotime($kk['dibuat_pada'])) ?></p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm font-medium text-gray-400 mb-1">Alamat</p>
                                <p class="text-base text-white"><?= $kk['alamat'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Family Members -->
                <div class="bg-dark-200 rounded-xl border border-dark-300 shadow-lg">
                    <div class="p-6 border-b border-dark-300">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-white">Anggota Keluarga</h2>
                            <a href="../penduduk/tambah.php?kk=<?= $kk['id'] ?>"
                                class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors duration-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Anggota
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-dark-300">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">NIK</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">JK</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">TTL</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Status Kawin</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Pekerjaan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-300">
                                <?php
                                $no = 1;
                                while ($anggota = mysqli_fetch_assoc($result_anggota)):
                                    $status_color = match ($anggota['status_keluarga']) {
                                        'KEPALA KELUARGA' => 'emerald',
                                        'ISTRI' => 'blue',
                                        'ANAK' => 'violet',
                                        default => 'gray'
                                    };
                                ?>
                                    <tr class="hover:bg-dark-300/50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400"><?= $no++ ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?= $anggota['nik'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white"><?= $anggota['nama_lengkap'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full 
                                                       bg-<?= $status_color ?>-500/10 text-<?= $status_color ?>-400">
                                                <?= $anggota['status_keluarga'] ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?= $anggota['jenis_kelamin'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            <?= $anggota['tempat_lahir'] ?>, <?= date('d/m/Y', strtotime($anggota['tanggal_lahir'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?= $anggota['status_kawin'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?= $anggota['pekerjaan'] ?? '-' ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-3">
                                                <a href="../penduduk/detail.php?id=<?= $anggota['id'] ?>"
                                                    class="text-indigo-400 hover:text-indigo-300">Detail</a>
                                                <a href="../penduduk/edit.php?id=<?= $anggota['id'] ?>"
                                                    class="text-amber-400 hover:text-amber-300">Edit</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>

</html>