<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
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
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';

// --- Types & Props ---
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

const props = withDefaults(
    defineProps<{
        resource: string; // e.g., 'Listings', 'Users'
        columns: Column[];
        items: any[];
        paginationLinks: PaginationLink[];
        modelValue: string;
        createRoute?: string;
        viewRoute?: (item: any) => string;
        editRoute?: (item: any) => string;
        deleteRoute?: (item: any) => string;
        showCreateButton?: boolean;
        showViewAction?: boolean;
        showEditAction?: boolean;
        showDeleteAction?: boolean;
        deleteLabelKey?: string;
    }>(),
    {
        showCreateButton: true,
        showViewAction: true,
        showEditAction: true,
        showDeleteAction: true,
        deleteLabelKey: 'id',
    },
);

const emit = defineEmits(['update:modelValue', 'delete-success']);

// --- State & Logic ---
const deleteDialogOpen = ref(false);
const itemToDelete = ref<any | null>(null);

const searchModel = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

// Singularize for the "Add" button (e.g. "Users" -> "User")
const resourceSingular = computed(() => {
    return props.resource.endsWith('s')
        ? props.resource.slice(0, -1)
        : props.resource;
});

const getProperty = (item: any, path: string) =>
    path.split('.').reduce((acc, part) => acc && acc[part], item);

const promptDelete = (item: any) => {
    itemToDelete.value = item;
    deleteDialogOpen.value = true;
};

const toggleDeleteDialog = (value: boolean) => {
    deleteDialogOpen.value = value;
    if (!value) setTimeout(() => (itemToDelete.value = null), 300);
};

const confirmDelete = () => {
    if (itemToDelete.value && props.deleteRoute) {
        router.delete(props.deleteRoute(itemToDelete.value), {
            preserveScroll: true,
            onSuccess: () => {
                toggleDeleteDialog(false);
                emit('delete-success');
            },
            onFinish: () => toggleDeleteDialog(false),
        });
    } else {
        toggleDeleteDialog(false);
    }
};
</script>

<template>
    <Head :title="$t('admin.dashboard.index.title', { resource: resource })" />

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
                            <CardTitle class="capitalize">
                                {{
                                    $t('admin.dashboard.index.title', {
                                        resource: resource,
                                    })
                                }}
                            </CardTitle>
                            <CardDescription>
                                {{
                                    $t('admin.dashboard.index.description', {
                                        resource: resource,
                                    })
                                }}
                            </CardDescription>
                        </div>

                        <div v-if="showCreateButton">
                            <Link v-if="createRoute" :href="createRoute">
                                <Button>
                                    <Plus class="mr-2 h-4 w-4" />
                                    <span class="capitalize">
                                        {{
                                            $t(
                                                'admin.dashboard.actions.add_resource',
                                                { resource: resourceSingular },
                                            )
                                        }}
                                    </span>
                                </Button>
                            </Link>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="relative mb-6">
                        <Search
                            class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-muted-foreground"
                        />
                        <Input
                            v-model="searchModel"
                            :placeholder="
                                $t(
                                    'admin.dashboard.actions.search_placeholder',
                                    { resource: resource },
                                )
                            "
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
                                                'admin.dashboard.table.actions_header',
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
                                                    <Link
                                                        v-if="
                                                            showViewAction &&
                                                            viewRoute
                                                        "
                                                        :href="viewRoute(item)"
                                                    >
                                                        <Button
                                                            variant="ghost"
                                                            size="sm"
                                                            ><Eye
                                                                class="h-4 w-4"
                                                        /></Button>
                                                    </Link>
                                                    <Link
                                                        v-if="
                                                            showEditAction &&
                                                            editRoute
                                                        "
                                                        :href="editRoute(item)"
                                                    >
                                                        <Button
                                                            variant="ghost"
                                                            size="sm"
                                                            ><Pencil
                                                                class="h-4 w-4"
                                                        /></Button>
                                                    </Link>
                                                    <Button
                                                        v-if="showDeleteAction"
                                                        variant="ghost"
                                                        size="sm"
                                                        @click="
                                                            promptDelete(item)
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
                                        {{
                                            $t('admin.dashboard.states.empty', {
                                                resource: resource,
                                            })
                                        }}
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
                                'opacity-50': !link.url,
                            }"
                            v-html="link.label"
                        />
                    </nav>
                </CardFooter>
            </Card>
        </div>

        <AlertDialog :open="deleteDialogOpen" @update:open="toggleDeleteDialog">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>{{
                        $t('admin.dashboard.dialogs.delete.title')
                    }}</AlertDialogTitle>
                    <AlertDialogDescription>
                        {{
                            $t('admin.dashboard.dialogs.delete.description', {
                                item: itemToDelete
                                    ? getProperty(itemToDelete, deleteLabelKey)
                                    : 'Item',
                            })
                        }}
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel @click="toggleDeleteDialog(false)">
                        {{ $t('actions.cancel') }}
                    </AlertDialogCancel>
                    <AlertDialogAction
                        @click="confirmDelete"
                        class="bg-destructive hover:bg-destructive/90"
                    >
                        {{ $t('actions.delete') }}
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>
