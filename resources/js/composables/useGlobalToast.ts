// resources/js/Composables/useToast.ts
import { AppPageProps } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { useToast as useVueToast } from 'vue-toastification';

export function useGlobalToast() {
    const page = usePage<AppPageProps>();
    const toast = useVueToast();

    const enableGlobalHandling = () => {
        watch(
            () => page.props?.flash?.notification,
            (notification) => {
                if (!notification) return;

                const { type, message, options } = notification;

                const method =
                    type &&
                    typeof toast[type as keyof typeof toast] === 'function'
                        ? (type as keyof typeof toast)
                        : 'success';

                toast[method](message, options || {});
            },
            {
                deep: true,
                immediate: true,
            },
        );
    };

    return {
        enableGlobalHandling,
        toast,
    };
}
