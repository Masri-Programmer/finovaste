<template>
    <nav class="hidden items-center gap-x-6 lg:flex">
        <NavigationMenu>
            <NavigationMenuList>
                <NavigationMenuItem v-for="section in menuSections" :key="section.value">
                    <NavigationMenuTrigger class="bg-transparent text-sm font-medium capitalize">
                        {{ t(section.titleKey) }}
                    </NavigationMenuTrigger>
                    <NavigationMenuContent>
                        <ul class="grid w-[300px] gap-3 p-4 md:w-[400px] lg:w-[500px]">
                            <li v-for="item in section.items" :key="item.i18nKey" class="row-span-3">
                                <NavItem :item="item" context="desktop" />
                            </li>
                        </ul>
                    </NavigationMenuContent>
                </NavigationMenuItem>
            </NavigationMenuList>
        </NavigationMenu>
    </nav>
</template>

<script setup lang="ts">
import {
    NavigationMenu,
    NavigationMenuContent,
    NavigationMenuItem,
    NavigationMenuList,
    NavigationMenuTrigger,
} from '@/components/ui/navigation-menu';
import { useI18n } from 'vue-i18n';
import NavItem from './NavItem.vue';

defineProps<{
    menuSections: Array<any>;
}>();

const { t } = useI18n();
</script>
