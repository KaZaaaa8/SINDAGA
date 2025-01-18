<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Query untuk dropdown wilayah
$query_wilayah = "SELECT id, nama_wilayah FROM wilayah ORDER BY nama_wilayah";
$result_wilayah = mysqli_query($koneksi, $query_wilayah);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomor_kk = mysqli_real_escape_string($koneksi, $_POST['nomor_kk']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $rt_rw = mysqli_real_escape_string($koneksi, $_POST['rt_rw']);
    $id_wilayah = $_POST['id_wilayah'];

    $query = "INSERT INTO kartu_keluarga (nomor_kk, alamat, rt_rw, id_wilayah) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "sssi", $nomor_kk, $alamat, $rt_rw, $id_wilayah);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php?success=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kartu Keluarga - SINDAGA</title>
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
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-white">Tambah Kartu Keluarga</h1>
                            <p class="text-base text-gray-400 mt-1">Tambah data kartu keluarga baru</p>
                        </div>
                        <a href="index.php"
                            class="px-4 py-2 bg-dark-200 text-gray-300 rounded-lg hover:bg-dark-300 
                                  transition-colors duration-200">
                            Kembali
                        </a>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-dark-200 rounded-xl border border-dark-300 shadow-lg">
                    <div class="border-b border-dark-300 p-8">
                        <h2 class="text-2xl font-semibold text-white">Formulir Kartu Keluarga</h2>
                        <p class="mt-2 text-base text-gray-400">Lengkapi semua field yang ditandai dengan <span class="text-rose-500">*</span></p>
                    </div>

                    <form method="POST" class="p-8 space-y-8">
                        <!-- Identitas KK -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-6 pb-2 border-b border-dark-300">
                                Identitas Kartu Keluarga
                            </h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Nomor KK <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="nomor_kk" required maxlength="16"
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Masukkan 16 digit nomor KK">
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Alamat Lengkap <span class="text-rose-500">*</span>
                                    </label>
                                    <textarea name="alamat" required rows="3"
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Masukkan alamat lengkap"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Data Wilayah -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-6 pb-2 border-b border-dark-300">
                                Data Wilayah
                            </h3>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        RT/RW <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="rt_rw" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Contoh: 001/002">
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Wilayah <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="id_wilayah" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Pilih Wilayah</option>
                                        <?php while ($row = mysqli_fetch_assoc($result_wilayah)): ?>
                                            <option value="<?= $row['id'] ?>">
                                                <?= $row['nama_wilayah'] ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
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
                                Simpan Data
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