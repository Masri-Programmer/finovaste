<template>
    <div v-if="listing.is_liked_by_current_user">
        <Link
            :href="unlike(listing.id)"
            method="delete"
            as="button"
            preserve-scroll
            class="relative flex h-10 w-10 items-center justify-center rounded-full bg-background/70 text-red-500 backdrop-blur-sm"
        >
            <span class="sr-only">Unlike</span>
            <Heart class="h-5 w-5 fill-current" />

            <span
                v-if="listing.likes_count > 0"
                class="absolute -top-1.5 -right-1.5 flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1 text-xs font-medium text-white"
            >
                {{ listing.likes_count }}
            </span>
        </Link>
    </div>
    <div v-else>
        <Link
            :href="like(listing.id)"
            method="post"
            as="button"
            preserve-scroll
            class="relative flex h-10 w-10 items-center justify-center rounded-full bg-background/70 text-gray-600 backdrop-blur-sm hover:text-red-500 dark:text-gray-400 dark:hover:text-red-500"
        >
            <span class="sr-only">Like</span>
            <Heart class="h-5 w-5" />

            <span
                v-if="listing.likes_count > 0"
                class="absolute -top-1.5 -right-1.5 flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1 text-xs font-medium text-white"
            >
                {{ listing.likes_count }}
            </span>
        </Link>
    </div>
</template>
<script setup lang="ts">
import { like, unlike } from '@/routes/listings';
import { Listing } from '@/types/listings';
import { Link } from '@inertiajs/vue3';
import { Heart } from 'lucide-vue-next';
import { PropType } from 'vue';

defineProps({
    listing: {
        type: Object as PropType<Listing>,
        required: true,
    },
});
</script>
