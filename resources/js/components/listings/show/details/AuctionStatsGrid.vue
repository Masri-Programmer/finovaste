<template>
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <!-- <InfoBox
            :icon="TrendingUp"
            :title="$t('listingdetails.boxes.current_bid')"
            :value="formattedCurrentBid"
            icon-bg-class="bg-green-100 text-green-700"
        /> -->
        <InfoBox
            :icon="FolderOpen"
            :title="$t('listingdetails.boxes.category')"
            :value="listing.category.name.en"
            icon-bg-class="bg-purple-100 text-purple-700"
        />
        <InfoBox
            :icon="Clock"
            :title="$t('listingdetails.boxes.time_left')"
            :value="timeRemaining"
            :icon-bg-class="timeCriticalClass"
        />

        <InfoBox
            :icon="Gavel"
            :title="$t('listingdetails.boxes.total_bids')"
            :value="bidCount"
            icon-bg-class="bg-blue-100 text-blue-700"
        />
    </section>
    <div class="space-y-1.5">
        <div class="flex justify-between text-xs text-muted-foreground">
            <span>{{ $t('listingdetails.started') }}</span>
            <span>{{ $t('listingdetails.ending') }}</span>
        </div>

        <Progress v-model="progressPercentage" class="h-2 w-full" />
    </div>
</template>

<script setup lang="ts">
import { Progress } from '@/components/ui/progress';
import type { AuctionListable, Listing } from '@/types/listings';
import { Clock, FolderOpen, Gavel } from 'lucide-vue-next';
import { computed } from 'vue';
import InfoBox from '../InfoBox.vue';

// Define the shape of the listing prop based on your JSON
interface Props {
    listing: Listing;
}

const props = defineProps<Props>();
const auctionData = computed(() => {
    return props.listing.listable as AuctionListable;
});

const bidCount = computed(() => {
    return (props.listing.bids_count || 0).toString();
});

// 3. Calculate Time Remaining
const timeRemaining = computed(() => {
    if (!auctionData.value.ends_at) return 'N/A';

    const end = new Date(auctionData.value.ends_at);
    const now = new Date();
    const diffMs = end.getTime() - now.getTime();

    if (diffMs <= 0) return 'Ended';

    const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    const hours = Math.floor(
        (diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60),
    );

    // If more than 1 day left, show Days + Hours
    if (days > 0) {
        return `${days}d ${hours}h`;
    }

    // If less than 24h, show Hours + Minutes for urgency
    const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
    return `${hours}h ${minutes}m`;
});

// 4. Dynamic Color for Urgency
const timeCriticalClass = computed(() => {
    if (!auctionData.value.ends_at) return 'bg-gray-100 text-gray-700';

    const end = new Date(auctionData.value.ends_at);
    const now = new Date();
    // Calculate difference in hours
    const diffHours = (end.getTime() - now.getTime()) / (1000 * 60 * 60);

    if (diffHours <= 0) return 'bg-gray-100 text-gray-500'; // Ended
    if (diffHours < 24) return 'bg-red-100 text-red-700'; // Critical (< 24h)
    if (diffHours < 72) return 'bg-orange-100 text-orange-700'; // Warning (< 3 days)

    return 'bg-purple-100 text-purple-700'; // Standard
});

const progressPercentage = computed(() => {
    const end = auctionData.value.ends_at
        ? new Date(auctionData.value.ends_at).getTime()
        : 0;

    // Fallback to 'created_at' if 'starts_at' is null (auction started immediately)
    const start = auctionData.value.starts_at
        ? new Date(auctionData.value.starts_at).getTime()
        : new Date(props.listing.created_at).getTime();

    const now = new Date().getTime();

    if (end === 0 || start >= end) return 100; // Edge case safety
    if (now >= end) return 100; // Auction ended
    if (now <= start) return 0; // Auction hasn't started

    const totalDuration = end - start;
    const elapsed = now - start;

    // Calculate percentage (0 to 100)
    return Math.min(100, Math.max(0, (elapsed / totalDuration) * 100));
});
</script>
