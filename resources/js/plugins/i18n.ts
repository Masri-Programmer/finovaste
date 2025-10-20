import { createI18n } from 'vue-i18n';

import enMessages from '../locales/en';

export type MessageSchema = typeof enMessages;

declare module 'vue-i18n' {
    export interface DefineLocaleMessage extends MessageSchema {} // eslint-disable-line @typescript-eslint/no-empty-interface
}

/**
 * 2. CREATE DYNAMIC IMPORT MAP
 *
 * This map tells Vite how to lazy-load our language bundles.
 * The key ('en', 'de') must match the locale code from Laravel.
 */
const localeMessages = {
    en: () => import('../locales/en'),
    de: () => import('../locales/de'),
};

export type Locale = keyof typeof localeMessages;

const i18n = createI18n<[MessageSchema], Locale>({
    legacy: false, // Use Composition API
    locale: 'en', // This will be overwritten by app.ts
    fallbackLocale: 'en',
    messages: {},
});

const loadedLanguages: Locale[] = []; // Cache for loaded languages

export async function loadLocaleMessages(locale: Locale) {
    if (
        i18n.global.locale.value === locale &&
        loadedLanguages.includes(locale)
    ) {
        return Promise.resolve(locale);
    }

    // If the locale is not supported, do nothing
    if (!localeMessages[locale]) {
        console.error(`Locale ${locale} is not supported.`);
        return;
    }

    const messages = await localeMessages[locale]();

    i18n.global.setLocaleMessage(locale, messages.default);
    loadedLanguages.push(locale);
    i18n.global.locale.value = locale;
    document.querySelector('html')?.setAttribute('lang', locale);

    return locale;
}

export default i18n;
