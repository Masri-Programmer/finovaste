<script setup lang="ts">
import { router, useForm, usePage } from '@inertiajs/vue3';
import { useToggle } from '@vueuse/core';
import { trans } from 'laravel-vue-i18n';
import { computed, ref } from 'vue';
import { useToast } from 'vue-toastification';

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
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    // DialogDescription, // No longer needed
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

// Import lucide-vue-next icons
import { DollarSign, Package, TrendingUp, Users } from 'lucide-vue-next';

// --- Type Definitions (from React component) ---
// Kept for Users, Categories, Settings, and Stats
interface User {
    id: string;
    email: string;
    name: string;
    role: 'user' | 'admin';
    emailConfirmed: boolean;
    joinedDate: string;
    totalSpent: number;
    status: 'active' | 'suspended';
}

interface Category {
    id: string;
    name: string;
    slug: string;
    description: string;
    icon: string;
    listingCount: number;
    isActive: boolean;
}

interface Listing {
    id: string;
    title: string;
    description: string;
    price: number;
    image: string;
    category: string;
    type: 'buy' | 'invest' | 'bid' | 'donate';
    location: string;
    rating: number;
    reviews: number;
    investmentProgress?: number;
    investmentGoal?: number;
    investors?: number;
    currentBid?: number;
    timeLeft?: string;
}

interface Settings {
    minPrice: number;
    maxPrice: number;
    minInvestment: number;
    maxInvestment: number;
    commissionRate: number;
    featuredListingsLimit: number;
    auctionDurations: string[];
    enableNotifications: boolean;
    enableEmailAlerts: boolean;
    maintenanceMode: boolean;
}

// --- Inertia Props ---
interface AdminDashboardProps {
    listings: Listing[];
    users: User[];
    categories: Category[];
    settings: Settings;
    auth: {
        user: {
            id: string;
            name: string;
            email: string;
            role: 'user' | 'admin';
        };
    };
}

// Use hardcoded data as fallback
const props = withDefaults(defineProps<AdminDashboardProps>(), {
    // ... (listings default remains for stats)
    listings: () => [
        // ... hardcoded listings ...
    ],
    // ... (users default)
    users: () => [
        // ... hardcoded users ...
    ],
    // ... (categories default)
    categories: () => [
        // ... hardcoded categories ...
    ],
    // ... (settings default)
    settings: () => ({
        // ... hardcoded settings ...
    }),
    // ... (auth default)
    auth: () => ({
        // ... hardcoded auth ...
    }),
});

// --- Composables ---
const toast = useToast();
const page = usePage<AdminDashboardProps>();
const user = computed(() => page.props.auth?.user || props.auth.user);

// --- State Management ---
// Dialog Toggles
const [userDialogOpen, toggleUserDialog] = useToggle(false);
const [categoryDialogOpen, toggleCategoryDialog] = useToggle(false);
const [deleteDialogOpen, toggleDeleteDialog] = useToggle(false);

// Editing State
const editingUser = ref<User | null>(null);
const editingCategory = ref<Category | null>(null);
const deleteTarget = ref<{
    type: 'user' | 'category'; // 'listing' removed
    id: string;
} | null>(null);

// Search State
const userSearch = ref('');
const categorySearch = ref('');

// --- Inertia Forms ---
// listingForm removed

const userForm = useForm({
    id: null as string | null,
    name: '',
    email: '',
    role: 'user' as User['role'],
    status: 'active' as User['status'],
});

const categoryForm = useForm({
    id: null as string | null,
    name: '',
    slug: '',
    description: '',
    icon: '',
    isActive: true,
});

const settingsForm = useForm(props.settings);

// --- Computed Properties ---
// filteredListings removed

const filteredUsers = computed(() =>
    props.users.filter(
        (user) =>
            user.name.toLowerCase().includes(userSearch.value.toLowerCase()) ||
            user.email.toLowerCase().includes(userSearch.value.toLowerCase()),
    ),
);

const filteredCategories = computed(() =>
    props.categories.filter(
        (category) =>
            category.name
                .toLowerCase()
                .includes(categorySearch.value.toLowerCase()) ||
            category.description
                .toLowerCase()
                .includes(categorySearch.value.toLowerCase()),
    ),
);

