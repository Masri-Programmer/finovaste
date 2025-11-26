<script setup lang="ts">
import Layout from '@/components/layout/Layout.vue';
import ListingAside from '@/components/listings/show/ListingAside.vue';
import ListingDetails from '@/components/listings/show/ListingDetails.vue';
import ListingHeader from '@/components/listings/show/ListingHeader.vue';
import ListingSlide from '@/components/listings/show/ListingSlide.vue';

import { show } from '@/routes/listings';
import { PageProps } from '@/types/listings';
const props = defineProps<PageProps>();
</script>

<template>
    <Layout :link="show.url(props.listing.id)">
        <div
            class="relative grid grid-cols-1 gap-8 lg:grid-cols-3 lg:items-start"
        >
            <div class="relative col-span-2">
                <ListingHeader :listing="props.listing" />
                <ListingSlide :listing="props.listing" />
            </div>
            <ListingAside :listing="props.listing" />
        </div>
        <ListingDetails :listing="props.listing" />
    </Layout>
</template>
