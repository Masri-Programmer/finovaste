import { usePage } from '@inertiajs/vue3';

export function useMoney() {
    /**
     * Format a number or string into a currency string.
     */
    const formatMoney = (
        amount: number | string | null | undefined,
        currency: string | null = null,
        locale: string | null = null,
    ): string => {
        if (amount === null || amount === undefined) {
            return '—';
        }

        const page = usePage();
        const user = page.props.auth.user;
        const moneyConfig = page.props.money;

        const effectiveCurrency =
            currency || user?.currency || moneyConfig?.default || 'EUR';
        const effectiveLocale = locale || user?.locale || 'de-DE';

        // Convert string inputs ("12.50") to number (12.50) for Intl
        const numericAmount =
            typeof amount === 'string' ? parseFloat(amount) : amount;

        if (isNaN(numericAmount)) {
            return '—';
        }

        try {
            return new Intl.NumberFormat(effectiveLocale, {
                style: 'currency',
                currency: effectiveCurrency,
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(numericAmount);
        } catch (error) {
            console.warn(
                `Money formatting failed for currency: ${effectiveCurrency}`,
                error,
            );
            return `${numericAmount}`;
        }
    };

    const getCurrencySymbol = (currency: string, locale: string = 'de-DE') => {
        try {
            return (0)
                .toLocaleString(locale, { style: 'currency', currency })
                .replace(/\d/g, '')
                .replace(/[.,\s]/g, ''); // Naive symbol extraction
        } catch (e) {
            return currency;
        }
    };

    return {
        formatMoney,
        getCurrencySymbol,
    };
}
