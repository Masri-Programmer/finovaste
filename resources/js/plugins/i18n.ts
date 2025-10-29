import { createI18n } from 'vue-i18n';

// --- Import DE messages ---
import auth from '../locales/de/auth.json';
import common from '../locales/de/common.json';
import homepage from '../locales/de/homepage.json';
import languages from '../locales/de/languages.json';
import layout from '../locales/de/layout.json';
import profile from '../locales/de/profile.json';
import settings from '../locales/de/settings.json';
import validation from '../locales/de/validation.json';

console.log('[Finovaste Debug] Imported common JSON:', common);
console.log('[Finovaste Debug] Imported homepage JSON:', homepage);
const i18n = createI18n({
    legacy: false,
    locale: 'de',
    fallbackLocale: 'de',
    messages: {
        en: {
            common,
            homepage,
            layout,
            settings,
            profile,
            validation,
            auth,
            languages,
        },
        de: {
            common,
            homepage,
            layout,
            settings,
            profile,
            validation,
            auth,
            languages,
        },
    },
    numberFormats: {
        'en-US': {
            // Example for EN
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
