<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useTimeAgo } from '@vueuse/core';
import { trans } from 'laravel-vue-i18n';
import { AlertCircle, Bell, Check, Clock, Info } from 'lucide-vue-next';
import { computed } from 'vue';
import { useToast } from 'vue-toastification';

// Types
interface NotificationItem {
    id: string;
    title: string;
    description: string;
    type: 'info' | 'warning' | 'success' | 'error';
    created_at: string;
    read_at: string | null;
    link: string;
}

const page = usePage();
const toast = useToast();

const items = computed<NotificationItem[]>(() => {
    const notifications = page.props.auth?.notifications || [];

    return notifications.map((n: any) => ({
        id: n.id,
        title: n.data.title || 'Notification',
        description: n.data.description || '',
        type: n.data.type || 'info',
        created_at: n.created_at,
        read_at: n.read_at,
        link: n.data.url || '#',
    }));
});

const unreadCountDisplay = computed(
    () => page.props.auth?.unread_notifications_count || 0,
);

/**
 * Handle marking a single notification as read and visiting its link.
 */
const handleNotificationClick = (notification: NotificationItem) => {
    if (!notification.read_at) {
        router.post(
            route('notifications.read', notification.id),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    // Navigate after marking as read if it has a link
                    if (notification.link && notification.link !== '#') {
                        window.location.href = notification.link;
                    }
                },
            },
        );
    } else if (notification.link && notification.link !== '#') {
        window.location.href = notification.link;
    }
};

/**
 * Handle "Mark all as read" action.
 */
const markAllAsRead = () => {
    router.post(
        route('notifications.mark_all_read'),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success(trans('notifications.mark_all_success'));
            },
            onError: () => {
                toast.error(trans('notifications.mark_all_error'));
            },
        },
    );
};

/**
 * Helper to get icon based on notification type
 */
const getIcon = (type: string) => {
    switch (type) {
        case 'warning':
            return AlertCircle;
        case 'success':
            return Check;
        case 'error':
            return AlertCircle;
        default:
            return Info;
    }
};

/**
 * Helper to get color class based on notification type
 */
const getIconColor = (type: string) => {
    switch (type) {
        case 'warning':
            return 'text-yellow-500';
        case 'success':
            return 'text-green-500';
        case 'error':
            return 'text-destructive';
        default:
            return 'text-primary';
    }
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button
                variant="ghost"
                size="icon"
                class="relative h-9 w-9 rounded-full focus-visible:ring-offset-0"
            >
                <Bell class="h-5 w-5 text-foreground" />
                <span class="sr-only">{{ $t('notifications.toggle') }}</span>

                <span
                    v-if="unreadCountDisplay > 0"
                    class="absolute -top-0.5 -right-0.5 flex h-4 w-4 animate-in items-center justify-center rounded-full bg-destructive text-[10px] font-bold text-destructive-foreground shadow-sm zoom-in"
                >
                    {{ unreadCountDisplay }}
                </span>
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent class="w-80 sm:w-96" align="end">
            <DropdownMenuLabel
                class="flex items-center justify-between py-3 font-sans"
            >
                <span class="text-base font-semibold">{{
                    $t('notifications.title')
                }}</span>
                <Button
                    v-if="unreadCountDisplay > 0"
                    variant="ghost"
                    size="sm"
                    class="h-auto px-2 text-xs text-muted-foreground hover:text-primary"
                    @click.stop="markAllAsRead"
                >
                    {{ $t('notifications.mark_all_read') }}
                </Button>
            </DropdownMenuLabel>

            <DropdownMenuSeparator />

            <ScrollArea class="h-[300px]">
                <div
                    v-if="items.length === 0"
                    class="flex flex-col items-center justify-center py-8 text-center"
                >
                    <Bell class="mb-2 h-8 w-8 text-muted-foreground/30" />
                    <p class="text-sm text-muted-foreground">
                        {{ $t('notifications.empty') }}
                    </p>
                </div>

                <div v-else class="flex flex-col gap-1 p-1">
                    <DropdownMenuItem
                        v-for="item in items"
                        :key="item.id"
                        class="flex cursor-pointer items-start gap-3 rounded-md p-3 transition-colors focus:bg-accent focus:text-accent-foreground"
                        :class="{ 'bg-muted/40': item.read_at }"
                        @click="handleNotificationClick(item)"
                    >
                        <div
                            class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-border bg-background shadow-sm"
                        >
                            <component
                                :is="getIcon(item.type)"
                                class="h-4 w-4"
                                :class="getIconColor(item.type)"
                            />
                        </div>

                        <div class="flex flex-1 flex-col gap-1">
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-sm leading-none font-medium"
                                    :class="{
                                        'font-semibold text-foreground':
                                            !item.read_at,
                                        'text-muted-foreground': item.read_at,
                                    }"
                                >
                                    {{ item.title }}
                                </span>
                                <span
                                    class="flex h-2 w-2 shrink-0 rounded-full bg-primary"
                                    v-if="!item.read_at"
                                ></span>
                            </div>

                            <p
                                class="line-clamp-2 text-xs text-muted-foreground"
                            >
                                {{ item.description }}
                            </p>

                            <div
                                class="mt-1 flex items-center gap-1 text-[10px] text-muted-foreground"
                            >
                                <Clock class="h-3 w-3" />
                                <span>{{
                                    useTimeAgo(item.created_at).value
                                }}</span>
                            </div>
                        </div>
                    </DropdownMenuItem>
                </div>
            </ScrollArea>

            <DropdownMenuSeparator />

            <div class="p-2">
                <Button
                    variant="ghost"
                    class="w-full justify-center text-xs text-muted-foreground hover:text-foreground"
                    as-child
                >
                    <Link :href="'#'">
                        {{ $t('notifications.view_all') }}
                    </Link>
                </Button>
            </div>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
