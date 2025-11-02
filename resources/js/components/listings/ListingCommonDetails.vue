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
import { type UseForm } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { de } from 'date-fns/locale';
import { Calendar as CalendarIcon } from 'lucide-vue-next';
import { computed, PropType } from 'vue';
import { useI18n } from 'vue-i18n';

// Define props
const props = defineProps({
    form: {
        type: Object as PropType<UseForm<any>>,
        required: true,
    },
    categories: {
        type: Array as PropType<
            Array<{
                id: number;
                name: { [key: string]: string };
            }>
        >,
        required: true,
    },
    locale: {
        type: String,
        required: true,
    },
    fallbackLocale: {
        type: String,
        required: true,
    },
});

const { t } = useI18n();

// --- COMPUTED PROPS ---
const formattedExpiresAt = computed(() => {
    return props.form.expires_at
        ? format(props.form.expires_at, 'PPP', { locale: de })
        : t('listing.createListing.fields.expires_at.placeholder');
});

// All file input refs and handlers have been removed.
// The defineExpose has also been removed.
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
                    v-model="form.title[locale]"
                    :placeholder="
                        t('listing.createListing.fields.title.placeholder')
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
                    {{ t('listing.createListing.fields.description.label') }}
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
        <span v-if="form.errors.title" class="text-sm text-destructive">
            {{ form.errors.title }}
        </span>
        <span v-if="form.errors.description" class="text-sm text-destructive">
            {{ form.errors.description }}
        </span>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="space-y-2">
                <Label for="category">
                    {{ t('listing.createListing.fields.category.label') }}
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
                                category.name[fallbackLocale]
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
                    {{ t('listing.createListing.fields.location.label') }}
                </Label>
                <Input
                    id="location"
                    v-model="form.location_text"
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
                                    !form.expires_at && 'text-muted-foreground',
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
    </div>
</template>
