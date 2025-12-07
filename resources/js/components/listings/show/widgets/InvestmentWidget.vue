<template>
    <ListingCard
        :model-value="investmentAmount"
        @update:model-value="updateSharesFromAmount"
        :min-investment-amount="data.minimum_investment"
        :total-capital-goal="data.investment_goal"
        :amount-raised="data.amount_raised"
        type="investment"
        @invest="toggleDialog(true)"
    />

    <Dialog :open="isDialogOpen" @update:open="toggleDialog">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>{{
                    $t('listings.investment.dialog.title')
                }}</DialogTitle>
                <DialogDescription>
                    {{
                        $t('listings.investment.dialog.description', {
                            amount: formattedAmount,
                        })
                    }}
                </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="submitInvestment" class="grid gap-4 py-4">
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="shares" class="text-right">
                        {{ $t('listings.investment.dialog.shares_label') }}
                    </Label>
                    <Input
                        id="shares"
                        v-model="investForm.shares"
                        type="number"
                        min="1"
                        :max="maxShares"
                        class="col-span-3"
                    />
                    <div
                        v-if="investForm.errors.shares"
                        class="col-span-4 text-right text-sm text-destructive"
                    >
                        {{ investForm.errors.shares }}
                    </div>
                </div>
                <div class="text-right font-bold">
                    Total: {{ formattedAmount }}
                </div>
                <Button
                    type="submit"
                    :disabled="investForm.processing"
                    class="mt-4 w-full"
                >
                    {{
                        investForm.processing
                            ? $t('listings.common.processing')
                            : $t('listings.investment.dialog.confirm_button')
                    }}
                </Button>
            </form>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { checkout } from '@/routes/listings';
import { InvestmentListable } from '@/types/listings';
import { useForm } from '@inertiajs/vue3';
import { useToggle } from '@vueuse/core';
import { computed } from 'vue';
import ListingCard from '../ListingCard.vue';

const props = defineProps<{
    data: InvestmentListable;
    listingId: number;
}>();

const [isDialogOpen, toggleDialog] = useToggle(false);

const investForm = useForm({
    shares: 1,
});

const investmentAmount = computed(() => {
    return investForm.shares * props.data.share_price;
});

// Calculate max shares available
const maxShares = computed(() => {
    if (!props.data.share_price) return 0;
    const soldShares = Math.floor(
        props.data.amount_raised / props.data.share_price,
    );
    return props.data.shares_offered - soldShares;
});

const formattedAmount = computed(() => {
    return investmentAmount.value.toLocaleString('de-DE', {
        style: 'currency',
        currency: 'EUR',
        maximumFractionDigits: 2,
    });
});

const updateSharesFromAmount = (amount: number) => {
    if (props.data.share_price > 0) {
        investForm.shares = Math.max(
            1,
            Math.round(amount / props.data.share_price),
        );
    }
};

const submitInvestment = () => {
    investForm.post(checkout.url(props.listingId), {
        preserveScroll: true,
        onSuccess: () => {
            toggleDialog(false);
        },
    });
};
</script>
