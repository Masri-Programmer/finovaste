<script setup lang="ts">
import { useMoney } from '@/composables/useMoney';
import { computed } from 'vue';

interface Props {
    amount: number | string | null;
    currency?: string;
    locale?: string;
}

const props = withDefaults(defineProps<Props>(), {
    currency: 'EUR',
    locale: 'de-DE',
    amount: 0,
});

const { formatMoney } = useMoney();

const formattedValue = computed(() => {
    return formatMoney(props.amount, props.currency, props.locale);
});
</script>

<template>
    <span class="font-mono tracking-tight text-gray-900 tabular-nums">
        {{ formattedValue }}
    </span>
</template>
