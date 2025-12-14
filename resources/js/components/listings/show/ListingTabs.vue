<template>
    <div class="w-full">
        <Tabs default-value="reviews" class="w-full rounded-lg border bg-card">
            <TabsList
                class="grid h-auto w-full grid-cols-3 rounded-none border-b p-0"
            >
                <TabsTrigger value="reviews" class="rounded-none py-3">
                    {{ $t('reviews.tabs.reviews') }} ({{ localReviews.length }})
                </TabsTrigger>

                <TabsTrigger value="documents" class="rounded-none py-3">
                    {{ $t('reviews.tabs.documents') }} ({{
                        media?.documents?.length || 0
                    }})
                </TabsTrigger>

                <TabsTrigger value="updates" class="rounded-none py-3">
                    {{ $t('reviews.tabs.updates') }} ({{ updates.length }})
                </TabsTrigger>
            </TabsList>

            <TabsContent value="reviews" class="p-6">
                <ListingReviews
                    :reviews="localReviews"
                    :listing-id="listingId"
                    :next-page-url="localNextPageUrl"
                    @load-more="loadMore"
                />
            </TabsContent>

            <TabsContent value="documents" class="p-6">
                <ListingDocuments :documents="media?.documents" />
            </TabsContent>

            <TabsContent value="updates" class="p-6">
                <ListingUpdates :updates="updates" :listing-id="listingId" />
            </TabsContent>
        </Tabs>
    </div>
</template>

<script setup lang="ts">
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { ListingMediaCollection, Review, Update } from '@/types/listings';
import axios from 'axios';
import { trans } from 'laravel-vue-i18n';
import { ref } from 'vue';
import { useToast } from 'vue-toastification';
import ListingDocuments from './tabs/ListingDocuments.vue';
import ListingReviews from './tabs/ListingReviews.vue';
import ListingUpdates from './tabs/ListingUpdates.vue';

const props = withDefaults(
    defineProps<{
        listingId: number;
        media?: ListingMediaCollection;
        reviews?: Review[];
        updates?: Update[];
        nextPageUrl?: string | null;
    }>(),
    {
        reviews: () => [],
        updates: () => [],
        nextPageUrl: null,
    },
);

const toast = useToast();
const localReviews = ref<Review[]>([...props.reviews]);
const localNextPageUrl = ref<string | null>(props.nextPageUrl);
const isLoadingMore = ref(false);

async function loadMore() {
    if (!localNextPageUrl.value) {
        toast.info(trans('reviews.notifications.no_more_reviews'));
        return;
    }

    if (isLoadingMore.value) {
        return;
    }

    try {
        isLoadingMore.value = true;
        const response = await axios.get(localNextPageUrl.value);

        if (response.data.reviews && Array.isArray(response.data.reviews)) {
            localReviews.value.push(...response.data.reviews);
            localNextPageUrl.value = response.data.next_page_url;
        }
    } catch (error: any) {
        console.error('Error loading more reviews:', error);
        console.error('Request URL:', localNextPageUrl.value);
        console.error('Error response:', error.response);
        toast.error(
            trans('reviews.notifications.load_error') ||
                'Failed to load more reviews',
        );
    } finally {
        isLoadingMore.value = false;
    }
}
</script>
