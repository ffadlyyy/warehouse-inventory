<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    user: Object,
});
</script>

<template>
    <div class="p-2 space-y-1">
        <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">
            Pengaturan Akun
        </div>
        
        <Link
            href="/settings/profile"
            class="block w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 rounded-md"
        >
            Profil
        </Link>

        <Link
            href="/logout"
            method="post"
            as="button"
            class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 rounded-md"
        >
            Log Out
        </Link>
    </div>
</template>