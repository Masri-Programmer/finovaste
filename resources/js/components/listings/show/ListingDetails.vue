<template>
    <div class="w-full max-w-4xl space-y-8">
        <section class="space-y-4">
            <div
                class="flex flex-col sm:flex-row sm:items-start sm:justify-between"
            >
                <h1 class="text-3xl font-bold text-foreground">
                    {{ localizedTitle }}
                </h1>
                <div
                    class="mt-2 flex-shrink-0 sm:mt-0"
                    v-if="listing.average_rating > 0"
                >
                    <div class="flex items-center space-x-2">
                        <Star
                            class="h-5 w-5 text-yellow-400"
                            fill="currentColor"
                        />
                        <span class="text-lg font-bold">{{
                            listing.average_rating
                        }}</span>
                        <span class="text-base text-muted-foreground">
                            ({{ listing.reviews_count }}
                            {{ t('listing_details.reviews') }})
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-4 text-muted-foreground">
                <div class="flex items-center space-x-1">
                    <MapPin class="h-4 w-4" />
                    <span
                        >{{ listing.address.city }},
                        {{ listing.address.state }}</span
                    >
                </div>
                <span>•</span>
                <span>{{ localizedCategoryName }}</span>
            </div>
            <p class="text-base text-foreground/80">
                {{ localizedDescription }}
            </p>
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <InfoBox
                :icon="TrendingUp"
                :title="t('listing_details.boxes.roi')"
                value="12-15%"
                icon-bg-class="bg-blue-100 text-blue-700"
            />
            <InfoBox
                :icon="CalendarDays"
                :title="t('listing_details.boxes.duration')"
                value="24 months"
                icon-bg-class="bg-green-100 text-green-700"
            />
            <InfoBox
                :icon="Users"
                :title="t('listing_details.boxes.investors')"
                :value="investorCount.toString()"
                icon-bg-class="bg-purple-100 text-purple-700"
            />
        </section>

        <section class="space-y-3">
            <div class="flex justify-between text-sm font-medium">
                <span class="text-muted-foreground">{{
                    t('listing_details.capital_raised')
                }}</span>
                <span class="text-foreground">
                    {{ formattedCurrentCapital }} / {{ formattedTotalCapital }}
                </span>
            </div>
            <Progress v-model="progressPercentage" class="w-full" />
        </section>

        <section>
            <h2 class="mb-4 text-lg font-semibold text-foreground">
                {{ t('listing_details.key_features') }}
            </h2>
            <div class="grid grid-cols-2 gap-x-8 gap-y-3">
                <div
                    v-for="feature in keyFeatures"
                    :key="feature.text"
                    class="flex items-center space-x-3"
                >
                    <component
                        :is="feature.icon"
                        class="h-5 w-5 flex-shrink-0 text-green-600"
                    />
                    <span class="text-sm text-muted-foreground">{{
                        t(feature.text)
                    }}</span>
                </div>
            </div>
        </section>

        <section>
            <h2 class="mb-4 text-lg font-semibold text-foreground">
                {{ t('listing_details.faq_title') }}
            </h2>
            <p class="mb-6 text-sm text-muted-foreground">
                {{ t('listing_details.faq_subtitle') }}
            </p>
            <Accordion type="single" class="w-full" collapsible>
                <AccordionItem
                    v-for="item in faqItems"
                    :key="item.value"
                    :value="item.value"
                >
                    <AccordionTrigger>{{ t(item.question) }}</AccordionTrigger>
                    <AccordionContent>
                        {{ t(item.answer) }}
                    </AccordionContent>
                </AccordionItem>
            </Accordion>
        </section>
    </div>
</template>

<script setup lang="ts">
import type { Address, Category, Listing } from '@/types';
import {
    BarChart,
    CalendarDays,
    CheckCircle,
    MapPin,
    ShieldCheck,
    Star,
    TrendingUp,
    Users,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import InfoBox from './InfoBox.vue';

import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Progress } from '@/components/ui/progress';

// --- Types ---
interface Translatable {
    en: string;
    de: string;
    [key: string]: string;
}

interface Address {
    city: string;
    state: string;
}

interface Category {
    name: Translatable;
}

interface Listing {
    id: number;
    title: Translatable;
    description: Translatable;
    average_rating: number;
    reviews_count: number;
    address: Address;
    category: Category;
}

// --- Props ---
const props = defineProps<{
    listing: Listing;
}>();

// --- i18n ---
const { t, locale } = useI18n();

// --- Data Placeholders ---
// These values were in the image but not the JSON.
// Using values from your *previous* component for consistency.
const totalCapitalGoal = 750000;
const currentCapital = 450000;
const investorCount = 52;

// --- Computed Properties ---

/**
 * Selects the correct language string from a translatable object
 * based on the current i18n locale, with a fallback to 'en'.
 */
const getLocalizedString = (field: Translatable) => {
    return field[locale.value] || field.en;
};

const localizedTitle = computed(() => getLocalizedString(props.listing.title));
const localizedDescription = computed(() =>
    getLocalizedString(props.listing.description),
);
const localizedCategoryName = computed(() =>
    getLocalizedString(props.listing.category.name),
);

/**
 * Calculates the progress bar percentage.
 */
const progressPercentage = computed(() => {
    return (currentCapital / totalCapitalGoal) * 100;
});

/**
 * Formats a number as German currency.
 */
const formatCurrency = (value: number): string => {
    return value.toLocaleString('de-DE', {
        style: 'currency',
        currency: 'EUR',
        maximumFractionDigits: 0,
    });
};

const formattedTotalCapital = computed(() => formatCurrency(totalCapitalGoal));
const formattedCurrentCapital = computed(() => formatCurrency(currentCapital));

// --- Hardcoded Content (from image) ---

const keyFeatures = [
    { text: 'listing_details.features.verified', icon: CheckCircle },
    { text: 'listing_details.features.secure', icon: ShieldCheck },
    { text: 'listing_details.features.support', icon: Users },
    { text: 'listing_details.features.growth', icon: BarChart },
];

const faqItems = [
    {
        value: 'item-1',
        question: 'listing_details.faq.q1',
        answer: 'listing_details.faq.a1',
    },
    {
        value: 'item-2',
        question: 'listing_details.faq.q2',
        answer: 'listing_details.faq.a2',
    },
    {
        value: 'item-3',
        question: 'listing_details.faq.q3',
        answer: 'listing_details.faq.a3',
    },
    {
        value: 'item-4',
        question: 'listing_details.faq.q4',
        answer: 'listing_details.faq.a4',
    },
];
</script>
