<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const page = usePage();
const user = page.props.auth.user;

// Form Update Informasi Profil
const profileForm = useForm({
    name: user?.name || '',
    email: user?.email || '',
});

const profileSuccessMessage = ref('');

const updateProfile = () => {
    profileSuccessMessage.value = '';
    profileForm.put('/profile', {
        onSuccess: () => {
            profileSuccessMessage.value = 'Informasi profil berhasil diperbarui!';
            setTimeout(() => {
                profileSuccessMessage.value = '';
            }, 3000);
        },
    });
};

// Form Update Kata Sandi
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const passwordSuccessMessage = ref('');

const updatePassword = () => {
    passwordSuccessMessage.value = '';
    passwordForm.put('/profile/password', {
        onSuccess: () => {
            passwordForm.reset();
            passwordSuccessMessage.value = 'Kata sandi berhasil diperbarui!';
            setTimeout(() => {
                passwordSuccessMessage.value = '';
            }, 3000);
        },
    });
};
</script>

<template>
    <AppLayout title="Pengaturan Profil">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Pengaturan Profil & Keamanan Akun
            </h2>
        </template>

        <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- 1. Form Update Informasi Profil -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Profil</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbarui nama pengguna dan alamat email akun Anda.</p>
                </div>

                <div v-if="profileSuccessMessage" class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-lg text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                    {{ profileSuccessMessage }}
                </div>

                <form @submit.prevent="updateProfile" class="space-y-4 max-w-xl">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Nama Lengkap</label>
                        <input v-model="profileForm.name" type="text" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm" />
                        <p v-if="profileForm.errors.name" class="text-xs text-rose-500 mt-1 font-semibold">{{ profileForm.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Alamat Email</label>
                        <input v-model="profileForm.email" type="email" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm" />
                        <p v-if="profileForm.errors.email" class="text-xs text-rose-500 mt-1 font-semibold">{{ profileForm.errors.email }}</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" :disabled="profileForm.processing" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium disabled:opacity-50">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- 2. Form Update Kata Sandi -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Ubah Kata Sandi</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pastikan akun Anda menggunakan kata sandi yang kuat demi keamanan sistem.</p>
                </div>

                <div v-if="passwordSuccessMessage" class="p-3 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-lg text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                    {{ passwordSuccessMessage }}
                </div>

                <form @submit.prevent="updatePassword" class="space-y-4 max-w-xl">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Kata Sandi Saat Ini</label>
                        <input v-model="passwordForm.current_password" type="password" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm" />
                        <p v-if="passwordForm.errors.current_password" class="text-xs text-rose-500 mt-1 font-semibold">{{ passwordForm.errors.current_password }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Kata Sandi Baru</label>
                        <input v-model="passwordForm.password" type="password" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm" />
                        <p v-if="passwordForm.errors.password" class="text-xs text-rose-500 mt-1 font-semibold">{{ passwordForm.errors.password }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input v-model="passwordForm.password_confirmation" type="password" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm" />
                    </div>

                    <div class="pt-2">
                        <button type="submit" :disabled="passwordForm.processing" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium disabled:opacity-50">
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </AppLayout>
</template>