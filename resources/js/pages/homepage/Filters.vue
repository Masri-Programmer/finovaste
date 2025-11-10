<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useWindowScroll, watchDebounced } from '@vueuse/core';
import { PropType, ref, watch } from 'vue';

import { Category } from '@/types';
import { getActiveLanguage } from 'laravel-vue-i18n';
import { Search } from 'lucide-vue-next';

const props = defineProps({
    categories: {
        type: Array as PropType<Category[]>,
        required: true,
    },
    currentFilters: {
        type: Object as PropType<{
            category?: string;
            search?: string;
        }>,
        required: true,
    },
});

const locale = ref(getActiveLanguage());
const activeCategory = ref(props.currentFilters.category || 'all');
const searchTerm = ref(props.currentFilters.search || '');

const { y } = useWindowScroll();
const isSticky = ref(false);
watch(y, (newY) => {
    isSticky.value = newY > 40;
});

function applyFilters() {
    // router.get(
    //     route('home'), // Assuming 'home' is the name of your HomeController@index route
    //     {
    //         category:
    //             activeCategory.value === 'all'
    //                 ? undefined
    //                 : activeCategory.value,
    //         search: searchTerm.value || undefined,
    //     },
    //     {
    //         preserveState: true,
    //         preserveScroll: true,
    //     },
    // );
}

// Updated to accept a 'slug' instead of an 'id'
function selectCategory(categorySlug: string) {
    activeCategory.value = categorySlug;
    applyFilters();
}

watchDebounced(
    searchTerm,
    () => {
        applyFilters();
    },
    { debounce: 300 },
);
</script>

<template>
    <nav
        class="w-full py-3 transition-all duration-200"
        :class="{
            'sticky top-0 z-40 bg-background/95 backdrop-blur-sm': isSticky,
            'border-b border-border': !isSticky,
        }"
    >
        <div
            class="flex flex-col flex-wrap items-center justify-between gap-4 md:flex-row"
        >
            <div
                class="flex w-full flex-wrap items-center gap-1 overflow-x-auto"
            >
                <Button
                    :variant="activeCategory === 'all' ? 'secondary' : 'ghost'"
                    class="flex-shrink-0 rounded-full"
                    @click="selectCategory('all')"
                >
                    {{ $t('categories.all') }}
                </Button>

                <Button
                    v-for="cat in categories"
                    :key="cat.id"
                    :variant="
                        activeCategory === cat.slug ? 'secondary' : 'ghost'
                    "
                    class="flex-shrink-0 rounded-full"
                    @click="selectCategory(cat.slug)"
                >
                    {{ cat.name[locale] || cat.name.de }}
                </Button>
            </div>

            <div class="relative w-full flex-shrink-0 md:w-auto">
                <Search
                    class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="searchTerm"
                    type="search"
                    :placeholder="$t('marketplace.searchPlaceholder')"
                    class="rounded-full pl-9"
                />
            </div>
        </div>
    </nav>
</template>

<style scoped>
.overflow-x-auto::-webkit-scrollbar {
    display: none;
}
.overflow-x-auto {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
