<script setup lang="ts">
import { useLanguageSwitcher } from '@/composables/useLanguageSwitcher';

const { locale, availableLanguages, setLocale } = useLanguageSwitcher();
</script>

<template>
    <div
        v-for="lang in availableLanguages"
        :key="lang.code"
        @click="setLocale(lang.code)"
        class="cursor-pointer"
        :class="{ 'bg-sidebar-accent': lang.code === locale }"
    >
        {{ $t(lang.nameKey) }}
    </div>
</template>