// Stats
const stats = computed(() => ({
    totalListings: props.listings.length, // Stays for stat card
    totalUsers: props.users.length,
    totalRevenue: props.users.reduce((sum, u) => sum + u.totalSpent, 0),
    activeListings: props.listings.filter(
        (l) => l.type === 'buy' || l.type === 'bid',
    ).length, // Stays for stat card
}));

// --- CRUD Handlers (Listings) ---
// handleCreateListing, handleEditListing, saveListing removed

// --- CRUD Handlers (Users) ---
const handleCreateUser = () => {
    userForm.reset();
    editingUser.value = null;
    toggleUserDialog(true);
};

const handleEditUser = (user: User) => {
    userForm.defaults(user).reset();
    editingUser.value = user;
    toggleUserDialog(true);
};

const saveUser = () => {
    // ... (saveUser logic)
    if (editingUser.value) {
        userForm.put(
            route('web.admin.users.update', { id: editingUser.value.id }),
            {
                onSuccess: () => {
                    toggleUserDialog(false);
                    toast.success(trans('admin.dashboard.user_saved'));
                },
                preserveScroll: true,
            },
        );
    } else {
        userForm.post(route('web.admin.users.store'), {
            onSuccess: () => {
                toggleUserDialog(false);
                toast.success(trans('admin.dashboard.user_created'));
            },
            preserveScroll: true,
        });
    }
};

// --- CRUD Handlers (Categories) ---
const handleCreateCategory = () => {
    categoryForm.reset();
    editingCategory.value = null;
    toggleCategoryDialog(true);
};

const handleEditCategory = (category: Category) => {
    categoryForm.defaults(category).reset();
    editingCategory.value = category;
    toggleCategoryDialog(true);
};

const saveCategory = () => {
    // ... (saveCategory logic)
    const routeName = editingCategory.value
        ? 'web.admin.categories.update'
        : 'web.admin.categories.store';

    const params = editingCategory.value
        ? { id: editingCategory.value.id }
        : {};

    categoryForm
        .transform((data) => ({
            ...data,
            slug: data.slug || data.name.toLowerCase().replace(/\s+/g, '-'),
        }))
        .post(route(routeName, params), {
            // Using .post() for PUT/POST simplicity
            onSuccess: () => {
                toggleCategoryDialog(false);
                toast.success(trans('admin.dashboard.category_saved'));
            },
            preserveScroll: true,
        });
};

// --- Settings Handler ---
const saveSettings = () => {
    // ... (saveSettings logic)
    settingsForm.post(route('web.admin.settings.update'), {
        onSuccess: () => {
            toast.success(trans('admin.dashboard.settings_saved'));
        },
        onError: () => {
            toast.error(trans('admin.dashboard.save_error'));
        },
        preserveScroll: true,
    });
};

// --- Delete Handler ---
const handleDelete = (type: 'user' | 'category', id: string) => {
    // 'listing' type removed
    deleteTarget.value = { type, id };
    toggleDeleteDialog(true);
};

