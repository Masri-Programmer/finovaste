<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useStorage, useToggle } from '@vueuse/core';
import { trans } from 'laravel-vue-i18n';
import { Search, SlidersHorizontal, X } from 'lucide-vue-next';
import { watch } from 'vue';
import { useToast } from 'vue-toastification';

// Shadcn-Vue Components
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { Slider } from '@/components/ui/slider';

// 1. Define Props & Emits
const emit = defineEmits<{
    (e: 'filterChange', filters: Record<string, any>): void;
}>();

// 2. Setup Hooks
const toast = useToast();

// 3. VueUse: useToggle
const [showAdvanced, toggleAdvanced] = useToggle(false);

// 4. VueUse: useStorage
const storedSearchTerm = useStorage('global-search-filter', '');

// 5. State Management
const form = useForm({
    search: storedSearchTerm.value,
    category: 'all',
    listingTypes: [] as string[],
    priceRange: [0, 1000000],
    location: '',
    sortBy: 'recent',
});

watch(
    () => form.search,
    (newSearch) => {
        storedSearchTerm.value = newSearch;
    },
);

// Static data
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
    emit('filterChange', form.data());
    toggleAdvanced(false);
    // Optional: toast.success(trans('filters.toast.applied'));
}
</script>

<template>
    <div class="mb-4 rounded-lg border bg-card p-4 shadow-sm">
        <div class="relative flex items-center gap-2">
            <div class="relative flex-1">
                <Search
                    class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="form.search"
                    :placeholder="$t('filters.searchPlaceholder')"
                    class="pl-10"
                />
            </div>

            <Sheet v-model:open="showAdvanced">
                <SheetTrigger as-child>
                    <Button
                        variant="outline"
                        size="icon"
                        :class="{
                            'border-primary/50 bg-secondary': showAdvanced,
                        }"
                        :aria-label="$t('filters.advancedTitle')"
                    >
                        <SlidersHorizontal class="h-4 w-4" />
                    </Button>
                </SheetTrigger>

                <SheetContent
                    side="left"
                    class="flex h-full w-[320px] flex-col gap-0 border-r-border p-0 sm:w-[400px]"
                >
                    <SheetHeader class="border-b border-border/50 p-6">
                        <SheetTitle class="text-xl">{{
                            $t('filters.advancedTitle')
                        }}</SheetTitle>
                        <SheetDescription>
                            {{ $t('filters.advancedDescription') }}
                        </SheetDescription>
                    </SheetHeader>

                    <ScrollArea class="flex-1">
                        <div class="flex flex-col gap-6 p-6">
                            <div class="space-y-2.5">
                                <Label
                                    for="category"
                                    class="text-sm font-semibold"
                                    >{{ $t('filters.category') }}</Label
                                >
                                <Select id="category" v-model="form.category">
                                    <SelectTrigger>
                                        <SelectValue
                                            :placeholder="
                                                $t('filters.allCategories')
                                            "
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

                            <div class="space-y-2.5">
                                <Label class="text-sm font-semibold">{{
                                    $t('filters.listingType')
                                }}</Label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div
                                        v-for="type in listingTypes"
                                        :key="type.id"
                                        class="flex items-center space-x-2"
                                    >
                                        <Checkbox
                                            :id="type.id"
                                            :checked="
                                                form.listingTypes.includes(
                                                    type.id,
                                                )
                                            "
                                            @update:checked="
                                                (checked) =>
                                                    handleTypeChange(
                                                        checked,
                                                        type.id,
                                                    )
                                            "
                                        />
                                        <label
                                            :for="type.id"
                                            class="cursor-pointer text-sm leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                        >
                                            {{ $t(type.labelKey) }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <Label class="text-sm font-semibold">{{
                                        $t('filters.priceRange')
                                    }}</Label>
                                    <Badge
                                        variant="outline"
                                        class="font-mono font-normal"
                                    >
                                        €{{
                                            form.priceRange[0].toLocaleString(
                                                'de-DE',
                                            )
                                        }}
                                        - €{{
                                            form.priceRange[1].toLocaleString(
                                                'de-DE',
                                            )
                                        }}
                                    </Badge>
                                </div>
                                <Slider
                                    v-model="form.priceRange"
                                    :max="1000000"
                                    :step="10000"
                                    class="pt-2"
                                />
                            </div>

                            <div class="space-y-2.5">
                                <Label
                                    for="location"
                                    class="text-sm font-semibold"
                                    >{{ $t('filters.location') }}</Label
                                >
                                <Input
                                    id="location"
                                    v-model="form.location"
                                    :placeholder="
                                        $t('filters.locationPlaceholder')
                                    "
                                />
                            </div>

                            <div class="space-y-2.5">
                                <Label
                                    for="sort-by"
                                    class="text-sm font-semibold"
                                    >{{ $t('filters.sortBy') }}</Label
                                >
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
                                class="flex flex-wrap gap-2 pt-2"
                            >
                                <Badge
                                    v-for="typeId in form.listingTypes"
                                    :key="typeId"
                                    variant="secondary"
                                    class="gap-1.5 px-2 py-1"
                                >
                                    {{ getListingTypeLabel(typeId) }}
                                    <X
                                        class="h-3 w-3 cursor-pointer transition-colors hover:text-destructive"
                                        @click="removeType(typeId)"
                                    />
                                </Badge>
                            </div>
                        </div>
                    </ScrollArea>

                    <SheetFooter
                        class="mt-auto border-t border-border/50 bg-muted/10 p-6"
                    >
                        <Button
                            class="w-full"
                            size="lg"
                            :disabled="form.processing"
                            @click="applyFilters"
                        >
                            {{ $t('filters.apply') }}
                        </Button>
                    </SheetFooter>
                </SheetContent>
            </Sheet>
        </div>
    </div>
</template>
