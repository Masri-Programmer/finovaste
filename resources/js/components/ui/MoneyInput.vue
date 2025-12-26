<script setup lang="ts">
import { ref, watch, computed } from 'vue';

interface Props {
    modelValue: number | string | null;
    currency?: string;
    placeholder?: string;
}

const props = withDefaults(defineProps<Props>(), {
    currency: undefined, // Let it fall back to computed default
    modelValue: null,
    placeholder: '0.00',
});

import { usePage } from '@inertiajs/vue3';
import { useMoney } from '@/composables/useMoney';

const { getCurrencySymbol } = useMoney();
const page = usePage();

const effectiveCurrency = computed(() => {
    return props.currency || page.props.auth.user?.currency || page.props.money?.default || 'EUR';
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

// Internal state for the input field display
const displayValue = ref<string | number>(props.modelValue ?? '');

// Helper to get currency symbol (e.g., € or $)
const currencySymbol = computed(() => {
    return getCurrencySymbol(effectiveCurrency.value, page.props.auth.user?.locale);
});

const onInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const rawValue = target.value;

    displayValue.value = rawValue;

    // Sanitize: allow numbers and one decimal point.
    // Example: "12,99" becomes "12.99" for backend consistency
    let sanitized = rawValue.replace(',', '.').replace(/[^0-9.]/g, '');

    // Prevent multiple decimals
    const parts = sanitized.split('.');
    if (parts.length > 2) {
        sanitized = parts[0] + '.' + parts.slice(1).join('');
    }

    emit('update:modelValue', sanitized);
};

const onBlur = () => {
    if (!displayValue.value) return;

    const num = parseFloat(displayValue.value.toString().replace(',', '.'));
    
    if (!isNaN(num)) {
        // Format nicely on blur (e.g., user typed "12", changes to "12.00")
        displayValue.value = num.toFixed(2);
        // Ensure backend gets the clean value
        emit('update:modelValue', num.toFixed(2));
    }
};

// Watch for external changes (e.g., loading saved data)
watch(() => props.modelValue, (newValue) => {
    if (newValue !== displayValue.value) {
        displayValue.value = newValue ?? '';
    }
});
</script>

<template>
    <div class="relative rounded-md shadow-sm">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <span class="text-gray-500 sm:text-sm">{{ currencySymbol }}</span>
        </div>
        
        <input
            type="text"
            inputmode="decimal"
            class="block w-full rounded-md border-gray-300 pl-8 pr-12 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            :placeholder="placeholder"
            :value="displayValue"
            @input="onInput"
            @blur="onBlur"
        />

        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <span class="text-gray-500 sm:text-sm uppercase">{{ currency }}</span>
        </div>
    </div>
</template>