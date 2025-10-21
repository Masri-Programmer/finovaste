<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { useLanguageSwitcher } from '@/composables/useLanguageSwitcher';
import { Check, Languages } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const { locale, availableLanguages, setLocale } = useLanguageSwitcher();
</script>

<template>
    <TooltipProvider>
        <Tooltip :delay-duration="0">
            <DropdownMenu>
                <TooltipTrigger as-child>
                    <DropdownMenuTrigger as-child>
                        <Button variant="ghost" size="icon" :aria-label="t('languages.changeLanguage')">
                            <Languages class="h-5 w-5" />
                        </Button>
                    </DropdownMenuTrigger>
                </TooltipTrigger>

                <TooltipContent>
                    <p>{{ t('languages.changeLanguage') }}</p>
                </TooltipContent>

                <DropdownMenuContent>
                    <DropdownMenuItem
                        v-for="lang in availableLanguages"
                        :key="lang.code"
                        class="w-full justify-between"
                        @click="setLocale(lang.code)"
                    >
                        {{ t(lang.nameKey) }}
                        <Check v-if="locale === lang.code" class="h-4 w-4" />
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </Tooltip>
    </TooltipProvider>
</template>
