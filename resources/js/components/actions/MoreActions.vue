<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { MoreHorizontal } from 'lucide-vue-next';
</script>

<template>
    <Button
        variant="secondary"
        class="relative flex h-10 w-10 items-center justify-center rounded-full bg-background/70 text-gray-600 backdrop-blur-sm hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
        @click.prevent
    >
        <span class="sr-only">More options</span>
        <MoreHorizontal class="h-5 w-5" />
    </Button>
</template>
