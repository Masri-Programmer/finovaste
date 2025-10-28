<script setup lang="ts">
import { useClipboard } from '@vueuse/core';
import { ref } from 'vue';
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
    Clock,
    ExternalLink,
    Heart,
    MapPin,
    MoreHorizontal,
    Share2,
    Star,
    Users,
} from 'lucide-vue-next';

import Pagination from '@/components/layout/Pagination.vue';
import MarketplaceCategoryFilters from './Filters.vue';
import ListingFaqs from './ListingFaqs.vue';

const { t } = useI18n();
const toast = useToast();
const { copy } = useClipboard();

// --- Enhanced Sample Data with new fields ---
const filters = ref({
    category: 'all',
    search: '',
});

const categories = ref([
    { id: 'properties', i18nKey: 'categories.properties', count: 89 },
    { id: 'vehicles', i18nKey: 'categories.vehicles', count: 45 },
    { id: 'furniture', i18nKey: 'categories.furniture', count: 32 },
    { id: 'electronics', i18nKey: 'categories.electronics', count: 28 },
    { id: 'art', i18nKey: 'categories.art', count: 21 },
    { id: 'businesses', i18nKey: 'categories.businesses', count: 12 },
    { id: 'startups', i18nKey: 'categories.startups', count: 7 },
]);

const listings = ref([
    {
        id: 1,
        titleKey: 'homepage.listings.apartment.title',
        descriptionKey: 'homepage.listings.apartment.description',
        category: 'Immobilien', // 🌐 Used German
        type: 'Investment',
        typeVariant: 'secondary',
        imageUrl: 'https://placehold.co/600x400.png?text=Luxury+Apartment',
        locationKey: 'homepage.locations.sanFrancisco',
        reviews: 34,
        rating: 4.5,
        capitalRaised: 450000,
        capitalGoal: 750000,
        investors: 52,
        minInvestment: 25000,
        // ✅ New Fields
        riskPercentage: 7.5, // Risks %
        campaigns: [
            // Campaigns badges
            {
                type: 'donating',
                variant: 'destructive',
                i18nKey: 'campaigns.spende',
            },
            {
                type: 'project',
                variant: 'primary',
                i18nKey: 'campaigns.projekt',
            },
        ],
        links: [
            // Card Links
            { type: 'website', i18nKey: 'links.website', url: '#' },
            { type: 'pitchdeck', i18nKey: 'links.pitchdeck', url: '#' },
        ],
        faqs: [
            // Card FAQs
            { questionKey: 'faqs.frage1', answerKey: 'faqs.antwort1' },
            { questionKey: 'faqs.frage2', answerKey: 'faqs.antwort2' },
        ],
    },
    {
        id: 2,
        titleKey: 'homepage.listings.carCollection.title',
        descriptionKey: 'homepage.listings.carCollection.description',
        category: 'Fahrzeuge', // 🌐 Used German
        type: 'Auction',
        typeVariant: 'secondary',
        imageUrl: 'https://placehold.co/600x400?text=Car+Collection',
        locationKey: 'homepage.locations.losAngeles',
        reviews: 28,
        rating: 5,
        auctionEndsIn: '2T 14Std', // 🌐 Used German
        startingBid: 150000,
        currentBid: 175000,
        // ✅ New Fields
        riskPercentage: 0, // Not applicable for auction, but keeping the field
        campaigns: [
            {
                type: 'info',
                variant: 'muted',
                i18nKey: 'campaigns.information',
            },
        ],
        links: [{ type: 'details', i18nKey: 'links.details', url: '#' }],
        faqs: [], // No FAQs for auction
    },
]);
// --- End Sample Data ---

// 🔄 Example of triggering Inertia requests and toast messages
function addToWishlist(listingId: number) {
    // router.post(
    //     route('wunschliste.hinzufuegen', { id: listingId }), // 🌐 Used German route
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
    toast.success(t('homepage.notifications.wishlistAdded')); // Mocked
}

