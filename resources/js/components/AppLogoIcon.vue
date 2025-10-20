<script setup lang="ts">
import logo from '@/images/LogoFinoVaste.png';
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
    <img :src="logo" alt="finvasto logo" :class="className" v-bind="$attrs" />
</template>
