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
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
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
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

// Import lucide-vue-next icons
import {
    DollarSign,
    Package,
    Pencil,
    Plus,
    Search,
    Trash2,
    TrendingUp,
    Users,
} from 'lucide-vue-next';

// --- Type Definitions (from React component) ---
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
// In a real FinoVaste app, data comes from Inertia props
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

// Use hardcoded data as fallback if props are not provided (for standalone demo)
const props = withDefaults(defineProps<AdminDashboardProps>(), {
    listings: () => [
        {
            id: '1',
            title: 'Modern Downtown Luxury Apartment',
            description: 'Stunning 3-bedroom apartment...',
            price: 25000,
            image: '...',
            category: 'Properties',
            type: 'invest',
            location: 'San Francisco, CA',
            rating: 4.8,
            reviews: 34,
            investmentProgress: 450000,
            investmentGoal: 750000,
            investors: 52,
        },
        {
            id: '2',
            title: '2024 Luxury Sports Car Collection',
            description: 'Rare collection...',
            price: 150000,
            image: '...',
            category: 'Vehicles',
            type: 'bid',
            location: 'Los Angeles, CA',
            rating: 4.9,
            reviews: 28,
            currentBid: 175000,
            timeLeft: '2d 14h',
        },
        {
            id: '3',
            title: 'Designer Furniture Collection',
            description: 'Premium mid-century...',
            price: 8500,
            image: '...',
            category: 'Furniture',
            type: 'buy',
            location: 'New York, NY',
            rating: 5.0,
            reviews: 12,
        },
    ],
    users: () => [
        {
            id: '1',
            email: 'admin@finovaste.com',
            name: 'Admin User',
            role: 'admin',
            emailConfirmed: true,
            joinedDate: '2024-01-15',
            totalSpent: 0,
            status: 'active',
        },
        {
            id: '2',
            email: 'john.doe@example.com',
            name: 'John Doe',
            role: 'user',
            emailConfirmed: true,
            joinedDate: '2024-02-20',
            totalSpent: 25000,
            status: 'active',
        },
    ],
    categories: () => [
        {
            id: '1',
            name: 'Properties',
            slug: 'properties',
            description: 'Real estate...',
            icon: '🏢',
            listingCount: 1,
            isActive: true,
        },
        {
            id: '2',
            name: 'Vehicles',
            slug: 'vehicles',
            description: 'Cars, motorcycles...',
            icon: '🚗',
            listingCount: 1,
            isActive: true,
        },
    ],
    settings: () => ({
        minPrice: 0,
        maxPrice: 10000000,
        minInvestment: 100,
        maxInvestment: 1000000,
        commissionRate: 5,
        featuredListingsLimit: 10,
        auctionDurations: ['1 day', '3 days', '7 days'],
        enableNotifications: true,
        enableEmailAlerts: true,
        maintenanceMode: false,
    }),
    auth: () => ({
        user: {
            id: '1',
            name: 'Admin User',
            email: 'admin@finovaste.com',
            role: 'admin',
        },
    }),
});

// --- Composables ---
const toast = useToast();
const page = usePage<AdminDashboardProps>();
const user = computed(() => page.props.auth?.user || props.auth.user);

// --- State Management ---
// Dialog Toggles (using @vueuse/useToggle)
const [listingDialogOpen, toggleListingDialog] = useToggle(false);
const [userDialogOpen, toggleUserDialog] = useToggle(false);
const [categoryDialogOpen, toggleCategoryDialog] = useToggle(false);
const [deleteDialogOpen, toggleDeleteDialog] = useToggle(false);

// Editing State
const editingListing = ref<Listing | null>(null);
const editingUser = ref<User | null>(null);
const editingCategory = ref<Category | null>(null);
const deleteTarget = ref<{
    type: 'listing' | 'user' | 'category';
    id: string;
} | null>(null);

// Search State
const listingSearch = ref('');
const userSearch = ref('');
const categorySearch = ref('');

// --- Inertia Forms ---
const listingForm = useForm({
    id: null as string | null,
    title: '',
    description: '',
    price: 0,
    category: 'Properties',
    type: 'buy' as Listing['type'],
    location: '',
    image: '',
});

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

// Note: Settings form uses `reactive` to bind to v-model,
// but will be sent via `router.post` in a real app.
// For this stack, we use `useForm`.
const settingsForm = useForm(props.settings);