// 🛠 Example: Share functionality using VueUse's useClipboard
function shareListing(listingId: number) {
    const listingUrl = route('marketplace.show', { id: listingId }); // 🔄 Laravel Typed Wayfinder
    copy(listingUrl)
        .then(() => {
            toast.info(t('homepage.notifications.linkCopied')); // 🔔 Info toast
        })
        .catch(() => {
            toast.error(t('homepage.notifications.copyFailed'));
        });
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
                    :key="listing.id"
                    class="flex flex-col overflow-hidden"
                >
                    <CardHeader class="relative p-0">
                        <img
                            :src="listing.imageUrl"
                            :alt="t(listing.titleKey)"
                            class="h-48 w-full object-cover"
                        />
                        <div class="absolute top-4 left-4 flex gap-2">
                            <Badge
                                :variant="listing.typeVariant"
                                class="text-xs"
                            >
                                {{ listing.type }}
                            </Badge>
                            <Badge
                                v-for="(campaign, index) in listing.campaigns"
                                :key="index"
                                :variant="campaign.variant"
                                class="text-xs"
                            >
                                {{ t(`homepage.${campaign.i18nKey}`) }}
                            </Badge>
                        </div>

                        <div class="absolute top-4 right-4 flex gap-2">
                            <Button
                                variant="secondary"
                                size="icon"
                                class="rounded-full bg-background/70 backdrop-blur-sm"
                                @click="shareListing(listing.id)"
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
                                @click="addToWishlist(listing.id)"
                            >
                                <Heart class="h-5 w-5" />
                            </Button>
                        </div>
                    </CardHeader>

                    <CardContent class="flex-grow pt-6">
                        <span class="text-sm font-medium text-primary">{{
                            listing.category
                        }}</span>
                        <CardTitle class="mt-1 mb-2 text-xl font-bold">
                            {{ t(listing.titleKey) }}
                        </CardTitle>
                        <CardDescription
                            class="line-clamp-2 text-muted-foreground"
                        >
                            {{ t(listing.descriptionKey) }}
                        </CardDescription>

                        <div
                            class="mt-4 flex flex-col items-start gap-2 text-sm text-muted-foreground sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="flex items-center gap-1">
                                <MapPin class="h-4 w-4" />
                                <span>{{ t(listing.locationKey) }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <Star class="h-4 w-4 text-yellow-500" />
                                <span
                                    >{{ listing.rating }} ({{
                                        listing.reviews
                                    }}
                                    {{ t('homepage.reviews.label') }})</span
                                >
                            </div>
                        </div>

                        <div
                            v-if="listing.type === 'Investment'"
                            class="mt-4 space-y-3"
                        >
                            <div
                                class="flex justify-between text-sm font-medium"
                            >
                                <span class="text-muted-foreground"
                                    >{{ t('homepage.listings.risk') }}:</span
                                >
                                <span
                                    class="font-bold"
                                    :class="{
                                        'text-destructive':
                                            listing.riskPercentage > 5,
                                        'text-foreground':
                                            listing.riskPercentage <= 5,
                                    }"
                                >
                                    {{ listing.riskPercentage.toFixed(1) }}%
                                </span>
                            </div>

                            <div>
                                <div
                                    class="mb-1 flex justify-between text-sm font-medium"
                                >
                                    <span>{{
                                        t('homepage.listings.capitalRaised')
                                    }}</span>
                                    <span class="text-foreground"
                                        >${{
                                            listing.capitalRaised.toLocaleString(
                                                'de-DE',
                                            )
                                        }}
                                        / ${{
                                            listing.capitalGoal.toLocaleString(
                                                'de-DE',
                                            )
                                        }}</span
                                    >
                                </div>
                                <Progress
                                    :model-value="
                                        (listing.capitalRaised /
                                            listing.capitalGoal) *
                                        100
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
                                    {{ listing.investors }}
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
                                    <span class="font-bold text-foreground"
                                        >${{
                                            listing.minInvestment.toLocaleString(
                                                'de-DE',
                                            )
                                        }}</span
                                    >
                                </div>
                            </div>

                            <ListingFaqs :faqs="listing.faqs" />
                        </div>

                        <div
                            v-if="listing.type === 'Auction'"
                            class="mt-4 space-y-3"
                        >
                            <div
                                class="flex items-center justify-between rounded-lg bg-secondary p-3 text-sm"
                            >
                                <span
                                    class="flex items-center gap-1 text-secondary-foreground"
                                >
                                    <Clock class="h-4 w-4" />
                                    {{ t('homepage.listings.auctionEnds') }}
                                </span>
                                <span
                                    class="font-bold text-secondary-foreground"
                                    >{{ listing.auctionEndsIn }}</span
                                >
                            </div>
                            <div
                                class="flex items-baseline justify-between text-sm"
                            >
                                <span class="text-muted-foreground"
                                    >{{
                                        t('homepage.listings.startingBid')
                                    }}:</span
                                >
                                <span class="font-medium text-foreground"
                                    >${{
                                        listing.startingBid.toLocaleString(
                                            'de-DE',
                                        )
                                    }}</span
                                >
                            </div>
                            <div
                                class="flex items-baseline justify-between text-sm"
                            >
                                <span class="text-muted-foreground"
                                    >{{
                                        t('homepage.listings.currentBid')
                                    }}:</span
                                >
                                <span class="text-lg font-bold text-primary"
                                    >${{
                                        listing.currentBid.toLocaleString(
                                            'de-DE',
                                        )
                                    }}</span
                                >
                            </div>
                        </div>
                    </CardContent>

                    <CardFooter class="flex flex-col gap-3">
                        <Button
                            v-if="listing.type === 'Investment'"
                            class="w-full"
                        >
                            {{ t('homepage.listings.investNow') }}
                        </Button>
                        <Button
                            v-if="listing.type === 'Auction'"
                            class="w-full"
                            variant="outline"
                        >
                            {{ t('homepage.listings.placeBid') }}
                        </Button>

                        <div
                            class="flex w-full justify-between gap-2 border-t border-border pt-3"
                        >
                            <Button
                                v-for="(link, index) in listing.links"
                                :key="index"
                                :variant="'ghost'"
                                size="sm"
                                class="w-full justify-center text-xs"
                                as-child
                            >
                                <a :href="link.url" target="_blank">
                                    {{ t(`homepage.${link.i18nKey}`) }}
                                    <ExternalLink class="ml-1 h-3 w-3" />
                                </a>
                            </Button>
                        </div>
                    </CardFooter>
                </Card>
            </div>
        </section>

        <Pagination />
    </div>
</template>