const confirmDelete = () => {
    if (!deleteTarget.value) return;

    let deleteRoute: string;
    switch (deleteTarget.value.type) {
        // 'listing' case removed
        case 'user':
            deleteRoute = route('web.admin.users.destroy', {
                id: deleteTarget.value.id,
            });
            break;
        case 'category':
            deleteRoute = route('web.admin.categories.destroy', {
                id: deleteTarget.value.id,
            });
            break;
    }

    router.delete(deleteRoute, {
        onSuccess: () => {
            toast.success(trans('admin.dashboard.item_deleted'));
            toggleDeleteDialog(false);
        },
        onError: () => {
            toast.error(trans('admin.dashboard.delete_error'));
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="mb-2 text-3xl font-bold text-foreground">
                {{ $t('admin.dashboard.title') }}
            </h1>
            <p class="text-muted-foreground">
                {{ $t('admin.dashboard.subtitle') }}
            </p>
        </div>

        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-4">
            <Card>
                <CardHeader
                    class="flex flex-row items-center justify-between pb-2"
                >
                    <CardTitle class="text-sm font-medium">
                        {{ $t('admin.dashboard.stats_total_listings') }}
                    </CardTitle>
                    <Package class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">
                        {{ stats.totalListings }}
                    </div>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ $t('admin.dashboard.stats_listings_desc') }}
                    </p>
                </CardContent>
            </Card>
            <Card>
                <CardHeader
                    class="flex flex-row items-center justify-between pb-2"
                >
                    <CardTitle class="text-sm font-medium">
                        {{ $t('admin.dashboard.stats_total_users') }}
                    </CardTitle>
                    <Users class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stats.totalUsers }}</div>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ $t('admin.dashboard.stats_users_desc') }}
                    </p>
                </CardContent>
            </Card>
            <Card>
                <CardHeader
                    class="flex flex-row items-center justify-between pb-2"
                >
                    <CardTitle class="text-sm font-medium">
                        {{ $t('admin.dashboard.stats_total_revenue') }}
                    </CardTitle>
                    <DollarSign class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">
                        ${{ stats.totalRevenue.toLocaleString() }}
                    </div>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ $t('admin.dashboard.stats_revenue_desc') }}
                    </p></CardContent
                >
            </Card>
            <Card>
                <CardHeader
                    class="flex flex-row items-center justify-between pb-2"
                >
                    <CardTitle class="text-sm font-medium">
                        {{ $t('admin.dashboard.stats_active_listings') }}
                    </CardTitle>
                    <TrendingUp class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">
                        {{ stats.activeListings }}
                    </div>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ $t('admin.dashboard.stats_active_desc') }}
                    </p>
                </CardContent>
            </Card>
        </div>
    </div>

    <Dialog :open="userDialogOpen" @update:open="toggleUserDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>
                    {{
                        editingUser
                            ? $t('admin.dashboard.edit_user_title')
                            : $t('admin.dashboard.create_user_title')
                    }}
                </DialogTitle>
            </DialogHeader>
            <form @submit.prevent="saveUser" class="space-y-4 py-4">
                <div class="space-y-2">
                    <Label for="name">{{
                        $t('admin.dashboard.form_label_name')
                    }}</Label>
                    <Input id="name" v-model="userForm.name" />
                    <span
                        v-if="userForm.errors.name"
                        class="text-sm text-destructive"
                    >
                        {{ userForm.errors.name }}
                    </span>
                </div>
                <div class="space-y-2">
                    <Label for="email">{{
                        $t('admin.dashboard.form_label_email')
                    }}</Label>
                    <Input id="email" type="email" v-model="userForm.email" />
                    <span
                        v-if="userForm.errors.email"
                        class="text-sm text-destructive"
                    >
                        {{ userForm.errors.email }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="role">{{
                            $t('admin.dashboard.form_label_role')
                        }}</Label>
                        <Select v-model="userForm.role">
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="user">User</SelectItem>
                                <SelectItem value="admin">Admin</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="space-y-2">
                        <Label for="status">{{
                            $t('admin.dashboard.form_label_status')
                        }}</Label>
                        <Select v-model="userForm.status">
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="active">Active</SelectItem>
                                <SelectItem value="suspended"
                                    >Suspended</SelectItem
                                >
                            </SelectContent>
                        </Select>
                    </div>
                </div>
                <DialogFooter>
                    <Button
                        variant="outline"
                        type="button"
                        @click="toggleUserDialog(false)"
                    >
                        {{ $t('admin.dashboard.button_cancel') }}
                    </Button>
                    <Button type="submit" :disabled="userForm.processing">
                        {{
                            editingUser
                                ? $t('admin.dashboard.button_update')
                                : $t('admin.dashboard.button_create')
                        }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <AlertDialog :open="deleteDialogOpen" @update:open="toggleDeleteDialog">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>
                    {{ $t('admin.dashboard.delete_confirm_title') }}
                </AlertDialogTitle>
                <AlertDialogDescription>
                    {{
                        $t('admin.dashboard.delete_confirm_desc', {
                            item: deleteTarget?.type,
                        })
                    }}
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="toggleDeleteDialog(false)">
                    {{ $t('admin.dashboard.button_cancel') }}
                </AlertDialogCancel>
                <AlertDialogAction
                    @click="confirmDelete"
                    class="bg-destructive hover:bg-destructive/90"
                >
                    {{ $t('admin.dashboard.button_delete') }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
