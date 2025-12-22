<script setup lang="ts">
import { ref } from 'vue';

import Pagination from '@/components/layout/Pagination.vue';
import ListingCard from '@/components/listings/index/ListingCard.vue';

import SearchFilter from '@/components/SearchFilter.vue';

const filters = ref({
    category: 'all',
    search: '',
});
</script>

<template>
    <div class="bg-background text-foreground">
        <header class="mb-4 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-foreground">
                {{ $t('listings.title') }}
            </h1>
        </header>

        <section>
            <SearchFilter v-if="!$page.props?.hide_filters" />
            <div
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-2 xl:grid-cols-3"
            >
                <ListingCard
                    v-for="listing in $page.props.listings.data"
                    :key="listing.uuid"
                    :listing="listing"
                    :locale="$page.props.locale"
                />
            </div>
        </section>

        <Pagination :paginator="$page.props.listings" class="mt-8" />
    </div>
</template>
