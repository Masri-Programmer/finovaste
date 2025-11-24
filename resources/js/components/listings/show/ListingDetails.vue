<template>
    <AuctionStatsGrid :listing="listing" />
    <ListingFaqSection
        :listing-id="listing.id"
        :faqs="listing.faqs || []"
        :is-owner="$page.props.auth.user?.id === listing.user_id"
    />
    <!-- 
    <section>
        <h2 class="mb-4 text-lg font-semibold text-foreground">
            {{ $t('listingdetails.key_features') }}
        </h2>
        <div class="grid grid-cols-2 gap-x-8 gap-y-3">
            <div
                v-for="feature in keyFeatures"
                :key="feature.text"
                class="flex items-center space-x-3"
            >
                <component
                    :is="feature.icon"
                    class="h-5 w-5 flex-shrink-0 text-green-600"
                />
                <span class="text-sm text-muted-foreground">{{
                    $t(feature.text)
                }}</span>
            </div>
        </div>
    </section> -->
</template>

<script setup lang="ts">
import { formatCurrency } from '@/composables/useCurrency';
import { Listing } from '@/types/listings';
import { BarChart, CheckCircle, ShieldCheck, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import AuctionStatsGrid from './details/AuctionStatsGrid.vue';
import ListingFaqSection from './details/ListingFaqSection.vue';
defineProps<{
    listing: Listing;
}>();

const totalCapitalGoal = 750000;
const currentCapital = 450000;
const investorCount = 52;

/**
 * Calculates the progress bar percentage.
 */
const progressPercentage = computed(() => {
    return (currentCapital / totalCapitalGoal) * 100;
});

const formattedTotalCapital = computed(() => formatCurrency(totalCapitalGoal));
const formattedCurrentCapital = computed(() => formatCurrency(currentCapital));

// --- Static Content ---

const keyFeatures = [
    { text: 'listingdetails.features.verified', icon: CheckCircle },
    { text: 'listingdetails.features.secure', icon: ShieldCheck },
    { text: 'listingdetails.features.support', icon: Users },
    { text: 'listingdetails.features.growth', icon: BarChart },
];

const faqItems = [
    {
        value: 'item-1',
        question: 'listingdetails.faq.q1',
        answer: 'listingdetails.faq.a1',
    },
    {
        value: 'item-2',
        question: 'listingdetails.faq.q2',
        answer: 'listingdetails.faq.a2',
    },
    {
        value: 'item-3',
        question: 'listingdetails.faq.q3',
        answer: 'listingdetails.faq.a3',
    },
    {
        value: 'item-4',
        question: 'listingdetails.faq.q4',
        answer: 'listingdetails.faq.a4',
    },
];
</script>
