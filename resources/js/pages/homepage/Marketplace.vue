<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { useDark, useToggle } from '@vueuse/core';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toastification';

// Import components from shadcn/ui
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

// Import icons
import { Clock, Heart, MapPin, Star, Users } from 'lucide-vue-next';

// Import the custom category filter component
import MarketplaceCategoryFilters from './Filters.vue';

// --- Initialize composables ---
const { t } = useI18n();
const toast = useToast();

// --- VueUse: Dark Mode Toggle ---
const isDark = useDark();
const toggleDark = useToggle(isDark);

// --- Sample Data ---
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
        category: 'Properties',
        type: 'Investment',
        typeVariant: 'accent',
        imageUrl: 'https://placehold.co/600x400.png?text=Luxury+Apartment',
        locationKey: 'homepage.locations.sanFrancisco',
        reviews: 34,
        rating: 4.5,
        capitalRaised: 450000,
        capitalGoal: 750000,
        investors: 52,
        minInvestment: 25000,
    },
    {
        id: 2,
        titleKey: 'homepage.listings.carCollection.title',
        descriptionKey: 'homepage.listings.carCollection.description',
        category: 'Vehicles',
        type: 'Auction',
        typeVariant: 'secondary',
        imageUrl: 'https://placehold.co/600x400?text=Car+Collection',
        locationKey: 'homepage.locations.losAngeles',
        reviews: 28,
        rating: 5,
        auctionEndsIn: '2d 14h',
        startingBid: 150000,
        currentBid: 175000,
    },
]);
// --- End Sample Data ---

function addToWishlist(listingId: number) {
    router.post(
        route('wishlist.add', { id: listingId }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success(t('homepage.notifications.wishlistAdded'));
            },
            onError: (errors) => {
                const errorMsg =
                    errors.message || t('homepage.notifications.errorGeneric');
                toast.error(errorMsg);
            },
        },
    );
}
</script>

<template>
    <div class="min-h-screen bg-background p-4 text-foreground md:p-8">
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

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
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
                        <Badge
                            :variant="listing.typeVariant"
                            class="absolute top-4 left-4"
                        >
                            {{ listing.type }}
                        </Badge>
                        <Button
                            variant="secondary"
                            size="icon"
                            class="absolute top-4 right-4 rounded-full bg-background/70 backdrop-blur-sm"
                            @click="addToWishlist(listing.id)"
                        >
                            <Heart class="h-5 w-5" />
                        </Button>
                    </CardHeader>

                    <CardContent class="flex-grow pt-6">
                        <span class="text-sm font-medium text-primary">{{
                            listing.category
                        }}</span>
                        <CardTitle class="mt-1 mb-2 text-lg font-bold">
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
                            <div>
                                <div
                                    class="mb-1 flex justify-between text-sm font-medium"
                                >
                                    <span>{{
                                        t('homepage.listings.capitalRaised')
                                    }}</span>
                                    <span class="text-foreground"
                                        >${{
                                            listing.capitalRaised.toLocaleString()
                                        }}
                                        / ${{
                                            listing.capitalGoal.toLocaleString()
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
                                            listing.minInvestment.toLocaleString()
                                        }}</span
                                    >
                                </div>
                            </div>
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
                                        listing.startingBid.toLocaleString()
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
                                        listing.currentBid.toLocaleString()
                                    }}</span
                                >
                            </div>
                        </div>
                    </CardContent>

                    <CardFooter>
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
                    </CardFooter>
                </Card>
            </div>
        </section>
    </div>
</template>
