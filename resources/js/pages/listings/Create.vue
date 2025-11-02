<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useStorage } from '@vueuse/core';
import { format } from 'date-fns';
import { de } from 'date-fns/locale';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toastification';

// shadcn/ui Components
import Layout from '@/components/layout/Layout.vue';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { useLanguageSwitcher } from '@/composables/useLanguageSwitcher';
import { cn } from '@/lib/utils';
import { store } from '@/routes/listings';
import { type BreadcrumbItem } from '@/types';
import { Calendar as CalendarIcon } from 'lucide-vue-next';
import { computed, PropType, watch } from 'vue';

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
// Get all available locales (e.g., ['en', 'de'])
const { locale, availableLanguages } = useLanguageSwitcher();
const { t, fallbackLocale } = useI18n();
const toast = useToast();

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
    title: { ...initialTranslations },
    description: { ...initialTranslations },

    category_id: null as number | null,
    expires_at: null as Date | null,
    images: [] as File[],
    documents: [] as File[],
    videos: [] as File[],

    listing_type: listingType.value,

    price: null as number | null,
    quantity: 1,
    condition: 'new',

    start_price: null as number | null,
    reserve_price: null as number | null,
    buy_it_now_price: null as number | null,
    starts_at: null as Date | null,
    ends_at: null as Date | null,

    donation_goal: null as number | null,
    is_goal_flexible: false,

    location_text: '',
});

watch(listingType, (newType) => {
    form.listing_type = newType;
    form.clearErrors();
});

const formattedExpiresAt = computed(() => {
    return form.expires_at
        ? format(form.expires_at, 'PPP', { locale: de })
        : t('listing.createListing.fields.expires_at.placeholder');
});
const formattedStartsAt = computed(() => {
    return form.starts_at
        ? format(form.starts_at, 'PPP', { locale: de })
        : t('listing.createListing.fields.starts_at.placeholder');
});
const formattedEndsAt = computed(() => {
    return form.ends_at
        ? format(form.ends_at, 'PPP', { locale: de })
        : t('listing.createListing.fields.ends_at.placeholder');
});

const onImagesChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.images = target.files ? Array.from(target.files) : [];
};

const onDocumentsChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.documents = target.files ? Array.from(target.files) : [];
};

const onVideosChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.videos = target.files ? Array.from(target.files) : [];
};

