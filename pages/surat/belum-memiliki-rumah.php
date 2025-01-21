<?php
session_start();
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Query untuk data penduduk
$query_penduduk = "SELECT p.*, kk.nomor_kk, w.nama_wilayah 
                   FROM penduduk p
                   LEFT JOIN kartu_keluarga kk ON p.id_kk = kk.id
                   LEFT JOIN wilayah w ON kk.id_wilayah = w.id
                   ORDER BY p.nama_lengkap";
$result_penduduk = mysqli_query($koneksi, $query_penduduk);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $id_penduduk = $_POST['id_penduduk'];
    $nomor_surat = $_POST['nomor_surat'];
    $tanggal_surat = $_POST['tanggal_surat'];
    $keperluan = $_POST['keperluan'];

    // Get penduduk data
    $query = "SELECT p.*, kk.alamat, kk.rt_rw, w.nama_wilayah 
              FROM penduduk p 
              LEFT JOIN kartu_keluarga kk ON p.id_kk = kk.id
              LEFT JOIN wilayah w ON kk.id_wilayah = w.id 
              WHERE p.id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_penduduk);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data_penduduk = mysqli_fetch_assoc($result);

    // Get absolute paths
    $template_path = realpath("../../Surat/SK_BELUM_MEMILIKI_RUMAH.docx");
    $output_dir = realpath("../../Surat/output");
    $output_filename = "surat_belum_rumah_" . time() . ".docx";
    $output_path = $output_dir . "/" . $output_filename;

    // Initialize template processor
    $template = new TemplateProcessor($template_path);

    // Replace template variables
    $template->setValue('nomor_surat', $nomor_surat);
    $template->setValue('tanggal_surat', date('d F Y', strtotime($tanggal_surat)));
    $template->setValue('nama_lengkap', $data_penduduk['nama_lengkap']);
    $template->setValue('nik', $data_penduduk['nik']);
    $template->setValue('tempat_lahir', $data_penduduk['tempat_lahir']);
    $template->setValue('tanggal_lahir', date('d F Y', strtotime($data_penduduk['tanggal_lahir'])));
    $template->setValue('jenis_kelamin', $data_penduduk['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan');
    $template->setValue('agama', $data_penduduk['agama']);
    $template->setValue('pekerjaan', $data_penduduk['pekerjaan']);
    $template->setValue('status_kawin', $data_penduduk['status_kawin']);
    $template->setValue('alamat', $data_penduduk['alamat']);
    $template->setValue('rt_rw', $data_penduduk['rt_rw']);
    $template->setValue('keperluan', $keperluan);

    // Set default values
    $template->setValue('nama_desa', 'DESA CONTOH');
    $template->setValue('nama_kecamatan', 'KECAMATAN CONTOH');
    $template->setValue('nama_kabupaten', 'KABUPATEN CONTOH');
    $template->setValue('nama_kepala_desa', 'NAMA KEPALA DESA');
    $template->setValue('nip_kepala_desa', '19XXXXXX XXXXXX X XXX');
    $template->setValue('alamat_desa', 'Jalan Contoh No. 123');
    $template->setValue('kode_pos', '12345');

    // Save document
    $template->saveAs($output_path);

    // Redirect with success message
    if (file_exists($output_path)) {
        header("Location: index.php?success=1&file=" . $output_filename);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Belum Memiliki Rumah - SINDAGA</title>
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
                            <h1 class="text-2xl font-bold text-white">Surat Keterangan Belum Memiliki Rumah</h1>
                            <p class="text-base text-gray-400 mt-1">Lengkapi formulir untuk membuat surat keterangan</p>
                        </div>
                        <a href="index.php"
                            class="px-4 py-2 bg-dark-200 text-gray-300 rounded-lg hover:bg-dark-300 transition-colors duration-200">
                            Kembali
                        </a>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-dark-200 rounded-xl border border-dark-300 shadow-lg">
                    <div class="border-b border-dark-300 p-8">
                        <h2 class="text-2xl font-semibold text-white">Formulir Surat Keterangan</h2>
                        <p class="mt-2 text-base text-gray-400">Lengkapi semua field yang ditandai dengan <span class="text-rose-500">*</span></p>
                    </div>

                    <form method="POST" class="p-8 space-y-12">
                        <!-- Informasi Surat -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">Informasi Surat</h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Nomor Surat <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="nomor_surat" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Masukkan nomor surat">
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Tanggal Surat <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="date" name="tanggal_surat" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                        </div>

                        <!-- Data Pemohon -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">Data Pemohon</h3>
                            <div>
                                <label class="block text-base font-medium text-gray-300 mb-3">
                                    Pilih Penduduk <span class="text-rose-500">*</span>
                                </label>
                                <select name="id_penduduk" required
                                    class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                           text-base py-3.5 px-5
                                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Pilih Penduduk</option>
                                    <?php while ($row = mysqli_fetch_assoc($result_penduduk)): ?>
                                        <option value="<?= $row['id'] ?>">
                                            <?= $row['nik'] ?> - <?= $row['nama_lengkap'] ?>
                                            (<?= $row['nama_wilayah'] ?? 'Wilayah tidak diketahui' ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">Keterangan Tambahan</h3>
                            <div>
                                <label class="block text-base font-medium text-gray-300 mb-3">
                                    Keperluan Surat <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="keperluan" required rows="4"
                                    class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                           text-base py-3.5 px-5 placeholder-gray-500
                                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Masukkan keperluan pembuatan surat"></textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-4 pt-8 border-t border-dark-300">
                            <button type="button" onclick="window.location.href='index.php'"
                                class="px-8 py-3.5 bg-dark-300 hover:bg-dark-100 text-gray-300 
                                       text-base font-medium rounded-lg transition-all duration-200">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-8 py-3.5 bg-indigo-500 hover:bg-indigo-600 text-white 
                                       text-base font-medium rounded-lg transition-all duration-200">
                                Buat Surat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>

</html>