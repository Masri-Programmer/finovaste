<template>
    <div class="container mx-auto py-6">
        <section>
            <ListingMedia :media="listing.media">
                <template #tags>
                    <span
                        class="rounded-full bg-primary/80 px-3 py-1 text-xs font-medium text-primary-foreground backdrop-blur-sm"
                    >
                        {{ $t('listings.investment_tag') }}
                    </span>
                </template>
            </ListingMedia>
        </section>

        <section class="mt-8 grid gap-8 md:grid-cols-3">
            <div class="md:col-span-2">
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
                                {{ $t('listings.reviews') }})
                            </span>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-2 flex items-center space-x-4 text-muted-foreground"
                >
                    <div class="flex items-center space-x-1">
                        <MapPin class="h-4 w-4" />
                        <span>{{
                            listing.address?.city || 'Unknown Location'
                        }}</span>
                    </div>
                    <span>•</span>
                    <span>{{ localizedCategoryName }}</span>
                </div>

                <p
                    class="mt-4 text-base whitespace-pre-line text-foreground/80"
                >
                    {{ localizedDescription }}
                </p>
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
import { LocaleString } from '@/types';
import { Listing } from '@/types/listings';
import { getActiveLanguage } from 'laravel-vue-i18n';
import { MapPin, Star } from 'lucide-vue-next';
import { computed, ref } from 'vue';

import ListingMedia from './ListingMedia.vue';

const props = defineProps<{
    listing: Listing;
}>();

const locale = ref(getActiveLanguage());
const getLocalizedString = (field: LocaleString) => {
    return field ? field[locale.value] || field.en : '';
};

const localizedTitle = computed(() => getLocalizedString(props.listing.title));
const localizedDescription = computed(() =>
    getLocalizedString(props.listing.description),
);
const localizedCategoryName = computed(() =>
    getLocalizedString(props.listing.category?.name),
);
</script>
