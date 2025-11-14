<template>
    <header class="fixed top-0 right-0 left-0 z-50">
        <div
            class="border-b border-secondary/20 bg-secondary/10 text-secondary-foreground shadow-sm backdrop-blur-2xl"
        >
            <div
                class="container-custom flex h-14 items-center !overflow-visible"
            >
                <Link
                    :href="home()"
                    prefetch
                    as="button"
                    class="flex flex-1 items-center justify-start"
                >
                    <AppLogoIcon class="size-6 fill-current" /> &nbsp;
                    {{ $page.props.name }}
                </Link>

                <div class="hidden lg:block"></div>

                <div class="flex flex-1 justify-end">
                    <div class="flex items-center gap-x-2">
                        <div class="hidden items-center gap-x-4 lg:flex">
                            <!-- <AppearanceIcon />
                            <LanguageSwitch /> -->
                            <nav class="flex items-center justify-end gap-2">
                                <template v-if="$page.props.auth.user">
                                    <Link :href="create()" prefetch as="button">
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="relative h-9 w-9 rounded-full"
                                        >
                                            <Plus class="h-5 w-5" />
                                        </Button>
                                    </Link>
                                    <Link :href="liked()" prefetch as="button">
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="relative h-9 w-9 rounded-full"
                                        >
                                            <Heart class="h-5 w-5" />
                                        </Button>
                                    </Link>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="relative h-9 w-9 rounded-full"
                                    >
                                        <Bell class="h-5 w-5" />
                                    </Button>
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                class="relative h-9 w-9 rounded-full"
                                            >
                                                <Avatar class="h-9 w-9">
                                                    <AvatarFallback>{{
                                                        getInitials(
                                                            $page.props.auth
                                                                .user.name,
                                                        )
                                                    }}</AvatarFallback>
                                                </Avatar>
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent
                                            class="w-56"
                                            align="end"
                                        >
                                            <UserMenuContent
                                                :user="auth.user"
                                                :roles="auth.roles"
                                            />
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </template>

                                <template v-else>
                                    <Link
                                        :href="login()"
                                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                                    >
                                        {{ $t('login.button') }}
                                    </Link>
                                    <Link
                                        :href="register()"
                                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                                    >
                                        {{ $t('register.button') }}
                                    </Link>
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="border-b border-secondary/20 bg-secondary/50 text-secondary-foreground shadow-sm backdrop-blur-xl"
            v-if="$page.props.breadcrumbs?.length > 1"
        >
            <div
                class="container-custom flex h-12 items-center !overflow-visible"
            >
                <Breadcrumbs />
            </div>
        </div>
    </header>
</template>

<script setup lang="ts">
// import AppearanceIcon from '@/components/AppearanceIcon.vue';
// import LanguageSwitch from '@/components/LanguageSwitch.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import UserMenuContent from '@/components/header/UserMenuContent.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { home, login, register } from '@/routes';
import { create, liked } from '@/routes/listings';
import { Link, usePage } from '@inertiajs/vue3';
import { Bell, Heart, Plus } from 'lucide-vue-next';
import { computed } from 'vue';

defineProps<{
    menuSections: Array<any>;
}>();

const page = usePage();
const auth = computed(() => page.props.auth);

const getInitials = (name: string) => {
    if (!name) return '';
    const names = name.split(' ');
    const initials = names.map((n) => n[0]).join('');
    return initials.toUpperCase();
};
</script>
