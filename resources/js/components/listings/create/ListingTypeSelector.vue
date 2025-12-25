<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { computed } from 'vue';

// --- 1. Add 'investment' to the type ---
type ListingType = 'private' | 'creative' | 'charity' | 'charity_auction';

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
                <RadioGroupItem value="private" class="sr-only" />
                <span class="font-semibold">
                    {{ $t('createListing.types.private.title') }}
                </span>
                <span class="text-center text-sm text-muted-foreground">
                    {{ $t('createListing.types.private.description') }}
                </span>
            </Label>

            <Label
                class="flex flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
            >
                <RadioGroupItem value="creative" class="sr-only" />
                <span class="font-semibold">
                    {{ $t('createListing.types.creative.title') }}
                </span>
                <span class="text-center text-sm text-muted-foreground">
                    {{ $t('createListing.types.creative.description') }}
                </span>
            </Label>

            <Label
                class="flex flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
            >
                <RadioGroupItem value="charity" class="sr-only" />
                <span class="font-semibold">
                    {{ $t('createListing.types.charity.title') }}
                </span>
                <span class="text-center text-sm text-muted-foreground">
                    {{ $t('createListing.types.charity.description') }}
                </span>
            </Label>

            <Label
                class="flex flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
            >
                <RadioGroupItem value="charity_auction" class="sr-only" />
                <span class="font-semibold">
                    {{ $t('createListing.types.charity_auction.title') }}
                </span>
                <span class="text-center text-sm text-muted-foreground">
                    {{ $t('createListing.types.charity_auction.description') }}
                </span>
            </Label>
        </RadioGroup>
    </div>
</template>
