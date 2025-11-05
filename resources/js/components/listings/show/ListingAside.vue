<template>
    <aside class="sticky space-y-4 overflow-y-auto">
        <ListingCard
            v-model="investmentAmount"
            :min-investment-amount="minInvestmentAmount"
            :total-capital-goal="totalCapitalGoal"
            @invest="toggleDialog(true)"
        />
        <UserProfileCard :user="user" />
        <Dialog :open="isDialogOpen" @update:open="toggleDialog">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{{
                        t('listings_show.dialog.title')
                    }}</DialogTitle>
                    <DialogDescription>
                        {{
                            t('listings_show.dialog.description', {
                                amount: investmentAmount.toLocaleString(
                                    'de-DE',
                                    {
                                        style: 'currency',
                                        currency: 'EUR',
                                        maximumFractionDigits: 0,
                                    },
                                ),
                            })
                        }}
                    </DialogDescription>
                </DialogHeader>
                <form
                    @submit.prevent="submitInvestment"
                    class="grid gap-4 py-4"
                >
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
    </aside>
</template>

<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { User } from '@/types';
import { Listing } from '@/types/listings';
import { useForm, usePage } from '@inertiajs/vue3';

import { useToggle } from '@vueuse/core';
import { useI18n } from 'vue-i18n';

import { computed, ref, watch } from 'vue';
import ListingCard from './ListingCard.vue';
import UserProfileCard from './UserProfileCard.vue';
const { t } = useI18n();
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

watch(investmentAmount, (newAmount) => {
    investForm.amount = newAmount;
});
</script>
