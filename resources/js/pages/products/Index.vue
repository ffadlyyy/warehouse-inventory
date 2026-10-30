<script setup>
import { ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    stats: {
        type: Object,
        default: () => ({
            total_products: 0,
            total_stock: 0,
            total_in: 0,
            low_stock_count: 0,
        }),
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
        }),
    },
    lowStockProducts: {
        type: Array,
        default: () => [],
    },
});

// Search State dengan Debouncing
const search = ref(props.filters?.search || '');
let searchTimeout = null;

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/products', { search: value }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

// Modal & Form State
const isModalOpen = ref(false);
const isEditMode = ref(false);
const editingProductId = ref(null);

const form = useForm({
    name: '',
    category_id: '',
    stock: 0,
    price: 0,
});

const openAddModal = () => {
    isEditMode.value = false;
    editingProductId.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (product) => {
    isEditMode.value = true;
    editingProductId.value = product.id;
    form.name = product.name;
    form.category_id = product.category_id;
    form.stock = product.stock;
    form.price = product.price;
    isModalOpen.value = true;
};

const submitForm = () => {
    if (isEditMode.value) {
        form.put(`/products/${editingProductId.value}`, {
            onSuccess: () => {
                form.reset();
                isModalOpen.value = false;
            },
        });
    } else {
        form.post('/products', {
            onSuccess: () => {
                form.reset();
                isModalOpen.value = false;
            },
        });
    }
};

const deleteProduct = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
        router.delete(`/products/${id}`);
    }
};

const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(number || 0);
};
</script>

<template>
    <AppLayout title="Dashboard Inventaris">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard & Manajemen Produk
            </h2>
        </template>

        <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Banner Notifikasi Stok Menipis -->
            <div v-if="lowStockProducts && lowStockProducts.length > 0" 
                class="p-4 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-amber-500 text-white rounded-lg font-bold text-xs uppercase">
                        Peringatan
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-amber-900 dark:text-amber-200">
                            Terdapat {{ lowStockProducts.length }} Produk dengan Stok Menipis (≤ 5)
                        </h4>
                        <p class="text-xs text-amber-700 dark:text-amber-300 mt-0.5">
                            Segera lakukan transaksi masuk untuk memperbarui persediaan gudang.
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-1.5 max-w-md">
                    <span v-for="item in lowStockProducts" :key="item.id" 
                        class="px-2 py-0.5 bg-amber-100 dark:bg-amber-800/60 text-amber-900 dark:text-amber-100 text-[11px] font-semibold rounded">
                        {{ item.name }} ({{ item.stock }})
                    </span>
                </div>
            </div>

            <!-- 1. Dashboard Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Jenis Produk</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ stats?.total_products ?? 0 }}</p>
                </div>
                
                <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Stok Gudang</p>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ stats?.total_stock ?? 0 }}</p>
                </div>
                
                <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Barang Masuk</p>
                    <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">+{{ stats?.total_in ?? 0 }}</p>
                </div>
                
                <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stok Menipis (≤ 5)</p>
                    <p class="text-3xl font-bold text-amber-500 mt-1">{{ stats?.low_stock_count ?? 0 }}</p>
                </div>
            </div>

            <!-- 2. Header, Search Input & Tombol Tambah -->
            <div class="flex flex-col sm:flex-row gap-3 justify-between items-stretch sm:items-center bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Daftar Produk</h3>
                
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <!-- Input Search -->
                    <div class="relative w-full sm:w-64">
                        <input 
                            v-model="search" 
                            type="text" 
                            placeholder="Cari produk / kategori..." 
                            class="w-full pl-9 pr-3 py-1.5 border rounded-lg text-xs dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none"
                        />
                        <span class="absolute left-3 top-2 text-gray-400 text-xs">🔍</span>
                    </div>

                    <!-- Tombol Tambah -->
                    <button 
                        @click="openAddModal"
                        class="w-full sm:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition whitespace-nowrap"
                    >
                        + Tambah Produk
                    </button>
                </div>
            </div>

            <!-- 3. Tabel Data Produk -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-300 text-xs font-semibold uppercase tracking-wider">
                                <th class="p-4">Nama Produk</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Stok</th>
                                <th class="p-4">Harga</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                            <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                <td class="p-4 font-medium text-gray-900 dark:text-white">{{ product.name }}</td>
                                <td class="p-4 text-gray-600 dark:text-gray-300">
                                    <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-700 rounded-md text-xs">
                                        {{ product.category?.name ?? 'Tanpa Kategori' }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span 
                                        :class="product.stock <= 5 ? 'text-amber-600 font-bold bg-amber-50 dark:bg-amber-900/30 px-2 py-0.5 rounded' : 'text-gray-700 dark:text-gray-200'"
                                    >
                                        {{ product.stock }}
                                    </span>
                                </td>
                                <td class="p-4 text-gray-700 dark:text-gray-200">{{ formatRupiah(product.price) }}</td>
                                <td class="p-4 text-right space-x-2">
                                    <button 
                                        @click="openEditModal(product)" 
                                        class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold px-2 py-1 rounded hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition"
                                    >
                                        Edit
                                    </button>
                                    
                                    <button 
                                        v-if="$page.props.auth.isAdmin"
                                        @click="deleteProduct(product.id)" 
                                        class="text-red-600 hover:text-red-800 text-xs font-semibold px-2 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!products || products.length === 0">
                                <td colspan="5" class="p-6 text-center text-gray-400">
                                    {{ search ? 'Tidak ada produk yang cocok dengan pencarian.' : 'Belum ada data produk.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. Modal Form (Tambah / Edit Produk) -->
            <div v-if="isModalOpen" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl max-w-md w-full p-6 shadow-xl space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ isEditMode ? 'Edit Produk' : 'Tambah Produk Baru' }}
                    </h3>
                    
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Nama Produk</label>
                            <input v-model="form.name" type="text" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Kategori</label>
                            <select v-model="form.category_id" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                                <option value="" disabled>Pilih Kategori</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Stok</label>
                                <input v-model="form.stock" type="number" min="0" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Harga (Rp)</label>
                                <input v-model="form.price" type="number" min="0" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm" />
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2 pt-2">
                            <button type="button" @click="isModalOpen = false" class="px-4 py-2 border rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700">Batal</button>
                            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium disabled:opacity-50">
                                {{ isEditMode ? 'Perbarui' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </AppLayout>
</template>