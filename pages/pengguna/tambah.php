<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $level = $_POST['level'];

    $query = "INSERT INTO pengguna (username, password, nama_lengkap, level) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $username, $password, $nama_lengkap, $level);

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
    <title>Tambah Pengguna - SINDAGA</title>
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
                            <h1 class="text-2xl font-bold text-white">Tambah Pengguna</h1>
                            <p class="text-base text-gray-400 mt-1">Tambah pengguna sistem baru</p>
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
                        <h2 class="text-2xl font-semibold text-white">Formulir Pengguna</h2>
                        <p class="mt-2 text-base text-gray-400">Lengkapi semua field yang ditandai dengan <span class="text-rose-500">*</span></p>
                    </div>

                    <form method="POST" class="p-8 space-y-12">
                        <!-- Informasi Akun -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">Informasi Akun</h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Username <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="username" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Masukkan username">
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Password <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="password" name="password" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Masukkan password">
                                </div>
                            </div>
                        </div>

                        <!-- Data Pengguna -->
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">Data Pengguna</h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Nama Lengkap <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="nama_lengkap" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Masukkan nama lengkap">
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">
                                        Level Akses <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="level" required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Pilih Level</option>
                                        <option value="admin">Admin</option>
                                        <option value="petugas">Petugas</option>
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
