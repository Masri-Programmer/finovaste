<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { PropType, ref, watch } from 'vue';
import { useToast } from 'vue-toastification';

import Layout from '@/components/layout/Layout.vue';
import ListingAuctionForm from '@/components/listings/create/ListingAuctionForm.vue';
import ListingCommonDetails from '@/components/listings/create/ListingCommonDetails.vue';
import ListingDonationForm from '@/components/listings/create/ListingDonationForm.vue';
import ListingInvestmentForm from '@/components/listings/create/ListingInvestmentForm.vue';
import ListingMediaUpload from '@/components/listings/create/ListingMediaUpload.vue';
import ListingPurchaseForm from '@/components/listings/create/ListingPurchaseForm.vue';
import ListingTypeSelector from '@/components/listings/create/ListingTypeSelector.vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
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
import { destroy, update } from '@/routes/listings';
import { Category } from '@/types';
import {
    AuctionListable,
    DonationListable,
    InvestmentListable,
    Listing,
    ListingMediaCollection,
    PurchaseListable,
} from '@/types/listings';

const { locale, availableLanguages } = useLanguageSwitcher();
const props = defineProps({
    listing: {
        type: Object as PropType<Listing>,
        required: true,
    },
    categories: {
        type: Array as PropType<Array<Category>>,
        required: true,
    },
});

const toast = useToast();

const mediaUploadRef = ref<InstanceType<typeof ListingMediaUpload> | null>(
    null,
);

const initialTranslations = availableLanguages.value.reduce<
    Record<string, string>
>((acc, lang) => {
    acc[lang.code] = props.listing.title[lang.code] ?? '';
    return acc;
}, {});

const initialDescriptionTranslations = availableLanguages.value.reduce<
    Record<string, string>
>((acc, lang) => {
    acc[lang.code] = props.listing.description[lang.code] ?? '';
    return acc;
}, {});

const existingMedia: {
    images: ListingMediaCollection[];
    documents: ListingMediaCollection[];
    videos: ListingMediaCollection[];
} = {
    images: props.listing.media.filter(
        (m: ListingMediaCollection) => m.collection_name === 'images',
    ),
    documents: props.listing.media.filter(
        (m: ListingMediaCollection) => m.collection_name === 'documents',
    ),
    videos: props.listing.media.filter(
        (m: ListingMediaCollection) => m.collection_name === 'videos',
    ),
};

const listingType = ref<'purchase' | 'auction' | 'donation' | 'investment'>(
    props.listing.listable_type.includes('PurchaseListing')
        ? 'purchase'
        : props.listing.listable_type.includes('AuctionListing')
          ? 'auction'
          : props.listing.listable_type.includes('InvestmentListing')
            ? 'investment'
            : 'purchase',
);

const form = useForm({
    _method: 'patch',

    // Common
    title: initialTranslations,
    description: initialDescriptionTranslations,
    category_id: props.listing.category_id as number | null,
    expires_at: props.listing.expires_at
        ? new Date(props.listing.expires_at)
        : null,
    location_text: props.listing.address?.full_address || '',
    listing_type: listingType.value,

    images: [] as File[],
    documents: [] as File[],
    videos: [] as File[],
    media_to_delete: [] as number[],

    // Buy Now
    price:
        listingType.value === 'purchase'
            ? Number((props.listing.listable as PurchaseListable).price)
            : null,
    quantity:
        listingType.value === 'purchase'
            ? (props.listing.listable as PurchaseListable).quantity
            : 1,
    condition:
        listingType.value === 'purchase'
            ? (props.listing.listable as PurchaseListable).condition
            : 'new',

    // Auction
    start_price:
        listingType.value === 'auction'
            ? (props.listing.listable as AuctionListable).start_price
            : null,
    reserve_price:
        listingType.value === 'auction'
            ? (props.listing.listable as AuctionListable).reserve_price
            : null,
    buy_it_now_price:
        listingType.value === 'auction'
            ? (props.listing.listable as AuctionListable).buy_it_now_price
            : null,
    starts_at:
        listingType.value === 'auction' &&
        (props.listing.listable as AuctionListable).starts_at
            ? new Date((props.listing.listable as AuctionListable).starts_at!)
            : null,
    ends_at:
        listingType.value === 'auction' &&
        (props.listing.listable as AuctionListable).ends_at
            ? new Date((props.listing.listable as AuctionListable).ends_at)
            : null,

    // Donation
    donation_goal:
        listingType.value === 'donation'
            ? Number((props.listing.listable as DonationListable).donation_goal)
            : null,
    is_goal_flexible:
        listingType.value === 'donation'
            ? (props.listing.listable as DonationListable).is_goal_flexible
            : false,

    // Investmen
    investment_goal:
        listingType.value === 'investment'
            ? Number(
                  (props.listing.listable as InvestmentListable)
                      .investment_goal,
              )
            : null,
    minimum_investment:
        listingType.value === 'investment'
            ? Number(
                  (props.listing.listable as InvestmentListable)
                      .minimum_investment,
              )
            : null,
    shares_offered:
        listingType.value === 'investment'
            ? (props.listing.listable as InvestmentListable).shares_offered
            : null,
    share_price:
        listingType.value === 'investment'
            ? Number((props.listing.listable as InvestmentListable).share_price)
            : null,
});

