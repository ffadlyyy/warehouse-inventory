<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    transactions: {
        type: Array,
        default: () => [],
    },
    products: {
        type: Array,
        default: () => [],
    },
});

const isModalOpen = ref(false);

const form = useForm({
    product_id: '',
    type: 'in',
    quantity: 1,
    note: '',
});

const submitTransaction = () => {
    form.post('/transactions', {
        onSuccess: () => {
            form.reset();
            isModalOpen.value = false;
        },
    });
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <AppLayout title="Riwayat Transaksi">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Riwayat & Catat Transaksi
            </h2>
        </template>

        <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Header & Tombol Tambah Transaksi -->
            <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Log Mutasi Stok</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Catat semua arus barang masuk dan keluar gudang</p>
                </div>
                <button 
                    @click="isModalOpen = true"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition"
                >
                    + Catat Transaksi
                </button>
            </div>

            <!-- Tabel Riwayat Transaksi -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-300 text-xs font-semibold uppercase tracking-wider">
                                <th class="p-4">Waktu</th>
                                <th class="p-4">Produk</th>
                                <th class="p-4">Tipe Transaksi</th>
                                <th class="p-4">Jumlah</th>
                                <th class="p-4">Petugas</th>
                                <th class="p-4">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                            <tr v-for="trx in transactions" :key="trx.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                <td class="p-4 text-xs text-gray-500 dark:text-gray-400">
                                    {{ formatDate(trx.created_at) }}
                                </td>
                                <td class="p-4 font-medium text-gray-900 dark:text-white">
                                    {{ trx.product?.name ?? 'Produk Dihapus' }}
                                </td>
                                <td class="p-4">
                                    <span 
                                        :class="trx.type === 'in' 
                                            ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' 
                                            : 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300'"
                                        class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                                    >
                                        {{ trx.type === 'in' ? 'Barang Masuk' : 'Barang Keluar' }}
                                    </span>
                                </td>
                                <td class="p-4 font-semibold text-gray-800 dark:text-gray-200">
                                    {{ trx.type === 'in' ? '+' : '-' }}{{ trx.quantity }}
                                </td>
                                <td class="p-4 text-gray-600 dark:text-gray-300 text-xs">
                                    {{ trx.user?.name ?? 'Sistem' }}
                                </td>
                                <td class="p-4 text-gray-500 dark:text-gray-400 text-xs italic">
                                    {{ trx.note ?? '-' }}
                                </td>
                            </tr>
                            <tr v-if="!transactions || transactions.length === 0">
                                <td colspan="6" class="p-6 text-center text-gray-400">Belum ada riwayat transaksi. Klik "+ Catat Transaksi" untuk memulai.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Form Transaksi -->
            <div v-if="isModalOpen" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl max-w-md w-full p-6 shadow-xl space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Catat Transaksi Barang</h3>
                    
                    <form @submit.prevent="submitTransaction" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Pilih Produk</label>
                            <select v-model="form.product_id" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                                <option value="" disabled>-- Pilih Produk --</option>
                                <option v-for="p in products" :key="p.id" :value="p.id">
                                    {{ p.name }} (Stok Saat Ini: {{ p.stock }})
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Tipe Mutasi</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center justify-center p-2.5 border rounded-lg cursor-pointer text-xs font-bold transition"
                                    :class="form.type === 'in' ? 'bg-emerald-50 border-emerald-500 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'dark:border-gray-600 text-gray-600 dark:text-gray-300'">
                                    <input type="radio" v-model="form.type" value="in" class="sr-only" />
                                    + Barang Masuk
                                </label>
                                <label class="flex items-center justify-center p-2.5 border rounded-lg cursor-pointer text-xs font-bold transition"
                                    :class="form.type === 'out' ? 'bg-rose-50 border-rose-500 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300' : 'dark:border-gray-600 text-gray-600 dark:text-gray-300'">
                                    <input type="radio" v-model="form.type" value="out" class="sr-only" />
                                    - Barang Keluar
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Jumlah (Qty)</label>
                            <input v-model="form.quantity" type="number" min="1" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm" />
                            <p v-if="form.errors.quantity" class="text-xs text-rose-500 mt-1 font-semibold">{{ form.errors.quantity }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Catatan / Keterangan (Opsional)</label>
                            <input v-model="form.note" type="text" placeholder="Contoh: Supplier A / Restock Bulanan" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm" />
                        </div>

                        <div class="flex justify-end space-x-2 pt-2">
                            <button type="button" @click="isModalOpen = false" class="px-4 py-2 border rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700">Batal</button>
                            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium disabled:opacity-50">
                                Simpan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </AppLayout>
</template>