<?php
require_once __DIR__ . '/../config/config.php';
$current_path = $_SERVER['PHP_SELF'];
$current_section = explode('/', $current_path)[2] ?? '';
?>

<aside class="w-64 bg-dark-200 border-r border-dark-300 min-h-screen hidden md:block">
    <nav class="p-4">
        <!-- Main Navigation -->
        <div class="space-y-2">
            <!-- Dashboard -->
            <a href="<?= $base_url ?>/index.php"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200
                      <?= $current_section == '' || $current_section == 'index.php' ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-400 hover:bg-dark-300 hover:text-white' ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Dashboard
            </a>

            <!-- Data Management Section -->
            <div class="pt-4 mt-4 border-t border-dark-300">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Data Management</p>

                <!-- Data Penduduk -->
                <a href="<?= $base_url ?>/pages/penduduk/index.php"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200
                          <?= $current_section == 'penduduk' ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-400 hover:bg-dark-300 hover:text-white' ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Data Penduduk
                </a>

                <!-- Kartu Keluarga -->
                <a href="<?= $base_url ?>/pages/kartu-keluarga/index.php"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200
                          <?= $current_section == 'kartu-keluarga' ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-400 hover:bg-dark-300 hover:text-white' ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Kartu Keluarga
                </a>

                <!-- RT -->
                <a href="<?= $base_url ?>/pages/wilayah/index.php"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200
                          <?= $current_section == 'wilayah' ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-400 hover:bg-dark-300 hover:text-white' ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Data RT
                </a>
            </div>

            <!-- Reports Section -->
            <div class="pt-4 mt-4 border-t border-dark-300">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Laporan</p>

                <!-- Laporan Penduduk -->
                <a href="<?= $base_url ?>/pages/laporan/penduduk.php"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200
                          <?= $current_section == 'laporan' && strpos($current_path, 'penduduk.php') ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-400 hover:bg-dark-300 hover:text-white' ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Laporan Penduduk
                </a>

                <!-- Laporan KK -->
                <a href="<?= $base_url ?>/pages/laporan/kartu-keluarga.php"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200
                          <?= $current_section == 'laporan' && strpos($current_path, 'kartu-keluarga.php') ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-400 hover:bg-dark-300 hover:text-white' ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Laporan KK
                </a>
            </div>

            <?php if ($_SESSION['level'] == 'admin'): ?>
                <!-- Admin Settings -->
                <div class="pt-4 mt-4 border-t border-dark-300">
                    <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Administrator</p>

                    <!-- Pengguna -->
                    <a href="<?= $base_url ?>/pages/pengguna/index.php"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200
                              <?= $current_section == 'pengguna' ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-400 hover:bg-dark-300 hover:text-white' ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Kelola Pengguna
                    </a>

                    <!-- Database -->
                    <a href="<?= $base_url ?>/pages/database/index.php"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200
                              <?= $current_section == 'database' ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-400 hover:bg-dark-300 hover:text-white' ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                        </svg>
                        Database
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </nav>
</aside>