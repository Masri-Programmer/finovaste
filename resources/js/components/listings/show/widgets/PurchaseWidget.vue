<template>
    <div class="rounded-lg border bg-card p-6 text-card-foreground shadow-sm">
        <h3 class="mb-4 leading-none font-semibold tracking-tight">
            {{ $t('listings.purchase.purchase_details') }}
        </h3>
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <span class="text-muted-foreground">{{
                    $t('listings.purchase.price')
                }}</span>
                <span class="text-2xl font-bold">{{
                    formatCurrency(data.price)
                }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-muted-foreground">{{
                    $t('listings.purchase.condition')
                }}</span>
                <span class="capitalize">
                    {{
                        data.condition
                            ? $t(
                                  'listings.purchase.condition_' +
                                      data.condition.toLowerCase(),
                              )
                            : $t('listings.purchase.condition_new')
                    }}
                </span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-muted-foreground">{{
                    $t('listings.purchase.available_qty')
                }}</span>
                <span>{{ data.quantity }}</span>
            </div>
            <Button
                class="mt-2 w-full"
                :disabled="isOwner"
                @click="router.post(buy.url({ listing: listingId }))"
            >
                {{
                    isOwner
                        ? $t('listings.purchase.ownership')
                        : $t('listings.purchase.action_btn')
                }}
            </Button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { formatCurrency } from '@/composables/useCurrency';
import { buy } from '@/routes/listings';
import { PurchaseListable } from '@/types/listings';
import { router } from '@inertiajs/vue3';
const props = defineProps<{
    data: PurchaseListable;
    listingId: number;
    isOwner?: boolean;
}>();
</script>
