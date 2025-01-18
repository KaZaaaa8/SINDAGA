<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<header class="bg-dark-200 border-b border-dark-300">
    <div class="container mx-auto">
        <div class="flex items-center justify-between px-4 py-3">
            <!-- Logo dan Nama Aplikasi -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="p-2 bg-indigo-500/10 rounded-lg">
                        <svg class="h-8 w-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-xl font-bold text-white">SINDAGA</h1>
                        <p class="text-xs text-gray-400">Sistem Informasi Data Warga</p>
                    </div>
                </div>
            </div>

            <!-- Menu User -->
            <div class="flex items-center space-x-6">
                <!-- User Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center space-x-3 hover:bg-dark-300 rounded-lg px-3 py-2 transition-colors duration-200">
                        <div class="flex-shrink-0">
                            <div class="p-2 bg-indigo-500/10 rounded-full">
                                <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-left">
                            <div class="text-sm font-semibold text-white"><?php echo $_SESSION['nama_lengkap']; ?></div>
                            <div class="text-xs text-gray-400"><?php echo ucfirst($_SESSION['level']); ?></div>
                        </div>
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open"
                        @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-dark-200 rounded-lg shadow-lg py-1 border border-dark-300"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100">
                        <a href="<?= str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 1) ?>pages/auth/profile.php"
                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-dark-300">
                            Profil Saya
                        </a>
                        <div class="border-t border-dark-300"></div>
                        <a href="<?= str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 1) ?>pages/auth/logout.php"
                            class="block px-4 py-2 text-sm text-rose-400 hover:bg-dark-300">
                            Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>