// --- Computed Properties ---
// Filtered Data
const filteredListings = computed(() =>
    props.listings.filter(
        (listing) =>
            listing.title
                .toLowerCase()
                .includes(listingSearch.value.toLowerCase()) ||
            listing.category
                .toLowerCase()
                .includes(listingSearch.value.toLowerCase()) ||
            listing.location
                .toLowerCase()
                .includes(listingSearch.value.toLowerCase()),
    ),
);

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
    totalListings: props.listings.length,
    totalUsers: props.users.length,
    totalRevenue: props.users.reduce((sum, u) => sum + u.totalSpent, 0),
    activeListings: props.listings.filter(
        (l) => l.type === 'buy' || l.type === 'bid',
    ).length,
}));

// --- CRUD Handlers (Listings) ---
const handleCreateListing = () => {
    listingForm.reset();
    editingListing.value = null;
    toggleListingDialog(true);
};

const handleEditListing = (listing: Listing) => {
    listingForm.defaults(listing).reset();
    editingListing.value = listing;
    toggleListingDialog(true);
};

const saveListing = () => {
    const onSuccess = () => {
        toggleListingDialog(false);
        toast.success(trans('admin.dashboard.listing_saved'));
    };
    const onError = () => {
        // Errors will typically be handled by Inertia's error bag
        toast.error(trans('admin.dashboard.save_error'));
    };

    if (editingListing.value) {
        listingForm.put(
            route('web.admin.listings.update', { id: editingListing.value.id }),
            {
                onSuccess,
                onError,
                preserveScroll: true,
            },
        );
    } else {
        listingForm.post(route('web.admin.listings.store'), {
            onSuccess,
            onError,
            preserveScroll: true,
        });
    }
};

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
const handleDelete = (type: 'listing' | 'user' | 'category', id: string) => {
    deleteTarget.value = { type, id };
    toggleDeleteDialog(true);
};

