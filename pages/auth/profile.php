<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM pengguna WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    $success = false;
    $error = null;

    if (empty($password_lama)) {
        $query = "UPDATE pengguna SET nama_lengkap = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "si", $nama_lengkap, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['nama_lengkap'] = $nama_lengkap;
            $success = true;
        }
    } else {
        if (password_verify($password_lama, $user['password'])) {
            if ($password_baru === $konfirmasi_password) {
                $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
                $query = "UPDATE pengguna SET nama_lengkap = ?, password = ? WHERE id = ?";
                $stmt = mysqli_prepare($koneksi, $query);
                mysqli_stmt_bind_param($stmt, "ssi", $nama_lengkap, $password_hash, $user_id);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['nama_lengkap'] = $nama_lengkap;
                    $success = true;
                }
            } else {
                $error = "Password baru dan konfirmasi tidak cocok";
            }
        } else {
            $error = "Password lama tidak sesuai";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - SINDAGA</title>
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
            <div class="max-w-3xl mx-auto">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-white">Pengaturan Profil</h1>
                    <p class="text-base text-gray-400 mt-1">Kelola informasi profil dan keamanan akun Anda</p>
                </div>

                <?php if (isset($success) && $success): ?>
                    <div class="mb-6 rounded-lg bg-emerald-500/10 p-4 border border-emerald-500/20">
                        <div class="flex">
                            <svg class="h-5 w-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="ml-3 text-sm text-emerald-400">Profil berhasil diperbarui</p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="mb-6 rounded-lg bg-rose-500/10 p-4 border border-rose-500/20">
                        <div class="flex">
                            <svg class="h-5 w-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="ml-3 text-sm text-rose-400"><?= $error ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Profile Form -->
                <div class="bg-dark-200 rounded-xl border border-dark-300 shadow-lg">
                    <form method="POST" class="divide-y divide-dark-300">
                        <!-- Basic Info Section -->
                        <div class="p-8 space-y-6">
                            <h3 class="text-lg font-semibold text-indigo-400 mb-8 pb-3 border-b border-dark-300">Informasi Dasar</h3>

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">Username</label>
                                    <input type="text"
                                        value="<?= htmlspecialchars($user['username']) ?>"
                                        disabled
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-400 
                                               text-base py-3.5 px-5">
                                    <p class="mt-2 text-sm text-gray-400">Username tidak dapat diubah</p>
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">Nama Lengkap</label>
                                    <input type="text"
                                        name="nama_lengkap"
                                        value="<?= htmlspecialchars($user['nama_lengkap']) ?>"
                                        required
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="p-8 space-y-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-indigo-400">Ubah Password</h3>
                                <span class="text-sm text-gray-400">Opsional</span>
                            </div>

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">Password Lama</label>
                                    <input type="password"
                                        name="password_lama"
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">Password Baru</label>
                                    <input type="password"
                                        name="password_baru"
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-300 mb-3">Konfirmasi Password Baru</label>
                                    <input type="password"
                                        name="konfirmasi_password"
                                        class="w-full rounded-lg bg-dark-100 border-dark-300 text-gray-300 
                                               text-base py-3.5 px-5 placeholder-gray-500
                                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="px-8 py-4 bg-dark-300/50 flex justify-end space-x-4">
                            <button type="button"
                                onclick="window.location.href='../../index.php'"
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