<script setup lang="ts">
import { computed } from 'vue';

import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Slider } from '@/components/ui/slider';
import { trans } from 'laravel-vue-i18n';

const props = defineProps<{
    modelValue: number;
    minInvestmentAmount: number;
    totalCapitalGoal: number;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: number): void;
    (e: 'invest'): void;
}>();

const investmentAmount = computed({
    get: () => props.modelValue,
    set: (value: number) => emit('update:modelValue', value),
});

const sliderValue = computed({
    get: () => [investmentAmount.value],
    set: (val: number[]) => {
        if (typeof val[0] === 'number') {
            investmentAmount.value = val[0];
        }
    },
});

const formatCurrency = (value: number): string => {
    return value.toLocaleString('de-DE', {
        style: 'currency',
        currency: 'EUR',
        maximumFractionDigits: 0,
    });
};

const formattedMinAmount = computed(() =>
    formatCurrency(props.minInvestmentAmount),
);

const formattedInvestmentAmount = computed(() =>
    formatCurrency(investmentAmount.value),
);

const capitalShare = computed(() => {
    const goal = props.totalCapitalGoal || 1;
    const share = (investmentAmount.value / goal) * 100;
    return share.toFixed(2);
});

const summaryDetails = computed(() => [
    {
        key: 'amount',
        label: trans('listings.amount'),
        value: formattedInvestmentAmount.value,
    },
    {
        key: 'share',
        label: trans('listings.capital_share'),
        value: `${capitalShare.value}%`,
    },
]);
</script>

<template>
    <Card>
        <CardContent class="space-y-4 p-6">
            <div class="text-sm font-medium text-muted-foreground">
                {{ $t('listings.min_investment') }}
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-lg font-semibold">
                    {{ formattedMinAmount }}
                </span>
                <span class="text-sm text-muted-foreground">
                    {{ $t('listings.minimum') }}
                </span>
            </div>
            <Separator class="my-2" />

            <div class="space-y-3">
                <Label
                    for="investment-slider"
                    class="text-sm font-medium text-muted-foreground"
                >
                    {{ $t('listings.your_investment') }}
                </Label>
                <Slider
                    id="investment-slider"
                    v-model="sliderValue"
                    :min="props.minInvestmentAmount"
                    :max="props.totalCapitalGoal"
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
                @click="emit('invest')"
                class="mt-4 w-full bg-primary text-primary-foreground hover:bg-primary/90"
            >
                {{ $t('listings.invest_button') }}
            </Button>
            <p class="mt-2 text-center text-xs text-muted-foreground">
                {{ $t('listings.secure_payment') }}
            </p>
        </CardContent>
    </Card>
</template>
