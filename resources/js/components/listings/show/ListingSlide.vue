<template>
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
                {{ $t('listings_show.watchVideo') }}
            </Button>
            <span
                class="absolute top-4 left-4 rounded-full bg-primary/80 px-3 py-1 text-xs font-medium text-primary-foreground backdrop-blur-sm"
            >
                {{ $t('listings_show.investmentTag') }}
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
    <section class="mt-8">
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
                    <Star class="h-5 w-5 text-yellow-400" fill="currentColor" />
                    <span class="text-lg font-bold">{{
                        listing.average_rating
                    }}</span>
                    <span class="text-base text-muted-foreground">
                        ({{ listing.reviews_count }}
                        {{ $t('listing.listing_details.reviews') }})
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
</template>

<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { LocaleString } from '@/types';
import { Listing } from '@/types/listings';
import { getActiveLanguage } from 'laravel-vue-i18n';
import { MapPin, Star } from 'lucide-vue-next';
import { computed, ref } from 'vue';
const props = defineProps<{
    listing: Listing;
}>();
const locale = ref(getActiveLanguage());
const getLocalizedString = (field: LocaleString) => {
    return field[locale.value] || field.en;
};

const localizedTitle = computed(() => getLocalizedString(props.listing.title));
const localizedDescription = computed(() =>
    getLocalizedString(props.listing.description),
);
const localizedCategoryName = computed(() =>
    getLocalizedString(props.listing.category.name),
);
</script>

<style scoped></style>
