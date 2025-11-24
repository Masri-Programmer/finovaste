<template>
    <div v-if="$page.props.auth.user?.id === listing.user_id">
        <Link
            :href="edit(listing.id)"
            method="delete"
            as="button"
            preserve-scroll
            class="relative flex h-10 w-10 items-center justify-center rounded-full bg-background/70 text-gray-600 backdrop-blur-sm hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
        >
            <span class="sr-only">Edit</span>
            <Pen class="h-5 w-5 fill-current" />
        </Link>
    </div>
</template>
<script setup lang="ts">
import { edit } from '@/routes/listings';
import { Listing } from '@/types/listings';
import { Link } from '@inertiajs/vue3';
import { Pen } from 'lucide-vue-next';
import { PropType } from 'vue';

defineProps({
    listing: {
        type: Object as PropType<Listing>,
        required: true,
    },
});
</script>
