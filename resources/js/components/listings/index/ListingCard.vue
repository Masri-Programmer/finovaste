<script setup lang="ts">
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
import { show } from '@/routes/listings';
import { Listing } from '@/types/listings';
import { Link } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import {
    BadgeDollarSign,
    CircleDollarSign,
    ExternalLink,
    Gavel,
    HandHeart,
    Heart,
    MapPin,
    MoreHorizontal,
    Share2,
    Star,
} from 'lucide-vue-next';
import { computed, PropType } from 'vue';
import { useToast } from 'vue-toastification';

import ListingAuctionContent from '@/components/listings/index/ListingAuctionContent.vue';
import ListingBuyNowContent from '@/components/listings/index/ListingBuyNowContent.vue';
import ListingDonationContent from '@/components/listings/index/ListingDonationContent.vue';
import ListingInvestmentContent from '@/components/listings/index/ListingInvestmentContent.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    listing: {
        type: Object as PropType<Listing>,
        required: true,
    },
    locale: {
        type: String,
        required: true,
    },
});

const toast = useToast();
const { copy } = useClipboard();

function getListingType(type: Listing['listable_type'] | string): {
    text: string;
    variant: 'secondary' | 'outline' | 'destructive' | 'default' | 'ghost';
} {
    switch (type) {
        case 'App\\Models\\InvestmentListing':
            return {
                text: trans('listingTypes.investment'),
                variant: 'default',
            };
        case 'App\\Models\\AuctionListing':
            return {
                text: trans('listingTypes.auction'),
                variant: 'secondary',
            };
        case 'App\\Models\\BuyNowListing':
            return {
                text: trans('listingTypes.buyNow'),
                variant: 'default',
            };
        case 'App\\Models\\DonationListing':
            return {
                text: trans('listingTypes.donation'),
                variant: 'destructive',
            };
        default:
            return { text: 'Unknown', variant: 'outline' };
    }
}

// --- Component Actions ---

function addToWishlist(uuid: string) {
    // 🔄 Inertia POST request
    // router.post(
    // route('wishlist.add', { uuid }), // 🔄 Using Typed Wayfinder
    // {},
    // {
    // preserveScroll: true,
    // onSuccess: () => {
    // toast.success(t('notifications.wishlistAdded')); // 🔔 Success toast
    // },
    // onError: (errors) => {
    // const errorMsg =
    // errors.message || t('notifications.errorGeneric');
    // toast.error(errorMsg); // 🔔 Error toast
    // },
    // },
    // );
    // toast.success(t('notifications.wishlistAdded')); // Mocked
}

// 🛠 Using VueUse's useClipboard

function shareListing(uuid: string) {
    // const listingUrl = route('marketplace.show', { uuid }); // 🔄 Laravel Typed Wayfinder
    // copy(listingUrl)
    // .then(() => {
    // toast.info(t('notifications.linkCopied')); // 🔔 Info toast
    // })
    // .catch(() => {
    // toast.error(t('notifications.copyFailed'));
    // });
}
// Computed property to check listable validity (optional but good)
const listable = computed(() => {
    if (
        typeof props.listing.listable === 'object' &&
        props.listing.listable !== null
    ) {
        return props.listing.listable;
    }
    return null;
});
</script>

<template>
    <Card>
        <Link
            :href="show.url(listing.id)"
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
                        :variant="getListingType(listing.listable_type).variant"
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
                        @click.prevent="shareListing(listing.uuid)"
                    >
                        <Share2 class="h-5 w-5" />
                    </Button>
                    <Button
                        variant="secondary"
                        size="icon"
                        class="rounded-full bg-background/70 backdrop-blur-sm"
                        @click.prevent
                    >
                        <MoreHorizontal class="h-5 w-5" />
                    </Button>
                    <Button
                        variant="secondary"
                        size="icon"
                        class="rounded-full bg-background/70 backdrop-blur-sm"
                        @click.prevent="addToWishlist(listing.uuid)"
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
                <CardDescription class="line-clamp-2 text-muted-foreground">
                    {{ listing.description[locale] }}
                </CardDescription>
                <div
                    class="mt-4 flex flex-col items-start gap-2 text-sm text-muted-foreground sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex items-center gap-1">
                        <MapPin class="h-4 w-4" />
                        <span>{{ $t('locations.generic') }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <Star class="h-4 w-4 text-yellow-500" />
                        <span
                            >{{ listing.average_rating }} ({{
                                listing.reviews_count
                            }}
                            {{ $t('reviews.label') }})</span
                        >
                    </div>
                </div>

                <ListingInvestmentContent
                    v-if="
                        listing.listable_type ===
                            'App\\Models\\InvestmentListing' && listable
                    "
                    :listable="listable"
                />
                <ListingAuctionContent
                    v-if="
                        listing.listable_type ===
                            'App\\Models\\AuctionListing' && listable
                    "
                    :listable="listable"
                />
                <ListingBuyNowContent
                    v-if="
                        listing.listable_type ===
                            'App\\Models\\BuyNowListing' && listable
                    "
                    :listable="listable"
                />
                <ListingDonationContent
                    v-if="
                        listing.listable_type ===
                            'App\\Models\\DonationListing' && listable
                    "
                    :listable="listable"
                />
            </CardContent>

            <CardFooter class="flex flex-col gap-3">
                <Button
                    v-if="
                        listing.listable_type ===
                        'App\\Models\\InvestmentListing'
                    "
                    class="w-full"
                    @click.prevent
                >
                    <CircleDollarSign class="mr-2 h-4 w-4" />
                    {{ $t('listings.investNow') }}
                </Button>
                <Button
                    v-if="
                        listing.listable_type === 'App\\Models\\AuctionListing'
                    "
                    class="w-full"
                    variant="outline"
                    @click.prevent
                >
                    <Gavel class="mr-2 h-4 w-4" />
                    {{ $t('listings.placeBid') }}
                </Button>
                <Button
                    v-if="
                        listing.listable_type === 'App\\Models\\BuyNowListing'
                    "
                    class="w-full"
                    @click.prevent
                >
                    <BadgeDollarSign class="mr-2 h-4 w-4" />
                    {{ $t('listings.buyNow') }}
                </Button>
                <Button
                    v-if="
                        listing.listable_type === 'App\\Models\\DonationListing'
                    "
                    class="w-full"
                    variant="destructive"
                    @click.prevent
                >
                    <HandHeart class="mr-2 h-4 w-4" />
                    {{ $t('listings.donateNow') }}
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
                        <a href="#" target="_blank" @click.stop>
                            {{ $t('links.details') }}
                            <ExternalLink class="ml-1 h-3 w-3" />
                        </a>
                    </Button>
                </div>
            </CardFooter>
        </Link>
    </Card>
</template>
