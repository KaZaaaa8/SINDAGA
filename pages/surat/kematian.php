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

// Query untuk data penduduk yang masih hidup
$query_penduduk = "SELECT p.*, kk.nomor_kk, kk.alamat, kk.rt_rw, w.nama_wilayah 
                   FROM penduduk p
                   LEFT JOIN kartu_keluarga kk ON p.id_kk = kk.id
                   LEFT JOIN wilayah w ON kk.id_wilayah = w.id
                   WHERE p.status != 'MENINGGAL'
                   ORDER BY p.nama_lengkap";
$result_penduduk = mysqli_query($koneksi, $query_penduduk);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $nomor_surat = $_POST['nomor_surat'];
    $tanggal_surat = $_POST['tanggal_surat'];
    $id_penduduk = $_POST['id_penduduk'];
    $hari_meninggal = $_POST['hari_meninggal'];
    $tanggal_meninggal = $_POST['tanggal_meninggal'];
    $tempat_meninggal = $_POST['tempat_meninggal'];
    $sebab = $_POST['sebab'];
    $nama_pelapor = $_POST['nama_pelapor'];
    $hubungan_pelapor = $_POST['hubungan_pelapor'];

    // Begin transaction
    mysqli_begin_transaction($koneksi);

    try {
        // Update status penduduk menjadi meninggal
        $query_update = "UPDATE penduduk SET status = 'MENINGGAL' WHERE id = ?";
        $stmt_update = mysqli_prepare($koneksi, $query_update);
        mysqli_stmt_bind_param($stmt_update, "i", $id_penduduk);
        mysqli_stmt_execute($stmt_update);

        // Get data penduduk
        $query = "SELECT p.*, kk.alamat, kk.rt_rw 
                  FROM penduduk p 
                  LEFT JOIN kartu_keluarga kk ON p.id_kk = kk.id 
                  WHERE p.id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id_penduduk);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data_penduduk = mysqli_fetch_assoc($result);

        // Calculate age
        $tanggal_lahir = new DateTime($data_penduduk['tanggal_lahir']);
        $today = new DateTime();
        $umur = $tanggal_lahir->diff($today)->y;

        // Get absolute paths
        $template_path = realpath("../../Surat/SK_KEMATIAN.docx");
        $output_dir = realpath("../../Surat/output");
        $output_filename = "surat_kematian_" . time() . ".docx";
        $output_path = $output_dir . "/" . $output_filename;

        // Initialize template processor
        $template = new TemplateProcessor($template_path);

        // Replace template variables
        $template->setValue('nomor_surat', $nomor_surat);
        $template->setValue('tanggal_surat', date('d F Y', strtotime($tanggal_surat)));
        $template->setValue('nama', $data_penduduk['nama_lengkap']);
        $template->setValue('jenis_kelamin', $data_penduduk['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan');
        $template->setValue('alamat', $data_penduduk['alamat'] . ' RT.' . $data_penduduk['rt_rw']);
        $template->setValue('umur', $umur . ' Tahun');
        $template->setValue('hari', $hari_meninggal);
        $template->setValue('tanggal', date('d F Y', strtotime($tanggal_meninggal)));
        $template->setValue('tempat', $tempat_meninggal);
        $template->setValue('sebab', $sebab);
        $template->setValue('nama_pelapor', $nama_pelapor);
        $template->setValue('hubungan_pelapor', $hubungan_pelapor);

        // Save document
        $template->saveAs($output_path);

        // Commit transaction
        mysqli_commit($koneksi);

        // Redirect with success message
        header("Location: index.php?success=1&file=" . $output_filename);
        exit();
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Kematian - SINDAGA</title>
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
                            <h1 class="text-2xl font-bold text-white">Surat Keterangan Kematian</h1>
                            <p class="text-base text-gray-400 mt-1">Buat surat keterangan kematian</p>
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
                        <h2 class="text-2xl font-semibold text-white">Formulir Surat Kematian</h2>
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

                        <!-- Data Almarhum -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">Data Almarhum</h3>
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

                        <!-- Informasi Kematian -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">Informasi Kematian</h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Hari <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="hari_meninggal" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Pilih Hari</option>
                                        <option value="Senin">Senin</option>
                                        <option value="Selasa">Selasa</option>
                                        <option value="Rabu">Rabu</option>
                                        <option value="Kamis">Kamis</option>
                                        <option value="Jumat">Jumat</option>
                                        <option value="Sabtu">Sabtu</option>
                                        <option value="Minggu">Minggu</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Tanggal <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="date" name="tanggal_meninggal" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Tempat <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="tempat_meninggal" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Masukkan tempat meninggal">
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Sebab <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="sebab" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Masukkan sebab kematian">
                                </div>
                            </div>
                        </div>

                        <!-- Data Pelapor -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">Data Pelapor</h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Nama Pelapor <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="nama_pelapor" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Masukkan nama pelapor">
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Hubungan dengan Almarhum <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="hubungan_pelapor" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Contoh: Anak Kandung">
                                </div>
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