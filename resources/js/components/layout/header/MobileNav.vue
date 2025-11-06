<template>
    <div class="lg:hidden">
        <Sheet
            :open="isSheetOpen"
            @update:open="$emit('update:isSheetOpen', $event)"
        >
            <SheetTrigger as-child>
                <Button variant="ghost" size="icon">
                    <Menu class="h-8 w-8" />
                    <span class="sr-only">Toggle Menu</span>
                </Button>
            </SheetTrigger>
            <SheetContent
                side="right"
                class="flex w-[300px] flex-col bg-sidebar text-sidebar-foreground sm:w-[400px]"
            >
                <SheetHeader class="p-4 pb-0 text-left">
                    <SheetTitle class="tracking-tighter"
                        >Masri Programmer</SheetTitle
                    >
                </SheetHeader>

                <div class="grow space-y-3 overflow-y-auto p-4 text-left">
                    <Accordion type="single" collapsible class="w-full">
                        <AccordionItem
                            v-for="section in menuSections"
                            :key="section.value"
                            :value="section.value"
                        >
                            <AccordionTrigger>{{
                                $t(section.titleKey)
                            }}</AccordionTrigger>
                            <AccordionContent class="pl-4">
                                <div class="flex flex-col space-y-2">
                                    <NavItem
                                        v-for="item in section.items"
                                        :key="item.i18nKey"
                                        :item="item"
                                        :on-navigate="closeSheet"
                                        context="mobile"
                                    />
                                </div>
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>

                    <span class="flex items-center pt-4 text-muted-foreground">
                        {{ $t('header.donationMessage') }}
                        <HandHeart class="mr-1.5 h-4 w-4 text-pink-500" />
                    </span>
                    <DonationBtns />
                </div>

                <div
                    class="shrink-0 space-y-4 border-t border-sidebar-border p-4"
                >
                    <div class="flex justify-center">
                        <AppLogoIcon />
                    </div>
                    <div class="flex justify-center gap-6">
                        <AppearanceIcon />
                        <LanguageSwitch />
                        <CurrencySwitch />
                    </div>
                </div>
            </SheetContent>
        </Sheet>
    </div>
</template>

<script setup lang="ts">
import AppearanceIcon from '@/components/AppearanceIcon.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import CurrencySwitch from '@/components/CurrencySwitch.vue';
import LanguageSwitch from '@/components/LanguageSwitch.vue';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { HandHeart, Menu } from 'lucide-vue-next';
import DonationBtns from './DonationBtns.vue';
import NavItem from './NavItem.vue';

defineProps<{
    menuSections: Array<any>;
    isSheetOpen: boolean;
}>();

const emit = defineEmits(['update:isSheetOpen']);

const closeSheet = () => {
    emit('update:isSheetOpen', false);
};
</script>
