<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useMoney } from '@/composables/useMoney';
import { checkout } from '@/routes/listings';
import { DonationListable, Listing } from '@/types/listings';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    data: DonationListable;
    listing: Listing;
}>();

const { formatMoney } = useMoney();

const amount = ref('');
const processing = ref(false);

const progressPercentage = computed(() => {
    const raisedValue =
        typeof props.data.raised === 'string'
            ? parseFloat(props.data.raised)
            : props.data.raised;
    const goal =
        typeof props.data.target === 'string'
            ? parseFloat(props.data.target)
            : props.data.target;
    if (goal === 0) return 0;
    return Math.min((raisedValue / goal) * 100, 100);
});

const donate = () => {
    router.post(
        checkout.url(props.listing.id),
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
                {{ formatMoney(data.raised) }}</span
            >
            <span
                >{{ $t('listings.donation.goal') }}:
                {{ formatMoney(data.target) }}</span
            >
        </div>

        <div class="mb-4 flex flex-col gap-1 text-center">
            <div class="text-xs text-muted-foreground">
                {{ $t('listings.auction.ends_at') }}
                {{
                    listing.expires_at
                        ? new Date(listing.expires_at).toLocaleDateString(
                              'de-DE',
                          )
                        : '-'
                }}
            </div>
            <div
                v-if="listing.is_expired"
                class="text-sm font-bold text-destructive"
            >
                {{ $t('listings.common.expired') }}
            </div>
        </div>

        <div class="mb-4">
            <Input
                v-model="amount"
                type="number"
                min="1"
                :placeholder="$t('listings.donation.amount_placeholder')"
                :disabled="listing.is_expired"
            />
        </div>

        <Button
            class="w-full"
            variant="default"
            @click="donate"
            :disabled="!amount || processing || listing.is_expired"
        >
            <span v-if="processing">{{ $t('actions.processing') }}</span>
            <span v-else-if="listing.is_expired">{{
                $t('listings.common.expired')
            }}</span>
            <span v-else>{{ $t('listings.donation.action_btn') }}</span>
        </Button>
    </div>
</template>
