<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useStorage } from '@vueuse/core';
import { format } from 'date-fns';
import { de } from 'date-fns/locale';
import { computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toastification';

// shadcn/ui Components
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
import { Textarea } from '@/components/ui/textarea';
import { cn } from '@/lib/utils';
import { Calendar as CalendarIcon } from 'lucide-vue-next';

// --- PROPS ---

const props = defineProps<{
    categories: Array<{ id: number; name: string }>;
}>();

// --- COMPOSABLES ---

const { t } = useI18n();
const toast = useToast();

const listingType = useStorage<'buy_now' | 'auction' | 'donation'>(
    'create-listing-type',
    'buy_now',
);

const form = useForm({
    title: '',
    description: '',
    category_id: null as number | null,
    expires_at: null as Date | null,
    image: null as File | null, // For file upload

    // Type identifier
    listing_type: listingType.value,

    // Buy Now Fields
    price: null as number | null,
    quantity: 1,
    condition: 'new',

    // Auction Fields
    start_price: null as number | null,
    reserve_price: null as number | null,
    buy_it_now_price: null as number | null,
    starts_at: null as Date | null,
    ends_at: null as Date | null,

    // Donation Fields
    donation_goal: null as number | null,
    is_goal_flexible: false,

    // TODO: A real address form would be more complex
    // For now, we'll just send a text string.
    location_text: '',
});

// --- STATE & WATCHERS ---

// Keep the Inertia form's `listing_type` in sync with the `useStorage` ref
watch(listingType, (newType) => {
    form.listing_type = newType;
    form.clearErrors(); // Clear errors when switching types
});

// --- COMPUTED ---

// Format dates for display on buttons
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

// --- METHODS ---

const onFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.image = target.files[0];
    }
};

const submit = () => {
    // form.post(route('listings.store'), {
    //     onSuccess: () => {
    //         toast.success(t('listing.createListing.notifications.success'));
    //         form.reset();
    //     },
    //     onError: (errors) => {
    //         console.error(errors);
    //         toast.error(t('listing.createListing.notifications.error'));
    //     },
    // });
};
</script>

<template>
    <form @submit.prevent="submit">
        <Card class="mx-auto w-full max-w-4xl">
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
                            <RadioGroupItem value="buy_now" class="sr-only" />
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
                            <RadioGroupItem value="auction" class="sr-only" />
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
                            <RadioGroupItem value="donation" class="sr-only" />
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
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
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
                                v-model="form.title"
                                :placeholder="
                                    t(
                                        'listing.createListing.fields.title.placeholder',
                                    )
                                "
                                required
                            />
                            <span
                                v-if="form.errors.title"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.title }}
                            </span>
                        </div>

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
                                        {{ category.name }}
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
                            v-model="form.description"
                            :placeholder="
                                t(
                                    'listing.createListing.fields.description.placeholder',
                                )
                            "
                            class="min-h-[120px]"
                        />
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
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
                                        <CalendarIcon class="mr-2 h-4 w-4" />
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
                        <Label for="image">
                            {{ t('listing.createListing.fields.image.label') }}
                        </Label>
                        <Input
                            id="image"
                            type="file"
                            @change="onFileChange"
                            accept="image/*"
                            class="file:font-semibold file:text-primary-foreground"
                        />
                        <span class="text-sm text-muted-foreground">
                            {{
                                t(
                                    'listing.createListing.fields.image.description',
                                )
                            }}
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
                                        <CalendarIcon class="mr-2 h-4 w-4" />
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
                                        <CalendarIcon class="mr-2 h-4 w-4" />
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
</template>

<style scoped>
/* This component automatically inherits the custom font (Montserrat) 
  and color palette (new light/dark themes) from your provided `app.css` 
  via the shadcn/ui variables and Tailwind.
*/
</style>
