<script setup lang="ts">
import { useClipboard } from '@vueuse/core';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toastification';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';

import {
    BadgeDollarSign,
    CalendarClock,
    CircleDollarSign,
    ExternalLink,
    Gavel,
    HandHeart,
    Heart,
    MapPin,
    MoreHorizontal,
    Package,
    Share2,
    Star,
    Target,
    Users,
} from 'lucide-vue-next';

import Pagination from '@/components/layout/Pagination.vue';
import { AppPageProps, Listing } from '@/types';
import MarketplaceCategoryFilters from './Filters.vue';

import { usePage } from '@inertiajs/vue3';

const { t, locale } = useI18n();
const toast = useToast();
const { copy } = useClipboard();
const page = usePage<AppPageProps>();

const listingsResponse = computed(() => page.props.listings);
const listings = computed(() => listingsResponse.value.data);
const categories = computed(() => page.props.categories);

const filters = ref({
    category: 'all',
    search: '',
});

function getListingType(type: Listing['listable_type'] | string): {
    text: string;
    variant: 'secondary' | 'outline' | 'destructive' | 'default' | 'ghost';
} {
    switch (type) {
        case 'App\\Models\\InvestmentListing':
            return {
                text: t('homepage.listingTypes.investment'),
                variant: 'default',
            };
        case 'App\\Models\\AuctionListing':
            return {
                text: t('homepage.listingTypes.auction'),
                variant: 'secondary',
            };
        case 'App\\Models\\BuyNowListing':
            return {
                text: t('homepage.listingTypes.buyNow'),
                variant: 'default',
            };
        case 'App\\Models\\DonationListing':
            return {
                text: t('homepage.listingTypes.donation'),
                variant: 'destructive',
            };
        default:
            return { text: 'Unknown', variant: 'outline' };
    }
}

/**
 * Formats a number as German currency (USD as per example)
 */
function formatCurrency(value: string | number | null): string {
    return new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: 'USD', // Using $ as per original component
        maximumFractionDigits: 0,
    }).format(Number(value || 0));
}

/**
 * Calculates progress percentage
 */
function getProgress(
    raised: string | number | null,
    goal: string | number | null,
): number {
    const numRaised = Number(raised || 0);
    const numGoal = Number(goal || 1); // Avoid division by zero
    if (numGoal === 0) return 0;
    return (numRaised / numGoal) * 100;
}

// --- Component Actions ---

function addToWishlist(uuid: string) {
    // 🔄 Inertia POST request
    // router.post(
    //     route('wishlist.add', { uuid }), // 🔄 Using Typed Wayfinder
    //     {},
    //     {
    //         preserveScroll: true,
    //         onSuccess: () => {
    //             toast.success(t('homepage.notifications.wishlistAdded')); // 🔔 Success toast
    //         },
    //         onError: (errors) => {
    //             const errorMsg =
    //                 errors.message || t('homepage.notifications.errorGeneric');
    //             toast.error(errorMsg); // 🔔 Error toast
    //         },
    //     },
    // );
    // toast.success(t('homepage.notifications.wishlistAdded')); // Mocked
}

// 🛠 Using VueUse's useClipboard
function shareListing(uuid: string) {
    // const listingUrl = route('marketplace.show', { uuid }); // 🔄 Laravel Typed Wayfinder
    // copy(listingUrl)
    //     .then(() => {
    //         toast.info(t('homepage.notifications.linkCopied')); // 🔔 Info toast
    //     })
    //     .catch(() => {
    //         toast.error(t('homepage.notifications.copyFailed'));
    //     });
}
</script>

