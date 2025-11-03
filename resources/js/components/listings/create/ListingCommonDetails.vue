<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { cn } from '@/lib/utils';
import { format } from 'date-fns';
import { de } from 'date-fns/locale';
import { Calendar as CalendarIcon } from 'lucide-vue-next';
import { computed, PropType } from 'vue';
import { useI18n } from 'vue-i18n';

// --- PROPS ---
const props = defineProps({
    title: {
        type: Object as PropType<{ [key: string]: string }>,
        required: true,
    },
    description: {
        type: Object as PropType<{ [key: string]: string }>,
        required: true,
    },
    category_id: {
        type: Number as PropType<number | null>,
        required: true,
    },
    location_text: {
        type: String,
        required: true,
    },
    expires_at: {
        type: Date as PropType<Date | null>,
        required: true,
    },
    // Other props
    categories: {
        type: Array as PropType<
            Array<{
                id: number;
                name: { [key: string]: string };
            }>
        >,
        required: true,
    },
    fallbackLocale: {
        type: String,
        required: true,
    },
    errors: {
        type: Object as PropType<Record<string, string>>,
        required: true,
    },
});

// --- EMITS ---
const emit = defineEmits([
    'update:title',
    'update:description',
    'update:category_id',
    'update:location_text',
    'update:expires_at',
]);

const { t, locale } = useI18n();

// --- COMPUTED PROPS ---
const formattedExpiresAt = computed(() => {
    return props.expires_at
        ? format(props.expires_at, 'PPP', { locale: de })
        : t('listing.createListing.fields.expires_at.placeholder');
});

// --- HANDLERS for translated fields ---
const updateTitle = (newTitle: string) => {
    emit('update:title', { ...props.title, [locale.value]: newTitle });
};

const updateDescription = (newDescription: string) => {
    emit('update:description', {
        ...props.description,
        [locale.value]: newDescription,
    });
};
</script>

<template>
    <div class="space-y-6">
        <h3 class="border-b pb-2 text-base font-semibold">
            {{ t('listing.createListing.sections.core') }}
        </h3>

        <div class="mt-6 space-y-4">
            <div class="space-y-2">
                <Label for="title">
                    {{ t('listing.createListing.fields.title.label') }}
                </Label>
                <Input
                    id="title"
                    :model-value="props.title[locale]"
                    @input="
                        updateTitle(($event.target as HTMLInputElement).value)
                    "
                    :placeholder="
                        t('listing.createListing.fields.title.placeholder')
                    "
                    required
                />
                <span
                    v-if="props.errors[`title.${locale}`]"
                    class="text-sm text-destructive"
                >
                    {{ props.errors[`title.${locale}`] }}
                </span>
            </div>

            <div class="space-y-2">
                <Label for="description">
                    {{ t('listing.createListing.fields.description.label') }}
                </Label>
                <Textarea
                    id="description"
                    :model-value="props.description[locale]"
                    @input="
                        updateDescription(
                            ($event.target as HTMLTextAreaElement).value,
                        )
                    "
                    :placeholder="
                        t(
                            'listing.createListing.fields.description.placeholder',
                        )
                    "
                    class="min-h-[120px]"
                />
                <span
                    v-if="props.errors[`description.${locale}`]"
                    class="text-sm text-destructive"
                >
                    {{ props.errors[`description.${locale}`] }}
                </span>
            </div>
        </div>
        <span v-if="props.errors.title" class="text-sm text-destructive">
            {{ props.errors.title }}
        </span>
        <span v-if="props.errors.description" class="text-sm text-destructive">
            {{ props.errors.description }}
        </span>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="space-y-2">
                <Label for="category_id">
                    {{ t('listing.createListing.fields.category.label') }}
                </Label>
                <Select
                    id="category_id"
                    :model-value="props.category_id"
                    @update:model-value="emit('update:category_id', $event)"
                    required
                >
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
                                (category.name && category.name[locale]) ||
                                (category.name &&
                                    category.name[props.fallbackLocale]) ||
                                '...'
                            }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <span
                    v-if="props.errors.category_id"
                    class="text-sm text-destructive"
                >
                    {{ props.errors.category_id }}
                </span>
            </div>

            <div class="space-y-2">
                <Label for="location_text">
                    {{ t('listing.createListing.fields.location.label') }}
                </Label>
                <Input
                    id="location_text"
                    :model-value="props.location_text"
                    @update:model-value="emit('update:location_text', $event)"
                    :placeholder="
                        t('listing.createListing.fields.location.placeholder')
                    "
                />
            </div>

            <div class="space-y-2">
                <Label for="expires_at">
                    {{ t('listing.createListing.fields.expires_at.label') }}
                </Label>
                <Popover>
                    <PopoverTrigger as-child>
                        <Button
                            variant="outline"
                            :class="
                                cn(
                                    'w-full justify-start text-left font-normal',
                                    !props.expires_at &&
                                        'text-muted-foreground',
                                )
                            "
                        >
                            <CalendarIcon class="mr-2 h-4 w-4" />
                            {{ formattedExpiresAt }}
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-auto p-0">
                        <Calendar
                            :model-value="props.expires_at"
                            @update:model-value="
                                emit('update:expires_at', $event)
                            "
                        />
                    </PopoverContent>
                </Popover>
            </div>
        </div>
    </div>
</template>
