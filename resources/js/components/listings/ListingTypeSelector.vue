<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

type ListingType = 'buy_now' | 'auction' | 'donation';

const props = defineProps<{
    modelValue: ListingType;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: ListingType): void;
}>();

const { t } = useI18n();

const listingType = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});
</script>

<template>
    <div class="space-y-4">
        <Label class="text-base font-semibold">
            {{ t('listing.createListing.sections.type') }}
        </Label>
        <RadioGroup
            v-model="listingType"
            class="grid grid-cols-1 gap-4 md:grid-cols-3"
        >
            <Label
                class="flex flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
            >
                <RadioGroupItem value="buy_now" class="sr-only" />
                <span class="font-semibold">
                    {{ t('listing.createListing.types.buy_now.title') }}
                </span>
                <span class="text-sm text-muted-foreground">
                    {{ t('listing.createListing.types.buy_now.description') }}
                </span>
            </Label>
            <Label
                class="flex flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
            >
                <RadioGroupItem value="auction" class="sr-only" />
                <span class="font-semibold">
                    {{ t('listing.createListing.types.auction.title') }}
                </span>
                <span class="text-sm text-muted-foreground">
                    {{ t('listing.createListing.types.auction.description') }}
                </span>
            </Label>
            <Label
                class="flex flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
            >
                <RadioGroupItem value="donation" class="sr-only" />
                <span class="font-semibold">
                    {{ t('listing.createListing.types.donation.title') }}
                </span>
                <span class="text-sm text-muted-foreground">
                    {{ t('listing.createListing.types.donation.description') }}
                </span>
            </Label>
        </RadioGroup>
    </div>
</template>
