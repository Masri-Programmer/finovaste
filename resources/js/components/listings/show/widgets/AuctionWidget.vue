<template>
    <div class="rounded-lg border bg-card p-6 text-card-foreground shadow-sm">
        <h3 class="mb-4 leading-none font-semibold tracking-tight">
            {{ $t('listings.auction.current_bid') }}
        </h3>
        <div class="flex flex-col gap-4">
            <div class="text-center text-3xl font-bold text-primary">
                {{ formatCurrency(data.current_bid || data.start_price) }}
            </div>
            <div class="text-center text-xs text-muted-foreground">
                {{ $t('listings.auction.ends_at') }}
                {{ new Date(data.ends_at).toLocaleDateString('de-DE') }}
            </div>

            <form
                @submit.prevent="submitBid"
                class="mt-2 grid w-full max-w-sm items-center gap-1.5"
            >
                <Label for="bidAmount">{{
                    $t('listings.auction.place_bid_label')
                }}</Label>
                <div class="flex w-full max-w-sm items-center space-x-2">
                    <Input
                        id="bidAmount"
                        v-model="bidForm.amount"
                        type="number"
                        :placeholder="$t('listings.common.amount')"
                    />
                    <Button type="submit" :disabled="bidForm.processing">
                        {{ $t('listings.auction.bid_btn') }}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { formatCurrency } from '@/composables/useCurrency';
import { AuctionListable } from '@/types/listings';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    data: AuctionListable;
    listingId: number;
}>();

const bidForm = useForm({
    amount: null as number | null,
    listing_id: props.listingId,
});

const submitBid = () => {
    console.log('Bidding', bidForm.data());
    // bidForm.post(...)
};
</script>
