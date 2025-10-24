<template>
    <header class="fixed top-0 right-0 left-0 z-50">
        <div
            class="border-b border-secondary/20 bg-secondary/10 text-secondary-foreground shadow-sm"
        >
            <div
                class="container-custom flex h-14 items-center !overflow-visible px-4"
            >
                <!-- Left Section: Brand -->
                <div class="flex flex-1 items-center justify-start">
                    <AppLogoIcon class="size-6 fill-current" /> &nbsp; Finovaste
                </div>

                <!-- Center Section: Desktop Navigation -->
                <div class="hidden lg:block">
                    <!-- <DesktopNav :menu-sections="menuSections" /> -->
                </div>

                <!-- Right Section: Actions & Mobile Menu -->
                <div class="flex flex-1 justify-end">
                    <div class="flex items-center gap-x-2">
                        <div class="hidden items-center gap-x-4 lg:flex">
                            <AppearanceIcon />
                            <LanguageSwitch />
                            <nav class="flex items-center justify-end gap-4">
                                <Link
                                    v-if="$page.props.auth.user"
                                    :href="dashboard()"
                                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                                >
                                    Dashboard
                                </Link>
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

                        <!-- Mobile-only menu trigger -->
                        <!-- <MobileNav v-model:is-sheet-open="isSheetOpen" :menu-sections="menuSections" /> -->
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

// const isSheetOpen = ref(false);
import { dashboard, login, register } from '@/routes';
import { Link } from '@inertiajs/vue3';
defineProps<{
    menuSections: Array<any>;
}>();
</script>
