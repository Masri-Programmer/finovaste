<script setup lang="ts">
import { computed, ref } from 'vue';

import Pagination from '@/components/layout/Pagination.vue';
import ListingCard from '@/components/listings/index/ListingCard.vue';
import { AppPageProps } from '@/types';
import MarketplaceCategoryFilters from './Filters.vue';

import { usePage } from '@inertiajs/vue3';

const page = usePage<AppPageProps>();

const locale = computed(() => page.props.locale);
const listingsResponse = computed(() => page.props.listings);
const listings = computed(() => listingsResponse.value.data);
const categories = computed(() => page.props.categories);

const filters = ref({
    category: 'all',
    search: '',
});
</script>

<template>
    <div class="min-h-screen bg-background text-foreground">
        <header class="mb-4 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-foreground">
                {{ $t('homepage.marketplace.title') }}
            </h1>
        </header>

        <MarketplaceCategoryFilters
            :categories="categories"
            :current-filters="filters"
        />

        <section class="mt-6">
            <h2 class="mb-4 text-2xl font-semibold">
                {{ $t('homepage.marketplace.featuredListings') }}
            </h2>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-2">
                <ListingCard
                    v-for="listing in listings"
                    :key="listing.uuid"
                    :listing="listing"
                    :locale="locale"
                />
            </div>
        </section>

        <Pagination :paginator="listingsResponse" class="mt-8" />
    </div>
</template>
