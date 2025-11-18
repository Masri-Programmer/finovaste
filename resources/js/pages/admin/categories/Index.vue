<script setup lang="ts">
import { computed, ref } from 'vue';
import { useToast } from 'vue-toastification';

// The new reusable component
import ResourceIndex from '@/components/resource/Index.vue';
import { Badge } from '@/components/ui/badge';
import { create, destroy, edit, show } from '@/routes/admin/categories';

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
</script>

<template>
    <ResourceIndex
        resource="categories"
        :columns="columns"
        :items="filteredCategories"
        :pagination-links="props.categories.links"
        v-model="categorySearch"
        :create-route="create.url()"
        :view-route="(category) => show.url(category.id)"
        :edit-route="(category) => edit.url(category.id)"
        :delete-route="(category) => destroy.url(category.id)"
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
                    {{ $t('admin.dashboard.states.active') }}
                </span>
                <span v-else>
                    {{ $t('admin.dashboard.states.inactive') }}
                </span>
            </Badge>
        </template>
    </ResourceIndex>
</template>
