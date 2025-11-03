<script setup lang="ts">
import { Link as InertiaLink, useForm } from '@inertiajs/vue3';
import { useToggle } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toastification';

import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Progress } from '@/components/ui/progress';
import { Separator } from '@/components/ui/separator';
import { Slider } from '@/components/ui/slider';

// Icons
import {
    ArrowLeft,
    Building,
    Clock,
    Heart,
    Mail,
    MapPin,
    Share2,
    Star,
    TrendingUp,
    Users,
    Video,
} from 'lucide-vue-next';

// --- Typescript Interfaces for Props ---
interface Translatable {
    en: string;
    de: string;
}

interface Address {
    city: string;
    state: string;
    country: string;
}

interface Category {
    name: Translatable;
    slug: string;
}

interface ListingUser {
    id: number;
    name: string;
}

interface ListingData {
    id: number;
    uuid: string;
    title: Translatable;
    description: Translatable;
    listable: {
        price: string;
    };
    user: ListingUser;
    category: Category;
    address: Address;
}

interface PageProps {
    listing: ListingData;
    locale: 'en' | 'de';
    errors: object;
}

const toast = useToast();
const props = defineProps<PageProps>();

const { t, locale } = useI18n();

// --- Helper Function for Translatable JSON ---
// Uses the active i18n locale, but falls back to 'en'
const getTranslation = (field: Translatable | string) => {
    if (typeof field === 'object' && field !== null) {
        return field[locale.value as 'en' | 'de'] || field['en'];
    }
    return String(field);
};

// --- Left Column: Computed Data ---
const listingTitle = computed(() => getTranslation(props.listing.title));
const listingDescription = computed(() =>
    getTranslation(props.listing.description),
);
const listingCategory = computed(() =>
    getTranslation(props.listing.category.name),
);
const listingLocation = computed(
    () => `${props.listing.address.city}, ${props.listing.address.state}`,
);

// Mock Data (from design, as it's missing in JSON)
const mockData = {
    rating: 4.8,
    reviews: 34,
    expectedRoi: '12-15%',
    duration: 24, // months
    investors: 52,
    capitalRaised: 450000, // EUR
    capitalGoal: 750000, // EUR
};

const capitalProgress = computed(
    () => (mockData.capitalRaised / mockData.capitalGoal) * 100,
);

// --- Right Column: Investment Card Logic ---
const minInvestmentAmount = 25000;
const totalCapitalGoal = mockData.capitalGoal;
const investmentAmount = ref(minInvestmentAmount);

const capitalShare = computed(() => {
    return ((investmentAmount.value / totalCapitalGoal) * 100).toFixed(2);
});

// VueUse: Toggle for Dialog
const [isDialogOpen, toggleDialog] = useToggle(false);

// Inertia: Investment Form
const investForm = useForm({
    amount: investmentAmount.value,
    listing_id: props.listing.id,
});

watch(investmentAmount, (newAmount) => {
    investForm.amount = newAmount;
});

const submitInvestment = () => {
    // router.post(route('listings.invest', props.listing.uuid), investForm, {
    //     onStart: () => {
    //         toast.info(t('listings.show.toast.starting'), {
    //             id: 'invest-submit',
    //         });
    //     },
    //     onSuccess: () => {
    //         toggleDialog(false);
    //         toast.success(t('listings.show.toast.success'), {
    //             id: 'invest-submit',
    //         });
    //     },
    //     onError: (errors) => {
    //         const errorMsg = errors.amount || t('listings.show.toast.failure');
    //         toast.error(errorMsg, { id: 'invest-submit' });
    //     },
    // });
};

const seller = {
    name: 'John Developer',
    rating: 4.9,
    reviews: 127,
    memberSince: 2020,
    responseTime: '~2 hours',
    totalListings: 24,
};
</script>

