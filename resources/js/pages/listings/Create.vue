<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useStorage } from '@vueuse/core';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toastification';

// Layout & UI
import Layout from '@/components/layout/Layout.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import ValidationErrorToast from '@/components/ValidationErrorToast.vue';
// Child Form Components
import ListingAuctionForm from '@/components/listings/ListingAuctionForm.vue';
import ListingBuyNowForm from '@/components/listings/ListingBuyNowForm.vue';
import ListingCommonDetails from '@/components/listings/ListingCommonDetails.vue';
import ListingDonationForm from '@/components/listings/ListingDonationForm.vue';
import ListingMediaUpload from '@/components/listings/ListingMediaUpload.vue';
import ListingTypeSelector from '@/components/listings/ListingTypeSelector.vue';

// Composables, Types, etc.
import { useLanguageSwitcher } from '@/composables/useLanguageSwitcher';
import { create, store } from '@/routes/listings';
import { type BreadcrumbItem } from '@/types';
import { PropType, ref, watch } from 'vue';

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

const { locale, availableLanguages } = useLanguageSwitcher();
const { t, fallbackLocale } = useI18n();
const toast = useToast();

const mediaUploadRef = ref<InstanceType<typeof ListingMediaUpload> | null>(
    null,
);

// --- STATE ---
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

// --- LOGIC ---

watch(listingType, (newType) => {
    form.listing_type = newType;
    form.clearErrors();
});

// Submit handler
const submit = () => {
    console.log(form);
    form.post(store.url(), {
        onSuccess: () => {
            toast.success(t('listing.createListing.notifications.success'));
            form.reset();
            mediaUploadRef.value?.reset();
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors);
            console.log(errors);
            toast.error({
                component: ValidationErrorToast,
                props: {
                    errors: errorMessages,
                },
            });
        },
    });
};

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('settings.profile.breadcrumb'),
        href: '#',
    },
];
</script>

<template>
    <Layout :link="create.url()" :breadcrumbs="breadcrumbItems">
        <form @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle class="text-2xl font-bold">
                        {{ t('listing.createListing.title') }}
                    </CardTitle>
                    <CardDescription>
                        {{ t('listing.createListing.description') }}
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
                        :fallback-locale="fallbackLocale as string"
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
                            {{ t('listing.createListing.sections.details') }}
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
                            {{ t('listing.createListing.buttons.submitting') }}
                        </span>
                        <span v-else>
                            {{ t('listing.createListing.buttons.submit') }}
                        </span>
                    </Button>
                </CardFooter>
            </Card>
        </form>
    </Layout>
</template>
