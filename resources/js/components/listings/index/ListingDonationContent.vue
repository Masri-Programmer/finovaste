<script setup lang="ts">
import { Progress } from '@/components/ui/progress';
import { useFormatting } from '@/composables/useFormatting';
import { Target, Users } from 'lucide-vue-next';

defineProps<{
    listable: any;
}>();

const { formatCurrency, getProgress } = useFormatting();
</script>

<template>
    <div class="mt-4 space-y-3">
        <div>
            <div class="mb-1 flex justify-between text-sm font-medium">
                <span>{{ $t('listings.donationsRaised') }}</span>
                <span class="text-foreground">
                    {{ formatCurrency(listable.raised) }} /
                    {{ formatCurrency(listable.target) }}
                </span>
            </div>
            <Progress
                :model-value="getProgress(listable.raised, listable.target)"
                class="[&>*]:bg-destructive"
            />
        </div>
        <div class="flex items-center justify-between text-sm">
            <span class="flex items-center gap-1 text-muted-foreground">
                <Users class="h-4 w-4" />
                {{ listable.donors_count }}
                {{ $t('listings.donors') }}
            </span>
            <span
                v-if="listable.is_capped"
                class="flex items-center gap-1 text-muted-foreground"
            >
                <Target class="h-4 w-4" />
                {{ $t('listings.flexibleGoal') }}
            </span>
        </div>
    </div>
</template>
