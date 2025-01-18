<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get filter parameters
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$wilayah = $_GET['wilayah'] ?? '';

// Build base query
$query = "
    SELECT 
        kk.*,
        w.nama_wilayah,
        (SELECT COUNT(*) FROM penduduk WHERE id_kk = kk.id) as jumlah_anggota,
        (SELECT nama_lengkap FROM penduduk WHERE id_kk = kk.id AND status_keluarga = 'KEPALA KELUARGA' LIMIT 1) as kepala_keluarga
    FROM 
        kartu_keluarga kk
        LEFT JOIN wilayah w ON kk.id_wilayah = w.id
";

// Build conditions
$conditions = [];
$conditions[] = "MONTH(kk.dibuat_pada) = '$bulan'";
$conditions[] = "YEAR(kk.dibuat_pada) = '$tahun'";

if ($wilayah) {
    $conditions[] = "w.id = " . intval($wilayah);
}

// Add conditions to query
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY kk.dibuat_pada DESC";
$result = mysqli_query($koneksi, $query);

// Get wilayah for dropdown
$query_wilayah = "SELECT id, nama_wilayah FROM wilayah ORDER BY nama_wilayah";
$result_wilayah = mysqli_query($koneksi, $query_wilayah);

// Statistics
$total_kk = mysqli_num_rows($result);

// Get nama bulan array
$nama_bulan = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kartu Keluarga - SINDAGA</title>
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

        @media print {
            body {
                background-color: white;
                color: black;
            }

            .no-print {
                display: none;
            }

            .print-only {
                display: block;
            }

            .bg-dark-200 {
                background-color: white;
            }

            .text-white {
                color: black;
            }

            .text-gray-300 {
                color: #374151;
            }

            .border-dark-300 {
                border-color: #e5e7eb;
            }
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
                            <h1 class="text-2xl font-bold text-white">Laporan Data Kartu Keluarga</h1>
                            <p class="text-base text-gray-400 mt-1">Laporan bulanan data kartu keluarga</p>
                        </div>

                        <div class="flex space-x-3">
                            <button onclick="window.print()"
                                class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 
                                       transition-colors duration-200 flex items-center no-print">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Cetak PDF
                            </button>

                            <button onclick="window.location.href='export-kk.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&wilayah=<?= $wilayah ?>'"
                                class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 
                                       transition-colors duration-200 flex items-center no-print">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export Excel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-dark-200 rounded-xl border border-dark-300 mb-6 no-print">
                    <div class="p-6">
                        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <!-- Bulan -->
                            <div>
                                <label class="block text-base font-medium text-gray-300 mb-2">Bulan</label>
                                <select name="bulan"
                                    class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                           text-base py-3 px-4
                                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <?php foreach ($nama_bulan as $value => $label): ?>
                                        <option value="<?= $value ?>" <?= $bulan == $value ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Tahun -->
                            <div>
                                <label class="block text-base font-medium text-gray-300 mb-2">Tahun</label>
                                <select name="tahun"
                                    class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                           text-base py-3 px-4
                                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <?php
                                    $tahun_sekarang = date('Y');
                                    for ($i = $tahun_sekarang; $i >= $tahun_sekarang - 5; $i--):
                                    ?>
                                        <option value="<?= $i ?>" <?= $tahun == $i ? 'selected' : '' ?>>
                                            <?= $i ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <!-- Wilayah -->
                            <div>
                                <label class="block text-base font-medium text-gray-300 mb-2">Wilayah</label>
                                <select name="wilayah"
                                    class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                           text-base py-3 px-4
                                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Semua Wilayah</option>
                                    <?php
                                    mysqli_data_seek($result_wilayah, 0);
                                    while ($row = mysqli_fetch_assoc($result_wilayah)):
                                    ?>
                                        <option value="<?= $row['id'] ?>" <?= $wilayah == $row['id'] ? 'selected' : '' ?>>
                                            <?= $row['nama_wilayah'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-end space-x-3">
                                <button type="submit"
                                    class="px-6 py-3 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 
                                           transition-colors duration-200">
                                    Tampilkan
                                </button>
                                <a href="kk.php"
                                    class="px-6 py-3 bg-dark-300 text-gray-300 rounded-lg hover:bg-dark-100 
                                          transition-colors duration-200">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-dark-200 rounded-lg p-4 border border-dark-300">
                        <p class="text-sm text-gray-400">Total KK</p>
                        <p class="text-xl font-bold text-white"><?= number_format($total_kk) ?></p>
                    </div>
                    <div class="bg-dark-200 rounded-lg p-4 border border-dark-300">
                        <p class="text-sm text-gray-400">Total Anggota</p>
                        <p class="text-xl font-bold text-white">
                            <?php
                            $total_anggota = 0;
                            mysqli_data_seek($result, 0);
                            while ($row = mysqli_fetch_assoc($result)) {
                                $total_anggota += $row['jumlah_anggota'];
                            }
                            echo number_format($total_anggota);
                            ?>
                        </p>
                    </div>
                    <div class="bg-dark-200 rounded-lg p-4 border border-dark-300">
                        <p class="text-sm text-gray-400">Rata-rata Anggota</p>
                        <p class="text-xl font-bold text-white">
                            <?= $total_kk > 0 ? number_format($total_anggota / $total_kk, 1) : '0' ?>
                        </p>
                    </div>
                </div>

                <!-- Report Header (Print Only) -->
                <div class="hidden print-only mb-8">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold">Laporan Data Kartu Keluarga</h2>
                        <p class="text-lg">Periode: <?= $nama_bulan[$bulan] ?> <?= $tahun ?></p>
                        <?php if ($wilayah): ?>
                            <?php
                            mysqli_data_seek($result_wilayah, 0);
                            while ($row = mysqli_fetch_assoc($result_wilayah)) {
                                if ($row['id'] == $wilayah) {
                                    echo "<p class='text-lg'>Wilayah: {$row['nama_wilayah']}</p>";
                                    break;
                                }
                            }
                            ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="bg-dark-200 rounded-lg border border-dark-300">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-dark-300">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">No KK</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Kepala Keluarga</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Alamat</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">RT/RW</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Wilayah</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Jumlah Anggota</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-300">
                                <?php
                                $no = 1;
                                mysqli_data_seek($result, 0);
                                while ($row = mysqli_fetch_assoc($result)):
                                ?>
                                    <tr class="hover:bg-dark-300/50 transition-colors duration-150">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-400"><?= $no++ ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300"><?= $row['nomor_kk'] ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-white"><?= $row['kepala_keluarga'] ?? '-' ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-300"><?= $row['alamat'] ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300"><?= $row['rt_rw'] ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300"><?= $row['nama_wilayah'] ?? '-' ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-indigo-500/10 text-indigo-400">
                                                <?= $row['jumlah_anggota'] ?> orang
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300">
                                            <?= date('d/m/Y', strtotime($row['dibuat_pada'])) ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Print Footer -->
                <div class="hidden print-only mt-8">
                    <div class="text-right">
                        <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
                        <p class="mt-8">Petugas,</p>
                        <p class="mt-20"><?= $_SESSION['nama_lengkap'] ?></p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>

</html>