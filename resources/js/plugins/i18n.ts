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

// --- Import EN messages (Example) ---
// Make sure these files exist
// import auth_en from '../locales/en/auth.json';
// import common_en from '../locales/en/common.json';
// import homepage_en from '../locales/en/homepage.json';
// import languages_en from '../locales/en/languages.json';
// import layout_en from '../locales/en/layout.json';
// import profile_en from '../locales/en/profile.json';
// import settings_en from '../locales/en/settings.json';
// import validation_en from '../locales/en/validation.json';
console.log('[Finovaste Debug] Imported common JSON:', common);
console.log('[Finovaste Debug] Imported homepage JSON:', homepage);
const i18n = createI18n({
    legacy: false,
    locale: 'de',
    fallbackLocale: 'de',
    messages: {
        // en: {
        //     common: common_en,
        //     homepage: homepage_en,
        //     layout: layout_en,
        //     settings: settings_en,
        //     profile: profile_en,
        //     validation: validation_en,
        //     auth: auth_en,
        //     languages: languages_en,
        // },
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
        // 'en-US': {
        //     // Example for EN
        //     currency: {
        //         style: 'currency',
        //         currency: 'USD',
        //         minimumFractionDigits: 0,
        //         maximumFractionDigits: 0,
        //     },
        // },
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
