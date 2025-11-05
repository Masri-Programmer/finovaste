<script setup lang="ts">
import { useFormatting } from '@/composables/useFormatting';
import { CircleDollarSign, Package } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

defineProps<{
    listable: any; // Or a more specific 'BuyNowListable' type
}>();

const { t } = useI18n();
const { formatCurrency } = useFormatting();
</script>

<template>
    <div class="mt-4 space-y-3">
        <div
            class="flex items-baseline justify-between rounded-lg bg-secondary p-3 text-sm"
        >
            <span class="flex items-center gap-1 text-secondary-foreground">
                <CircleDollarSign class="h-4 w-4" />
                {{ t('homepage.listings.price') }}
            </span>
            <span class="text-lg font-bold text-secondary-foreground">
                {{ formatCurrency(listable.price) }}
            </span>
        </div>
        <div class="flex items-center justify-between text-sm">
            <span class="flex items-center gap-1 text-muted-foreground">
                <Package class="h-4 w-4" />
                {{ t('homepage.listings.quantity') }}:
            </span>
            <span class="font-medium text-foreground">
                {{ listable.quantity }}
            </span>
        </div>
    </div>
</template>