const confirmDelete = () => {
    if (!deleteTarget.value) return;

    let deleteRoute: string;
    switch (deleteTarget.value.type) {
        case 'listing':
            deleteRoute = route('web.admin.listings.destroy', {
                id: deleteTarget.value.id,
            });
            break;
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

        <Tabs default-value="listings" class="w-full">
            <TabsList class="grid w-full max-w-3xl grid-cols-4">
                <TabsTrigger value="listings">
                    {{ $t('admin.dashboard.listings_tab') }}
                </TabsTrigger>
                <TabsTrigger value="users">
                    {{ $t('admin.dashboard.users_tab') }}
                </TabsTrigger>
                <TabsTrigger value="categories">
                    {{ $t('admin.dashboard.categories_tab') }}
                </TabsTrigger>
                <TabsTrigger value="settings">
                    {{ $t('admin.dashboard.settings_tab') }}
                </TabsTrigger>
            </TabsList>

            <TabsContent value="listings" class="mt-6">
                <Card>
                    <CardHeader>
                        <div
                            class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
                        >
                            <div>
                                <CardTitle>{{
                                    $t('admin.dashboard.listings_title')
                                }}</CardTitle>
                                <CardDescription>
                                    {{ $t('admin.dashboard.listings_desc') }}
                                </CardDescription>
                            </div>
                            <Button @click="handleCreateListing">
                                <Plus class="mr-2 h-4 w-4" />
                                {{ $t('admin.dashboard.add_listing_button') }}
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="relative mb-6">
                            <Search
                                class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-muted-foreground"
                            />
                            <Input
                                v-model="listingSearch"
                                :placeholder="
                                    $t(
                                        'admin.dashboard.search_listings_placeholder',
                                    )
                                "
                                class="pl-10"
                            />
                        </div>
                        <div class="overflow-hidden rounded-lg border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_title',
                                            )
                                        }}</TableHead>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_category',
                                            )
                                        }}</TableHead>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_type',
                                            )
                                        }}</TableHead>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_price',
                                            )
                                        }}</TableHead>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_location',
                                            )
                                        }}</TableHead>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_rating',
                                            )
                                        }}</TableHead>
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
                                    <template
                                        v-if="filteredListings.length > 0"
                                    >
                                        <TableRow
                                            v-for="listing in filteredListings"
                                            :key="listing.id"
                                        >
                                            <TableCell
                                                class="max-w-xs truncate font-medium"
                                            >
                                                {{ listing.title }}
                                            </TableCell>
                                            <TableCell>{{
                                                listing.category
                                            }}</TableCell>
                                            <TableCell>
                                                <Badge variant="outline">{{
                                                    listing.type
                                                }}</Badge>
                                            </TableCell>
                                            <TableCell
                                                >${{
                                                    listing.price.toLocaleString()
                                                }}</TableCell
                                            >
                                            <TableCell
                                                class="max-w-xs truncate"
                                            >
                                                {{ listing.location }}
                                            </TableCell>
                                            <TableCell>
                                                <span
                                                    v-if="listing.rating"
                                                    class="flex items-center gap-1"
                                                >
                                                    ⭐
                                                    {{
                                                        listing.rating.toFixed(
                                                            1,
                                                        )
                                                    }}
                                                </span>
                                                <span
                                                    v-else
                                                    class="text-muted-foreground"
                                                    >-</span
                                                >
                                            </TableCell>
                                            <TableCell class="text-right">
                                                <div
                                                    class="flex justify-end gap-2"
                                                >
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        @click="
                                                            handleEditListing(
                                                                listing,
                                                            )
                                                        "
                                                    >
                                                        <Pencil
                                                            class="h-4 w-4"
                                                        />
                                                    </Button>
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        @click="
                                                            handleDelete(
                                                                'listing',
                                                                listing.id,
                                                            )
                                                        "
                                                    >
                                                        <Trash2
                                                            class="h-4 w-4 text-destructive"
                                                        />
                                                    </Button>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    </template>
                                    <TableRow v-else>
                                        <TableCell
                                            col-span="7"
                                            class="py-8 text-center text-muted-foreground"
                                        >
                                            {{
                                                $t(
                                                    'admin.dashboard.no_listings_found',
                                                )
                                            }}
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>
            </TabsContent>

            <TabsContent value="users" class="mt-6">
                <Card>
                    <CardHeader>
                        <div
                            class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
                        >
                            <div>
                                <CardTitle>{{
                                    $t('admin.dashboard.users_title')
                                }}</CardTitle>
                                <CardDescription>
                                    {{ $t('admin.dashboard.users_desc') }}
                                </CardDescription>
                            </div>
                            <Button @click="handleCreateUser">
                                <Plus class="mr-2 h-4 w-4" />
                                {{ $t('admin.dashboard.add_user_button') }}
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="relative mb-6">
                            <Search
                                class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-muted-foreground"
                            />
                            <Input
                                v-model="userSearch"
                                :placeholder="
                                    $t(
                                        'admin.dashboard.search_users_placeholder',
                                    )
                                "
                                class="pl-10"
                            />
                        </div>
                        <div class="overflow-hidden rounded-lg border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_name',
                                            )
                                        }}</TableHead>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_email',
                                            )
                                        }}</TableHead>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_role',
                                            )
                                        }}</TableHead>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_status',
                                            )
                                        }}</TableHead>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_joined',
                                            )
                                        }}</TableHead>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_spent',
                                            )
                                        }}</TableHead>
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
                                    <template v-if="filteredUsers.length > 0">
                                        <TableRow
                                            v-for="user in filteredUsers"
                                            :key="user.id"
                                        >
                                            <TableCell class="font-medium">{{
                                                user.name
                                            }}</TableCell>
                                            <TableCell>{{
                                                user.email
                                            }}</TableCell>
                                            <TableCell>
                                                <Badge
                                                    :variant="
                                                        user.role === 'admin'
                                                            ? 'default'
                                                            : 'secondary'
                                                    "
                                                >
                                                    {{ user.role }}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                <Badge
                                                    :variant="
                                                        user.status === 'active'
                                                            ? 'default'
                                                            : 'destructive'
                                                    "
                                                    :class="
                                                        user.status === 'active'
                                                            ? 'bg-green-500 hover:bg-green-600'
                                                            : ''
                                                    "
                                                >
                                                    {{ user.status }}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                {{
                                                    new Date(
                                                        user.joinedDate,
                                                    ).toLocaleDateString()
                                                }}
                                            </TableCell>
                                            <TableCell
                                                >${{
                                                    user.totalSpent.toLocaleString()
                                                }}</TableCell
                                            >
                                            <TableCell class="text-right">
                                                <div
                                                    class="flex justify-end gap-2"
                                                >
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        @click="
                                                            handleEditUser(user)
                                                        "
                                                    >
                                                        <Pencil
                                                            class="h-4 w-4"
                                                        />
                                                    </Button>
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        @click="
                                                            handleDelete(
                                                                'user',
                                                                user.id,
                                                            )
                                                        "
                                                    >
                                                        <Trash2
                                                            class="h-4 w-4 text-destructive"
                                                        />
                                                    </Button>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    </template>
                                    <TableRow v-else>
                                        <TableCell
                                            col-span="7"
                                            class="py-8 text-center text-muted-foreground"
                                        >
                                            {{
                                                $t(
                                                    'admin.dashboard.no_users_found',
                                                )
                                            }}
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>
            </TabsContent>

            <TabsContent value="categories" class="mt-6">
                <Card>
                    <CardHeader>
                        <div
                            class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
                        >
                            <div>
                                <CardTitle>{{
                                    $t('admin.dashboard.categories_title')
                                }}</CardTitle>
                                <CardDescription>
                                    {{ $t('admin.dashboard.categories_desc') }}
                                </CardDescription>
                            </div>
                            <Button @click="handleCreateCategory">
                                <Plus class="mr-2 h-4 w-4" />
                                {{ $t('admin.dashboard.add_category_button') }}
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="relative mb-6">
                            <Search
                                class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-muted-foreground"
                            />
                            <Input
                                v-model="categorySearch"
                                :placeholder="
                                    $t(
                                        'admin.dashboard.search_categories_placeholder',
                                    )
                                "
                                class="pl-10"
                            />
                        </div>
                        <div class="overflow-hidden rounded-lg border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_icon',
                                            )
                                        }}</TableHead>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_name',
                                            )
                                        }}</TableHead>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_slug',
                                            )
                                        }}</TableHead>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_desc',
                                            )
                                        }}</TableHead>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_listings',
                                            )
                                        }}</TableHead>
                                        <TableHead>{{
                                            $t(
                                                'admin.dashboard.table_header_status',
                                            )
                                        }}</TableHead>
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
                                    <template
                                        v-if="filteredCategories.length > 0"
                                    >
                                        <TableRow
                                            v-for="category in filteredCategories"
                                            :key="category.id"
                                        >
                                            <TableCell class="text-2xl">{{
                                                category.icon
                                            }}</TableCell>
                                            <TableCell class="font-medium">{{
                                                category.name
                                            }}</TableCell>
                                            <TableCell
                                                class="text-muted-foreground"
                                                >{{ category.slug }}</TableCell
                                            >
                                            <TableCell
                                                class="max-w-xs truncate"
                                            >
                                                {{ category.description }}
                                            </TableCell>
                                            <TableCell>{{
                                                category.listingCount
                                            }}</TableCell>
                                            <TableCell>
                                                <Badge
                                                    :variant="
                                                        category.isActive
                                                            ? 'default'
                                                            : 'secondary'
                                                    "
                                                    :class="
                                                        category.isActive
                                                            ? 'bg-green-500 hover:bg-green-600'
                                                            : ''
                                                    "
                                                >
                                                    {{
                                                        category.isActive
                                                            ? $t(
                                                                  'admin.dashboard.status_active',
                                                              )
                                                            : $t(
                                                                  'admin.dashboard.status_inactive',
                                                              )
                                                    }}
                                                </Badge>
                                            </TableCell>
                                            <TableCell class="text-right">
                                                <div
                                                    class="flex justify-end gap-2"
                                                >
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        @click="
                                                            handleEditCategory(
                                                                category,
                                                            )
                                                        "
                                                    >
                                                        <Pencil
                                                            class="h-4 w-4"
                                                        />
                                                    </Button>
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        @click="
                                                            handleDelete(
                                                                'category',
                                                                category.id,
                                                            )
                                                        "
                                                    >
                                                        <Trash2
                                                            class="h-4 w-4 text-destructive"
                                                        />
                                                    </Button>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    </template>
                                    <TableRow v-else>
                                        <TableCell
                                            col-span="7"
                                            class="py-8 text-center text-muted-foreground"
                                        >
                                            {{
                                                $t(
                                                    'admin.dashboard.no_categories_found',
                                                )
                                            }}
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>
            </TabsContent>

            <TabsContent value="settings" class="mt-6">
                <form @submit.prevent="saveSettings" class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{
                                $t('admin.dashboard.settings_platform_title')
                            }}</CardTitle>
                            <CardDescription>
                                {{
                                    $t('admin.dashboard.settings_platform_desc')
                                }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="commissionRate">{{
                                        $t(
                                            'admin.dashboard.settings_commission',
                                        )
                                    }}</Label>
                                    <Input
                                        id="commissionRate"
                                        type="number"
                                        v-model="settingsForm.commissionRate"
                                    />
                                    <span
                                        v-if="
                                            settingsForm.errors.commissionRate
                                        "
                                        class="text-sm text-destructive"
                                    >
                                        {{ settingsForm.errors.commissionRate }}
                                    </span>
                                </div>
                                <div class="space-y-2">
                                    <Label for="featuredLimit">{{
                                        $t(
                                            'admin.dashboard.settings_featured_limit',
                                        )
                                    }}</Label>
                                    <Input
                                        id="featuredLimit"
                                        type="number"
                                        v-model="
                                            settingsForm.featuredListingsLimit
                                        "
                                    />
                                    <span
                                        v-if="
                                            settingsForm.errors
                                                .featuredListingsLimit
                                        "
                                        class="text-sm text-destructive"
                                    >
                                        {{
                                            settingsForm.errors
                                                .featuredListingsLimit
                                        }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <Label>{{
                                        $t(
                                            'admin.dashboard.settings_maintenance',
                                        )
                                    }}</Label>
                                    <p class="text-sm text-muted-foreground">
                                        {{
                                            $t(
                                                'admin.dashboard.settings_maintenance_desc',
                                            )
                                        }}
                                    </p>
                                </div>
                                <Button
                                    type="button"
                                    :variant="
                                        settingsForm.maintenanceMode
                                            ? 'destructive'
                                            : 'outline'
                                    "
                                    size="sm"
                                    @click="
                                        settingsForm.maintenanceMode =
                                            !settingsForm.maintenanceMode
                                    "
                                >
                                    {{
                                        settingsForm.maintenanceMode
                                            ? $t(
                                                  'admin.dashboard.status_active',
                                              )
                                            : $t(
                                                  'admin.dashboard.status_inactive',
                                              )
                                    }}
                                </Button>
                            </div>
                            <div class="pt-4">
                                <Button
                                    type="submit"
                                    :disabled="settingsForm.processing"
                                >
                                    {{
                                        $t(
                                            'admin.dashboard.save_settings_button',
                                        )
                                    }}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </form>
            </TabsContent>
        </Tabs>
    </div>

    <div class="container mx-auto px-4 py-12">
        <Card>
            <CardHeader>
                <CardTitle>{{
                    $t('admin.dashboard.access_denied_title')
                }}</CardTitle>
                <CardDescription>
                    {{ $t('admin.dashboard.access_denied_desc') }}
                </CardDescription>
            </CardHeader>
        </Card>
    </div>

    <Dialog :open="listingDialogOpen" @update:open="toggleListingDialog">
        <DialogContent class="max-h-[90vh] max-w-2xl overflow-y-auto">
            <DialogHeader>
                <DialogTitle>
                    {{
                        editingListing
                            ? $t('admin.dashboard.edit_listing_title')
                            : $t('admin.dashboard.create_listing_title')
                    }}
                </DialogTitle>
                <DialogDescription>
                    {{
                        editingListing
                            ? $t('admin.dashboard.edit_listing_desc')
                            : $t('admin.dashboard.create_listing_desc')
                    }}
                </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="saveListing" class="space-y-4 py-4">
                <div class="space-y-2">
                    <Label for="title">{{
                        $t('admin.dashboard.form_label_title')
                    }}</Label>
                    <Input id="title" v-model="listingForm.title" />
                    <span
                        v-if="listingForm.errors.title"
                        class="text-sm text-destructive"
                    >
                        {{ listingForm.errors.title }}
                    </span>
                </div>
                <DialogFooter>
                    <Button
                        variant="outline"
                        type="button"
                        @click="toggleListingDialog(false)"
                    >
                        {{ $t('admin.dashboard.button_cancel') }}
                    </Button>
                    <Button type="submit" :disabled="listingForm.processing">
                        {{
                            editingListing
                                ? $t('admin.dashboard.button_update')
                                : $t('admin.dashboard.button_create')
                        }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

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
