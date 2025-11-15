<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

// Layout
import AppLayout from '@/layouts/AppLayout.vue';

// UI Components
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Eye, Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';

// --- Types ---

interface Column {
    key: string;
    label: string;
    class?: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

// --- Props ---

const props = withDefaults(
    defineProps<{
        // --- Page & Card ---
        pageTitle: string;
        title: string;
        description: string;

        // --- Search ---
        modelValue: string; // For v-model:search
        searchPlaceholder?: string;

        // --- Table ---
        columns: Column[];
        items: any[];
        emptyStateMessage?: string;

        // --- Actions ---
        showCreateButton?: boolean;
        showViewAction?: boolean;
        showEditAction?: boolean;
        showDeleteAction?: boolean;

        // --- Pagination ---
        paginationLinks: PaginationLink[];
    }>(),
    {
        // Default values
        searchPlaceholder: 'Search items...',
        emptyStateMessage: 'No items found.',
        showCreateButton: true,
        showViewAction: false, // Defaulting to false as it's less common
        showEditAction: true,
        showDeleteAction: true,
    },
);

// --- Emits ---

const emit = defineEmits([
    'update:modelValue', // For v-model:search
    'create',
    'view',
    'edit',
    'delete',
]);

// --- Computed ---

/**
 * Computed property to proxy v-model for the search input.
 */
const searchModel = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

/**
 * Helper to get nested property values (e.g., 'name.de') for default cell rendering.
 */
const getProperty = (item: any, path: string) => {
    return path.split('.').reduce((acc, part) => acc && acc[part], item);
};
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout>
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <Card>
                <CardHeader>
                    <div
                        class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
                    >
                        <div>
                            <CardTitle>{{ title }}</CardTitle>
                            <CardDescription>
                                {{ description }}
                            </CardDescription>
                        </div>
                        <Button v-if="showCreateButton" @click="emit('create')">
                            <Plus class="mr-2 h-4 w-4" />
                            {{ $t('admin.dashboard.add_new') }}
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="relative mb-6">
                        <Search
                            class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-muted-foreground"
                        />
                        <Input
                            v-model="searchModel"
                            :placeholder="searchPlaceholder"
                            class="pl-10"
                        />
                    </div>

                    <div class="overflow-hidden rounded-lg border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead
                                        v-for="col in columns"
                                        :key="col.key"
                                        :class="col.class"
                                    >
                                        {{ col.label }}
                                    </TableHead>
                                    <TableHead class="text-right">
                                        {{
                                            $t(
                                                'admin.dashboard.table_header_actions',
                                            )
                                        }}
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <template v-if="items.length > 0">
                                    <TableRow
                                        v-for="item in items"
                                        :key="item.id"
                                    >
                                        <TableCell
                                            v-for="col in columns"
                                            :key="col.key"
                                            :class="col.class"
                                        >
                                            <slot
                                                :name="`cell-${col.key}`"
                                                :item="item"
                                            >
                                                {{ getProperty(item, col.key) }}
                                            </slot>
                                        </TableCell>

                                        <TableCell class="text-right">
                                            <slot name="actions" :item="item">
                                                <div
                                                    class="flex justify-end gap-2"
                                                >
                                                    <Button
                                                        v-if="showViewAction"
                                                        variant="ghost"
                                                        size="sm"
                                                        @click="
                                                            emit('view', item)
                                                        "
                                                    >
                                                        <Eye class="h-4 w-4" />
                                                    </Button>
                                                    <Button
                                                        v-if="showEditAction"
                                                        variant="ghost"
                                                        size="sm"
                                                        @click="
                                                            emit('edit', item)
                                                        "
                                                    >
                                                        <Pencil
                                                            class="h-4 w-4"
                                                        />
                                                    </Button>
                                                    <Button
                                                        v-if="showDeleteAction"
                                                        variant="ghost"
                                                        size="sm"
                                                        @click="
                                                            emit(
                                                                'delete',
                                                                item.id,
                                                            )
                                                        "
                                                    >
                                                        <Trash2
                                                            class="h-4 w-4 text-destructive"
                                                        />
                                                    </Button>
                                                </div>
                                            </slot>
                                        </TableCell>
                                    </TableRow>
                                </template>
                                <TableRow v-else>
                                    <TableCell
                                        :col-span="columns.length + 1"
                                        class="py-8 text-center text-muted-foreground"
                                    >
                                        {{ emptyStateMessage }}
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>

                <CardFooter
                    v-if="paginationLinks.length > 3"
                    class="flex justify-center border-t pt-4"
                >
                    <nav class="flex gap-1">
                        <Link
                            v-for="(link, index) in paginationLinks"
                            :key="index"
                            :href="link.url ?? '#'"
                            class="inline-flex h-9 min-w-9 items-center justify-center rounded-md px-3 py-2 text-sm"
                            :class="{
                                'bg-primary text-primary-foreground':
                                    link.active,
                                'text-muted-foreground hover:bg-accent':
                                    !link.active && link.url,
                                'cursor-not-allowed opacity-50': !link.url,
                            }"
                            v-html="link.label"
                        />
                    </nav>
                </CardFooter>
            </Card>
        </div>
    </AppLayout>
</template>
