import language from '@/routes/language';
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

export function useLanguageSwitcher() {
    const { locale: i18nLocale } = useI18n();

    const page = usePage();
    const locale = computed(() => page.props.locale as string);

    console.log(
        '[Finovaste Debug] page.props.locale received:',
        page.props.locale,
    );
    const supportedLocales = computed(
        () => page.props.supported_locales as string[],
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
            console.log(
                '[Finovaste Debug] Watcher triggered. New locale is:',
                newLocale,
            );
            if (newLocale) {
                i18nLocale.value = newLocale;
                console.log(
                    '[Finovaste Debug] Set i18n-active-locale to:',
                    i18nLocale.value,
                );
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
