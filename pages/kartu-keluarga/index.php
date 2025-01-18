<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Handle search and filter
$where_clause = "1=1";
if (isset($_GET['wilayah']) && $_GET['wilayah'] != '') {
    $wilayah = mysqli_real_escape_string($koneksi, $_GET['wilayah']);
    $where_clause .= " AND kk.id_wilayah = '$wilayah'";
}

if (isset($_GET['search']) && $_GET['search'] != '') {
    $search = mysqli_real_escape_string($koneksi, $_GET['search']);
    $where_clause .= " AND (kk.nomor_kk LIKE '%$search%' OR kk.alamat LIKE '%$search%')";
}

// Query untuk data KK dengan wilayah dan jumlah anggota
$query = "SELECT kk.*, w.nama_wilayah,
          (SELECT COUNT(*) FROM penduduk WHERE id_kk = kk.id) as jumlah_anggota
          FROM kartu_keluarga kk
          LEFT JOIN wilayah w ON kk.id_wilayah = w.id 
          WHERE $where_clause
          ORDER BY kk.dibuat_pada DESC";
$result = mysqli_query($koneksi, $query);

// Query untuk dropdown wilayah
$query_wilayah = "SELECT id, nama_wilayah FROM wilayah ORDER BY nama_wilayah";
$result_wilayah = mysqli_query($koneksi, $query_wilayah);
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kartu Keluarga - SINDAGA</title>
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
                            <h1 class="text-2xl font-bold text-white">Data Kartu Keluarga</h1>
                            <p class="text-base text-gray-400 mt-1">Kelola data kartu keluarga</p>
                        </div>

                        <button onclick="window.location.href='tambah.php'"
                            class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 
                                       transition-colors duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Tambah KK
                        </button>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-dark-200 rounded-xl border border-dark-300 p-6 mb-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-base font-medium text-gray-300 mb-2">Wilayah</label>
                            <select name="wilayah"
                                class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                           text-base py-3 px-4
                                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Semua Wilayah</option>
                                <?php while ($row = mysqli_fetch_assoc($result_wilayah)): ?>
                                    <option value="<?= $row['id'] ?>"
                                        <?= ($_GET['wilayah'] ?? '') == $row['id'] ? 'selected' : '' ?>>
                                        <?= $row['nama_wilayah'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-base font-medium text-gray-300 mb-2">Cari</label>
                            <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>"
                                class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                          text-base py-3 px-4 placeholder-gray-500
                                          focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Cari nomor KK atau alamat...">
                        </div>

                        <div class="flex items-end space-x-3">
                            <button type="submit"
                                class="px-6 py-3 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 
                                           transition-colors duration-200">
                                Filter
                            </button>
                            <?php if (isset($_GET['wilayah']) || isset($_GET['search'])): ?>
                                <a href="index.php"
                                    class="px-6 py-3 bg-dark-300 text-gray-300 rounded-lg hover:bg-dark-100 
                                          transition-colors duration-200">
                                    Reset
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="bg-dark-200 rounded-xl border border-dark-300 shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-dark-300">
                            <thead class="bg-dark-300">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        No
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Nomor KK
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Alamat
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Wilayah
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Anggota
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-300">
                                <?php
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)):
                                ?>
                                    <tr class="hover:bg-dark-300/50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                            <?= $no++ ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                            <?= $row['nomor_kk'] ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-300">
                                            <?= $row['alamat'] ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            <?= $row['nama_wilayah'] ?? '-' ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                                       bg-indigo-500/10 text-indigo-400">
                                                <?= $row['jumlah_anggota'] ?> orang
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-3">
                                                <a href="detail.php?id=<?= $row['id'] ?>"
                                                    class="text-indigo-400 hover:text-indigo-300 transition-colors duration-200">
                                                    Detail
                                                </a>
                                                <a href="edit.php?id=<?= $row['id'] ?>"
                                                    class="text-amber-400 hover:text-amber-300 transition-colors duration-200">
                                                    Edit
                                                </a>
                                                <button onclick="confirmDelete(<?= $row['id'] ?>)"
                                                    class="text-rose-400 hover:text-rose-300 transition-colors duration-200">
                                                    Hapus
                                                </button>
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
            if (confirm('Apakah Anda yakin ingin menghapus data kartu keluarga ini?')) {
                window.location.href = 'hapus.php?id=' + id;
            }
        }
    </script>
</body>

</html>