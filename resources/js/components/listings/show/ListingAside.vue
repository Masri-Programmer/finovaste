<template>
    <aside class="sticky top-4 space-y-4 overflow-y-auto">
        <AuctionWidget
            v-if="isAuction && auctionData"
            :data="auctionData"
            :listing-id="listing.id"
        />

        <DonationWidget
            v-else-if="isDonation && donationData"
            :data="donationData"
            :listing-id="listing.id"
        />

        <PrivateLinkWidget :listing="listing" />

        <UserProfileCard v-if="listing.user" :user="listing.user" />
    </aside>
</template>

<script setup lang="ts">
import { AuctionListable, DonationListable, Listing } from '@/types/listings';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import UserProfileCard from './UserProfileCard.vue';
import AuctionWidget from './widgets/AuctionWidget.vue';
import DonationWidget from './widgets/DonationWidget.vue';
import PrivateLinkWidget from './widgets/PrivateLinkWidget.vue';

const page = usePage();
const listing = computed<Listing>(() => page.props.listing as Listing);

// Type Guards / Checkers
const isAuction = computed(
    () => listing.value.listable_type === 'App\\Models\\AuctionListing',
);
const isDonation = computed(
    () => listing.value.listable_type === 'App\\Models\\DonationListing',
);

// Data Extraction (Type Casting)
const auctionData = computed(() =>
    isAuction.value ? (listing.value.listable as AuctionListable) : null,
);
const donationData = computed(() =>
    isDonation.value ? (listing.value.listable as DonationListable) : null,
);
</script>
