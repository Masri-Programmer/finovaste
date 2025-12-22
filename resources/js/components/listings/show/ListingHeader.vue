<template>
    <header
        class="absolute top-0 right-0 z-10 mb-6 flex items-center justify-between"
    >
        <div class="flex items-center space-x-2">
            <ShareAction
                :share-url="shareUrl"
                :listing-title="`Check out this listing:`"
            />
            <LikeAction :listing="$page.props.listing" />
            <EditAction :listing="$page.props.listing" />
        </div>
    </header>
</template>

<script setup lang="ts">
import EditAction from '@/components/actions/EditAction.vue';
import LikeAction from '@/components/actions/LikeAction.vue';
import ShareAction from '@/components/actions/ShareAction.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
const page = usePage();
const shareUrl = computed(() => {
    return window.location.origin + page.url;
});
</script>
