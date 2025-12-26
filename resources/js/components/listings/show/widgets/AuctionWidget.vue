<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useMoney } from '@/composables/useMoney';
import { bid } from '@/routes/listings';
import { AuctionListable, Listing } from '@/types/listings';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    data: AuctionListable;
    listing: Listing;
}>();

const { formatMoney } = useMoney();

const bidForm = useForm({
    amount: null as number | null,
});

const submitBid = () => {
    bidForm.post(bid.url(props.listing.id), {
        preserveScroll: true,
        onSuccess: () => bidForm.reset('amount'),
    });
};
</script>

<template>
    <div class="rounded-lg border bg-card p-6 text-card-foreground shadow-sm">
        <h3 class="mb-4 leading-none font-semibold tracking-tight">
            {{ $t('listings.auction.current_bid') }}
        </h3>
        <div class="flex flex-col gap-4">
            <div class="text-center text-3xl font-bold text-primary">
                {{ formatMoney(data.current_bid || data.start_price) }}
            </div>
            <div class="text-center text-xs text-muted-foreground">
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
                class="text-center text-sm font-bold text-destructive"
            >
                {{ $t('listings.common.expired') }}
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
                        v-model="bidForm.amount as any"
                        type="number"
                        :placeholder="$t('listings.common.amount')"
                        :disabled="listing.is_expired"
                    />

                    <Button
                        type="submit"
                        :disabled="bidForm.processing || listing.is_expired"
                    >
                        {{
                            listing.is_expired
                                ? $t('listings.common.status.expired')
                                : $t('listings.auction.bid_btn')
                        }}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
