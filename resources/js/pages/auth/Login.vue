<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
        default: false,
    },
    status: {
        type: String,
        default: '',
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk ke Sistem" />

    <div class="min-h-screen bg-gray-950 flex flex-col justify-center items-center p-4 sm:p-6 font-sans antialiased text-gray-100">
        
        <!-- Header Branding -->
        <div class="w-full max-w-md text-center mb-6 space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 text-2xl mb-1 shadow-lg shadow-indigo-500/10">
                📦
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">WarehouseStuff</h1>
            <p class="text-xs text-gray-400">Warehouse Inventory Management System</p>
        </div>

        <!-- Kartu Form Login -->
        <div class="w-full max-w-md bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl p-6 sm:p-8 space-y-6">
            
            <div class="border-b border-gray-800 pb-4">
                <h2 class="text-lg font-semibold text-white">Selamat Datang Kembali</h2>
                <p class="text-xs text-gray-400 mt-1">Masukkan email dan kata sandi Anda untuk masuk ke sistem.</p>
            </div>

            <!-- Pesan Status Session -->
            <div v-if="status" class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs font-medium text-emerald-400">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <!-- Field Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">
                        Alamat Email
                    </label>
                    <input 
                        id="email"
                        v-model="form.email" 
                        type="email" 
                        required 
                        autofocus
                        autocomplete="username"
                        placeholder="admin@warehouse.com" 
                        class="w-full px-3.5 py-2.5 bg-gray-800/80 border border-gray-700 rounded-xl text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    />
                    <p v-if="form.errors.email" class="text-xs text-rose-400 mt-1.5 font-medium">
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Field Password -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider">
                            Kata Sandi
                        </label>
                        <Link 
                            v-if="canResetPassword" 
                            href="/forgot-password" 
                            class="text-xs text-indigo-400 hover:text-indigo-300 font-medium transition"
                        >
                            Lupa sandi?
                        </Link>
                    </div>
                    <input 
                        id="password"
                        v-model="form.password" 
                        type="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="••••••••" 
                        class="w-full px-3.5 py-2.5 bg-gray-800/80 border border-gray-700 rounded-xl text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    />
                    <p v-if="form.errors.password" class="text-xs text-rose-400 mt-1.5 font-medium">
                        {{ form.errors.password }}
                    </p>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center pt-1">
                    <label class="flex items-center cursor-pointer space-x-2">
                        <input 
                            type="checkbox" 
                            v-model="form.remember" 
                            class="w-4 h-4 rounded border-gray-700 bg-gray-800 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-gray-900"
                        />
                        <span class="text-xs text-gray-400 select-none">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Tombol Submit -->
                <button 
                    type="submit" 
                    :disabled="form.processing" 
                    class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-indigo-600/20 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900 transition disabled:opacity-50 disabled:cursor-not-allowed mt-2"
                >
                    <span v-if="form.processing">Memproses...</span>
                    <span v-else>Masuk ke Akun</span>
                </button>
            </form>

            <div class="pt-4 border-t border-gray-800 text-center text-xs text-gray-500">
                Belum memiliki akun? Contact administrator gudang Anda.
            </div>

        </div>
    </div>
</template>