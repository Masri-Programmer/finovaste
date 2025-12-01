// resources/js/Composables/useToast.ts
import { AppNotification, AppPageProps } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';

const toastState = reactive<{ items: AppNotification[] }>({
    items: [],
});

export function useToast() {
    const page = usePage<AppPageProps>();

    const add = (notification: AppNotification) => {
        const id = Date.now() + Math.random();

        const newItem: AppNotification = {
            ...notification,
            id: id,
        };

        toastState.items.push(newItem);

        const duration = notification.duration ?? 3000;

        if (duration > 0) {
            setTimeout(() => {
                remove(id);
            }, duration);
        }
    };

    const remove = (id: number | undefined) => {
        if (!id) return;
        toastState.items = toastState.items.filter((item) => item.id !== id);
    };

    const enableGlobalHandling = () => {
        watch(
            () => page.props.flash?.notification,
            (newNotification) => {
                if (newNotification) {
                    add(newNotification);
                }
            },
            { deep: true },
        );
    };

    return {
        toastState,
        add,
        remove,
        enableGlobalHandling,
    };
}
