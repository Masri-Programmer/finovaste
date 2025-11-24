export const formatCurrency = (value: string | number) => {
    return Number(value).toLocaleString('de-DE', {
        style: 'currency',
        currency: 'EUR',
    });
};
