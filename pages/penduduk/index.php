<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Build Query Conditions
$conditions = [];
if (!empty($_GET['wilayah'])) {
    $wilayah = mysqli_real_escape_string($koneksi, $_GET['wilayah']);
    $conditions[] = "kk.id_wilayah = '$wilayah'";
}
if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($koneksi, $_GET['search']);
    $conditions[] = "(p.nik LIKE '%$search%' OR p.nama_lengkap LIKE '%$search%')";
}
if (!empty($_GET['status'])) {
    $status = mysqli_real_escape_string($koneksi, $_GET['status']);
    $conditions[] = "p.status = '$status'";
}
$where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

// Main Query
$query = "
    SELECT 
        p.*,
        kk.nomor_kk,
        kk.alamat,
        w.nama_wilayah,
        (SELECT COUNT(*) FROM penduduk WHERE id_kk = kk.id) as jumlah_anggota
    FROM 
        penduduk p
        LEFT JOIN kartu_keluarga kk ON p.id_kk = kk.id
        LEFT JOIN wilayah w ON kk.id_wilayah = w.id 
    $where_clause
    ORDER BY p.dibuat_pada DESC
";
$result = mysqli_query($koneksi, $query);

// Statistics Queries
$query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penduduk");
$total_penduduk = mysqli_fetch_assoc($query_total)['total'];

$query_menetap = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penduduk WHERE status = 'MENETAP'");
$total_menetap = mysqli_fetch_assoc($query_menetap)['total'];

$query_pindah = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penduduk WHERE status = 'PINDAH'");
$total_pindah = mysqli_fetch_assoc($query_pindah)['total'];

$query_meninggal = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penduduk WHERE status = 'MENINGGAL'");
$total_meninggal = mysqli_fetch_assoc($query_meninggal)['total'];

// Wilayah Dropdown Data
$query_wilayah = "SELECT id, nama_wilayah FROM wilayah ORDER BY nama_wilayah";
$result_wilayah = mysqli_query($koneksi, $query_wilayah);

