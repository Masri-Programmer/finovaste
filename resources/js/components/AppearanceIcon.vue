<script setup lang="ts">
import { computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';
import FooterToolbarButton from '@/components/FooterToolbarButton.vue';
import { Monitor, Moon, Sun } from 'lucide-vue-next';

const { appearance, updateAppearance } = useAppearance();

const themes = [
    { value: 'light', Icon: Sun, label: 'Light' },
    { value: 'dark', Icon: Moon, label: 'Dark' },
    { value: 'system', Icon: Monitor, label: 'System' },
] as const;

const currentIcon = computed(() => {
    return themes.find((theme) => theme.value === appearance.value)?.Icon ?? Monitor;
});

const ariaLabel = computed(() => {
    const currentLabel = themes.find((theme) => theme.value === appearance.value)?.label ?? 'System';
    return `Toggle theme, current is ${currentLabel}`;
});

const toggleAppearance = () => {
    const currentIndex = themes.findIndex((theme) => theme.value === appearance.value);
    const nextIndex = (currentIndex + 1) % themes.length;
    updateAppearance(themes[nextIndex].value);
};
</script>

<template>
    <FooterToolbarButton
        tooltip-text="Toggle theme"
        :aria-label="ariaLabel"
        @click="toggleAppearance"
    >
        <component :is="currentIcon" class="h-5 w-5" />
    </FooterToolbarButton>
</template>