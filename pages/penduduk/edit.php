<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'];

// Query untuk data penduduk
$query = "SELECT * FROM penduduk WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

// Query untuk dropdown KK
$query_kk = "SELECT kk.*, w.nama_wilayah,
            (SELECT COUNT(*) FROM penduduk WHERE id_kk = kk.id) as jumlah_anggota
            FROM kartu_keluarga kk
            LEFT JOIN wilayah w ON kk.id_wilayah = w.id
            ORDER BY kk.nomor_kk";
$result_kk = mysqli_query($koneksi, $query_kk);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik = mysqli_real_escape_string($koneksi, $_POST['nik']);
    $id_kk = $_POST['id_kk'] ?: null;
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $tempat_lahir = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $agama = $_POST['agama'];
    $status_kawin = $_POST['status_kawin'];
    $pekerjaan = mysqli_real_escape_string($koneksi, $_POST['pekerjaan']);
    $status_keluarga = $_POST['status_keluarga'];
    $status = $_POST['status'] ?? 'MENETAP';

    $query = "UPDATE penduduk SET 
              nik = ?, id_kk = ?, nama_lengkap = ?, tempat_lahir = ?, 
              tanggal_lahir = ?, jenis_kelamin = ?, agama = ?, 
              status_kawin = ?, pekerjaan = ?, status_keluarga = ?,
              status = ?
              WHERE id = ?";

    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "sisssssssssi",
        $nik,
        $id_kk,
        $nama_lengkap,
        $tempat_lahir,
        $tanggal_lahir,
        $jenis_kelamin,
        $agama,
        $status_kawin,
        $pekerjaan,
        $status_keluarga,
        $status,
        $id
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php?success=2");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Penduduk - SINDAGA</title>
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
                            <h1 class="text-2xl font-bold text-white">Edit Penduduk</h1>
                            <p class="text-base text-gray-400 mt-1">Edit data penduduk yang sudah ada</p>
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
                        <h2 class="text-2xl font-semibold text-white">Formulir Edit Data</h2>
                        <p class="mt-2 text-base text-gray-400">Perbarui informasi data penduduk</p>
                    </div>

                    <form method="POST" class="p-8 space-y-12">
                        <!-- Identitas Utama -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">
                                Identitas Utama
                            </h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        NIK <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="nik" required maxlength="16"
                                        value="<?= $data['nik'] ?>"
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Kartu Keluarga <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="id_kk" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Pilih Kartu Keluarga</option>
                                        <?php while ($row = mysqli_fetch_assoc($result_kk)): ?>
                                            <option value="<?= $row['id'] ?>" <?= $data['id_kk'] == $row['id'] ? 'selected' : '' ?>>
                                                <?= $row['nomor_kk'] ?> - <?= $row['nama_wilayah'] ?>
                                                (<?= $row['jumlah_anggota'] ?> anggota)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
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
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Nama Lengkap <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="nama_lengkap" required
                                        value="<?= $data['nama_lengkap'] ?>"
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Status dalam Keluarga <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="status_keluarga" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Pilih Status</option>
                                        <?php
                                        $status_list = ['KEPALA KELUARGA', 'ISTRI', 'ANAK', 'LAINNYA'];
                                        foreach ($status_list as $status):
                                        ?>
                                            <option value="<?= $status ?>" <?= $data['status_keluarga'] == $status ? 'selected' : '' ?>>
                                                <?= $status ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Kelahiran -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">
                                Informasi Kelahiran
                            </h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Tempat Lahir <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="tempat_lahir" required
                                        value="<?= $data['tempat_lahir'] ?>"
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Tanggal Lahir <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="date" name="tanggal_lahir" required
                                        value="<?= $data['tanggal_lahir'] ?>"
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                        </div>

                        <!-- Data Tambahan -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">
                                Data Tambahan
                            </h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Jenis Kelamin <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="flex space-x-10 mt-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="jenis_kelamin" value="L" required
                                                <?= $data['jenis_kelamin'] == 'L' ? 'checked' : '' ?>
                                                class="w-6 h-6 text-indigo-500 bg-dark-100 border-dark-300 
                                                       focus:ring-2 focus:ring-indigo-500">
                                            <span class="ml-3 text-base text-gray-300">Laki-laki</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="jenis_kelamin" value="P" required
                                                <?= $data['jenis_kelamin'] == 'P' ? 'checked' : '' ?>
                                                class="w-6 h-6 text-indigo-500 bg-dark-100 border-dark-300 
                                                       focus:ring-2 focus:ring-indigo-500">
                                            <span class="ml-3 text-base text-gray-300">Perempuan</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Agama <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="agama" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Pilih Agama</option>
                                        <?php foreach (['ISLAM', 'KRISTEN', 'KATOLIK', 'HINDU', 'BUDDHA', 'KONGHUCU'] as $agama): ?>
                                            <option value="<?= $agama ?>" <?= $data['agama'] == $agama ? 'selected' : '' ?>>
                                                <?= ucfirst(strtolower($agama)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-base font-medium text-gray-300 mb-3">
                                Status <span class="text-rose-500">*</span>
                            </label>
                            <select name="status" required
                                class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
               text-base py-3.5 px-5
               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="MENETAP">Menetap</option>
                                <option value="PINDAH">Pindah</option>
                                <option value="MENINGGAL">Meninggal</option>
                            </select>
                        </div>

                        <!-- Status & Pekerjaan -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">
                                Status & Pekerjaan
                            </h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Status Perkawinan <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="status_kawin" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Pilih Status</option>
                                        <?php foreach (['BELUM KAWIN', 'KAWIN', 'CERAI'] as $status): ?>
                                            <option value="<?= $status ?>" <?= $data['status_kawin'] == $status ? 'selected' : '' ?>>
                                                <?= $status ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Pekerjaan
                                    </label>
                                    <input type="text" name="pekerjaan"
                                        value="<?= $data['pekerjaan'] ?>"
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Masukkan pekerjaan">
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
                                Simpan Perubahan
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