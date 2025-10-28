<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="relative h-9 w-9 rounded-full">
                <Avatar class="h-9 w-9">
                    <!-- <AvatarImage
                        :src="$page.props.auth.user.profile_photo_url"
                        :alt="$page.props.auth.user.name"
                    /> -->
                    <AvatarFallback>{{
                        getInitials($page.props.auth.user.name)
                    }}</AvatarFallback>
                </Avatar>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-56" align="end">
            <DropdownMenuLabel>{{
                t('profile.userMenu.title')
            }}</DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuGroup>
                <!-- <DropdownMenuItem as-child>
                    <Link :href="profile()">
                        <User class="mr-2 h-4 w-4" />
                        <span>{{ t('profile.userMenu.profile') }}</span>
                    </Link>
                </DropdownMenuItem>
                <DropdownMenuItem as-child>
                    <Link :href="myListings()">
                        <LayoutGrid class="mr-2 h-4 w-4" />
                        <span>{{ t('profile.userMenu.myListings') }}</span>
                    </Link>
                </DropdownMenuItem>
                <DropdownMenuItem as-child>
                    <Link :href="myInvestments()">
                        <Briefcase class="mr-2 h-4 w-4" />
                        <span>{{ t('profile.userMenu.myInvestments') }}</span>
                    </Link>
                </DropdownMenuItem> -->
                <DropdownMenuItem as-child>
                    <Link :href="'#'">
                        <Settings class="mr-2 h-4 w-4" />
                        <span>{{ t('profile.userMenu.settings') }}</span>
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuGroup>
            <DropdownMenuSeparator />
            <DropdownMenuItem @click="handleLogout">
                <LogOut class="mr-2 h-4 w-4" />
                <span>{{ t('profile.userMenu.logout') }}</span>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>

<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Link, router } from '@inertiajs/vue3';
import { LogOut, Settings } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toastification';

import { logout } from '@/routes';

const { t } = useI18n();
const toast = useToast();

const getInitials = (name: string) => {
    const names = name.split('profile. ');
    let initials = names[0].substring(0, 1).toUpperCase();
    if (names.length > 1) {
        initials += names[names.length - 1].substring(0, 1).toUpperCase();
    }
    return initials;
};

const handleLogout = () => {
    router.post(
        logout(),
        {},
        {
            onSuccess: () => {
                toast.success(t('profile.userMenu.messages.logoutSuccess'));
            },
            onError: () => {
                toast.error(t('profile.userMenu.messages.logoutError'));
            },
        },
    );
};
</script>
