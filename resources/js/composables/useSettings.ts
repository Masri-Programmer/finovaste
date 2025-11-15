import type { GeneralSettings } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useSettings() {
    const page = usePage();

    const general = computed<GeneralSettings | null>(() => {
        return page.props.settings ? page.props.settings.general : null;
    });

    return {
        general,
    };
}
