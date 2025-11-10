<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

import Layout from '@/components/layout/Layout.vue';
import Pagination from '@/components/layout/Pagination.vue';
import ListingCard from '@/components/listings/index/ListingCard.vue';
</script>

<template>
    <Head :title="trans('listings.liked.title')" />
    <Layout>
        <div class="min-h-screen bg-background text-foreground">
            <header class="mb-4 flex items-center justify-between">
                <h1 class="text-3xl font-bold text-foreground">
                    {{ trans('listings.liked.title') }}
                </h1>
            </header>

            <section class="mt-6">
                <div
                    v-if="$page.props.listings.data.length > 0"
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                >
                    <ListingCard
                        v-for="listing in $page.props.listings.data"
                        :key="listing.uuid"
                        :listing="listing"
                        :locale="$page.props.locale"
                    />
                </div>
                <div v-else>
                    <p class="text-center text-muted-foreground">
                        {{ trans('listings.liked.empty') }}
                    </p>
                </div>
            </section>

            <Pagination :paginator="$page.props.listings" class="mt-8" />
        </div>
    </Layout>
</template>
