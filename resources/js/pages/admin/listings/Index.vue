<script setup lang="ts">
import ResourceIndex from '@/components/resource/Index.vue';
import { Badge } from '@/components/ui/badge';
import { destroy, show } from '@/routes/admin/listings';
import { create, edit } from '@/routes/listings';
import { computed, ref } from 'vue';

interface TranslatableString {
    en: string;
    de: string;
}

interface Category {
    name: TranslatableString;
}

interface Listing {
    id: number;
    title: TranslatableString;
    category: Category;
    listable_type: string;
    listable: any;
    average_rating: number;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
    listings: {
        data: Listing[];
        links: PaginationLink[];
    };
}>();

const listingSearch = ref('');

const columns = [
    {
        key: 'title.de',
        label: 'Title',
        class: 'font-medium max-w-xs truncate',
    },
    {
        key: 'category.name.de',
        label: 'Category',
    },
    {
        key: 'type',
        label: 'Type',
    },
    {
        key: 'price',
        label: 'Price',
    },
    {
        key: 'rating',
        label: 'Rating',
    },
];

const formatListableType = (type: string) => {
    if (!type) return 'N/A';
    const parts = type.split('\\');
    const modelName = parts.pop() || '';
    return modelName
        .replace('Listing', '')
        .replace(/([A-Z])/g, ' $1')
        .trim();
};

const getListablePrice = (listing: Listing) => {
    const formatter = new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: 'EUR',
    });
    let price: number | string | null = null;

    if (!listing.listable) return 'N/A';

    switch (listing.listable_type) {
        case 'App\\Models\\InvestmentListing':
            price = listing.listable.investment_goal;
            break;
        case 'App\\Models\\BuyNowListing':
            price = listing.listable.price;
            break;
        case 'App\\Models\\AuctionListing':
            price = listing.listable.start_price;
            break;
        case 'App\\Models\\DonationListing':
            price = listing.listable.donation_goal;
            break;
        default:
            return 'N/A';
    }

    return formatter.format(Number(price));
};

const filteredListings = computed(() =>
    props.listings.data.filter(
        (listing) =>
            listing.title.de
                .toLowerCase()
                .includes(listingSearch.value.toLowerCase()) ||
            listing.title.en
                .toLowerCase()
                .includes(listingSearch.value.toLowerCase()) ||
            listing.category.name.de
                .toLowerCase()
                .includes(listingSearch.value.toLowerCase()) ||
            listing.category.name.en
                .toLowerCase()
                .includes(listingSearch.value.toLowerCase()),
    ),
);
</script>

<template>
    <ResourceIndex
        resource="listings"
        :columns="columns"
        :items="filteredListings"
        :pagination-links="props.listings.links"
        v-model="listingSearch"
        :create-route="create.url()"
        :view-route="(listing) => show.url(listing.id)"
        :edit-route="(listing) => edit.url(listing.id)"
        :delete-route="(listing) => destroy.url(listing.id)"
    >
        <template #cell-type="{ item }">
            <Badge variant="outline">
                {{ formatListableType(item.listable_type) }}
            </Badge>
        </template>

        <template #cell-price="{ item }">
            {{ getListablePrice(item) }}
        </template>

        <template #cell-rating="{ item }">
            <span
                v-if="item.average_rating > 0"
                class="flex items-center gap-1"
            >
                ⭐
                {{ item.average_rating.toFixed(1) }}
            </span>
            <span v-else class="text-muted-foreground">-</span>
        </template>
    </ResourceIndex>
</template>
