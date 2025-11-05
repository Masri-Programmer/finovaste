<template>
    <div class="space-y-4">
        <Card class="sticky top-8 border-border shadow-lg">
            <CardContent class="space-y-4 p-6">
                <div class="text-sm font-medium text-muted-foreground">
                    {{ t('listings_show.min') }}
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-lg font-semibold">
                        {{ formattedMinAmount }}
                    </span>
                    <span class="text-sm text-muted-foreground">
                        {{ t('listings_show.minimum') }}
                    </span>
                </div>
                <Separator class="my-2" />

                <div class="space-y-3">
                    <Label
                        for="investment-slider"
                        class="text-sm font-medium text-muted-foreground"
                    >
                        {{ t('listings_show.your') }}
                    </Label>
                    <Slider
                        id="investment-slider"
                        v-model="investmentAmount"
                        :min="minInvestmentAmount"
                        :max="totalCapitalGoal"
                        :step="5000"
                        class="w-full"
                    />
                </div>

                <div class="space-y-2 pt-1">
                    <div
                        v-for="detail in summaryDetails"
                        :key="detail.key"
                        class="flex justify-between text-sm"
                    >
                        <span class="text-muted-foreground">{{
                            detail.label
                        }}</span>
                        <span class="font-bold text-foreground">{{
                            detail.value
                        }}</span>
                    </div>
                </div>

                <Button
                    @click="toggleDialog(true)"
                    class="mt-4 w-full bg-primary text-primary-foreground hover:bg-primary/90"
                >
                    {{ t('listings_show.button.invest') }}
                </Button>
                <p class="mt-2 text-center text-xs text-muted-foreground">
                    {{ t('listings_show.securePayment') }}
                </p>
            </CardContent>
        </Card>
    </div>
    <Dialog :open="isDialogOpen" @update:open="toggleDialog">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>{{ t('listings_show.dialog.title') }}</DialogTitle>
                <DialogDescription>
                    {{
                        t('listings_show.dialog.description', {
                            amount: investmentAmount.toLocaleString('de-DE', {
                                style: 'currency',
                                currency: 'EUR',
                                maximumFractionDigits: 0,
                            }),
                        })
                    }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submitInvestment" class="grid gap-4 py-4">
                <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="amount" class="text-right">
                        {{ t('listings_show.amount') }}
                    </Label>
                    <Input
                        id="amount"
                        v-model="investForm.amount"
                        type="number"
                        :min="minInvestmentAmount"
                        :max="totalCapitalGoal"
                        class="col-span-3"
                        disabled
                    />
                </div>
                <div
                    v-if="investForm.errors.amount"
                    class="col-span-4 text-sm text-destructive"
                >
                    {{ investForm.errors.amount }}
                </div>

                <Button
                    type="submit"
                    :disabled="investForm.processing"
                    class="mt-4 w-full bg-primary text-primary-foreground hover:bg-primary/90"
                >
                    {{
                        investForm.processing
                            ? t('listings_show.dialog.processing')
                            : t('listings_show.dialog.confirmButton')
                    }}
                </Button>
            </form>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Separator from '@/components/ui/separator/Separator.vue';
import { User } from '@/types';
import { Listing } from '@/types/listings';
import { useForm, usePage } from '@inertiajs/vue3';

import { useToggle } from '@vueuse/core';
import { useI18n } from 'vue-i18n';

import { computed, ref, watch } from 'vue';
import { useToast } from 'vue-toastification';
const { t } = useI18n();
const toast = useToast();
const page = usePage();
const listing = computed<Listing>(() => {
    return page.props.listing as Listing;
});

const user = computed<User>(() => {
    return page.props.auth.user as User;
});
const [isDialogOpen, toggleDialog] = useToggle(false);

const minInvestmentAmount: number = 25000;
const totalCapitalGoal: number = 750000;
const investmentAmount = ref<number>(minInvestmentAmount);

const investForm = useForm({
    amount: investmentAmount.value,
    listing_id: listing.value.id,
});

const capitalShare = computed(() => {
    const share = (investmentAmount.value / totalCapitalGoal) * 100;
    // .toFixed(2) ensures it's always two decimal places, e.g., "10.00"
    return share.toFixed(2);
});

const submitInvestment = () => {
    // Use the Inertia form helper to send a POST request
    // 'route' function generates the URL from Laravel Typed Wayfinder
    // investForm.post(route('listings.invest', listing.value.uuid), {
    //     // --- vue-toastification: onStart ---
    //     onStart: () => {
    //         toast.info(t('listings_show.toast.starting'), {
    //             id: 'invest-submit', // Use an ID to update the toast later
    //         });
    //     },
    //     // --- vue-toastification: onSuccess ---
    //     onSuccess: () => {
    //         toggleDialog(false); // Close dialog on success
    //         toast.success(t('listings_show.toast.success'), {
    //             id: 'invest-submit', // This updates the 'starting' toast
    //         });
    //         // Optionally reset slider
    //         // investmentAmount.value = minInvestmentAmount;
    //     },
    //     // --- vue-toastification: onError ---
    //     onError: (errors) => {
    //         // Use the specific error from Inertia or a generic fallback
    //         const errorMsg = errors.amount || t('listings_show.toast.failure');
    //         toast.error(errorMsg, { id: 'invest-submit' });
    //     },
    // });
};

// --- Watchers ---
// Syncs the slider's 'investmentAmount' ref with the 'investForm.amount'
watch(investmentAmount, (newAmount) => {
    investForm.amount = newAmount;
});

const formatCurrency = (value: number): string => {
    return value.toLocaleString('de-DE', {
        style: 'currency',
        currency: 'EUR',
        maximumFractionDigits: 0,
    });
};

// --- 2. Create computed properties for display ---
const formattedMinAmount = computed(() => formatCurrency(minInvestmentAmount));
const formattedInvestmentAmount = computed(() =>
    formatCurrency(investmentAmount.value),
);

// --- 3. Create a data-driven list for the summary ---
const summaryDetails = computed(() => [
    {
        key: 'amount',
        label: t('listings_show.amount'),
        value: formattedInvestmentAmount.value,
    },
    {
        key: 'share',
        label: t('listings_show.share'),
        value: `${capitalShare.value}%`, // Assuming 'capitalShare' is a computed ref
    },
]);
</script>
