import { createI18n } from 'vue-i18n';

// import auth_de from '../locales/de/auth.json';
// import common_de from '../locales/de/common.json';
// import homepage_de from '../locales/de/homepage.json';
// import languages_de from '../locales/de/languages.json';
// import layout_de from '../locales/de/layout.json';
// import listing_de from '../locales/de/listing.json';
// import listings_show_de from '../locales/de/listings/show.json';
// import profile_de from '../locales/de/profile.json';
// import settings_de from '../locales/de/settings.json';
// import validation_de from '../locales/de/validation.json';

// import auth_en from '../locales/en/auth.json';
// import common_en from '../locales/en/common.json';
// import homepage_en from '../locales/en/homepage.json';
// import languages_en from '../locales/en/languages.json';
// import layout_en from '../locales/en/layout.json';
// import listing_en from '../locales/en/listing.json';
// import listings_show_en from '../locales/en/listings/show.json';
// import profile_en from '../locales/en/profile.json';
// import settings_en from '../locales/en/settings.json';
// import validation_en from '../locales/en/validation.json';
const modules = import.meta.glob('../locales/**/*.json', { eager: true });
const messages = {};

// 2. Loop over all found modules
for (const path in modules) {
    // 3. Apply the fix for the 'unknown' type error
    const value = (modules[path] as { default: object }).default;

    // 4. Parse the path to get locale and group
    //    e.g., path = '../locales/de/homepage.json'
    const pathParts = path.split('/'); // ['..', 'locales', 'de', 'homepage.json']

    const locale = pathParts[2]; // 'de'
    const group = pathParts[3].replace('.json', ''); // 'homepage'

    // 5. Build the nested messages object
    if (!messages[locale]) {
        messages[locale] = {};
    }

    messages[locale][group] = value;
}
const i18n = createI18n({
    legacy: false,
    fallbackLocale: 'de',
    messages,
    // messages: {
    //     en: {
    //         common: common_en,
    //         homepage: homepage_en,
    //         layout: layout_en,
    //         settings: settings_en,
    //         profile: profile_en,
    //         listings_show: listings_show_en,
    //         listing: listing_en,
    //         validation: validation_en,
    //         auth: auth_en,
    //         languages: languages_en,
    //     },
    //     de: {
    //         common: common_de,
    //         listing: listing_de,
    //         listings_show: listings_show_de,
    //         homepage: homepage_de,
    //         layout: layout_de,
    //         settings: settings_de,
    //         profile: profile_de,
    //         validation: validation_de,
    //         auth: auth_de,
    //         languages: languages_de,
    //     },
    // },
    numberFormats: {
        en: {
            currency: {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            },
        },
        de: {
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
