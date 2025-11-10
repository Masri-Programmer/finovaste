<template>
    <div class="w-full">
        <Tabs default-value="reviews" class="w-full rounded-lg border bg-card">
            <TabsList
                class="grid h-auto w-full grid-cols-3 rounded-none border-b p-0"
            >
                <TabsTrigger value="reviews" class="rounded-none py-3">
                    {{ $t('reviews.tabs.reviews') }} ({{ reviews.length }})
                </TabsTrigger>
                <TabsTrigger value="documents" class="rounded-none py-3">
                    {{ $t('reviews.tabs.documents') }} ({{ documents.length }})
                </TabsTrigger>
                <TabsTrigger value="updates" class="rounded-none py-3">
                    {{ $t('reviews.tabs.updates') }} ({{ updates.length }})
                </TabsTrigger>
            </TabsList>

            <TabsContent value="reviews" class="p-6">
                <div class="space-y-6">
                    <template v-if="reviews.length > 0">
                        <div
                            v-for="(review, index) in reviews"
                            :key="review.id"
                            class="space-y-4"
                        >
                            <div class="flex space-x-4">
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
                                            v-if="review.author.isVerified"
                                            variant="outline"
                                            class="border-green-600 text-green-700 dark:border-green-500 dark:text-green-500"
                                        >
                                            {{
                                                $t('reviews.verified_investor')
                                            }}
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
                                                        ? 'fill-yellow-400 text-yellow-400'
                                                        : 'text-muted-foreground/50'
                                                "
                                            />
                                        </div>
                                        <span
                                            class="text-xs font-medium text-muted-foreground"
                                            >{{
                                                timeAgo(review.createdAt)
                                            }}</span
                                        >
                                    </div>
                                    <p
                                        class="text-sm text-foreground/90"
                                        v-html="review.text"
                                    ></p>
                                </div>
                            </div>
                            <Separator
                                v-if="index < reviews.length - 1"
                                class="mt-4"
                            />
                        </div>
                    </template>
                    <p v-else class="py-4 text-center text-muted-foreground">
                        {{ $t('reviews.tabs.reviews_empty') }}
                    </p>

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

            <TabsContent value="documents" class="p-6">
                <div v-if="documents.length > 0" class="space-y-3">
                    <div
                        v-for="doc in documents"
                        :key="doc.id"
                        class="flex items-center justify-between rounded-lg border p-3 hover:bg-muted/50"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-md bg-primary/10"
                            >
                                <FileText class="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <div
                                    class="text-sm font-medium text-foreground"
                                >
                                    {{ doc.name }}
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    {{
                                        (doc.sizeInBytes / 1024 / 1024).toFixed(
                                            2,
                                        )
                                    }}
                                    MB
                                </div>
                            </div>
                        </div>
                        <Button
                            size="sm"
                            variant="outline"
                            @click="downloadDocument(doc)"
                        >
                            <Download class="mr-2 h-4 w-4" />
                            {{ $t('reviews.documents.download_button') }}
                        </Button>
                    </div>
                </div>
                <p v-else class="text-center text-muted-foreground">
                    {{ $t('reviews.tabs.documents_empty') }}
                </p>
            </TabsContent>

            <TabsContent value="updates" class="p-6">
                <div v-if="updates.length > 0" class="space-y-5">
                    <div
                        v-for="update in updates"
                        :key="update.id"
                        class="flex gap-3"
                    >
                        <div
                            class="mt-2 h-2 w-2 flex-shrink-0 rounded-full bg-primary"
                        ></div>
                        <div class="flex-1">
                            <div class="mb-1 text-sm text-muted-foreground">
                                {{ timeAgo(update.createdAt) }}
                            </div>
                            <p class="text-sm text-foreground/90">
                                {{ update.text }}
                            </p>
                        </div>
                    </div>
                    <div class="pt-4 text-center text-xs text-muted-foreground">
                        {{ $t('reviews.updates.last_checked') }}:
                        {{ lastChecked.toLocaleString() }}
                    </div>
                </div>
                <p v-else class="text-center text-muted-foreground">
                    {{ $t('reviews.tabs.updates_empty') }}
                </p>
            </TabsContent>
        </Tabs>
    </div>
</template>

<script setup lang="ts">
import { useNow, useTimeAgo } from '@vueuse/core';
import { trans } from 'laravel-vue-i18n';
import { Download, FileText, Star } from 'lucide-vue-next';
import { useToast } from 'vue-toastification';
// import { route } from 'laravel-typed-wayfinder';
// Shadcn-vue components
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
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