const submit = () => {
    form.post(store.url(), {
        onSuccess: () => {
            toast.success(t('listing.createListing.notifications.success'));
            form.reset();
        },
        onError: (errors) => {
            console.error(errors);
            toast.error(t('listing.createListing.notifications.error'));
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
    <Layout link="/" :breadcrumbs="breadcrumbItems">
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
                    <div class="space-y-4">
                        <Label class="text-base font-semibold">
                            {{ t('listing.createListing.sections.type') }}
                        </Label>
                        <RadioGroup
                            v-model="listingType"
                            class="grid grid-cols-1 gap-4 md:grid-cols-3"
                        >
                            <Label
                                class="flex flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
                            >
                                <RadioGroupItem
                                    value="buy_now"
                                    class="sr-only"
                                />
                                <span class="font-semibold">
                                    {{
                                        t(
                                            'listing.createListing.types.buy_now.title',
                                        )
                                    }}
                                </span>
                                <span class="text-sm text-muted-foreground">
                                    {{
                                        t(
                                            'listing.createListing.types.buy_now.description',
                                        )
                                    }}
                                </span>
                            </Label>
                            <Label
                                class="flex flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
                            >
                                <RadioGroupItem
                                    value="auction"
                                    class="sr-only"
                                />
                                <span class="font-semibold">
                                    {{
                                        t(
                                            'listing.createListing.types.auction.title',
                                        )
                                    }}
                                </span>
                                <span class="text-sm text-muted-foreground">
                                    {{
                                        t(
                                            'listing.createListing.types.auction.description',
                                        )
                                    }}
                                </span>
                            </Label>
                            <Label
                                class="flex flex-col items-center justify-center rounded-md border-2 border-muted bg-popover p-4 hover:bg-accent hover:text-accent-foreground [&:has([data-state=checked])]:border-primary"
                            >
                                <RadioGroupItem
                                    value="donation"
                                    class="sr-only"
                                />
                                <span class="font-semibold">
                                    {{
                                        t(
                                            'listing.createListing.types.donation.title',
                                        )
                                    }}
                                </span>
                                <span class="text-sm text-muted-foreground">
                                    {{
                                        t(
                                            'listing.createListing.types.donation.description',
                                        )
                                    }}
                                </span>
                            </Label>
                        </RadioGroup>
                    </div>

                    <div class="space-y-6">
                        <h3 class="border-b pb-2 text-base font-semibold">
                            {{ t('listing.createListing.sections.core') }}
                        </h3>
                        <div class="mt-6 space-y-4">
                            <div class="space-y-2">
                                <Label for="title">
                                    {{
                                        t(
                                            'listing.createListing.fields.title.label',
                                        )
                                    }}
                                </Label>
                                <Input
                                    id="title"
                                    v-model="form.title[locale]"
                                    :placeholder="
                                        t(
                                            'listing.createListing.fields.title.placeholder',
                                        )
                                    "
                                    required
                                />
                                <span
                                    v-if="form.errors[`title.${locale}`]"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors[`title.${locale}`] }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <Label for="description">
                                    {{
                                        t(
                                            'listing.createListing.fields.description.label',
                                        )
                                    }}
                                </Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description[locale]"
                                    :placeholder="
                                        t(
                                            'listing.createListing.fields.description.placeholder',
                                        )
                                    "
                                    class="min-h-[120px]"
                                />
                                <span
                                    v-if="form.errors[`description.${locale}`]"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors[`description.${locale}`] }}
                                </span>
                            </div>
                        </div>
                        <span
                            v-if="form.errors.title"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.title }}
                        </span>
                        <span
                            v-if="form.errors.description"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.description }}
                        </span>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="category">
                                    {{
                                        t(
                                            'listing.createListing.fields.category.label',
                                        )
                                    }}
                                </Label>
                                <Select v-model="form.category_id" required>
                                    <SelectTrigger>
                                        <SelectValue
                                            :placeholder="
                                                t(
                                                    'listing.createListing.fields.category.placeholder',
                                                )
                                            "
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="category in props.categories"
                                            :key="category.id"
                                            :value="category.id"
                                        >
                                            {{
                                                category.name[locale] ||
                                                category.name[
                                                    fallbackLocale as string
                                                ]
                                            }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <span
                                    v-if="form.errors.category_id"
                                    class="text-sm text-destructive"
                                >
                                    {{ form.errors.category_id }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <Label for="location">
                                    {{
                                        t(
                                            'listing.createListing.fields.location.label',
                                        )
                                    }}
                                </Label>
                                <Input
                                    id="location"
                                    v-model="form.location_text"
                                    :placeholder="
                                        t(
                                            'listing.createListing.fields.location.placeholder',
                                        )
                                    "
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="expires_at">
                                    {{
                                        t(
                                            'listing.createListing.fields.expires_at.label',
                                        )
                                    }}
                                </Label>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :class="
                                                cn(
                                                    'w-full justify-start text-left font-normal',
                                                    !form.expires_at &&
                                                        'text-muted-foreground',
                                                )
                                            "
                                        >
                                            <CalendarIcon
                                                class="mr-2 h-4 w-4"
                                            />
                                            {{ formattedExpiresAt }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0">
                                        <Calendar v-model="form.expires_at" />
                                    </PopoverContent>
                                </Popover>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="images">
                                {{
                                    t(
                                        'listing.createListing.fields.media.images',
                                    )
                                }}</Label
                            >
                            <Input
                                id="images"
                                type="file"
                                @change="onImagesChange"
                                multiple
                                accept="image/jpeg,image/png,image/webp"
                                class="file:font-semibold file:text-primary-foreground"
                            />
                            <span
                                v-if="form.errors.images"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.images }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            <Label for="documents">
                                {{
                                    t(
                                        'listing.createListing.fields.media.documents',
                                    )
                                }}</Label
                            >
                            <Input
                                id="documents"
                                type="file"
                                @change="onDocumentsChange"
                                multiple
                                accept=".pdf,.doc,.docx"
                                class="file:font-semibold file:text-primary-foreground"
                            />
                            <span
                                v-if="form.errors.documents"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.documents }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            <Label for="videos">
                                {{
                                    t(
                                        'listing.createListing.fields.media.videos',
                                    )
                                }}</Label
                            >
                            <Input
                                id="videos"
                                type="file"
                                @change="onVideosChange"
                                multiple
                                accept="video/mp4,video/quicktime"
                                class="file:font-semibold file:text-primary-foreground"
                            />
                            <span
                                v-if="form.errors.videos"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.videos }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <h3 class="border-b pb-2 text-base font-semibold">
                            {{ t('listing.createListing.sections.details') }}
                        </h3>

                        <div
                            v-if="listingType === 'buy_now'"
                            class="grid grid-cols-1 gap-6 md:grid-cols-3"
                        >
                            <div class="space-y-2">
                                <Label for="price">
                                    {{
                                        t(
                                            'listing.createListing.fields.price.label',
                                        )
                                    }}
                                </Label>
                                <Input
                                    id="price"
                                    v-model.number="form.price"
                                    type="number"
                                    step="0.01"
                                    :placeholder="
                                        t(
                                            'listing.createListing.fields.price.placeholder',
                                        )
                                    "
                                    required
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="quantity">
                                    {{
                                        t(
                                            'listing.createListing.fields.quantity.label',
                                        )
                                    }}
                                </Label>
                                <Input
                                    id="quantity"
                                    v-model.number="form.quantity"
                                    type="number"
                                    step="1"
                                    min="1"
                                    required
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="condition">
                                    {{
                                        t(
                                            'listing.createListing.fields.condition.label',
                                        )
                                    }}
                                </Label>
                                <Select v-model="form.condition">
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="new">
                                            {{
                                                t(
                                                    'listing.createListing.fields.condition.options.new',
                                                )
                                            }}
                                        </SelectItem>
                                        <SelectItem value="used">
                                            {{
                                                t(
                                                    'listing.createListing.fields.condition.options.used',
                                                )
                                            }}
                                        </SelectItem>
                                        <SelectItem value="refurbished">
                                            {{
                                                t(
                                                    'listing.createListing.fields.condition.options.refurbished',
                                                )
                                            }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div
                            v-if="listingType === 'auction'"
                            class="grid grid-cols-1 gap-6 md:grid-cols-2"
                        >
                            <div class="space-y-2">
                                <Label for="start_price">
                                    {{
                                        t(
                                            'listing.createListing.fields.start_price.label',
                                        )
                                    }}
                                </Label>
                                <Input
                                    id="start_price"
                                    v-model.number="form.start_price"
                                    type="number"
                                    step="0.01"
                                    :placeholder="
                                        t(
                                            'listing.createListing.fields.start_price.placeholder',
                                        )
                                    "
                                    required
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="reserve_price">
                                    {{
                                        t(
                                            'listing.createListing.fields.reserve_price.label',
                                        )
                                    }}
                                </Label>
                                <Input
                                    id="reserve_price"
                                    v-model.number="form.reserve_price"
                                    type="number"
                                    step="0.01"
                                    :placeholder="
                                        t(
                                            'listing.createListing.fields.reserve_price.placeholder',
                                        )
                                    "
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="buy_it_now_price">
                                    {{
                                        t(
                                            'listing.createListing.fields.buy_it_now_price.label',
                                        )
                                    }}
                                </Label>
                                <Input
                                    id="buy_it_now_price"
                                    v-model.number="form.buy_it_now_price"
                                    type="number"
                                    step="0.01"
                                    :placeholder="
                                        t(
                                            'listing.createListing.fields.buy_it_now_price.placeholder',
                                        )
                                    "
                                />
                            </div>
                            <div></div>
                            <div class="space-y-2">
                                <Label for="starts_at">
                                    {{
                                        t(
                                            'listing.createListing.fields.starts_at.label',
                                        )
                                    }}
                                </Label>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :class="
                                                cn(
                                                    'w-full justify-start text-left font-normal',
                                                    !form.starts_at &&
                                                        'text-muted-foreground',
                                                )
                                            "
                                        >
                                            <CalendarIcon
                                                class="mr-2 h-4 w-4"
                                            />
                                            {{ formattedStartsAt }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0">
                                        <Calendar v-model="form.starts_at" />
                                    </PopoverContent>
                                </Popover>
                            </div>
                            <div class="space-y-2">
                                <Label for="ends_at">
                                    {{
                                        t(
                                            'listing.createListing.fields.ends_at.label',
                                        )
                                    }}
                                </Label>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :class="
                                                cn(
                                                    'w-full justify-start text-left font-normal',
                                                    !form.ends_at &&
                                                        'text-muted-foreground',
                                                )
                                            "
                                        >
                                            <CalendarIcon
                                                class="mr-2 h-4 w-4"
                                            />
                                            {{ formattedEndsAt }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0">
                                        <Calendar v-model="form.ends_at" />
                                    </PopoverContent>
                                </Popover>
                            </div>
                        </div>

                        <div
                            v-if="listingType === 'donation'"
                            class="grid grid-cols-1 gap-6 md:grid-cols-2"
                        >
                            <div class="space-y-2">
                                <Label for="donation_goal">
                                    {{
                                        t(
                                            'listing.createListing.fields.donation_goal.label',
                                        )
                                    }}
                                </Label>
                                <Input
                                    id="donation_goal"
                                    v-model.number="form.donation_goal"
                                    type="number"
                                    step="1"
                                    :placeholder="
                                        t(
                                            'listing.createListing.fields.donation_goal.placeholder',
                                        )
                                    "
                                    required
                                />
                            </div>
                            <div class="flex items-center space-x-2 pt-6">
                                <Switch
                                    id="is_goal_flexible"
                                    v-model="form.is_goal_flexible"
                                />
                                <Label for="is_goal_flexible">
                                    {{
                                        t(
                                            'listing.createListing.fields.is_goal_flexible.label',
                                        )
                                    }}
                                </Label>
                            </div>
                        </div>
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
