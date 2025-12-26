<script setup lang="ts">
import logo from '@/images/ranalp_logo_1.jpg';
import type { HTMLAttributes } from 'vue';
defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img
        :src="logo"
        :alt="$page.props.name + ' logo'"
        :class="className"
        v-bind="$attrs"
    />
</template>
