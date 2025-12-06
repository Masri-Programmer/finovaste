<template>
    <aside class="sticky top-4 space-y-4 overflow-y-auto">
        <InvestmentWidget
            v-if="isInvestment && investmentData"
            :data="investmentData"
            :listing-id="listing.id"
        />

        <BuyNowWidget
            v-else-if="isBuyNow && buyNowData"
            :data="buyNowData"
            :listing-id="listing.id"
            :is-owner="isOwner"
        />

        <AuctionWidget
            v-else-if="isAuction && auctionData"
            :data="auctionData"
            :listing-id="listing.id"
        />

        <DonationWidget
            v-else-if="isDonation && donationData"
            :data="donationData"
            :listing-id="listing.id"
        />

        <UserProfileCard v-if="listing.user" :user="listing.user" />
    </aside>
</template>

<script setup lang="ts">
import {
    AuctionListable,
    BuyNowListable,
    DonationListable,
    InvestmentListable,
    Listing,
} from '@/types/listings';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import UserProfileCard from './UserProfileCard.vue';
import AuctionWidget from './widgets/AuctionWidget.vue';
import BuyNowWidget from './widgets/BuyWidget.vue';
import DonationWidget from './widgets/DonationWidget.vue';
import InvestmentWidget from './widgets/InvestmentWidget.vue';

const page = usePage();
const listing = computed<Listing>(() => page.props.listing as Listing);

// Type Guards / Checkers
const isInvestment = computed(
    () => listing.value.listable_type === 'App\\Models\\InvestmentListing',
);
const isBuyNow = computed(
    () => listing.value.listable_type === 'App\\Models\\BuyNowListing',
);
const isAuction = computed(
    () => listing.value.listable_type === 'App\\Models\\AuctionListing',
);
const isDonation = computed(
    () => listing.value.listable_type === 'App\\Models\\DonationListing',
);

const isOwner = computed(() => {
    const user = page.props.auth.user;
    return user && user.id === listing.value.user_id;
});

// Data Extraction (Type Casting)
const investmentData = computed(() =>
    isInvestment.value ? (listing.value.listable as InvestmentListable) : null,
);
const buyNowData = computed(() =>
    isBuyNow.value ? (listing.value.listable as BuyNowListable) : null,
);
const auctionData = computed(() =>
    isAuction.value ? (listing.value.listable as AuctionListable) : null,
);
const donationData = computed(() =>
    isDonation.value ? (listing.value.listable as DonationListable) : null,
);
</script>
