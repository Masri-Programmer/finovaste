<template>
    <component :is="componentTag" v-bind="componentAttrs">
        <div
            v-if="isDesktop"
            class="text-sm leading-tight font-medium capitalize"
        >
            {{ $t(item.i18nKey) }}
        </div>
        <template v-else>{{ $t(item.i18nKey) }}</template>
    </component>
</template>

<script setup lang="ts">
import { useScrollSpy } from '@/composables/useScrollSpy';
import { Link } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed, type Component } from 'vue';

const props = defineProps<{
    item: {
        type: 'scroll' | 'link' | 'external';
        i18nKey: string;
        target: any;
    };
    onNavigate?: () => void;
    context: 'desktop' | 'mobile';
}>();

const { activeSection, handleScroll } = useScrollSpy();
const isDesktop = computed(() => props.context === 'desktop');
const isActive = computed(
    () => props.item.type === 'scroll' && props.item.target === activeSection,
);

const componentTag = computed<string | Component>(() => {
    switch (props.item.type) {
        case 'scroll':
            return 'button';
        case 'link':
            return Link;
        case 'external':
            return 'a';
        default:
            return 'div';
    }
});

const itemClasses = computed(() => [
    isDesktop.value
        ? 'flex h-full w-full select-none flex-col justify-end rounded-xl p-4 text-left no-underline outline-none hover:bg-accent focus:bg-accent'
        : 'text-left hover:underline',
    { 'text-blue-600 hover:text-blue-700': isActive.value },
]);
const handleScrollClick = () => {
    handleScroll(props.item.target as string);
    if (props.onNavigate) {
        props.onNavigate();
    }
};

const componentAttrs = computed(() => {
    const attrs: Record<string, any> = {
        class: itemClasses.value,
    };

    switch (props.item.type) {
        case 'scroll':
            attrs['aria-label'] = trans(props.item.i18nKey);
            attrs.onClick = handleScrollClick;
            break;
        case 'link':
            if (typeof props.item.target === 'string') {
                attrs.href = props.item.target;
            } else if (typeof props.item.target.url === 'function') {
                attrs.href = props.item.target.url();
            } else if (
                props.item.target.route &&
                Array.isArray(props.item.target.params)
            ) {
                attrs.href = props.item.target.route.url(
                    ...props.item.target.params,
                );
            } else {
                console.warn('Invalid link target:', props.item.target);
                attrs.href = '#';
            }
            if (props.onNavigate) attrs.onClick = props.onNavigate;
            break;
        case 'external':
            attrs.href = props.item.target as string;
            attrs.target = '_blank';
            attrs.rel = 'noopener noreferrer';
            if (props.onNavigate) attrs.onClick = props.onNavigate;
            break;
    }
    return attrs;
});
</script>
