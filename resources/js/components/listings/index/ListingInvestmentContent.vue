<script setup lang="ts">
import { Progress } from '@/components/ui/progress';
import { useFormatting } from '@/composables/useFormatting';
import { Users } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

defineProps<{
    listable: any; // Or a more specific 'InvestmentListable' type
}>();

const { t } = useI18n();
const { formatCurrency, getProgress } = useFormatting();
</script>

<template>
    <div class="mt-4 space-y-3">
        <div>
            <div class="mb-1 flex justify-between text-sm font-medium">
                <span>{{ t('homepage.listings.capitalRaised') }}</span>
                <span class="text-foreground">
                    {{ formatCurrency(listable.amount_raised) }} /
                    {{ formatCurrency(listable.investment_goal) }}
                </span>
            </div>
            <Progress
                :model-value="
                    getProgress(
                        listable.amount_raised,
                        listable.investment_goal,
                    )
                "
            />
        </div>
        <div
            class="flex flex-col items-start gap-2 text-sm sm:flex-row sm:items-center sm:justify-between"
        >
            <span class="flex items-center gap-1 text-muted-foreground">
                <Users class="h-4 w-4" />
                {{ listable.investors_count }}
                {{ t('homepage.listings.investors') }}
            </span>
            <div>
                <span class="text-muted-foreground"
                    >{{ t('homepage.listings.minInvestment') }}:
                </span>
                <span class="font-bold text-foreground">
                    {{ formatCurrency(listable.minimum_investment) }}
                </span>
            </div>
        </div>
    </div>
</template>