interface Document {
    id: number;
    name: string;
    sizeInBytes: number;
}

interface Update {
    id: number;
    text: string;
    createdAt: string | Date;
}

// --- Props ---
// Use withDefaults to provide fake data if no props are passed
const props = withDefaults(
    defineProps<{
        reviews?: Review[];
        documents?: Document[];
        updates?: Update[];
        nextPageUrl?: string | null; // For pagination
    }>(),
    {
        // Default factory functions for fake data
        reviews: () => [
            {
                id: 1,
                rating: 5,
                text: 'Eine hervorragende Investitionsmöglichkeit. Der Prozess war transparent und das Team sehr hilfsbereit. Ich freue mich auf die zukünftige Entwicklung!',
                createdAt: new Date(
                    Date.now() - 2 * 24 * 60 * 60 * 1000,
                ).toISOString(), // 2 days ago
                author: {
                    name: 'Maximilian Mustermann',
                    avatarUrl: 'https://i.pravatar.cc/150?img=68',
                    initials: 'MM',
                    isVerified: true,
                },
            },
            {
                id: 2,
                rating: 4,
                text: 'Solides Projekt mit gutem Potenzial. Einige Dokumente waren anfangs schwer zu finden, aber der Support hat schnell geholfen.',
                createdAt: new Date(
                    Date.now() - 5 * 24 * 60 * 60 * 1000,
                ).toISOString(), // 5 days ago
                author: {
                    name: 'Sophie Müller',
                    avatarUrl: 'https://i.pravatar.cc/150?img=49',
                    initials: 'SM',
                    isVerified: false,
                },
            },
        ],
        documents: () => [
            {
                id: 1,
                name: 'Investment Prospectus (Q4 2025)',
                sizeInBytes: 2.3 * 1024 * 1024, // 2.3 MB
            },
            {
                id: 2,
                name: 'Financial Reports (2024)',
                sizeInBytes: 1.8 * 1024 * 1024, // 1.8 MB
            },
            {
                id: 3,
                name: 'Business Plan Overview',
                sizeInBytes: 0.8 * 1024 * 1024, // 0.8 MB
            },
        ],
        updates: () => [
            {
                id: 1,
                text: 'Neuer Meilenstein erreicht! Wir haben die Marke von 50 Investoren überschritten.',
                createdAt: new Date(
                    Date.now() - 3 * 24 * 60 * 60 * 1000,
                ).toISOString(), // 3 days ago
            },
            {
                id: 2,
                text: 'Aktualisierte Finanzprognosen sind jetzt im Dokumentenbereich verfügbar.',
                createdAt: new Date(
                    Date.now() - 7 * 24 * 60 * 60 * 1000,
                ).toISOString(), // 1 week ago
            },
        ],
        nextPageUrl: null,
    },
);

// --- Composables ---
const toast = useToast();
const lastChecked = useNow(); // VueUse composable
const timeAgo = (date: string | Date) => useTimeAgo(new Date(date)).value;

// --- Methods ---

/**
 * Loads more reviews using Inertia.
 */
function loadMore() {
    if (!props.nextPageUrl) {
        toast.info(trans('reviews.notifications.no_more_reviews'));
        return;
    }

    // router.get(props.nextPageUrl, {
    //     preserveState: true,
    //     preserveScroll: true,
    //     onSuccess: () => {
    //         toast.success(trans('reviews.notifications.load_more_success'));
    //     },
    //     onError: () => {
    //         toast.error(trans('reviews.notifications.load_more_error'));
    //     },
    // });
}

/**
 * Simulates a document download request.
 */
function downloadDocument(doc: Document) {
    // try {
    //     // Use Laravel Typed Wayfinder to generate the download URL
    //     const downloadUrl = route('web.documents.download', { id: doc.id });
    //     // Use Inertia.visit for file downloads, or window.open for simplicity
    //     window.open(downloadUrl, '_blank');
    //     toast.success(
    //         trans('reviews.notifications.download_start', {
    //             name: doc.name,
    //         }),
    //     );
    // } catch (error)
    //     // Simulate error for fake data since route() won't work
    //     toast.info(
    //         trans('reviews.notifications.download_start', {
    //             name: doc.name,
    //         }),
    //     );
    //     console.warn(
    //         'Simulating download. `route()` will fail without a backend.',
    //     );
    // }
}
</script>
