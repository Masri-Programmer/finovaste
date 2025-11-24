<template>
    <div class="rounded-lg border bg-card p-6 text-card-foreground shadow-sm">
        <h3 class="mb-4 leading-none font-semibold tracking-tight">
            {{ $t('listings.buy_now.purchase_details') }}
        </h3>
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <span class="text-muted-foreground">{{
                    $t('listings.buy_now.price')
                }}</span>
                <span class="text-2xl font-bold">{{
                    formatCurrency(data.price)
                }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-muted-foreground">{{
                    $t('listings.buy_now.condition')
                }}</span>
                <span class="capitalize">
                    {{
                        data.condition
                            ? $t(
                                  'listings.buy_now.condition_' +
                                      data.condition.toLowerCase(),
                              )
                            : $t('listings.buy_now.condition_new')
                    }}
                </span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-muted-foreground">{{
                    $t('listings.buy_now.available_qty')
                }}</span>
                <span>{{ data.quantity }}</span>
            </div>
            <Button class="mt-2 w-full" @click="handleBuyNow">
                {{ $t('listings.buy_now.action_btn') }}
            </Button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { formatCurrency } from '@/composables/useCurrency';
import { BuyNowListable } from '@/types/listings';

const props = defineProps<{
    data: BuyNowListable;
    listingId: number;
}>();

const handleBuyNow = () => {
    // Implement Buy Now Logic or emit event
    console.log('Buy now for listing', props.listingId);
};
</script>
