// resources/js/Composables/useLocale.ts

import { computed, ComputedRef } from 'vue';
import { useI18n } from 'vue-i18n';

// Using the interface defined above (or define it here if you prefer)
interface LocaleConfig {
    code: string;
    name: string;
}

/**
 * Interface defining the structure of the returned composable object.
 */
interface LocaleComposable {
    currentLocale: ComputedRef<string>;
    supportedLocales: LocaleConfig[];
    getSwitchUrl: (langCode: string) => string;
}

/**
 * Composables for managing and accessing the current application locale.
 * It integrates with the vue-i18n instance injected by laravel-vue-i18n.
 */
export function useLocale(): LocaleComposable {
    // 1. Access the i18n instance and destructure 'locale'
    const { locale } = useI18n();

    // The locale is reactive and changes when the page reloads
    const currentLocale: ComputedRef<string> = computed(() => {
        return locale.value;
    });

    const supportedLocales: LocaleConfig[] = [
        { code: 'en', name: 'English' },
        { code: 'fr', name: 'Français' },
        { code: 'es', name: 'Español' },
        // Add any other languages here
    ];

    /**
     * Generates the URL used to switch the language via the Laravel route.
     * Assumes the global 'route' function (Ziggy) is available.
     * * @param langCode The locale code (e.g., 'fr').
     * @returns The full URL for switching.
     */
    const getSwitchUrl = (langCode: string): string => {
        // We use type assertion for the global `route` function if you rely on Ziggy.
        // If not using Ziggy, this type casting is unnecessary.
        const ziggyRoute = (window as any).route;

        if (typeof ziggyRoute === 'function') {
            // 'language.switch' is the named route created in web.php
            return ziggyRoute('language.switch', { locale: langCode });
        }
        // Fallback if Ziggy is not installed/loaded
        return `/lang/${langCode}`;
    };

    return {
        currentLocale,
        supportedLocales,
        getSwitchUrl,
    };
}
