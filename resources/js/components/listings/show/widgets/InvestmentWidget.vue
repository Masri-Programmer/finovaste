<template>
    <ListingCard
        v-model="investmentAmount"
        :min-investment-amount="data.minimum_investment"
        :total-capital-goal="data.investment_goal"
        :amount-raised="data.amount_raised"
        type="investment"
        @action="toggleDialog(true)"
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
                    <Label for="amount" class="text-right">
                        {{ $t('listings.investment.dialog.amount_label') }}
                    </Label>
                    <Input
                        id="amount"
                        v-model="investForm.amount"
                        type="number"
                        :min="data.minimum_investment"
                        :max="
                            data.investment_goal -
                            parseFloat(data.amount_raised)
                        "
                        class="col-span-3"
                    />
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
import { checkout } from '@/actions/App/Http/Controllers/PaymentController';
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
import { InvestmentListable } from '@/types/listings';
import { useForm } from '@inertiajs/vue3';
import { useToggle } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import ListingCard from '../ListingCard.vue';

const props = defineProps<{
    data: InvestmentListable;
    listingId: number;
}>();

const [isDialogOpen, toggleDialog] = useToggle(false);
const investmentAmount = ref<number>(props.data.minimum_investment || 0);

const investForm = useForm({
    amount: investmentAmount.value,
    listing_id: props.listingId,
    shares: 1,
});

watch(investmentAmount, (newVal) => (investForm.amount = newVal));

const formattedAmount = computed(() => {
    return investmentAmount.value.toLocaleString('de-DE', {
        style: 'currency',
        currency: 'EUR',
        maximumFractionDigits: 0,
    });
});

const submitInvestment = () => {
    investForm.post(checkout.url(props.listingId));
};
</script>
