<script setup>
import { ref } from 'vue';
import { Link, router, Head } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: 'WarehouseStuff',
    },
});

const isUserMenuOpen = ref(false);

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <div>
        <Head :title="title" />

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 font-sans text-gray-900 dark:text-gray-100">
            <!-- Navigation Header Bar -->
            <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        
                        <!-- Logo & Menu Navigasi Utama -->
                        <div class="flex items-center space-x-8">
                            <!-- Logo / Judul Sistem -->
                            <Link href="/dashboard" class="flex items-center space-x-2 font-bold text-lg text-indigo-600 dark:text-indigo-400">
                                <span>📦 WarehouseStuff</span>
                            </Link>

                            <!-- Link Navigasi -->
                            <div class="hidden sm:flex sm:space-x-4 text-sm font-medium">
                                <Link 
                                    href="/dashboard" 
                                    :class="$page.url.startsWith('/dashboard') || $page.url.startsWith('/products') 
                                        ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 font-bold' 
                                        : 'text-gray-600 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white'"
                                    class="px-3 py-2 rounded-lg transition"
                                >
                                    Dashboard & Produk
                                </Link>

                                <Link 
                                    href="/transactions" 
                                    :class="$page.url.startsWith('/transactions') 
                                        ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 font-bold' 
                                        : 'text-gray-600 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white'"
                                    class="px-3 py-2 rounded-lg transition"
                                >
                                    Transaksi
                                </Link>

                                <Link 
                                    href="/categories" 
                                    :class="$page.url.startsWith('/categories') 
                                        ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 font-bold' 
                                        : 'text-gray-600 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white'"
                                    class="px-3 py-2 rounded-lg transition"
                                >
                                    Kategori
                                </Link>

                                <!-- Menu Manajemen User (Hanya Tampil untuk Admin) -->
                                <Link 
                                    v-if="$page.props.auth.isAdmin"
                                    href="/users" 
                                    :class="$page.url.startsWith('/users') 
                                        ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 font-bold' 
                                        : 'text-gray-600 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white'"
                                    class="px-3 py-2 rounded-lg transition"
                                >
                                    Manajemen User
                                </Link>
                            </div>
                        </div>

                        <!-- Menu User / Profil & Logout (Kanan) -->
                        <div class="hidden sm:flex sm:items-center sm:space-x-3">
                            <div class="relative">
                                <button 
                                    @click="isUserMenuOpen = !isUserMenuOpen"
                                    class="flex items-center space-x-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-white px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                >
                                    <span>👤 {{ $page.props.auth.user?.name ?? 'Pengguna' }}</span>
                                    <span class="text-xs bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 px-2 py-0.5 rounded font-bold uppercase">
                                        {{ $page.props.auth.user?.role ?? 'Staff' }}
                                    </span>
                                </button>

                                <!-- Dropdown Profil & Logout -->
                                <div 
                                    v-if="isUserMenuOpen" 
                                    @click="isUserMenuOpen = false" 
                                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-50 space-y-1"
                                >
                                    <Link 
                                        href="/profile" 
                                        class="block px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 hover:text-indigo-600 transition"
                                    >
                                        ⚙️ Pengaturan Profil
                                    </Link>
                                    
                                    <button 
                                        @click="logout" 
                                        class="w-full text-left px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition"
                                    >
                                        🚪 Log Out
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Responsive Nav Mobile -->
                <div class="sm:hidden border-t border-gray-200 dark:border-gray-700 px-4 py-3 space-y-2 text-xs font-medium">
                    <Link href="/dashboard" class="block py-1">Dashboard & Produk</Link>
                    <Link href="/transactions" class="block py-1">Transaksi</Link>
                    <Link href="/categories" class="block py-1">Kategori</Link>
                    <Link v-if="$page.props.auth.isAdmin" href="/users" class="block py-1">Manajemen User</Link>
                    <Link href="/profile" class="block py-1">Pengaturan Profil</Link>
                    <button @click="logout" class="block py-1 text-rose-500 font-bold">Log Out</button>
                </div>
            </nav>

            <!-- Page Header -->
            <header v-if="$slots.header" class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-100 dark:border-gray-700">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>