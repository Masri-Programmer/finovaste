<script setup lang="ts">
import { computed, useAttrs } from 'vue';

import { useAppearance } from '@/composables/useAppearance';

const { isDark } = useAppearance();
const props = defineProps({
    id: {
        type: String,

        required: true,
    },

    background: {
        type: String as () => 'default' | 'muted',

        default: 'default',
    },

    fullWidth: {
        type: Boolean,

        default: false,
    },

    parallaxImage: {
        type: String,

        default: null,
    },

    ariaLabelledby: {
        type: String,

        default: null,
    },
});

const attrs = useAttrs();
const sectionClasses = computed(() => [
    {
        'page-background': props.id.endsWith('-page') && isDark.value,

        // 'bg-muted/40 dark:bg-background': props.background === 'muted',

        'container-custom-y': !props.fullWidth,

        'parallax-section': !!props.parallaxImage,
    },

    attrs.class,
]);

const sectionStyle = computed(() => {
    if (!props.parallaxImage) {
        return {};
    }

    return {
        backgroundImage: `url(${props.parallaxImage})`,
    };
});
</script>

<template>
    <section :id="id" :class="sectionClasses" :style="sectionStyle" :aria-labelledby="ariaLabelledby">
        <div class="container-custom">
            <slot />
            <!-- <RippleButton> Click me! </RippleButton> -->
        </div>
    </section>
</template>
