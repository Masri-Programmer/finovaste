<template>
    <Card class="sticky top-8 border-border shadow-lg">
        <CardContent class="space-y-4 p-6">
            <div class="text-sm font-medium text-muted-foreground">
                {{ t('listings_show.min') }}
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-lg font-semibold">
                    {{
                        minInvestmentAmount.toLocaleString('de-DE', {
                            style: 'currency',
                            currency: 'EUR',
                            maximumFractionDigits: 0,
                        })
                    }}
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

            <div class="flex justify-between text-sm">
                <span class="text-muted-foreground">{{
                    t('listings_show.amount')
                }}</span>
                <span class="font-bold text-foreground">
                    {{
                        investmentAmount.toLocaleString('de-DE', {
                            style: 'currency',
                            currency: 'EUR',
                            maximumFractionDigits: 0,
                        })
                    }}
                </span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-muted-foreground">{{
                    t('listings_show.share')
                }}</span>
                <span class="font-bold text-foreground"
                    >{{ capitalShare }}%</span
                >
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

    <Card class="border-border shadow-sm">
        <CardHeader>
            <CardTitle class="text-lg font-semibold">
                {{ t('listings_show.listedBy') }}
            </CardTitle>
        </CardHeader>
        <CardContent class="space-y-4 p-6 pt-0">
            <div class="flex items-center space-x-4">
                <Avatar class="h-12 w-12">
                    <AvatarImage
                        :src="user?.profile_photo_path"
                        :alt="user?.name"
                    />
                    <AvatarFallback>
                        {{ user?.name?.substring(0, 2).toUpperCase() ?? '??' }}
                    </AvatarFallback>
                </Avatar>
                <div class="space-y-1">
                    <p class="text-base font-semibold">
                        {{ user?.name ?? 'Loading...' }}
                    </p>
                    <div
                        class="flex items-center text-sm text-muted-foreground"
                    >
                        <Star
                            class="mr-1 h-4 w-4 fill-amber-400 text-amber-400"
                        />
                        <span>
                            {{ user?.rating ?? 0 }} ({{ user?.reviews ?? 0 }}
                            {{ t('listings_show.reviews') }})
                        </span>
                    </div>
                </div>
            </div>

            <Separator />

            <div class="grid grid-cols-2 gap-y-2 text-sm">
                <p class="text-muted-foreground">
                    {{ t('listings_show.memberSince') }}
                </p>
                <p class="text-right font-medium">
                    {{ user?.memberSince ?? 'N/A' }}
                </p>
                <p class="text-muted-foreground">
                    {{ t('listings_show.responseTime') }}
                </p>
                <p class="text-right font-medium">
                    {{ user?.responseTime ?? 'N/A' }}
                </p>
                <p class="text-muted-foreground">
                    {{ t('listings_show.totalListings') }}
                </p>
                <p class="text-right font-medium">
                    {{ user?.totalListings ?? 0 }}
                </p>
            </div>

            <Button variant="secondary" class="mt-4 w-full">
                <Mail class="mr-2 h-4 w-4" />
                {{ t('listings_show.button.contactSeller') }}
            </Button>
        </CardContent>
    </Card>

    <!-- <Dialog :open="isDialogOpen" @update:open="toggleDialog">
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
                    {{ t('listings_show.dialog.confirmButton') }}
                </Button>
            </form>
        </DialogContent>
    </Dialog> -->
</template>

<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
// import Dialog from '@/components/ui/dialog/Dialog.vue';
// import DialogContent from '@/components/ui/dialog/DialogContent.vue';
// import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
// import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
// import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
// import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Separator from '@/components/ui/separator/Separator.vue';
import Slider from '@/components/ui/slider/Slider.vue';
import { useForm } from '@inertiajs/vue3';
import { useToggle } from '@vueuse/core';
import { Mail } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const props = defineProps({
    listing: { type: Object, required: true },
    user: {
        type: Object,
        required: true,
    },
});

const minInvestmentAmount = 25000;
const totalCapitalGoal = 750000;
const investmentAmount = ref(minInvestmentAmount);

const [isDialogOpen, toggleDialog] = useToggle(false);

const investForm = useForm({
    amount: investmentAmount.value,
    listing_id: props.listing.id,
});

// 2. Add 'capitalShare' computed property
const capitalShare = computed(() => {
    if (totalCapitalGoal === 0) {
        return '0.00'; // Avoid division by zero
    }
    const share = (investmentAmount.value / totalCapitalGoal) * 100;
    return share.toFixed(2); // Format to 2 decimal places
});

const submitInvestment = () => {
    // router.post(route('listings.invest', props.listing.uuid), investForm, {
    //     onStart: () => {
    //         toast.info(t('listings_show.toast.starting'), {
    //             id: 'invest-submit',
    //         });
    //     },
    //     onSuccess: () => {
    //         toggleDialog(false);
    //         toast.success(t('listings_show.toast.success'), {
    //             id: 'invest-submit',
    //         });
    //     },
    //     onError: (errors) => {
    //         const errorMsg = errors.amount || t('listings_show.toast.failure');
    //         toast.error(errorMsg, { id: 'invest-submit' });
    //     },
    // });
};

watch(investmentAmount, (newAmount) => {
    investForm.amount = newAmount;
});
</script>
