<template>
    <div
        v-if="documents && documents.length > 0"
        class="rounded-lg border bg-card text-card-foreground shadow-sm"
    >
        <div class="p-6">
            <h3 class="mb-4 text-lg leading-none font-semibold tracking-tight">
                {{ $t('listings.documents') }}
            </h3>
            <ul class="grid gap-3">
                <li v-for="doc in documents" :key="doc.id">
                    <a
                        :href="doc.url"
                        target="_blank"
                        class="flex items-center rounded-md border p-3 transition-colors hover:bg-muted"
                    >
                        <FileText class="mr-3 h-5 w-5 text-primary" />
                        <div class="flex-1 overflow-hidden">
                            <p
                                class="truncate text-sm font-medium text-foreground"
                            >
                                {{ doc.file_name }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ doc.size }}
                            </p>
                        </div>
                        <Download class="ml-2 h-4 w-4 text-muted-foreground" />
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Download, FileText } from 'lucide-vue-next';

defineProps<{
    documents: Array<{
        id: number;
        url: string;
        file_name: string;
        size: string;
    }>;
}>();
</script>
