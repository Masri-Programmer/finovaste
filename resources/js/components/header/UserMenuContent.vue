<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { logout } from '@/routes';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/listings/users';
import { edit } from '@/routes/profile';
import { index as transactions } from '@/routes/transactions';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import {
    CreditCard,
    LogOut,
    Scroll,
    Settings,
    User as UserIcon,
} from 'lucide-vue-next';
interface Props {
    user: User;
    roles: Array<string>;
}
const handleLogout = () => {
    router.flushAll();
};
defineProps<Props>();
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link
                class="block w-full"
                :href="index($page.props.auth.user.id)"
                as="button"
            >
                <Scroll class="mr-2 h-4 w-4" />
                {{ $t('menu.listings') }}
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full" :href="edit()" as="button">
                <UserIcon class="mr-2 h-4 w-4" />
                {{ $t('menu.profile') }}
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuGroup v-if="roles.includes('admin')">
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full" :href="dashboard()" as="button">
                <Settings class="mr-2 h-4 w-4" />
                {{ $t('menu.settings') }}
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full" :href="transactions()" as="button">
                <CreditCard class="mr-2 h-4 w-4" />
                {{ $t('menu.transactions') }}
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>

    <DropdownMenuSeparator />
    <DropdownMenuItem :as-child="true">
        <Link
            class="block w-full"
            :href="logout()"
            @click="handleLogout"
            as="button"
            method="post"
            data-test="logout-button"
        >
            <LogOut class="mr-2 h-4 w-4" />
            {{ $t('menu.logout') }}
        </Link>
    </DropdownMenuItem>
</template>
