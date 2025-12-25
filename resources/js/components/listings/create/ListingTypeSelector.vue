<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { computed } from 'vue';

// Define the exact types based on your requirements
type ListingType =
    | 'private_occasion'
    | 'charity_action'
    | 'donation_campaign'
    | 'founders_creatives';

const props = defineProps<{
    modelValue: ListingType;
    disabled?: boolean;
}>();

const emit = defineEmits<{
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
            class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"
        >
            <Label
                class="flex cursor-pointer flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 transition-all hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
            >
                <RadioGroupItem value="private_occasion" class="sr-only" />
                <span class="mb-1 text-center font-semibold">
                    {{ $t('createListing.types.private_occasion.title') }}
                </span>
                <span
                    class="text-center text-xs leading-snug text-muted-foreground"
                >
                    {{ $t('createListing.types.private_occasion.description') }}
                </span>
            </Label>

            <Label
                class="flex cursor-pointer flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 transition-all hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
            >
                <RadioGroupItem value="charity_action" class="sr-only" />
                <span class="mb-1 text-center font-semibold">
                    {{ $t('createListing.types.charity_action.title') }}
                </span>
                <span
                    class="text-center text-xs leading-snug text-muted-foreground"
                >
                    {{ $t('createListing.types.charity_action.description') }}
                </span>
            </Label>

            <Label
                class="flex cursor-pointer flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 transition-all hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
            >
                <RadioGroupItem value="donation_campaign" class="sr-only" />
                <span class="mb-1 text-center font-semibold">
                    {{ $t('createListing.types.donation_campaign.title') }}
                </span>
                <span
                    class="text-center text-xs leading-snug text-muted-foreground"
                >
                    {{
                        $t('createListing.types.donation_campaign.description')
                    }}
                </span>
            </Label>

            <Label
                class="flex cursor-pointer flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 transition-all hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
            >
                <RadioGroupItem value="founders_creatives" class="sr-only" />
                <span class="mb-1 text-center font-semibold">
                    {{ $t('createListing.types.founders_creatives.title') }}
                </span>
                <span
                    class="text-center text-xs leading-snug text-muted-foreground"
                >
                    {{
                        $t('createListing.types.founders_creatives.description')
                    }}
                </span>
            </Label>
        </RadioGroup>
    </div>
</template>
