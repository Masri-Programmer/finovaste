import { createI18n } from 'vue-i18n';

// --- GERMAN IMPORTS ---
import auth_de from '../locales/de/auth.json';
import common_de from '../locales/de/common.json';
import homepage_de from '../locales/de/homepage.json';
import languages_de from '../locales/de/languages.json';
import layout_de from '../locales/de/layout.json';
import profile_de from '../locales/de/profile.json';
import settings_de from '../locales/de/settings.json';
import validation_de from '../locales/de/validation.json';

// --- ENGLISH IMPORTS (You must create these files) ---
import auth_en from '../locales/en/auth.json';
import common_en from '../locales/en/common.json';
import homepage_en from '../locales/en/homepage.json';
import languages_en from '../locales/en/languages.json';
import layout_en from '../locales/en/layout.json';
import profile_en from '../locales/en/profile.json';
import settings_en from '../locales/en/settings.json';
import validation_en from '../locales/en/validation.json';

const i18n = createI18n({
    legacy: false,
    fallbackLocale: 'de',
    messages: {
        en: {
            common: common_en,
            homepage: homepage_en,
            layout: layout_en,
            settings: settings_en,
            profile: profile_en,
            validation: validation_en,
            auth: auth_en,
            languages: languages_en,
        },
        de: {
            common: common_de,
            homepage: homepage_de,
            layout: layout_de,
            settings: settings_de,
            profile: profile_de,
            validation: validation_de,
            auth: auth_de,
            languages: languages_de,
        },
    },
    numberFormats: {
        // --- Good to add EN format too ---
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
