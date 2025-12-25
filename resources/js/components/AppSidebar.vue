<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes/admin';
import { index as categories } from '@/routes/admin/categories';
import { index } from '@/routes/admin/listings';
import { show } from '@/routes/admin/settings';
import { index as users } from '@/routes/admin/users';
import { logs } from '@/routes/developer';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    Bug,
    ChartBarStacked,
    Folder,
    LayoutGrid,
    List,
    Settings,
    User,
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Einträge',
        href: index(),
        icon: List,
    },
    {
        title: 'Nutzereinstellungen',
        href: users(),
        icon: User,
    },
    {
        title: 'Kategorienverwaltung',
        href: categories(),
        icon: ChartBarStacked,
    },
    {
        title: 'Einstellungen',
        href: show(),
        icon: Settings,
    },
    {
        title: 'Logs',
        href: logs(),
        icon: Bug,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/Masri-Programmer/ranalp',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://github.com/Masri-Programmer/ranalp',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
