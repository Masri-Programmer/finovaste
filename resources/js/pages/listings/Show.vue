<script setup lang="ts">
import Layout from '@/components/layout/Layout.vue';
import ListingAside from '@/components/listings/show/ListingAside.vue';
import ListingHeader from '@/components/listings/show/ListingHeader.vue';
import ListingSlide from '@/components/listings/show/ListingSlide.vue';
import { show } from '@/routes/listings';
import { LocaleString } from '@/types';
import { PageProps } from '@/types/listings';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<PageProps>();

const { t, locale } = useI18n();

const getTranslation = (field: LocaleString | string) => {
    if (typeof field === 'object' && field !== null) {
        return field[locale.value as 'en' | 'de'] || field['en'];
    }
    return String(field);
};

const listingTitle = computed(() => getTranslation(props.listing.title));
const listingDescription = computed(() =>
    getTranslation(props.listing.description),
);
const listingCategory = computed(() =>
    getTranslation(props.listing.category.name),
);
const listingLocation = computed(
    () => `${props.listing.address.city}, ${props.listing.address.state}`,
);

// Mock Data (from design, as it's missing in JSON)
const mockData = {
    rating: 4.8,
    reviews: 34,
    expectedRoi: '12-15%',
    duration: 24, // months
    investors: 52,
    capitalRaised: 450000, // EUR
    capitalGoal: 750000, // EUR
};

const capitalProgress = computed(
    () => (mockData.capitalRaised / mockData.capitalGoal) * 100,
);

// --- Right Column: Investment Card Logic ---
const minInvestmentAmount = 25000;
const totalCapitalGoal = mockData.capitalGoal;
const investmentAmount = ref(minInvestmentAmount);

const capitalShare = computed(() => {
    return ((investmentAmount.value / totalCapitalGoal) * 100).toFixed(2);
});
</script>

<template>
    <Layout
        :link="show.url(props.listing.id)"
        class="container mx-auto max-w-7xl p-4 py-8 md:p-8"
    >
        <ListingHeader :listing="props.listing" />
        <main class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <ListingSlide :listing="props.listing" />
            </div>

            <aside class="space-y-6 lg:col-span-1">
                <ListingAside :listing="props.listing" :user="props.user" />
            </aside>
        </main>
    </Layout>
</template>
