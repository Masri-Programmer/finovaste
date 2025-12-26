<template>
    <header class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">
            {{ $t(`createListing.types.${listing.type}.title`) }}
        </h1>
        <div class="flex items-center space-x-2">
            <ShareAction
                :share-url="shareUrl"
                :listing-title="`Check out this listing:`"
            />
            <LikeAction :listing="listing" />
            <EditAction :listing="listing" />
        </div>
    </header>
</template>

<script setup lang="ts">
import EditAction from '@/components/actions/EditAction.vue';
import LikeAction from '@/components/actions/LikeAction.vue';
import ShareAction from '@/components/actions/ShareAction.vue';
import { Listing } from '@/types/listings';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    listing?: Listing;
}>();

const page = usePage();
const listing = computed<Listing>(
    () => (props.listing || page.props.listing) as Listing,
);

const shareUrl = computed(() => {
    return window.location.origin + page.url;
});
</script>
