<template>
    <div class="space-y-6">
        <!-- Review Form -->
        <div v-if="$page.props.auth.user" class="rounded-lg border bg-card p-4">
            <h3 class="mb-4 text-lg font-semibold">
                {{ $t('reviews.write_review') }}
            </h3>
            <form @submit.prevent="submitReview" class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">{{
                        $t('reviews.rating')
                    }}</label>
                    <div class="flex items-center space-x-1">
                        <button
                            v-for="i in 5"
                            :key="i"
                            type="button"
                            @click="form.rating = i"
                            class="focus:outline-none"
                        >
                            <Star
                                class="h-6 w-6"
                                :class="
                                    i <= form.rating
                                        ? 'fill-yellow-400 text-yellow-400'
                                        : 'text-muted-foreground/30'
                                "
                            />
                        </button>
                    </div>
                    <div
                        v-if="form.errors.rating"
                        class="mt-1 text-sm text-destructive"
                    >
                        {{ form.errors.rating }}
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">{{
                        $t('reviews.comment')
                    }}</label>
                    <Textarea
                        v-model="form.body"
                        :placeholder="$t('reviews.placeholder')"
                        rows="3"
                    />
                    <div
                        v-if="form.errors.body"
                        class="mt-1 text-sm text-destructive"
                    >
                        {{ form.errors.body }}
                    </div>
                </div>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        {{ $t('reviews.submit') }}
                    </Button>
                </div>
            </form>
        </div>

        <!-- Reviews List -->
        <template v-if="reviews.length > 0">
            <div
                v-for="(review, index) in reviews"
                :key="review.id"
                class="space-y-4"
            >
                <div class="flex space-x-4">
                    <Avatar>
                        <AvatarImage
                            :src="review.user.profile_photo_url"
                            :alt="review.user.name"
                        />
                        <AvatarFallback>{{
                            getInitials(review.user.name)
                        }}</AvatarFallback>
                    </Avatar>
                    <div class="flex-1 space-y-2">
                        <div
                            class="flex flex-col items-start sm:flex-row sm:items-center sm:space-x-3"
                        >
                            <span
                                class="text-sm font-semibold text-foreground"
                                >{{ review.user.name }}</span
                            >
                            <Badge
                                v-if="review.user.is_verified"
                                variant="outline"
                                class="border-green-600 text-green-700 dark:border-green-500 dark:text-green-500"
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
                                            ? 'fill-yellow-400 text-yellow-400'
                                            : 'text-muted-foreground/50'
                                    "
                                />
                            </div>
                            <span
                                class="text-xs font-medium text-muted-foreground"
                                >{{ review.time_ago }}</span
                            >
                        </div>

                        <!-- Edit Mode -->
                        <div
                            v-if="editingReviewId === review.id"
                            class="mt-2 space-y-3"
                        >
                            <div class="flex items-center space-x-1">
                                <button
                                    v-for="i in 5"
                                    :key="i"
                                    type="button"
                                    @click="editForm.rating = i"
                                    class="focus:outline-none"
                                >
                                    <Star
                                        class="h-5 w-5"
                                        :class="
                                            i <= editForm.rating
                                                ? 'fill-yellow-400 text-yellow-400'
                                                : 'text-muted-foreground/30'
                                        "
                                    />
                                </button>
                            </div>
                            <Textarea v-model="editForm.body" rows="3" />
                            <div class="flex space-x-2">
                                <Button
                                    size="sm"
                                    @click="updateReview(review)"
                                    :disabled="editForm.processing"
                                >
                                    {{ $t('actions.save') }}
                                </Button>
                                <Button
                                    size="sm"
                                    variant="ghost"
                                    @click="cancelEdit"
                                >
                                    {{ $t('actions.cancel') }}
                                </Button>
                            </div>
                        </div>

                        <!-- Display Mode -->
                        <div v-else>
                            <p
                                class="text-sm whitespace-pre-wrap text-foreground/90"
                            >
                                {{ review.body }}
                            </p>

                            <!-- Actions -->
                            <div
                                v-if="review.can_edit"
                                class="mt-2 flex space-x-2"
                            >
                                <button
                                    @click="startEdit(review)"
                                    class="text-xs text-muted-foreground hover:text-primary"
                                >
                                    {{ $t('actions.edit') }}
                                </button>
                                <button
                                    @click="deleteReview(review)"
                                    class="text-xs text-destructive hover:text-destructive/80"
                                >
                                    {{ $t('actions.delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <Separator v-if="index < reviews.length - 1" class="mt-4" />
            </div>
        </template>
        <p v-else class="py-4 text-center text-muted-foreground">
            {{ $t('reviews.tabs.reviews_empty') }}
        </p>

        <Button
            v-if="nextPageUrl"
            @click="$emit('load-more')"
            variant="outline"
            class="w-full"
        >
            {{ $t('reviews.load_more_button') }}
        </Button>
    </div>
</template>

<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import { destroy, store, update } from '@/routes/listings/reviews';
import { Review } from '@/types/listings';
import { useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { Star } from 'lucide-vue-next';
import { ref } from 'vue';
const props = defineProps<{
    reviews: Review[];
    listingId: number;
    nextPageUrl?: string | null;
}>();

const emit = defineEmits(['load-more']);

// Create Review Form
const form = useForm({
    rating: 0,
    body: '',
});

const submitReview = () => {
    form.post(store.url(props.listingId), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

// Edit Review Logic
const editingReviewId = ref<number | null>(null);
const editForm = useForm({
    rating: 0,
    body: '',
});

const startEdit = (review: Review) => {
    editingReviewId.value = review.id;
    editForm.rating = review.rating;
    editForm.body = review.body;
};

const cancelEdit = () => {
    editingReviewId.value = null;
    editForm.reset();
};

const updateReview = (review: Review) => {
    editForm.put(update.url(review.id), {
        preserveScroll: true,
        onSuccess: () => {
            editingReviewId.value = null;
        },
    });
};

const deleteReview = (review: Review) => {
    if (confirm(trans('actions.are_you_sure'))) {
        useForm({}).delete(destroy.url(review.id), {
            preserveScroll: true,
        });
    }
};

function getInitials(name: string): string {
    if (!name) return '??';
    const parts = name.trim().split(' ');
    if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}
</script>
