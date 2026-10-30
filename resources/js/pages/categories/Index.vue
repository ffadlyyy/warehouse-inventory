<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

// Modal & Form State
const isModalOpen = ref(false);
const isEditMode = ref(false);
const editingCategoryId = ref(null);

const form = useForm({
    name: '',
});

const openAddModal = () => {
    isEditMode.value = false;
    editingCategoryId.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (category) => {
    isEditMode.value = true;
    editingCategoryId.value = category.id;
    form.name = category.name;
    isModalOpen.value = true;
};

const submitForm = () => {
    if (isEditMode.value) {
        form.put(`/categories/${editingCategoryId.value}`, {
            onSuccess: () => {
                form.reset();
                isModalOpen.value = false;
            },
        });
    } else {
        form.post('/categories', {
            onSuccess: () => {
                form.reset();
                isModalOpen.value = false;
            },
        });
    }
};

const deleteCategory = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
        router.delete(`/categories/${id}`);
    }
};
</script>

<template>
    <AppLayout title="Manajemen Kategori">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Manajemen Kategori Barang
            </h2>
        </template>

        <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Header & Tombol Tambah Kategori -->
            <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Daftar Kategori</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kelola kelompok jenis barang di inventaris</p>
                </div>
                <button 
                    @click="openAddModal"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition"
                >
                    + Tambah Kategori
                </button>
            </div>

            <!-- Tabel Data Kategori -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-300 text-xs font-semibold uppercase tracking-wider">
                                <th class="p-4">Nama Kategori</th>
                                <th class="p-4">Jumlah Produk Terikat</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                            <tr v-for="category in categories" :key="category.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                <td class="p-4 font-medium text-gray-900 dark:text-white">
                                    {{ category.name }}
                                </td>
                                <td class="p-4 text-gray-600 dark:text-gray-300">
                                    <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-700 rounded-md text-xs font-semibold">
                                        {{ category.products_count ?? 0 }} Produk
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-2">
                                    <button 
                                        @click="openEditModal(category)" 
                                        class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold px-2 py-1 rounded hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition"
                                    >
                                        Edit
                                    </button>
                                    
                                    <button 
                                        v-if="$page.props.auth.isAdmin"
                                        @click="deleteCategory(category.id)" 
                                        class="text-red-600 hover:text-red-800 text-xs font-semibold px-2 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!categories || categories.length === 0">
                                <td colspan="3" class="p-6 text-center text-gray-400">Belum ada kategori. Klik "+ Tambah Kategori" untuk membuat.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Form (Tambah / Edit Kategori) -->
            <div v-if="isModalOpen" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl max-w-md w-full p-6 shadow-xl space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ isEditMode ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                    </h3>
                    
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Nama Kategori</label>
                            <input v-model="form.name" type="text" required placeholder="Contoh: Elektronik, Pakaian" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm" />
                            <p v-if="form.errors.name" class="text-xs text-rose-500 mt-1 font-semibold">{{ form.errors.name }}</p>
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