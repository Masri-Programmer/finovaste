<script setup lang="ts">
import { useToast } from '@/composables/useToast';
import {
    AlertCircle,
    AlertTriangle,
    CheckCircle2,
    Info,
    X,
} from 'lucide-vue-next';
import { onMounted } from 'vue';

import type { ToastType } from '@/types';

// Logic
const { toastState, remove, enableGlobalHandling } = useToast();

onMounted(() => {
    enableGlobalHandling();
});

const getIcon = (type: string) => {
    switch (type as ToastType) {
        case 'success':
            return CheckCircle2;
        case 'error':
            return AlertCircle;
        case 'warning':
            return AlertTriangle;
        case 'info':
            return Info;
        default:
            return Info;
    }
};

const getVariantClasses = (type: string) => {
    switch (type as ToastType) {
        case 'error':
            return 'border-destructive/50 bg-destructive text-destructive-foreground';
        case 'success':
            return 'border-primary/20 bg-primary/10 text-primary';
        case 'warning':
            return 'border-yellow-500/50 bg-yellow-500/10 text-yellow-600 dark:text-yellow-500';
        default:
            return 'border-border bg-background text-foreground';
    }
};
</script>

<template>
    <div
        class="fixed right-0 bottom-0 z-[100] flex max-h-screen w-full flex-col-reverse gap-2 p-4 sm:top-auto sm:right-0 sm:bottom-0 sm:flex-col md:max-w-[420px]"
    >
        <TransitionGroup
            tag="div"
            enter-active-class="transition ease-out duration-300"
            enter-from-class="transform translate-y-full opacity-0 sm:translate-y-0 sm:translate-x-full"
            enter-to-class="transform translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="transform opacity-100"
            leave-to-class="transform opacity-0 scale-95"
            move-class="transition-all duration-300 ease-in-out"
        >
            <div
                v-for="item in toastState.items"
                :key="item.id"
                class="pointer-events-auto relative flex w-full items-center justify-between space-x-4 overflow-hidden rounded-[var(--radius)] border p-6 pr-8 shadow-lg transition-all"
                :class="getVariantClasses(item.type || 'default')"
                role="alert"
            >
                <div class="flex gap-3">
                    <component
                        :is="getIcon(item.type || 'default')"
                        class="h-5 w-5 shrink-0"
                        :class="{
                            'text-destructive-foreground':
                                item.type === 'error',
                            'text-primary': item.type === 'success',
                        }"
                    />

                    <div class="grid gap-1">
                        <h3
                            v-if="item.title"
                            class="leading-none font-semibold tracking-tight"
                        >
                            {{ item.title }}
                        </h3>
                        <p v-if="item.message" class="text-sm opacity-90">
                            {{ item.message }}
                        </p>
                    </div>
                </div>

                <button
                    @click="remove(item.id)"
                    class="absolute top-2 right-2 rounded-md p-1 opacity-0 transition-opacity group-hover:opacity-100 hover:bg-black/5 focus:opacity-100 focus:ring-2 focus:outline-none dark:hover:bg-white/10"
                    :class="{
                        'text-destructive-foreground/80 hover:text-destructive-foreground':
                            item.type === 'error',
                        'text-foreground/50 hover:text-foreground':
                            item.type !== 'error',
                    }"
                    :aria-label="$t('components.toast.close')"
                >
                    <X class="h-4 w-4" />
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>
