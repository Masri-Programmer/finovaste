import { createI18n } from 'vue-i18n';

// --- GERMAN IMPORTS ---

// --- ENGLISH IMPORTS (EXAMPLE) ---
// You must create these files and import them
// import layout_en from '../locales/en/layout.json';
// import profile_en from '../locales/en/profile.json';
// import settings_en from '../locales/en/settings.json';

import de from '../locales/de.json';
import en from '../locales/en.json';

const i18n = createI18n({
    legacy: false,
    locale: 'de',
    fallbackLocale: 'de',
    messages: {
        en: {
            ...en,
        },
        de: {
            ...de,
        },
    },
    numberFormats: {
        'en-US': {
            currency: {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            },
        },
        'de-DE': {
            currency: {
                style: 'currency',
                currency: 'EUR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            },
        },
    },
});

export default i18n;
