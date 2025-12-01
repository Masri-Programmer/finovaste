<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useStorage } from '@vueuse/core';
import { useToast } from 'vue-toastification';

// Layout & UI
import Layout from '@/components/layout/Layout.vue';
import ListingAuctionForm from '@/components/listings/create/ListingAuctionForm.vue';
import ListingBuyNowForm from '@/components/listings/create/ListingBuyNowForm.vue';
import ListingCommonDetails from '@/components/listings/create/ListingCommonDetails.vue';
import ListingDonationForm from '@/components/listings/create/ListingDonationForm.vue';
import ListingMediaUpload from '@/components/listings/create/ListingMediaUpload.vue';
import ListingTypeSelector from '@/components/listings/create/ListingTypeSelector.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

import { useLanguageSwitcher } from '@/composables/useLanguageSwitcher';
import { create, store } from '@/routes/listings';
import { PropType, ref, watch } from 'vue';

const { locale, availableLanguages } = useLanguageSwitcher();
const props = defineProps({
    categories: {
        type: Array as PropType<
            Array<{
                id: number;
                name: { [key: string]: string };
            }>
        >,
        required: true,
    },
});
const toast = useToast();

const mediaUploadRef = ref<InstanceType<typeof ListingMediaUpload> | null>(
    null,
);

const listingType = useStorage<'buy_now' | 'auction' | 'donation'>(
    'create-listing-type',
    'buy_now',
);

const initialTranslations = availableLanguages.value.reduce(
    (acc, lang) => {
        acc[lang.code] = '';
        return acc;
    },
    {} as { [key: string]: string },
);

const form = useForm({
    // Common
    title: { ...initialTranslations },
    description: { ...initialTranslations },
    category_id: null as number | null,
    expires_at: null as Date | null,
    images: [] as File[],
    documents: [] as File[],
    videos: [] as File[],
    location_text: '',
    listing_type: listingType.value,

    // Buy Now
    price: null as number | null,
    quantity: 1,
    condition: 'new',

    // Auction
    start_price: null as number | null,
    reserve_price: null as number | null,
    buy_it_now_price: null as number | null,
    starts_at: null as Date | null,
    ends_at: null as Date | null,

    // Donation
    donation_goal: null as number | null,
    is_goal_flexible: false,
});

watch(listingType, (newType) => {
    form.listing_type = newType;
    form.clearErrors();
});

const submit = () => {
    form.post(store.url(), {
        onSuccess: () => {
            form.reset();
            mediaUploadRef.value?.reset();
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors);
            console.log(errors);
        },
    });
};
</script>

<template>
    <Layout :link="create.url()">
        <form @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle class="text-2xl font-bold">
                        {{ $t('createListing.title') }}
                    </CardTitle>
                    <CardDescription>
                        {{ $t('createListing.description') }}
                    </CardDescription>
                </CardHeader>

                <CardContent class="space-y-8">
                    <ListingTypeSelector v-model="listingType" />

                    <ListingCommonDetails
                        v-model:title="form.title"
                        v-model:description="form.description"
                        v-model:category_id="form.category_id"
                        v-model:location_text="form.location_text"
                        v-model:expires_at="form.expires_at"
                        :categories="props.categories"
                        :locale="locale"
                        :fallback-locale="'de'"
                        :errors="form.errors"
                    />

                    <ListingMediaUpload
                        ref="mediaUploadRef"
                        v-model:images="form.images"
                        v-model:documents="form.documents"
                        v-model:videos="form.videos"
                        :image-error="form.errors.images"
                        :document-error="form.errors.documents"
                        :video-error="form.errors.videos"
                    />

                    <div class="space-y-6">
                        <h3 class="border-b pb-2 text-base font-semibold">
                            {{ $t('createListing.sections.details') }}
                        </h3>

                        <ListingBuyNowForm
                            v-if="listingType === 'buy_now'"
                            :form="form"
                        />
                        <ListingAuctionForm
                            v-if="listingType === 'auction'"
                            :form="form"
                        />
                        <ListingDonationForm
                            v-if="listingType === 'donation'"
                            :form="form"
                        />
                    </div>
                </CardContent>

                <CardFooter>
                    <Button type="submit" :disabled="form.processing">
                        <span v-if="form.processing">
                            {{ $t('createListing.buttons.submitting') }}
                        </span>
                        <span v-else>
                            {{ $t('createListing.buttons.submit') }}
                        </span>
                    </Button>
                </CardFooter>
            </Card>
        </form>
    </Layout>
</template>
