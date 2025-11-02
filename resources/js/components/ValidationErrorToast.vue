<script setup lang="ts">
import { PropType } from 'vue';

defineProps({
    /**
     * Eine Liste von deutschen Fehlermeldungen, die von Laravel kommen.
     */
    errors: {
        type: Array as PropType<string[]>,
        required: true,
    },
});
</script>

<template>
    <div>
        <span class="font-semibold">Das Formular enthält Fehler:</span>

        <ul class="mt-2 list-inside list-disc text-sm">
            <li v-for="(error, index) in errors" :key="index">
                {{ error }}
            </li>
        </ul>
    </div>
</template>
