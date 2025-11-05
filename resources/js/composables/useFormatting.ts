export function useFormatting() {
    function formatCurrency(value: string | number | null): string {
        return new Intl.NumberFormat('de-DE', {
            style: 'currency',
            currency: 'USD', // Using $ as per original component
            maximumFractionDigits: 0,
        }).format(Number(value || 0));
    }

    /**
     * Calculates progress percentage
     */
    function getProgress(
        raised: string | number | null,
        goal: string | number | null,
    ): number {
        const numRaised = Number(raised || 0);
        const numGoal = Number(goal || 1);
        if (numGoal === 0) return 0;
        return (numRaised / numGoal) * 100;
    }

    return { formatCurrency, getProgress };
}
