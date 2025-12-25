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
                {{ formatCurrency(data.target) }}</span
            >
        </div>

        <div class="mb-4">
            <Input
                v-model="amount"
                type="number"
                min="1"
                :placeholder="$t('listings.donation.amount_placeholder')"
            />
        </div>

        <Button
            class="w-full"
            variant="default"
            @click="donate"
            :disabled="!amount || processing"
        >
            <span v-if="processing">{{ $t('common.processing') }}</span>
            <span v-else>{{ $t('listings.donation.action_btn') }}</span>
        </Button>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { formatCurrency } from '@/composables/useCurrency';
import { checkout } from '@/routes/listings';
import { DonationListable } from '@/types/listings';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    data: DonationListable;
    listingId: number;
}>();

const amount = ref('');
const processing = ref(false);

const progressPercentage = computed(() => {
    const raised = parseFloat(props.data.amount_raised);
    const goal = parseFloat(props.data.target);
    if (goal === 0) return 0;
    return Math.min((raised / goal) * 100, 100);
});

const donate = () => {
    router.post(
        checkout.url(props.listingId),
        {
            amount: amount.value,
        },
        {
            onStart: () => (processing.value = true),
            onFinish: () => (processing.value = false),
        },
    );
};
</script>