<template>
    <div class="min-h-screen bg-background text-foreground">
        <header class="mb-4 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-foreground">
                {{ t('homepage.marketplace.title') }}
            </h1>
        </header>

        <MarketplaceCategoryFilters
            :categories="categories"
            :current-filters="filters"
        />

        <section class="mt-6">
            <h2 class="mb-4 text-2xl font-semibold">
                {{ t('homepage.marketplace.featuredListings') }}
            </h2>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-2">
                <Card
                    v-for="listing in listings"
                    :key="listing.uuid"
                    class="flex flex-col overflow-hidden"
                >
                    <CardHeader class="relative p-0">
                        <img
                            :src="
                                listing.image_url ||
                                'https://placehold.co/600x400.png?text=Listing+Image'
                            "
                            :alt="listing.title[locale]"
                            class="h-48 w-full object-cover"
                        />
                        <div class="absolute top-4 left-4 flex gap-2">
                            <Badge
                                :variant="
                                    getListingType(listing.listable_type)
                                        .variant
                                "
                                class="text-xs"
                            >
                                {{ getListingType(listing.listable_type).text }}
                            </Badge>
                        </div>

                        <div class="absolute top-4 right-4 flex gap-2">
                            <Button
                                variant="secondary"
                                size="icon"
                                class="rounded-full bg-background/70 backdrop-blur-sm"
                                @click="shareListing(listing.uuid)"
                            >
                                <Share2 class="h-5 w-5" />
                            </Button>
                            <Button
                                variant="secondary"
                                size="icon"
                                class="rounded-full bg-background/70 backdrop-blur-sm"
                            >
                                <MoreHorizontal class="h-5 w-5" />
                            </Button>
                            <Button
                                variant="secondary"
                                size="icon"
                                class="rounded-full bg-background/70 backdrop-blur-sm"
                                @click="addToWishlist(listing.uuid)"
                            >
                                <Heart class="h-5 w-5" />
                            </Button>
                        </div>
                    </CardHeader>

                    <CardContent class="flex-grow pt-6">
                        <span class="text-sm font-medium text-primary">{{
                            listing.category.name[locale]
                        }}</span>
                        <CardTitle class="mt-1 mb-2 text-xl font-bold">
                            {{ listing.title[locale] }}
                        </CardTitle>
                        <CardDescription
                            class="line-clamp-2 text-muted-foreground"
                        >
                            {{ listing.description[locale] }}
                        </CardDescription>

                        <div
                            class="mt-4 flex flex-col items-start gap-2 text-sm text-muted-foreground sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="flex items-center gap-1">
                                <MapPin class="h-4 w-4" />
                                <span>{{
                                    t('homepage.locations.generic')
                                }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <Star class="h-4 w-4 text-yellow-500" />
                                <span
                                    >{{ listing.average_rating }} ({{
                                        listing.reviews_count
                                    }}
                                    {{ t('homepage.reviews.label') }})</span
                                >
                            </div>
                        </div>

                        <div
                            v-if="
                                listing.listable_type ===
                                    'App\\Models\\InvestmentListing' &&
                                typeof listing.listable === 'object' &&
                                'investment_goal' in listing.listable
                            "
                            class="mt-4 space-y-3"
                        >
                            <div>
                                <div
                                    class="mb-1 flex justify-between text-sm font-medium"
                                >
                                    <span>{{
                                        t('homepage.listings.capitalRaised')
                                    }}</span>
                                    <span class="text-foreground">
                                        {{
                                            formatCurrency(
                                                listing.listable.amount_raised,
                                            )
                                        }}
                                        /
                                        {{
                                            formatCurrency(
                                                listing.listable
                                                    .investment_goal,
                                            )
                                        }}
                                    </span>
                                </div>
                                <Progress
                                    :model-value="
                                        getProgress(
                                            listing.listable.amount_raised,
                                            listing.listable.investment_goal,
                                        )
                                    "
                                />
                            </div>
                            <div
                                class="flex flex-col items-start gap-2 text-sm sm:flex-row sm:items-center sm:justify-between"
                            >
                                <span
                                    class="flex items-center gap-1 text-muted-foreground"
                                >
                                    <Users class="h-4 w-4" />
                                    {{ listing.listable.investors_count }}
                                    {{ t('homepage.listings.investors') }}
                                </span>
                                <div>
                                    <span class="text-muted-foreground"
                                        >{{
                                            t(
                                                'homepage.listings.minInvestment',
                                            )
                                        }}:
                                    </span>
                                    <span class="font-bold text-foreground">
                                        {{
                                            formatCurrency(
                                                listing.listable
                                                    .minimum_investment,
                                            )
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="
                                listing.listable_type ===
                                    'App\\Models\\AuctionListing' &&
                                typeof listing.listable === 'object' &&
                                'ends_at' in listing.listable
                            "
                            class="mt-4 space-y-3"
                        >
                            <div
                                class="flex items-center justify-between rounded-lg bg-secondary p-3 text-sm"
                            >
                                <span
                                    class="flex items-center gap-1 text-secondary-foreground"
                                >
                                    <CalendarClock class="h-4 w-4" />
                                    {{ t('homepage.listings.auctionEnds') }}
                                </span>
                                <span
                                    class="font-bold text-secondary-foreground"
                                >
                                    {{
                                        new Date(
                                            listing.listable.ends_at,
                                        ).toLocaleString('de-DE')
                                    }}
                                </span>
                            </div>
                            <div
                                class="flex items-baseline justify-between text-sm"
                            >
                                <span class="text-muted-foreground"
                                    >{{
                                        t('homepage.listings.startingBid')
                                    }}:</span
                                >
                                <span class="font-medium text-foreground">
                                    {{
                                        formatCurrency(
                                            listing.listable.start_price,
                                        )
                                    }}
                                </span>
                            </div>
                            <div
                                class="flex items-baseline justify-between text-sm"
                            >
                                <span class="text-muted-foreground">{{
                                    t('homepage.listings.currentBid')
                                }}</span>
                                <span class="text-lg font-bold text-primary">
                                    {{
                                        formatCurrency(
                                            listing.listable.current_bid ||
                                                listing.listable.start_price,
                                        )
                                    }}
                                </span>
                            </div>
                        </div>

                        <div
                            v-if="
                                listing.listable_type ===
                                    'App\\Models\\BuyNowListing' &&
                                typeof listing.listable === 'object' &&
                                'price' in listing.listable
                            "
                            class="mt-4 space-y-3"
                        >
                            <div
                                class="flex items-baseline justify-between rounded-lg bg-secondary p-3 text-sm"
                            >
                                <span
                                    class="flex items-center gap-1 text-secondary-foreground"
                                >
                                    <CircleDollarSign class="h-4 w-4" />
                                    {{ t('homepage.listings.price') }}
                                </span>
                                <span
                                    class="text-lg font-bold text-secondary-foreground"
                                >
                                    {{ formatCurrency(listing.listable.price) }}
                                </span>
                            </div>
                            <div
                                class="flex items-center justify-between text-sm"
                            >
                                <span
                                    class="flex items-center gap-1 text-muted-foreground"
                                >
                                    <Package class="h-4 w-4" />
                                    {{ t('homepage.listings.quantity') }}:
                                </span>
                                <span class="font-medium text-foreground">
                                    {{ listing.listable.quantity }}
                                </span>
                            </div>
                        </div>

                        <div
                            v-if="
                                listing.listable_type ===
                                    'App\\Models\\DonationListing' &&
                                typeof listing.listable === 'object' &&
                                'donation_goal' in listing.listable
                            "
                            class="mt-4 space-y-3"
                        >
                            <div>
                                <div
                                    class="mb-1 flex justify-between text-sm font-medium"
                                >
                                    <span>{{
                                        t('homepage.listings.donationsRaised')
                                    }}</span>
                                    <span class="text-foreground">
                                        {{
                                            formatCurrency(
                                                listing.listable.amount_raised,
                                            )
                                        }}
                                        /
                                        {{
                                            formatCurrency(
                                                listing.listable.donation_goal,
                                            )
                                        }}
                                    </span>
                                </div>
                                <Progress
                                    :model-value="
                                        getProgress(
                                            listing.listable.amount_raised,
                                            listing.listable.donation_goal,
                                        )
                                    "
                                    class="[&>*]:bg-destructive"
                                />
                            </div>
                            <div
                                class="flex items-center justify-between text-sm"
                            >
                                <span
                                    class="flex items-center gap-1 text-muted-foreground"
                                >
                                    <Users class="h-4 w-4" />
                                    {{ listing.listable.donors_count }}
                                    {{ t('homepage.listings.donors') }}
                                </span>
                                <span
                                    v-if="listing.listable.is_goal_flexible"
                                    class="flex items-center gap-1 text-muted-foreground"
                                >
                                    <Target class="h-4 w-4" />
                                    {{ t('homepage.listings.flexibleGoal') }}
                                </span>
                            </div>
                        </div>
                    </CardContent>

                    <CardFooter class="flex flex-col gap-3">
                        <Button
                            v-if="
                                listing.listable_type ===
                                'App\\Models\\InvestmentListing'
                            "
                            class="w-full"
                        >
                            <CircleDollarSign class="mr-2 h-4 w-4" />
                            {{ t('homepage.listings.investNow') }}
                        </Button>
                        <Button
                            v-if="
                                listing.listable_type ===
                                'App\\Models\\AuctionListing'
                            "
                            class="w-full"
                            variant="outline"
                        >
                            <Gavel class="mr-2 h-4 w-4" />
                            {{ t('homepage.listings.placeBid') }}
                        </Button>
                        <Button
                            v-if="
                                listing.listable_type ===
                                'App\\Models\\BuyNowListing'
                            "
                            class="w-full"
                        >
                            <BadgeDollarSign class="mr-2 h-4 w-4" />
                            {{ t('homepage.listings.buyNow') }}
                        </Button>
                        <Button
                            v-if="
                                listing.listable_type ===
                                'App\\Models\\DonationListing'
                            "
                            class="w-full"
                            variant="destructive"
                        >
                            <HandHeart class="mr-2 h-4 w-4" />
                            {{ t('homepage.listings.donateNow') }}
                        </Button>

                        <div
                            class="flex w-full justify-between gap-2 border-t border-border pt-3"
                        >
                            <Button
                                :variant="'ghost'"
                                size="sm"
                                class="w-full justify-center text-xs"
                                as-child
                            >
                                <a href="#" target="_blank">
                                    {{ t('homepage.links.details') }}
                                    <ExternalLink class="ml-1 h-3 w-3" />
                                </a>
                            </Button>
                        </div>
                    </CardFooter>
                </Card>
            </div>
        </section>

        <Pagination :links="listingsResponse.links" class="mt-8" />
    </div>
</template>
