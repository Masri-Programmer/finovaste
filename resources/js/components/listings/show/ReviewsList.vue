<template>
    <div class="w-full">
        <Tabs default-value="reviews" class="w-full">
            <TabsList class="grid w-full grid-cols-3">
                <TabsTrigger value="reviews">
                    {{ $t('reviews.tabs.reviews') }} ({{ reviews.length }})
                </TabsTrigger>
                <TabsTrigger value="documents">
                    {{ $t('reviews.tabs.documents') }}
                </TabsTrigger>
                <TabsTrigger value="updates">
                    {{ $t('reviews.tabs.updates') }}
                </TabsTrigger>
            </TabsList>

            <TabsContent value="reviews" class="mt-6">
                <div class="space-y-6">
                    <div
                        v-for="review in reviews"
                        :key="review.id"
                        class="flex space-x-4 border-b border-border pb-6 last:border-b-0 last:pb-0"
                    >
                        <Avatar>
                            <AvatarImage
                                :src="review.author.avatarUrl"
                                :alt="review.author.name"
                            />
                            <AvatarFallback>{{
                                review.author.initials
                            }}</AvatarFallback>
                        </Avatar>
                        <div class="flex-1 space-y-2">
                            <div
                                class="flex flex-col items-start sm:flex-row sm:items-center sm:space-x-3"
                            >
                                <span
                                    class="text-sm font-semibold text-foreground"
                                    >{{ review.author.name }}</span
                                >
                                <Badge
                                    vIf="review.author.isVerified"
                                    variant="outline"
                                    class="border-green-600 text-green-700"
                                >
                                    {{ $t('reviews.verified_investor') }}
                                </Badge>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="flex items-center">
                                    <Star
                                        v-for="i in 5"
                                        :key="i"
                                        class="h-4 w-4"
                                        :class="
                                            i <= review.rating
                                                ? 'text-yellow-400'
                                                : 'text-muted-foreground/50'
                                        "
                                        fill="currentColor"
                                    />
                                </div>
                                <span
                                    class="text-xs font-medium text-muted-foreground"
                                    >{{ timeAgo(review.createdAt) }}</span
                                >
                            </div>
                            <p class="text-sm text-foreground/90">
                                {{ review.text }}
                            </p>
                        </div>
                    </div>

                    <Button
                        v-if="nextPageUrl"
                        @click="loadMore"
                        variant="outline"
                        class="w-full"
                    >
                        {{ $t('reviews.load_more_button') }}
                    </Button>
                </div>
            </TabsContent>

            <TabsContent value="documents" class="mt-6">
                <p class="text-center text-muted-foreground">
                    {{ $t('reviews.tabs.documents_empty') }}
                </p>
            </TabsContent>
            <TabsContent value="updates" class="mt-6">
                <p class="text-center text-muted-foreground">
                    {{ $t('reviews.tabs.updates_empty') }}
                </p>
            </TabsContent>
        </Tabs>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { useTimeAgo } from '@vueuse/core';
import { Star } from 'lucide-vue-next';

// Shadcn-vue components
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

// --- Types ---
interface ReviewAuthor {
    name: string;
    avatarUrl: string;
    initials: string;
    isVerified: boolean;
}

interface Review {
    id: number;
    rating: number; // e.g., 4
    text: string;
    createdAt: string | Date; // ISO string or Date object
    author: ReviewAuthor;
}

// --- Props ---
const props = defineProps<{
    reviews: Review[];
    nextPageUrl: string | null; // For pagination
}>();

// --- Mocks (replace with your actual data/helpers) ---
const t = (key: string) => {
    const keys: Record<string, string> = {
        'reviews.tabs.reviews': 'Reviews',
        'reviews.tabs.documents': 'Documents',
        'reviews.tabs.updates': 'Updates',
        'reviews.verified_investor': 'Verified Investor',
        'reviews.load_more_button': 'Load More Reviews',
        'reviews.tabs.documents_empty': 'No documents available.',
        'reviews.tabs.updates_empty': 'No updates available.',
    };
    return keys[key] || key;
};

// --- Composables ---
// Use i18n

// Use VueUse useTimeAgo for relative timestamps
const timeAgo = (date: string | Date) => useTimeAgo(date).value;

// --- Methods ---

/**
 * Loads more reviews using Inertia.
 * This assumes the backend will return the *next* page of reviews,
 * which Inertia will merge with the existing `reviews` prop.
 */
function loadMore() {
    if (!props.nextPageUrl) return;

    router.get(props.nextPageUrl, {
        preserveState: true,
        preserveScroll: true,
    });
}
</script>
