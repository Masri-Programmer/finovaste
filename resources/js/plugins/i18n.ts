import { createI18n } from 'vue-i18n';
import de from '../locales/de';
import en from '../locales/en';

const i18n = createI18n({
    legacy: false,
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
