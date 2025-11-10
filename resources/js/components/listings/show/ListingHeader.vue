<template>
    <header class="mb-6 flex items-center justify-between">
        <Link
            :href="home.url()"
            class="flex items-center text-sm font-medium text-muted-foreground hover:text-primary"
        >
            <ArrowLeft class="mr-2 h-4 w-4" />
            {{ $t('listings.backToMarketplace') }}
        </Link>
        <div class="flex items-center space-x-2">
            <ShareAction
                :share-url="shareUrl"
                :listing-title="`Check out this listing:`"
            />
            <LikeAction :listing="$page.props.listing" />
        </div>
    </header>
</template>

<script setup lang="ts">
import LikeAction from '@/components/actions/LikeAction.vue';
import ShareAction from '@/components/actions/ShareAction.vue';
import { home } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { computed } from 'vue';
const page = usePage();
const shareUrl = computed(() => {
    return page.url;
});
</script>
