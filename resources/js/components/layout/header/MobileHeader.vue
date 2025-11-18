<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    Bell,
    Heart,
    LogOut,
    Menu,
    Plus,
    Scroll,
    Settings,
    User as UserIcon,
} from 'lucide-vue-next';
import { computed } from 'vue';

// Route Imports
import { home, login, logout, register } from '@/routes';
import { create, liked } from '@/routes/listings';
// Specific User Menu Routes
import { dashboard } from '@/routes/admin';
import { index as userListingsIndex } from '@/routes/listings/users';
import { edit as editProfile } from '@/routes/profile';

// Types
interface MenuItem {
    label: string;
    route: string;
}

interface MenuSection {
    title?: string;
    items: MenuItem[];
}

defineProps<{
    menuSections?: MenuSection[];
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);
const roles = computed(() => (page.props.auth.roles as string[]) || []);

// Helper for initials
const getInitials = (name: string) => {
    if (!name) return '';
    const names = name.split(' ');
    const initials = names.map((n) => n[0]).join('');
    return initials.toUpperCase();
};

// Logout Logic (Matches UserMenuContent)
const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <Sheet>
        <SheetTrigger as-child>
            <Button
                variant="ghost"
                size="icon"
                class="text-secondary-foreground hover:bg-secondary/20 lg:hidden"
                :aria-label="$t('header.mobile.open_menu')"
            >
                <Menu class="h-6 w-6" />
            </Button>
        </SheetTrigger>

        <SheetContent
            side="left"
            class="w-[300px] border-r border-border bg-background p-0 font-sans text-foreground sm:w-[350px]"
        >
            <SheetHeader class="border-b border-border/50 px-6 pt-6 pb-4">
                <div class="flex items-center justify-between">
                    <SheetTitle class="text-lg font-bold tracking-tight">
                        {{ $page.props.name }}
                    </SheetTitle>
                </div>
                <SheetDescription class="sr-only">
                    {{ $t('header.mobile.description') }}
                </SheetDescription>
            </SheetHeader>

            <ScrollArea class="h-[calc(100vh-8rem)]">
                <div class="flex flex-col gap-6 p-6">
                    <template v-if="user">
                        <div class="flex items-center gap-4">
                            <Avatar class="h-10 w-10 border border-border">
                                <AvatarImage
                                    :src="user.profile_photo_path"
                                    :alt="user.name"
                                />
                                <AvatarFallback
                                    class="bg-primary/10 text-primary"
                                >
                                    {{ getInitials(user.name) }}
                                </AvatarFallback>
                            </Avatar>
                            <div class="flex flex-col">
                                <span
                                    class="max-w-[200px] truncate leading-none font-medium"
                                    >{{ user.name }}</span
                                >
                                <span
                                    class="mt-1.5 max-w-[200px] truncate text-sm leading-none text-muted-foreground"
                                >
                                    {{ user.email }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <Link :href="create()" as="button" class="w-full">
                                <div
                                    class="flex flex-col items-center justify-center gap-1.5 rounded-lg border border-transparent bg-secondary/50 p-2.5 transition-all hover:border-border hover:bg-secondary"
                                >
                                    <Plus class="h-5 w-5 text-foreground" />
                                </div>
                            </Link>
                            <Link :href="liked()" as="button" class="w-full">
                                <div
                                    class="flex flex-col items-center justify-center gap-1.5 rounded-lg border border-transparent bg-secondary/50 p-2.5 transition-all hover:border-border hover:bg-secondary"
                                >
                                    <Heart class="h-5 w-5 text-foreground" />
                                </div>
                            </Link>
                            <button
                                class="flex w-full flex-col items-center justify-center gap-1.5 rounded-lg border border-transparent bg-secondary/50 p-2.5 transition-all hover:border-border hover:bg-secondary"
                            >
                                <Bell class="h-5 w-5 text-foreground" />
                                <!-- <span
                                    class="text-[10px] font-bold tracking-wider uppercase opacity-70"
                                    >{{ $t('action.notify') }}</span
                                > -->
                            </button>
                        </div>

                        <div class="flex flex-col space-y-1">
                            <Link
                                :href="userListingsIndex(user.id)"
                                class="flex items-center rounded-md px-2 py-2 text-sm font-medium transition-colors hover:bg-secondary/50"
                            >
                                <Scroll
                                    class="mr-3 h-4 w-4 text-muted-foreground"
                                />
                                {{ $t('menu.listings') }}
                            </Link>

                            <Link
                                :href="editProfile()"
                                class="flex items-center rounded-md px-2 py-2 text-sm font-medium transition-colors hover:bg-secondary/50"
                            >
                                <UserIcon
                                    class="mr-3 h-4 w-4 text-muted-foreground"
                                />
                                {{ $t('menu.profile') }}
                            </Link>

                            <Link
                                v-if="roles.includes('admin')"
                                :href="dashboard()"
                                class="flex items-center rounded-md px-2 py-2 text-sm font-medium text-primary transition-colors hover:bg-secondary/50"
                            >
                                <Settings class="mr-3 h-4 w-4" />
                                {{ $t('menu.settings') }}
                            </Link>
                        </div>
                    </template>

                    <template v-else>
                        <div class="flex flex-col gap-3">
                            <Link :href="login()" class="w-full">
                                <Button
                                    variant="outline"
                                    class="w-full justify-center font-semibold"
                                >
                                    {{ $t('login.button') }}
                                </Button>
                            </Link>
                            <Link :href="register()" class="w-full">
                                <Button
                                    class="w-full justify-center bg-primary font-bold text-primary-foreground shadow-sm hover:bg-primary/90"
                                >
                                    {{ $t('register.button') }}
                                </Button>
                            </Link>
                        </div>
                    </template>

                    <Separator />

                    <nav class="flex flex-col space-y-1">
                        <Link
                            :href="home()"
                            class="flex items-center px-2 py-2 text-sm font-medium transition-colors hover:text-primary"
                        >
                            {{ $t('menu.home') }}
                        </Link>

                        <template v-if="menuSections">
                            <div
                                v-for="(section, index) in menuSections"
                                :key="index"
                                class="mt-4 flex flex-col gap-1 first:mt-2"
                            >
                                <h4
                                    v-if="section.title"
                                    class="mb-2 px-2 text-xs font-bold tracking-widest text-muted-foreground uppercase"
                                >
                                    {{ section.title }}
                                </h4>
                                <Link
                                    v-for="item in section.items"
                                    :key="item.route"
                                    :href="item.route"
                                    class="flex items-center px-2 py-2 text-sm text-foreground/80 transition-transform hover:translate-x-1 hover:text-primary"
                                >
                                    {{ item.label }}
                                </Link>
                            </div>
                        </template>
                    </nav>

                    <div v-if="user" class="mt-auto pt-6">
                        <Link
                            :href="logout()"
                            method="post"
                            as="button"
                            class="flex w-full items-center rounded-md px-2 py-2 text-sm font-medium text-destructive transition-colors hover:bg-destructive/10"
                            @click="handleLogout"
                        >
                            <LogOut class="mr-3 h-4 w-4" />
                            {{ $t('menu.logout') }}
                        </Link>
                    </div>
                </div>
            </ScrollArea>
        </SheetContent>
    </Sheet>
</template>
