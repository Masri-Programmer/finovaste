<script setup lang="ts">
import AddressSelector from '@/components/listings/AddressSelector.vue';

import TiptapEditor from '@/components/tiptap/TiptapEditor.vue';
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
import { cn } from '@/lib/utils';
import { format } from 'date-fns';
import { de } from 'date-fns/locale';
import { getActiveLanguage, trans } from 'laravel-vue-i18n';
import { Calendar as CalendarIcon } from 'lucide-vue-next';
import { computed, PropType, ref } from 'vue';

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
    address_id: {
        type: Number as PropType<number | null>,
        required: true,
    },
    addresses: {
        type: Array as PropType<Array<any>>,
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
    'update:address_id',
    'update:expires_at',
]);

const locale = ref(getActiveLanguage());

// --- COMPUTED PROPS ---
const formattedExpiresAt = computed(() => {
    return props.expires_at
        ? format(props.expires_at, 'PPP', { locale: de })
        : trans('createListing.fields.expires_at.placeholder');
});

// --- HANDLERS for translated fields ---
const updateTitle = (newTitle: string) => {
    emit('update:title', { ...props.title, [locale.value]: newTitle });
};

// --- NOTE: Tiptap emits the raw HTML string directly, matching this signature ---
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
            {{ $t('createListing.sections.core') }}
        </h3>

        <div class="mt-6 space-y-4">
            <div class="space-y-2">
                <Label for="title">
                    {{ $t('createListing.fields.title.label') }}
                </Label>
                <Input
                    id="title"
                    :model-value="props.title[locale]"
                    @input="
                        updateTitle(($event.target as HTMLInputElement).value)
                    "
                    :placeholder="$t('createListing.fields.title.placeholder')"
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
                    {{ $t('createListing.fields.description.label') }}
                </Label>

                <div class="min-h-[250px]">
                    <TiptapEditor
                        :content="props.description[locale] || ''"
                        @update:content="updateDescription"
                        :placeholder="
                            $t('createListing.fields.description.placeholder')
                        "
                    />
                </div>

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
                    {{ $t('createListing.fields.category.label') }}
                </Label>
                <Select
                    id="category_id"
                    :model-value="props.category_id?.toString()"
                    @update:model-value="
                        (val) => emit('update:category_id', Number(val))
                    "
                    required
                >
                    <SelectTrigger>
                        <SelectValue
                            :placeholder="
                                $t('createListing.fields.category.placeholder')
                            "
                        />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="category in props.categories"
                            :key="category.id"
                            :value="category.id.toString()"
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
                <Label for="address_id">
                    {{ $t('createListing.fields.location.label') }}
                </Label>
                <AddressSelector
                    id="address_id"
                    :model-value="props.address_id"
                    @update:model-value="emit('update:address_id', $event)"
                    :addresses="props.addresses"
                    :error="props.errors.address_id"
                />
            </div>

            <div class="space-y-2">
                <Label for="expires_at">
                    {{ $t('createListing.fields.expires_at.label') }}
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
                            :model-value="
                                (props.expires_at as any) || undefined
                            "
                            @update:model-value="
                                emit('update:expires_at', $event)
                            "
                        />
                    </PopoverContent>
                </Popover>
                <span
                    v-if="props.errors.expires_at"
                    class="text-sm text-destructive"
                >
                    {{ props.errors.expires_at }}
                </span>
            </div>
        </div>
    </div>
</template>
