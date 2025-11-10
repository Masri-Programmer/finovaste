<template>
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <InfoBox
            :icon="TrendingUp"
            :title="$t('listingdetails.boxes.roi')"
            value="12-15%"
            icon-bg-class="bg-blue-100 text-blue-700"
        />
        <InfoBox
            :icon="CalendarDays"
            :title="$t('listingdetails.boxes.duration')"
            value="24 months"
            icon-bg-class="bg-green-100 text-green-700"
        />
        <InfoBox
            :icon="Users"
            :title="$t('listingdetails.boxes.investors')"
            :value="investorCount.toString()"
            icon-bg-class="bg-purple-100 text-purple-700"
        />
    </section>

    <section class="space-y-3">
        <div class="flex justify-between text-sm font-medium">
            <span class="text-muted-foreground">{{
                $t('listingdetails.capital_raised')
            }}</span>
            <span class="text-foreground">
                {{ formattedCurrentCapital }} / {{ formattedTotalCapital }}
            </span>
        </div>
        <Progress v-model="progressPercentage" class="w-full" />
    </section>

    <section>
        <h2 class="mb-4 text-lg font-semibold text-foreground">
            {{ $t('listingdetails.key_features') }}
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
                    $t(feature.text)
                }}</span>
            </div>
        </div>
    </section>

    <section>
        <h2 class="mb-4 text-lg font-semibold text-foreground">
            {{ $t('listingdetails.faq_title') }}
        </h2>
        <p class="mb-6 text-sm text-muted-foreground">
            {{ $t('listingdetails.faq_subtitle') }}
        </p>
        <Accordion type="single" class="w-full" collapsible>
            <AccordionItem
                v-for="item in faqItems"
                :key="item.value"
                :value="item.value"
            >
                <AccordionTrigger>{{ $t(item.question) }}</AccordionTrigger>
                <AccordionContent>
                    {{ $t(item.answer) }}
                </AccordionContent>
            </AccordionItem>
        </Accordion>
    </section>
</template>

<script setup lang="ts">
// --- Imports ---
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Progress } from '@/components/ui/progress';
import { Listing } from '@/types/listings';
import {
    BarChart,
    CalendarDays,
    CheckCircle,
    ShieldCheck,
    TrendingUp,
    Users,
} from 'lucide-vue-next';
import { computed } from 'vue';
import InfoBox from './InfoBox.vue'; // Assuming InfoBox.vue is in the same directory

defineProps<{
    listing: Listing;
}>();

const totalCapitalGoal = 750000;
const currentCapital = 450000;
const investorCount = 52;

/**
 * Calculates the progress bar percentage.
 */
const progressPercentage = computed(() => {
    return (currentCapital / totalCapitalGoal) * 100;
});

/**
 * Formats a number as German currency (EUR).
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

// --- Static Content ---

const keyFeatures = [
    { text: 'listingdetails.features.verified', icon: CheckCircle },
    { text: 'listingdetails.features.secure', icon: ShieldCheck },
    { text: 'listingdetails.features.support', icon: Users },
    { text: 'listingdetails.features.growth', icon: BarChart },
];

const faqItems = [
    {
        value: 'item-1',
        question: 'listingdetails.faq.q1',
        answer: 'listingdetails.faq.a1',
    },
    {
        value: 'item-2',
        question: 'listingdetails.faq.q2',
        answer: 'listingdetails.faq.a2',
    },
    {
        value: 'item-3',
        question: 'listingdetails.faq.q3',
        answer: 'listingdetails.faq.a3',
    },
    {
        value: 'item-4',
        question: 'listingdetails.faq.q4',
        answer: 'listingdetails.faq.a4',
    },
];
</script>
