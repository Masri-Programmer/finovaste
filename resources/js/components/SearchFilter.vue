<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useStorage, useToggle } from '@vueuse/core';
import { Search, SlidersHorizontal, X } from 'lucide-vue-next';
import { watch } from 'vue';
import { useToast } from 'vue-toastification';

// Shadcn-Vue Components
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Slider } from '@/components/ui/slider';
import { trans } from 'laravel-vue-i18n';

// 1. Define Props & Emits
const emit = defineEmits<{
    (e: 'filterChange', filters: Record<string, any>): void;
}>();

// 2. Setup Hooks
const toast = useToast();

// 3. VueUse: useToggle for advanced search
const [showAdvanced, toggleAdvanced] = useToggle(false);

// 4. VueUse: useStorage to persist search term
const storedSearchTerm = useStorage('global-search-filter', '');

// 5. State Management (Inertia Form + Local Data)
const form = useForm({
    search: storedSearchTerm.value,
    category: 'all',
    listingTypes: [] as string[],
    priceRange: [0, 1000000],
    location: '',
    sortBy: 'recent',
});

// Watch for changes in the form's search and update storage
watch(
    () => form.search,
    (newSearch) => {
        storedSearchTerm.value = newSearch;
    },
);

// Static data (driven by i18n keys)
const categories = [
    'filters.categories.properties',
    'filters.categories.vehicles',
    'filters.categories.furniture',
    'filters.categories.electronics',
    'filters.categories.art',
    'filters.categories.businesses',
    'filters.categories.startups',
];

const listingTypes = [
    { id: 'buy', labelKey: 'filters.types.buy' },
    { id: 'invest', labelKey: 'filters.types.invest' },
    { id: 'bid', labelKey: 'filters.types.bid' },
    { id: 'donate', labelKey: 'filters.types.donate' },
];

const sortOptions = [
    { id: 'recent', labelKey: 'filters.sortOptions.recent' },
    { id: 'price-low', labelKey: 'filters.sortOptions.priceLow' },
    { id: 'price-high', labelKey: 'filters.sortOptions.priceHigh' },
    { id: 'popular', labelKey: 'filters.sortOptions.popular' },
    { id: 'roi', labelKey: 'filters.sortOptions.roi' },
];

function handleTypeChange(checked: boolean, typeId: string) {
    if (checked) {
        if (!form.listingTypes.includes(typeId)) {
            form.listingTypes.push(typeId);
        }
    } else {
        form.listingTypes = form.listingTypes.filter((t) => t !== typeId);
    }
}

function removeType(typeId: string) {
    form.listingTypes = form.listingTypes.filter((t) => t !== typeId);
}

function getListingTypeLabel(typeId: string): string {
    const type = listingTypes.find((t) => t.id === typeId);
    return type ? trans(type.labelKey) : '';
}

function applyFilters() {
    //   form.get(route('listings.index'), {
    //     preserveState: true,
    //     preserveScroll: true,
    //     onSuccess: () => {
    //       // 8. Toast Notification
    //       toast.success($t('filters.toast.success'))
    //       emi$t('filterChange', form.data())
    //       toggleAdvanced(false) // Optionally close panel on success
    //     },
    //     onError: (errors) => {
    //       toast.error($t('filters.toast.error'))
    //       console.error('Filter error:', errors)
    //     },
    //   })
}
</script>

<template>
    <div class="mb-4 space-y-4 rounded-lg border bg-card p-4">
        <div class="relative">
            <Search
                class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
            />
            <Input
                v-model="form.search"
                :placeholder="$t('filters.searchPlaceholder')"
                class="pr-10 pl-10"
            />
            <Button
                variant="ghost"
                size="icon"
                class="absolute top-1/2 right-1 -translate-y-1/2"
                @click="toggleAdvanced()"
            >
                <SlidersHorizontal class="h-4 w-4" />
            </Button>
        </div>

        <div v-if="showAdvanced" class="space-y-4 border-t pt-4">
            <div class="space-y-2">
                <Label for="category">{{ $t('filters.category') }}</Label>
                <Select id="category" v-model="form.category">
                    <SelectTrigger>
                        <SelectValue
                            :placeholder="$t('filters.allCategories')"
                        />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">{{
                            $t('filters.allCategories')
                        }}</SelectItem>
                        <SelectItem
                            v-for="catKey in categories"
                            :key="catKey"
                            :value="catKey"
                        >
                            {{ $t(catKey) }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="space-y-2">
                <Label>{{ $t('filters.listingType') }}</Label>
                <div class="space-y-2">
                    <div
                        v-for="type in listingTypes"
                        :key="type.id"
                        class="flex items-center space-x-2"
                    >
                        <Checkbox
                            :id="type.id"
                            :checked="form.listingTypes.includes(type.id)"
                            @update:checked="
                                (checked: boolean) =>
                                    handleTypeChange(checked, type.id)
                            "
                        />
                        <label :for="type.id" class="cursor-pointer text-sm">
                            {{ $t(type.labelKey) }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <Label>{{ $t('filters.priceRange') }}</Label>
                <div class="pt-2">
                    <Slider
                        v-model="form.priceRange"
                        :max="1000000"
                        :step="10000"
                        class="mb-2"
                    />
                    <div
                        class="flex justify-between text-sm text-muted-foreground"
                    >
                        <span
                            >€{{
                                form.priceRange[0].toLocaleString('de-DE')
                            }}</span
                        >
                        <span
                            >€{{
                                form.priceRange[1].toLocaleString('de-DE')
                            }}</span
                        >
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <Label for="location">{{ $t('filters.location') }}</Label>
                <Input
                    id="location"
                    v-model="form.location"
                    :placeholder="$t('filters.locationPlaceholder')"
                />
            </div>

            <div class="space-y-2">
                <Label for="sort-by">{{ $t('filters.sortBy') }}</Label>
                <Select id="sort-by" v-model="form.sortBy">
                    <SelectTrigger>
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="opt in sortOptions"
                            :key="opt.id"
                            :value="opt.id"
                        >
                            {{ $t(opt.labelKey) }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div
                v-if="form.listingTypes.length > 0"
                class="flex flex-wrap gap-2"
            >
                <Badge
                    v-for="typeId in form.listingTypes"
                    :key="typeId"
                    variant="secondary"
                    class="gap-1.5"
                >
                    {{ getListingTypeLabel(typeId) }}
                    <X
                        class="h-3 w-3 cursor-pointer"
                        @click="removeType(typeId)"
                    />
                </Badge>
            </div>

            <Button
                class="w-full"
                :disabled="form.processing"
                @click="applyFilters"
            >
                {{ $t('filters.apply') }}
            </Button>
        </div>
    </div>
</template>
