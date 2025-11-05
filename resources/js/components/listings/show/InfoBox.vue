<template>
    <Card>
        <CardContent class="flex items-center space-x-4 p-6">
            <div
                class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg"
                :class="iconBgClass"
            >
                <component :is="icon" class="h-6 w-6" />
            </div>
            <div>
                <div class="text-sm font-medium text-muted-foreground">
                    {{ title }}
                </div>
                <div class="text-2xl font-bold text-foreground">
                    {{ value }}
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import type { FunctionalComponent } from 'vue';

defineProps<{
    icon: FunctionalComponent;
    title: string;
    value: string;
    iconBgClass: string; // e.g., 'bg-blue-100 text-blue-700'
}>();
</script>
