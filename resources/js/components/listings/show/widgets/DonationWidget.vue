<template>
    <div class="rounded-lg border bg-card p-6 text-card-foreground shadow-sm">
        <h3 class="mb-2 leading-none font-semibold tracking-tight">
            {{ $t('listings.donation.support_cause') }}
        </h3>

        <div class="mb-1 h-2 w-full rounded-full bg-secondary">
            <div
                class="h-2 rounded-full bg-primary"
                :style="{ width: progressPercentage + '%' }"
            ></div>
        </div>
        <div class="mb-4 flex justify-between text-xs text-muted-foreground">
            <span
                >{{ $t('listings.donation.raised') }}:
                {{ formatCurrency(data.amount_raised) }}</span
            >
            <span
                >{{ $t('listings.donation.goal') }}:
                {{ formatCurrency(data.donation_goal) }}</span
            >
        </div>

        <Button class="w-full" variant="default">
            {{ $t('listings.donation.action_btn') }}
        </Button>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { formatCurrency } from '@/composables/useCurrency';
import { DonationListable } from '@/types/listings';
import { computed } from 'vue';

const props = defineProps<{
    data: DonationListable;
    listingId: number;
}>();

const progressPercentage = computed(() => {
    const raised = parseFloat(props.data.amount_raised);
    const goal = parseFloat(props.data.donation_goal);
    if (goal === 0) return 0;
    return Math.min((raised / goal) * 100, 100);
});
</script>