watch(listingType, (newType) => {
    form.listing_type = newType;
    form.clearErrors();
});

const submit = () => {
    form.post(
        update.url({
            listing: props.listing.id,
        }),
    );
};

function handleMediaDelete(mediaIds: number[]) {
    form.media_to_delete = mediaIds;
}

const deleteListing = () => {
    form.delete(destroy.url({ listing: props.listing.id }));
};
</script>

<template>
    <Layout :link="update.url({ listing: props.listing.id })">
        <form @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle class="text-2xl font-bold">
                        {{
                            $t('listings.edit.title', {
                                title: listing.title[locale],
                            })
                        }}
                    </CardTitle>
                    <CardDescription>
                        {{ $t('listings.edit.description') }}
                    </CardDescription>
                </CardHeader>

                <CardContent class="space-y-8">
                    <ListingTypeSelector
                        v-model="listingType"
                        :disabled="true"
                    />

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
                        @media-delete="handleMediaDelete"
                        :existing-media="existingMedia"
                        :image-error="form.errors.images"
                        :document-error="form.errors.documents"
                        :video-error="form.errors.videos"
                    />

                    <div class="space-y-6">
                        <h3 class="border-b pb-2 text-base font-semibold">
                            {{ $t('createListing.sections.details') }}
                        </h3>

                        <ListingPurchaseForm
                            v-if="listingType === 'purchase'"
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
                        <ListingInvestmentForm
                            v-if="listingType === 'investment'"
                            :form="form"
                        />
                    </div>
                </CardContent>

                <CardFooter class="flex justify-between">
                    <AlertDialog>
                        <AlertDialogTrigger as-child>
                            <Button variant="destructive" type="button">
                                {{ $t('listings.edit.actions.delete') }}
                            </Button>
                        </AlertDialogTrigger>
                        <AlertDialogContent>
                            <AlertDialogHeader>
                                <AlertDialogTitle>
                                    {{
                                        $t(
                                            'listings.edit.delete_confirmation.title',
                                        )
                                    }}
                                </AlertDialogTitle>
                                <AlertDialogDescription>
                                    {{
                                        $t(
                                            'listings.edit.delete_confirmation.description',
                                        )
                                    }}
                                </AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel>
                                    {{
                                        $t(
                                            'listings.edit.delete_confirmation.cancel',
                                        )
                                    }}
                                </AlertDialogCancel>
                                <AlertDialogAction @click="deleteListing">
                                    {{
                                        $t(
                                            'listings.edit.delete_confirmation.confirm',
                                        )
                                    }}
                                </AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>

                    <Button type="submit" :disabled="form.processing">
                        <span v-if="form.processing">
                            {{ $t('listings.edit.actions.saving') }}
                        </span>
                        <span v-else>
                            {{ $t('listings.edit.actions.save') }}
                        </span>
                    </Button>
                </CardFooter>
            </Card>
        </form>
    </Layout>
</template>
