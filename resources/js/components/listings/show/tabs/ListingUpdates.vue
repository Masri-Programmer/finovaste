<template>
    <div>
        <div v-if="updates.length > 0" class="space-y-5">
            <div v-for="update in updates" :key="update.id" class="flex gap-3">
                <div
                    class="mt-2 h-2 w-2 flex-shrink-0 rounded-full bg-primary"
                ></div>
                <div class="flex-1">
                    <div class="mb-1 text-sm text-muted-foreground">
                        {{ calculateTimeAgo(update.createdAt) }}
                    </div>
                    <p class="text-sm text-foreground/90">
                        {{ update.text }}
                    </p>
                </div>
            </div>
            <div class="pt-4 text-center text-xs text-muted-foreground">
                {{ $t('reviews.updates.last_checked') }}:
                {{ lastChecked.toLocaleString() }}
            </div>
        </div>
        <p v-else class="text-center text-muted-foreground">
            {{ $t('reviews.tabs.updates_empty') }}
        </p>
    </div>
    <ListingTimeline
        :listing-id="listingId"
        :updates="updates"
        :is-owner="$page.props.listing.user_id === $page.props.auth.user?.id"
    />
</template>

<script setup lang="ts">
import { Update } from '@/types/listings';
import { useNow, useTimeAgo } from '@vueuse/core';
import ListingTimeline from './ListingTimeline.vue';

defineProps<{
    listingId: number;
    updates: Update[];
}>();

const lastChecked = useNow();

const calculateTimeAgo = (date: string | Date) =>
    useTimeAgo(new Date(date)).value;
</script>
