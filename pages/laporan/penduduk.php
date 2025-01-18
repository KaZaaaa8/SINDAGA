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
        p.*,
        kk.nomor_kk,
        kk.alamat,
        kk.rt_rw,
        w.nama_wilayah,
        w.ketua_rt
    FROM 
        penduduk p
        LEFT JOIN kartu_keluarga kk ON p.id_kk = kk.id
        LEFT JOIN wilayah w ON kk.id_wilayah = w.id
";

// Build conditions
$conditions = [];
$conditions[] = "MONTH(p.dibuat_pada) = '$bulan'";
$conditions[] = "YEAR(p.dibuat_pada) = '$tahun'";

if ($wilayah) {
    $conditions[] = "w.id = " . intval($wilayah);
}

// Add conditions to query
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY p.dibuat_pada DESC";
$result = mysqli_query($koneksi, $query);

// Get wilayah for dropdown
$query_wilayah = "SELECT id, nama_wilayah FROM wilayah ORDER BY nama_wilayah";
$result_wilayah = mysqli_query($koneksi, $query_wilayah);

// Statistics
$total_penduduk = mysqli_num_rows($result);

// Get status counts using the same conditions
$status_query = $query;
$status_result = mysqli_query($koneksi, $status_query);
$status_count = [
    'MENETAP' => 0,
    'PINDAH' => 0,
    'MENINGGAL' => 0
];

while ($row = mysqli_fetch_assoc($status_result)) {
    $status_count[$row['status']]++;
}
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penduduk - SINDAGA</title>
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
                            <h1 class="text-2xl font-bold text-white">Laporan Data Penduduk</h1>
                            <p class="text-base text-gray-400 mt-1">Laporan bulanan data penduduk</p>
                        </div>

                        <div class="flex space-x-3">
                            <!-- Print Button -->
                            <button onclick="window.print()"
                                class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 
                                       transition-colors duration-200 flex items-center no-print">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Cetak PDF
                            </button>

                            <!-- Excel Export Button -->
                            <button onclick="window.location.href='export-penduduk.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&wilayah=<?= $wilayah ?>'"
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
                                    <?php
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
                                    foreach ($nama_bulan as $value => $label):
                                    ?>
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
                                <a href="penduduk.php"
                                    class="px-6 py-3 bg-dark-300 text-gray-300 rounded-lg hover:bg-dark-100 
                                          transition-colors duration-200">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-4 gap-4 mb-6">
                    <div class="bg-dark-200 rounded-lg p-4 border border-dark-300">
                        <p class="text-sm text-gray-400">Total Data</p>
                        <p class="text-xl font-bold text-white"><?= number_format($total_penduduk) ?></p>
                    </div>
                    <div class="bg-dark-200 rounded-lg p-4 border border-dark-300">
                        <p class="text-sm text-emerald-400">Menetap</p>
                        <p class="text-xl font-bold text-white"><?= number_format($status_count['MENETAP']) ?></p>
                    </div>
                    <div class="bg-dark-200 rounded-lg p-4 border border-dark-300">
                        <p class="text-sm text-amber-400">Pindah</p>
                        <p class="text-xl font-bold text-white"><?= number_format($status_count['PINDAH']) ?></p>
                    </div>
                    <div class="bg-dark-200 rounded-lg p-4 border border-dark-300">
                        <p class="text-sm text-rose-400">Meninggal</p>
                        <p class="text-xl font-bold text-white"><?= number_format($status_count['MENINGGAL']) ?></p>
                    </div>
                </div>

                <!-- Report Header (Print Only) -->
                <div class="hidden print-only mb-8">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold">Laporan Data Penduduk</h2>
                        <p class="text-lg">Periode: <?= $nama_bulan[$bulan] ?> <?= $tahun ?></p>
                        <?php if ($wilayah): ?>
                            <?php
                            $wilayah_name = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_wilayah FROM wilayah WHERE id = " . intval($wilayah)))['nama_wilayah'];
                            ?>
                            <p class="text-lg">Wilayah: <?= $wilayah_name ?></p>
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
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">NIK</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Nama</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">No KK</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">RT/RW</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Wilayah</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Status</th>
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
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300"><?= $row['nik'] ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-white"><?= $row['nama_lengkap'] ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300"><?= $row['nomor_kk'] ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300"><?= $row['rt_rw'] ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300"><?= $row['nama_wilayah'] ?? '-' ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap">
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