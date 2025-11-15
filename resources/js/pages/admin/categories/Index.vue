<script setup lang="ts">
import { computed, ref } from 'vue';
import { useToast } from 'vue-toastification';

// The new reusable component
import ResourceIndex from '@/components/resource/Index.vue';
import { Badge } from '@/components/ui/badge';

// --- Types (Copied from your old Category Index) ---
interface TranslatableString {
    en: string;
    de: string;
}
interface CategoryParent {
    id: number;
    name: TranslatableString;
}
interface Category {
    id: number;
    name: TranslatableString;
    is_active: boolean;
    parent: CategoryParent | null;
    children: any[];
    type: string;
}
interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

// --- Props ---
const props = defineProps<{
    categories: {
        data: Category[];
        links: PaginationLink[];
    };
}>();

const toast = useToast();

// --- State ---
const categorySearch = ref('');

// --- Table Column Definition ---
const columns = [
    { key: 'name.de', label: 'Name (DE)', class: 'font-medium' },
    { key: 'parent', label: 'Parent' },
    { key: 'type', label: 'Type' },
    { key: 'children', label: 'Children' },
    { key: 'is_active', label: 'Status' },
];

// --- Computed Filter ---
const filteredCategories = computed(() =>
    props.categories.data.filter(
        (category) =>
            category.name.de
                .toLowerCase()
                .includes(categorySearch.value.toLowerCase()) ||
            category.name.en
                .toLowerCase()
                .includes(categorySearch.value.toLowerCase()) ||
            (category.parent &&
                category.parent.name.de
                    .toLowerCase()
                    .includes(categorySearch.value.toLowerCase())),
    ),
);

// --- CRUD Handlers (Stubs) ---
const handleCreateCategory = () => {
    toast.info('Create category form logic goes here.');
};

const handleEditCategory = (category: Category) => {
    toast.info(`Editing: ${category.name.de}. Form logic goes here.`);
};

const handleDeleteCategory = (id: number) => {
    toast.warning(`Confirm delete for ID: ${id}. Confirm logic goes here.`);
};
</script>

<template>
    <ResourceIndex
        pageTitle="Manage Categories"
        :title="$t('admin.dashboard.categories_title')"
        :description="$t('admin.dashboard.categories_desc')"
        :search-placeholder="
            $t('admin.dashboard.search_categories_placeholder')
        "
        :empty-state-message="$t('admin.dashboard.no_categories_found')"
        :columns="columns"
        :items="filteredCategories"
        :pagination-links="props.categories.links"
        v-model="categorySearch"
        @create="handleCreateCategory"
        @edit="handleEditCategory"
        @delete="handleDeleteCategory"
    >
        <template #cell-parent="{ item }">
            <span v-if="item.parent" class="text-muted-foreground">
                {{ item.parent.name.de }}
            </span>
            <span v-else class="text-muted-foreground">-</span>
        </template>

        <template #cell-type="{ item }">
            <Badge variant="outline">{{ item.type }}</Badge>
        </template>

        <template #cell-children="{ item }">
            {{ item.children.length }}
        </template>

        <template #cell-is_active="{ item }">
            <Badge :variant="item.is_active ? 'secondary' : 'outline'">
                <span v-if="item.is_active">
                    {{ $t('admin.dashboard.active') }}
                </span>
                <span v-else>
                    {{ $t('admin.dashboard.inactive') }}
                </span>
            </Badge>
        </template>
    </ResourceIndex>
</template>