<template>
    <div class="container mx-auto max-w-7xl p-4 py-8 md:p-8">
        <header class="mb-6 flex items-center justify-between">
            <InertiaLink
                :href="'#'"
                class="flex items-center text-sm font-medium text-muted-foreground hover:text-primary"
            >
                <ArrowLeft class="mr-2 h-4 w-4" />
                {{ t('listings.show.backToMarketplace') }}
            </InertiaLink>
            <div class="flex items-center space-x-2">
                <Button variant="ghost" size="icon">
                    <Share2 class="h-5 w-5" />
                </Button>
                <Button variant="ghost" size="icon">
                    <Heart class="h-5 w-5" />
                </Button>
            </div>
        </header>

        <main class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <section>
                    <div
                        class="relative aspect-video w-full overflow-hidden rounded-lg bg-muted"
                    >
                        <img
                            src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600"
                            alt="Luxury Apartment"
                            class="h-full w-full object-cover"
                        />
                        <Button
                            variant="secondary"
                            class="absolute right-4 bottom-4 shadow-md"
                        >
                            <Video class="mr-2 h-4 w-4" />
                            {{ t('listings.show.watchVideo') }}
                        </Button>
                        <span
                            class="absolute top-4 left-4 rounded-full bg-primary/80 px-3 py-1 text-xs font-medium text-primary-foreground backdrop-blur-sm"
                        >
                            {{ t('listings.show.investmentTag') }}
                        </span>
                    </div>
                    <div class="mt-2 grid grid-cols-4 gap-2">
                        <div
                            v-for="i in 4"
                            :key="i"
                            class="aspect-video w-full cursor-pointer overflow-hidden rounded-md bg-muted ring-offset-background transition-opacity hover:opacity-80 focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:outline-none"
                            :class="{ 'ring-2 ring-primary': i === 1 }"
                        >
                            <img
                                src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=300"
                                alt="Thumbnail"
                                class="h-full w-full object-cover"
                            />
                        </div>
                    </div>
                </section>

                <section>
                    <div class="flex items-start justify-between">
                        <div>
                            <h1
                                class="text-3xl font-bold tracking-tight text-foreground"
                            >
                                {{ listingTitle }}
                            </h1>
                            <div
                                class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-muted-foreground"
                            >
                                <div class="flex items-center">
                                    <MapPin class="mr-1.5 h-4 w-4" />
                                    {{ listingLocation }}
                                </div>
                                <div class="flex items-center">
                                    <Building class="mr-1.5 h-4 w-4" />
                                    {{ listingCategory }}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-shrink-0 flex-col items-end">
                            <div class="flex items-center space-x-1">
                                <Star
                                    class="h-5 w-5 fill-amber-400 text-amber-400"
                                />
                                <span class="text-lg font-semibold">{{
                                    mockData.rating
                                }}</span>
                            </div>
                            <span class="text-sm text-muted-foreground">
                                {{ mockData.reviews }}
                                {{ t('listings.show.reviews') }}
                            </span>
                        </div>
                    </div>
                    <Separator class="my-6" />
                    <p class="text-base leading-relaxed text-foreground/80">
                        {{ listingDescription }}
                    </p>
                </section>

                <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <Card
                        class="border-blue-200 bg-blue-50 dark:border-accent/30 dark:bg-accent/20"
                    >
                        <CardHeader
                            class="flex flex-row items-center justify-between space-y-0 pb-2"
                        >
                            <CardTitle
                                class="text-sm font-medium text-blue-900 dark:text-accent-foreground"
                            >
                                {{ t('listings.show.stats.roi') }}
                            </CardTitle>
                            <TrendingUp
                                class="h-4 w-4 text-blue-700 dark:text-accent-foreground/70"
                            />
                        </CardHeader>
                        <CardContent>
                            <div
                                class="text-2xl font-bold text-blue-950 dark:text-foreground"
                            >
                                {{ mockData.expectedRoi }}
                            </div>
                        </CardContent>
                    </Card>
                    <Card
                        class="border-green-200 bg-green-50 dark:border-secondary/30 dark:bg-secondary/20"
                    >
                        <CardHeader
                            class="flex flex-row items-center justify-between space-y-0 pb-2"
                        >
                            <CardTitle
                                class="text-sm font-medium text-green-900 dark:text-secondary-foreground"
                            >
                                {{ t('listings.show.stats.duration') }}
                            </CardTitle>
                            <Clock
                                class="h-4 w-4 text-green-700 dark:text-secondary-foreground/70"
                            />
                        </CardHeader>
                        <CardContent>
                            <div
                                class="text-2xl font-bold text-green-950 dark:text-foreground"
                            >
                                {{ mockData.duration }}
                                {{ t('listings.show.stats.months') }}
                            </div>
                        </CardContent>
                    </Card>
                    <Card
                        class="border-pink-200 bg-pink-50 dark:border-destructive/20 dark:bg-destructive/10"
                    >
                        <CardHeader
                            class="flex flex-row items-center justify-between space-y-0 pb-2"
                        >
                            <CardTitle
                                class="text-sm font-medium text-pink-900 dark:text-destructive-foreground/80"
                            >
                                {{ t('listings.show.stats.investors') }}
                            </CardTitle>
                            <Users
                                class="h-4 w-4 text-pink-700 dark:text-destructive-foreground/70"
                            />
                        </CardHeader>
                        <CardContent>
                            <div
                                class="text-2xl font-bold text-pink-950 dark:text-foreground"
                            >
                                {{ mockData.investors }}
                            </div>
                        </CardContent>
                    </Card>
                </section>

                <section>
                    <h3 class="text-lg font-semibold">
                        {{ t('listings.show.capitalRaised') }}
                    </h3>
                    <Progress :model-value="capitalProgress" class="mt-4 h-3" />
                    <div
                        class="mt-3 flex justify-between text-sm font-medium text-muted-foreground"
                    >
                        <span class="font-bold text-foreground">
                            {{
                                mockData.capitalRaised.toLocaleString('de-DE', {
                                    style: 'currency',
                                    currency: 'EUR',
                                })
                            }}
                        </span>
                        <span>
                            /
                            {{
                                mockData.capitalGoal.toLocaleString('de-DE', {
                                    style: 'currency',
                                    currency: 'EUR',
                                })
                            }}
                        </span>
                    </div>
                </section>
            </div>

            <aside class="space-y-6 lg:col-span-1">
                <Card class="sticky top-8 border-border shadow-lg">
                    <CardContent class="space-y-4 p-6">
                        <div class="text-sm font-medium text-muted-foreground">
                            {{ t('listings.show.min') }}
                        </div>
                        <div class="flex items-baseline justify-between">
                            <span class="text-lg font-semibold">
                                {{
                                    minInvestmentAmount.toLocaleString(
                                        'de-DE',
                                        {
                                            style: 'currency',
                                            currency: 'EUR',
                                            maximumFractionDigits: 0,
                                        },
                                    )
                                }}
                            </span>
                            <span class="text-sm text-muted-foreground">
                                {{ t('listings.show.minimum') }}
                            </span>
                        </div>

                        <Separator class="my-2" />

                        <div class="space-y-3">
                            <Label
                                for="investment-slider"
                                class="text-sm font-medium text-muted-foreground"
                            >
                                {{ t('listings.show.your') }}
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
                                t('listings.show.amount')
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
                                t('listings.show.share')
                            }}</span>
                            <span class="font-bold text-foreground"
                                >{{ capitalShare }}%</span
                            >
                        </div>

                        <Button
                            @click="toggleDialog(true)"
                            class="mt-4 w-full bg-primary text-primary-foreground hover:bg-primary/90"
                        >
                            {{ t('listings.show.button.invest') }}
                        </Button>
                        <p
                            class="mt-2 text-center text-xs text-muted-foreground"
                        >
                            {{ t('listings.show.securePayment') }}
                        </p>
                    </CardContent>
                </Card>

                <Card class="border-border shadow-sm">
                    <CardHeader>
                        <CardTitle class="text-lg font-semibold">
                            {{ t('listings.show.listedBy') }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4 p-6 pt-0">
                        <div class="flex items-center space-x-4">
                            <Avatar class="h-12 w-12">
                                <AvatarImage
                                    :src="seller.profilePhoto"
                                    :alt="seller.name"
                                />
                                <AvatarFallback>
                                    {{
                                        seller.name
                                            .substring(0, 2)
                                            .toUpperCase()
                                    }}
                                </AvatarFallback>
                            </Avatar>
                            <div class="space-y-1">
                                <p class="text-base font-semibold">
                                    {{ seller.name }}
                                </p>
                                <div
                                    class="flex items-center text-sm text-muted-foreground"
                                >
                                    <Star
                                        class="mr-1 h-4 w-4 fill-amber-400 text-amber-400"
                                    />
                                    <span>
                                        {{ seller.rating }} ({{
                                            seller.reviews
                                        }}
                                        {{ t('listings.show.reviews') }})
                                    </span>
                                </div>
                            </div>
                        </div>

                        <Separator />

                        <div class="grid grid-cols-2 gap-y-2 text-sm">
                            <p class="text-muted-foreground">
                                {{ t('listings.show.memberSince') }}
                            </p>
                            <p class="text-right font-medium">
                                {{ seller.memberSince }}
                            </p>
                            <p class="text-muted-foreground">
                                {{ t('listings.show.responseTime') }}
                            </p>
                            <p class="text-right font-medium">
                                {{ seller.responseTime }}
                            </p>
                            <p class="text-muted-foreground">
                                {{ t('listings.show.totalListings') }}
                            </p>
                            <p class="text-right font-medium">
                                {{ seller.totalListings }}
                            </p>
                        </div>

                        <Button variant="secondary" class="mt-4 w-full">
                            <Mail class="mr-2 h-4 w-4" />
                            {{ t('listings.show.button.contactSeller') }}
                        </Button>
                    </CardContent>
                </Card>
            </aside>
        </main>

        <Dialog :open="isDialogOpen" @update:open="toggleDialog">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{{
                        t('listings.show.dialog.title')
                    }}</DialogTitle>
                    <DialogDescription>
                        {{
                            t('listings.show.dialog.description', {
                                amount: investmentAmount.toLocaleString(
                                    'de-DE',
                                    {
                                        style: 'currency',
                                        currency: 'EUR',
                                        maximumFractionDigits: 0,
                                    },
                                ),
                            })
                        }}
                    </DialogDescription>
                </DialogHeader>

                <form
                    @submit.prevent="submitInvestment"
                    class="grid gap-4 py-4"
                >
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="amount" class="text-right">
                            {{ t('listings.show.amount') }}
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
                        {{ t('listings.show.dialog.confirmButton') }}
                    </Button>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
