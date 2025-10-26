<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { useWindowScroll, watchDebounced } from '@vueuse/core';
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

import { Search } from 'lucide-vue-next';
const props = defineProps<{
    categories: {
        id: string;
        i18nKey: string;
        count: number;
    }[];
    currentFilters: {
        category?: string;
        search?: string;
    };
}>();

const { t } = useI18n();

const activeCategory = ref(props.currentFilters.category || 'all');
const searchTerm = ref(props.currentFilters.search || '');

const { y } = useWindowScroll();
const isSticky = ref(false);
watch(y, (newY) => {
    isSticky.value = newY > 10;
});

function applyFilters() {
    router.get(
        route('marketplace.index'),
        {
            category:
                activeCategory.value === 'all'
                    ? undefined
                    : activeCategory.value,
            search: searchTerm.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}

function selectCategory(categoryId: string) {
    activeCategory.value = categoryId;
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
            'sticky top-0 z-40 bg-background/95 shadow-md backdrop-blur-sm':
                isSticky,
            'border-b border-border': !isSticky,
        }"
    >
        <div
            class="flex flex-col items-center justify-between gap-4 md:flex-row"
        >
            <div class="flex w-full items-center gap-1 overflow-x-auto">
                <Button
                    :variant="activeCategory === 'all' ? 'secondary' : 'ghost'"
                    class="flex-shrink-0 rounded-full"
                    @click="selectCategory('all')"
                >
                    {{ t('homepage.categories.all') }}
                    <Badge variant="outline" class="ml-2">
                        {{
                            categories.reduce((acc, cat) => acc + cat.count, 0)
                        }}
                    </Badge>
                </Button>

                <Button
                    v-for="cat in categories"
                    :key="cat.id"
                    :variant="activeCategory === cat.id ? 'secondary' : 'ghost'"
                    class="flex-shrink-0 rounded-full"
                    @click="selectCategory(cat.id)"
                >
                    {{ t(`homepage.${cat.i18nKey}`) }}
                    <Badge variant="outline" class="ml-2">
                        {{ cat.count }}
                    </Badge>
                </Button>
            </div>

            <div class="relative w-full flex-shrink-0 md:w-auto">
                <Search
                    class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="searchTerm"
                    type="search"
                    :placeholder="t('homepage.marketplace.searchPlaceholder')"
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
