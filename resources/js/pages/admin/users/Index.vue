<script setup lang="ts">
import { computed, ref } from 'vue';
import { useToast } from 'vue-toastification';

// The new reusable component
import ResourceIndex from '@/components/resource/Index.vue';
import { Badge } from '@/components/ui/badge';

// --- Types (Copied from your old User Index) ---
interface UserRole {
    id: number;
    name: string;
}
interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    roles: UserRole[];
}
interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

// --- Props ---
const props = defineProps<{
    users: {
        data: User[];
        links: PaginationLink[];
    };
}>();

const toast = useToast();

// --- State ---
const userSearch = ref('');

// --- Table Column Definition ---
const columns = [
    { key: 'name', label: 'Name', class: 'font-medium' },
    { key: 'email', label: 'Email' },
    { key: 'roles', label: 'Roles' },
    { key: 'email_verified_at', label: 'Verified' },
    { key: 'created_at', label: 'Joined' },
];

// --- Computed Filter ---
const filteredUsers = computed(() =>
    props.users.data.filter(
        (user) =>
            user.name.toLowerCase().includes(userSearch.value.toLowerCase()) ||
            user.email.toLowerCase().includes(userSearch.value.toLowerCase()),
    ),
);

// --- Helper Functions ---
const formatDate = (dateString: string | null) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('de-DE');
};
const isEmailVerified = (dateString: string | null) => !!dateString;

// --- CRUD Handlers (Stubs) ---
const handleCreateUser = () => {
    toast.info('Create user form logic goes here.');
    // router.get(route('web.admin.users.create'));
};

const handleEditUser = (user: User) => {
    toast.info(`Editing: ${user.name}. Form logic goes here.`);
    // router.get(route('web.admin.users.edit', user.id));
};

const handleDeleteUser = (id: number) => {
    toast.warning(`Confirm delete for ID: ${id}. Confirm logic goes here.`);
    // Show delete confirmation modal...
    // router.delete(route('web.admin.users.destroy', id), { ... });
};
</script>

<template>
    <ResourceIndex
        pageTitle="Manage Users"
        :title="$t('admin.dashboard.users_title')"
        :description="$t('admin.dashboard.users_desc')"
        :search-placeholder="$t('admin.dashboard.search_users_placeholder')"
        :empty-state-message="$t('admin.dashboard.no_users_found')"
        :columns="columns"
        :items="filteredUsers"
        :pagination-links="props.users.links"
        v-model="userSearch"
        @create="handleCreateUser"
        @edit="handleEditUser"
        @delete="handleDeleteUser"
    >
        <template #cell-roles="{ item }">
            <div v-if="item.roles.length > 0" class="flex flex-wrap gap-1">
                <Badge
                    v-for="role in item.roles"
                    :key="role.id"
                    :variant="role.name === 'admin' ? 'default' : 'secondary'"
                >
                    {{ role.name }}
                </Badge>
            </div>
            <span v-else class="text-muted-foreground">-</span>
        </template>

        <template #cell-email_verified_at="{ item }">
            <Badge
                :variant="
                    isEmailVerified(item.email_verified_at)
                        ? 'secondary'
                        : 'outline'
                "
            >
                <span v-if="isEmailVerified(item.email_verified_at)">
                    {{ $t('admin.dashboard.verified') }}
                </span>
                <span v-else>
                    {{ $t('admin.dashboard.pending') }}
                </span>
            </Badge>
        </template>

        <template #cell-created_at="{ item }">
            {{ formatDate(item.created_at) }}
        </template>
    </ResourceIndex>
</template>
