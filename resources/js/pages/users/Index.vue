<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },
});

const isModalOpen = ref(false);
const selectedUser = ref(null);

const form = useForm({
    role: 'staff',
});

const openRoleModal = (user) => {
    selectedUser.value = user;
    form.role = user.role || 'staff';
    form.clearErrors();
    isModalOpen.value = true;
};

const updateRole = () => {
    if (!selectedUser.value) return;

    form.put(`/users/${selectedUser.value.id}/role`, {
        onSuccess: () => {
            isModalOpen.value = false;
            selectedUser.value = null;
        },
    });
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};
</script>

<template>
    <AppLayout title="Manajemen Pengguna">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Manajemen Peran Pengguna
            </h2>
        </template>

        <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Header Informasi -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Daftar Pengguna Sistem</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola tingkat hak akses (Admin / Staff) untuk setiap akun pengguna terdaftar.</p>
            </div>

            <!-- Tabel Data Pengguna -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-300 text-xs font-semibold uppercase tracking-wider">
                                <th class="p-4">Nama</th>
                                <th class="p-4">Email</th>
                                <th class="p-4">Peran (Role)</th>
                                <th class="p-4">Tanggal Terdaftar</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                <td class="p-4 font-medium text-gray-900 dark:text-white flex items-center space-x-2">
                                    <span>{{ user.name }}</span>
                                    <span v-if="user.id === $page.props.auth.user?.id" class="text-[10px] bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 px-1.5 py-0.5 rounded font-bold">
                                        Anda
                                    </span>
                                </td>
                                <td class="p-4 text-gray-600 dark:text-gray-300 text-xs">
                                    {{ user.email }}
                                </td>
                                <td class="p-4">
                                    <span 
                                        :class="user.role === 'admin' 
                                            ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300' 
                                            : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'"
                                        class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                                    >
                                        {{ user.role === 'admin' ? 'Administrator' : 'Staff' }}
                                    </span>
                                </td>
                                <td class="p-4 text-xs text-gray-500 dark:text-gray-400">
                                    {{ formatDate(user.created_at) }}
                                </td>
                                <td class="p-4 text-right">
                                    <button 
                                        @click="openRoleModal(user)" 
                                        class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold px-2.5 py-1 rounded hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition"
                                    >
                                        Ubah Role
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!users || users.length === 0">
                                <td colspan="5" class="p-6 text-center text-gray-400">Belum ada data pengguna terdaftar.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Ubah Role -->
            <div v-if="isModalOpen" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl max-w-md w-full p-6 shadow-xl space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Ubah Role Pengguna</h3>
                    
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Pengguna: <strong class="text-gray-800 dark:text-gray-200">{{ selectedUser?.name }}</strong> ({{ selectedUser?.email }})
                    </p>

                    <form @submit.prevent="updateRole" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-2">Pilih Peran Baru</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label 
                                    class="flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition text-center"
                                    :class="form.role === 'staff' ? 'bg-blue-50 border-blue-500 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 font-bold' : 'dark:border-gray-600 text-gray-600 dark:text-gray-300'"
                                >
                                    <input type="radio" v-model="form.role" value="staff" class="sr-only" />
                                    <span class="text-sm">Staff</span>
                                    <span class="text-[10px] font-normal opacity-80 mt-1">Akses standar tanpa hak hapus</span>
                                </label>

                                <label 
                                    class="flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition text-center"
                                    :class="form.role === 'admin' ? 'bg-purple-50 border-purple-500 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 font-bold' : 'dark:border-gray-600 text-gray-600 dark:text-gray-300'"
                                >
                                    <input type="radio" v-model="form.role" value="admin" class="sr-only" />
                                    <span class="text-sm">Admin</span>
                                    <span class="text-[10px] font-normal opacity-80 mt-1">Akses penuh termasuk hapus & kelola role</span>
                                </label>
                            </div>
                            <p v-if="form.errors.role" class="text-xs text-rose-500 mt-2 font-semibold">{{ form.errors.role }}</p>
                        </div>

                        <div class="flex justify-end space-x-2 pt-2">
                            <button type="button" @click="isModalOpen = false" class="px-4 py-2 border rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700">Batal</button>
                            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium disabled:opacity-50">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </AppLayout>
</template>