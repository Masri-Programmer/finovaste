<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { useWindowScroll, watchDebounced } from '@vueuse/core';
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

// Import shadcn/ui components
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

// Import icons
import { Search } from 'lucide-vue-next';

// Define props
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

// --- Initialize composables ---
const { t } = useI18n();

// --- Local state for filters ---
// Initialize from props, allowing local modification
const activeCategory = ref(props.currentFilters.category || 'all');
const searchTerm = ref(props.currentFilters.search || '');

// --- VueUse: Sticky Header ---
const { y } = useWindowScroll();
const isSticky = ref(false);
watch(y, (newY) => {
    // Make bar sticky after scrolling 10px
    isSticky.value = newY > 10;
});

/**
 * --- Inertia: Apply Filters ---
 * This function is called to update the marketplace results.
 */
function applyFilters() {
    // @ts-ignore - Assuming 'route' is globally available via Ziggy
    router.get(
        route('marketplace.index'),
        {
            // Pass filters as query parameters
            category:
                activeCategory.value === 'all'
                    ? undefined
                    : activeCategory.value,
            search: searchTerm.value || undefined,
        },
        {
            // Preserve state and scroll for a smooth UX
            preserveState: true,
            preserveScroll: true,
        },
    );
}

// --- Event Handlers ---

/**
 * Selects a category and triggers a filter refresh.
 */
function selectCategory(categoryId: string) {
    activeCategory.value = categoryId;
    applyFilters();
}

/**
 * --- VueUse: Debounced Search ---
 * Automatically triggers a filter refresh 300ms after the user stops typing.
 */
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
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-1 overflow-x-auto">
                <Button
                    :variant="activeCategory === 'all' ? 'secondary' : 'ghost'"
                    class="flex-shrink-0 rounded-full"
                    @click="selectCategory('all')"
                >
                    {{ t('categories.all') }}
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
                    {{ t(cat.i18nKey) }}
                    <Badge variant="outline" class="ml-2">
                        {{ cat.count }}
                    </Badge>
                </Button>
            </div>

            <div class="relative flex-shrink-0">
                <Search
                    class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="searchTerm"
                    type="search"
                    :placeholder="t('marketplace.searchPlaceholder')"
                    class="rounded-full pl-9"
                />
            </div>
        </div>
    </nav>
</template>

<style scoped>
/* Hide scrollbar for the horizontal category list */
.overflow-x-auto::-webkit-scrollbar {
    display: none;
}
.overflow-x-auto {
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}
</style>
