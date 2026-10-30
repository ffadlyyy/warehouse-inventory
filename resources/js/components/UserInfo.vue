<script setup lang="ts">
import { computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';

// Tambahkan opsi null/undefined pada User agar aman saat tidak ada sesi login
type Props = {
    user?: User | null;
    showEmail?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
});

const { getInitials } = useInitials();

// Gunakan optional chaining (?.) untuk memastikan user tidak null
const showAvatar = computed(
    () => props.user?.avatar && props.user.avatar !== '',
);
</script>

<template>
    <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
        <AvatarImage v-if="showAvatar" :src="user?.avatar || ''" :alt="user?.name ?? 'Guest'" />
        <AvatarFallback class="rounded-lg text-black dark:text-white">
            <!-- Fallback ke "Guest" jika user.name null -->
            {{ getInitials(user?.name ?? 'Guest') }}
        </AvatarFallback>
    </Avatar>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <!-- Tampilkan Guest jika tidak ada nama -->
        <span class="truncate font-medium">{{ user?.name ?? 'Guest' }}</span>
        
        <!-- Sembunyikan blok email sepenuhnya jika user atau email null -->
        <span v-if="showEmail && user?.email" class="truncate text-xs text-muted-foreground">
            {{ user.email }}
        </span>
    </div>
</template>