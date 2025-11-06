import language from '@/routes/language';
import { router, usePage } from '@inertiajs/vue3';
import { getActiveLanguage } from 'laravel-vue-i18n';
import { computed, ref, watch } from 'vue';

export function useLanguageSwitcher() {
    const i18nLocale = ref(getActiveLanguage());
    const page = usePage();
    const locale = computed(() => page.props.locale as string);

    const supportedLocales = computed(
        () => page.props.supportedLocales as string[],
    );

    const availableLanguages = computed(() =>
        supportedLocales.value.map((code) => ({
            code: code,
            nameKey: `languages.${{ en: 'english', de: 'german', fr: 'french', ar: 'arabic' }[code] || code}`,
        })),
    );

    const setLocale = (langCode: string) => {
        i18nLocale.value = langCode;
        router.get(
            language.switch.url(langCode),
            {},
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };

    watch(
        locale,
        (newLocale) => {
            if (newLocale) {
                i18nLocale.value = newLocale;
                if (typeof document !== 'undefined') {
                    document.documentElement.lang = newLocale;
                }
            }
        },
        { immediate: true },
    );

    return {
        locale,
        availableLanguages,
        setLocale,
    };
}
