<template>
    <header class="fixed top-0 right-0 left-0 z-50">
        <div
            class="border-b border-secondary/20 bg-secondary/10 text-secondary-foreground shadow-sm backdrop-blur-2xl"
        >
            <div
                class="container-custom flex h-14 items-center !overflow-visible"
            >
                <div class="flex flex-1 items-center justify-start">
                    <AppLogoIcon class="size-6 fill-current" /> &nbsp;
                    {{ $page.props.name }}
                </div>

                <div class="hidden lg:block"></div>

                <div class="flex flex-1 justify-end">
                    <div class="flex items-center gap-x-2">
                        <div class="hidden items-center gap-x-4 lg:flex">
                            <AppearanceIcon />
                            <LanguageSwitch />
                            <nav class="flex items-center justify-end gap-2">
                                <template v-if="$page.props.auth.user">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="relative h-9 w-9 rounded-full"
                                    >
                                        <Bell class="h-5 w-5" />
                                        <!-- <span
                                            v-if="
                                                $page.props.auth
                                                    .notifications_count > 0
                                            "
                                            class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white"
                                        >
                                            {{
                                                $page.props.auth
                                                    .notifications_count
                                            }}
                                        </span> -->
                                    </Button>

                                    <UserMenu />
                                </template>

                                <template v-else>
                                    <Link
                                        :href="login()"
                                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                                    >
                                        Log in
                                    </Link>
                                    <Link
                                        :href="register()"
                                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                                    >
                                        Register
                                    </Link>
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup lang="ts">
import AppearanceIcon from '@/components/AppearanceIcon.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import LanguageSwitch from '@/components/LanguageSwitch.vue';
import { Button } from '@/components/ui/button';
import UserMenu from '@/components/UserMenu.vue';
import { Bell } from 'lucide-vue-next';

import { login, register } from '@/routes';
import { Link } from '@inertiajs/vue3';
defineProps<{
    menuSections: Array<any>;
}>();
</script>