// Status Colors Configuration
$status_colors = [
    'KEPALA KELUARGA' => 'emerald',
    'ISTRI' => 'blue',
    'ANAK' => 'violet',
    'LAINNYA' => 'gray'
];
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penduduk - SINDAGA</title>
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
                            <h1 class="text-xl font-bold text-white">Data Penduduk</h1>
                            <p class="text-sm text-gray-400">Kelola data penduduk</p>
                        </div>

                        <button onclick="window.location.href='tambah.php'"
                            class="inline-flex items-center px-3 py-1.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Penduduk
                        </button>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-4 gap-3 mt-3">
                        <div class="bg-dark-200 rounded-lg p-3 border border-dark-300">
                            <p class="text-xs text-gray-400">Total Penduduk</p>
                            <p class="text-lg font-bold text-white"><?= number_format($total_penduduk) ?></p>
                        </div>
                        <div class="bg-dark-200 rounded-lg p-3 border border-dark-300">
                            <p class="text-xs text-emerald-400">Menetap</p>
                            <p class="text-lg font-bold text-white"><?= number_format($total_menetap) ?></p>
                        </div>
                        <div class="bg-dark-200 rounded-lg p-3 border border-dark-300">
                            <p class="text-xs text-amber-400">Pindah</p>
                            <p class="text-lg font-bold text-white"><?= number_format($total_pindah) ?></p>
                        </div>
                        <div class="bg-dark-200 rounded-lg p-3 border border-dark-300">
                            <p class="text-xs text-rose-400">Meninggal</p>
                            <p class="text-lg font-bold text-white"><?= number_format($total_meninggal) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-dark-200 rounded-xl border border-dark-300 shadow-lg mb-6">
                    <div class="p-6">
                        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <!-- Wilayah Filter -->
                            <div>
                                <label class="block text-base font-medium text-gray-300 mb-2">Wilayah</label>
                                <select name="wilayah"
                                    class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                           text-base py-3 px-4
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Semua Wilayah</option>
                                    <?php while ($row = mysqli_fetch_assoc($result_wilayah)): ?>
                                        <option value="<?= $row['id'] ?>" <?= ($_GET['wilayah'] ?? '') == $row['id'] ? 'selected' : '' ?>>
                                            <?= $row['nama_wilayah'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label class="block text-base font-medium text-gray-300 mb-2">Status</label>
                                <select name="status"
                                    class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                           text-base py-3 px-4
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Semua Status</option>
                                    <option value="MENETAP" <?= ($_GET['status'] ?? '') == 'MENETAP' ? 'selected' : '' ?>>Menetap</option>
                                    <option value="PINDAH" <?= ($_GET['status'] ?? '') == 'PINDAH' ? 'selected' : '' ?>>Pindah</option>
                                    <option value="MENINGGAL" <?= ($_GET['status'] ?? '') == 'MENINGGAL' ? 'selected' : '' ?>>Meninggal</option>
                                </select>
                            </div>

                            <!-- Search Input -->
                            <div>
                                <label class="block text-base font-medium text-gray-300 mb-2">Cari</label>
                                <div class="relative">
                                    <input type="text" name="search"
                                        value="<?= $_GET['search'] ?? '' ?>"
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                               text-base py-3 pl-10 pr-4 placeholder-gray-500
                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Cari NIK atau nama...">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-end space-x-3">
                                <button type="submit"
                                    class="px-6 py-3 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 
                           transition-colors duration-200">
                                    Filter
                                </button>
                                <?php if (isset($_GET['wilayah']) || isset($_GET['search']) || isset($_GET['status'])): ?>
                                    <a href="index.php"
                                        class="px-6 py-3 bg-dark-300 text-gray-300 rounded-lg hover:bg-dark-100 
                              transition-colors duration-200">
                                        Reset
                                    </a>
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
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase">NIK</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase">Nama</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase">No KK</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase">Status KK</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase">Status</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase">JK</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase">Wilayah</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-300">
                                <?php
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)):
                                    $status_color = $status_colors[$row['status_keluarga']] ?? 'gray';
                                    $gender_color = $row['jenis_kelamin'] == 'L' ? 'blue' : 'rose';
                                ?>
                                    <tr class="hover:bg-dark-300/50 transition-colors duration-150">
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-400"><?= $no++ ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-300"><?= $row['nik'] ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-white"><?= $row['nama_lengkap'] ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-300"><?= $row['nomor_kk'] ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-<?= $status_color ?>-500/10 text-<?= $status_color ?>-400">
                                                <?= $row['status_keluarga'] ?>
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php
                                                switch ($row['status']) {
                                                    case 'MENETAP':
                                                        echo 'bg-emerald-500/10 text-emerald-400';
                                                        break;
                                                    case 'PINDAH':
                                                        echo 'bg-amber-500/10 text-amber-400';
                                                        break;
                                                    case 'MENINGGAL':
                                                        echo 'bg-rose-500/10 text-rose-400';
                                                        break;
                                                }
                                                ?>">
                                                <?= $row['status'] ?>
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-<?= $gender_color ?>-500/10 text-<?= $gender_color ?>-400">
                                                <?= $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-300"><?= $row['nama_wilayah'] ?? '-' ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="detail.php?id=<?= $row['id'] ?>"
                                                    class="text-indigo-400 hover:text-indigo-300 transition-colors duration-200">Detail</a>
                                                <a href="edit.php?id=<?= $row['id'] ?>"
                                                    class="text-amber-400 hover:text-amber-300 transition-colors duration-200">Edit</a>
                                                <button onclick="confirmDelete(<?= $row['id'] ?>)"
                                                    class="text-rose-400 hover:text-rose-300 transition-colors duration-200">Hapus</button>
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

    <script>
        function confirmDelete(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data penduduk ini?')) {
                window.location.href = 'hapus.php?id=' + id;
            }
        }
    </script>
</body>

</html>