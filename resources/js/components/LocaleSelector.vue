<script setup lang="ts">
import language from '@/routes/language';
import { router, usePage } from '@inertiajs/vue3';
import { loadLanguageAsync } from 'laravel-vue-i18n';
import { computed, ref } from 'vue';

const page = usePage();
const locale = page.props.locale as string;
const supportedLocales = computed(
    () => page.props.supported_locales as string[],
);
const selectedLocale = ref(locale);

const selectLocale = (code: string) => {
    router.get(
        language.switch.url(code),
        {},
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
    loadLanguageAsync(code);
    selectedLocale.value = code;
};

// watch(
//     locale,
//     (newLocale) => {
//         if (newLocale) {
//             if (typeof document !== 'undefined') {
//                 document.documentElement.lang = newLocale;
//             }
//         }
//     },
//     { immediate: true },
// );
</script>

<template>
    <div
        v-for="locale in supportedLocales"
        :key="locale"
        @click="selectLocale(locale)"
        class="cursor-pointer"
        :class="{ 'bg-sidebar-accent': locale == selectedLocale }"
    >
        {{ locale }}
    </div>
</template>
