<script setup lang="ts">
import { useFormatting } from '@/composables/useFormatting';
import { CalendarClock } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

defineProps<{
    listable: any; // Or a more specific 'AuctionListable' type
}>();

const { t } = useI18n();
const { formatCurrency } = useFormatting();
</script>

<template>
    <div class="mt-4 space-y-3">
        <div
            class="flex items-center justify-between rounded-lg bg-secondary p-3 text-sm"
        >
            <span class="flex items-center gap-1 text-secondary-foreground">
                <CalendarClock class="h-4 w-4" />
                {{ t('homepage.listings.auctionEnds') }}
            </span>
            <span class="font-bold text-secondary-foreground">
                {{ new Date(listable.ends_at).toLocaleString('de-DE') }}
            </span>
        </div>
        <div class="flex items-baseline justify-between text-sm">
            <span class="text-muted-foreground">{{
                t('homepage.listings.startingBid')
            }}</span>
            <span class="font-medium text-foreground">
                {{ formatCurrency(listable.start_price) }}
            </span>
        </div>
        <div class="flex items-baseline justify-between text-sm">
            <span class="text-muted-foreground">{{
                t('homepage.listings.currentBid')
            }}</span>
            <span class="text-lg font-bold text-primary">
                {{
                    formatCurrency(listable.current_bid || listable.start_price)
                }}
            </span>
        </div>
    </div>
</template>
