<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { computed } from 'vue';

// --- 1. Add 'investment' to the type ---
type ListingType = 'buy_now' | 'auction' | 'donation' | 'investment';

const props = defineProps<{
    modelValue: ListingType;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    // --- 2. Add 'investment' to the emit definition ---
    (e: 'update:modelValue', value: ListingType): void;
}>();

const listingType = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});
</script>

<template>
    <div class="space-y-4">
        <Label class="text-base font-semibold">
            {{ $t('createListing.sections.type') }}
        </Label>
        <RadioGroup
            v-model="listingType"
            :disabled="disabled"
            class="grid grid-cols-1 gap-4 md:grid-cols-4"
        >
            <Label
                class="flex flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
            >
                <RadioGroupItem value="buy_now" class="sr-only" />
                <span class="font-semibold">
                    {{ $t('createListing.types.buy_now.title') }}
                </span>
                <span class="text-sm text-muted-foreground">
                    {{ $t('createListing.types.buy_now.description') }}
                </span>
            </Label>
            <Label
                class="flex flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
            >
                <RadioGroupItem value="auction" class="sr-only" />
                <span class="font-semibold">
                    {{ $t('createListing.types.auction.title') }}
                </span>
                <span class="text-sm text-muted-foreground">
                    {{ $t('createListing.types.auction.description') }}
                </span>
            </Label>
            <Label
                class="flex flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
            >
                <RadioGroupItem value="donation" class="sr-only" />
                <span class="font-semibold">
                    {{ $t('createListing.types.donation.title') }}
                </span>
                <span class="text-sm text-muted-foreground">
                    {{ $t('createListing.types.donation.description') }}
                </span>
            </Label>

            <Label
                class="flex flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
            >
                <RadioGroupItem value="investment" class="sr-only" />
                <span class="font-semibold">
                    {{ $t('createListing.types.investment.title') }}
                </span>
                <span class="text-sm text-muted-foreground">
                    {{ $t('createListing.types.investment.description') }}
                </span>
            </Label>
        </RadioGroup>
    </div>
</template>
