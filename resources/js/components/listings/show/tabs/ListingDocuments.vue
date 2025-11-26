<template>
    <div>
        <div v-if="documents && documents.length > 0" class="space-y-3">
            <div
                v-for="doc in documents"
                :key="doc.id"
                class="flex items-center justify-between rounded-lg border p-3 transition-colors hover:bg-muted/50"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-md bg-primary/10"
                    >
                        <FileText class="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <div class="text-sm font-medium text-foreground">
                            {{ doc.file_name }}
                        </div>
                        <div class="text-xs text-muted-foreground">
                            {{ doc.size }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        @click="openPreview(doc)"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50"
                        title="Preview"
                    >
                        <Eye class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>

        <p v-else class="text-center text-muted-foreground">
            {{ $t('reviews.tabs.documents_empty') }}
        </p>

        <Teleport to="body">
            <div
                v-if="isPreviewOpen && previewDoc"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm"
                @click.self="closePreview"
            >
                <div
                    class="relative flex h-[85vh] w-full max-w-5xl flex-col overflow-hidden rounded-lg bg-background shadow-lg"
                >
                    <div
                        class="flex items-center justify-between border-b px-4 py-3"
                    >
                        <h3 class="truncate pr-4 text-lg font-semibold">
                            {{ previewDoc.file_name }}
                        </h3>
                        <button
                            @click="closePreview"
                            class="rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:outline-none"
                        >
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <div
                        class="relative flex flex-1 items-center justify-center overflow-hidden bg-muted/20"
                    >
                        <img
                            v-if="isImage"
                            :src="previewDoc.url"
                            class="max-h-full max-w-full object-contain"
                            alt="Preview"
                        />

                        <iframe
                            v-else
                            :src="previewDoc.url"
                            class="h-full w-full border-0"
                            title="Document Preview"
                        ></iframe>
                    </div>

                    <div
                        class="flex justify-end gap-2 border-t bg-background p-4"
                    >
                        <button
                            @click="closePreview"
                            class="rounded-md border px-4 py-2 text-sm font-medium hover:bg-muted"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup lang="ts">
import { ListingDocument } from '@/types/listings';
import { Eye, FileText, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    documents?: ListingDocument[];
}>();

const previewDoc = ref<ListingDocument | null>(null);
const isPreviewOpen = ref(false);

const openPreview = (doc: ListingDocument) => {
    previewDoc.value = doc;
    isPreviewOpen.value = true;
};

const closePreview = () => {
    isPreviewOpen.value = false;
    previewDoc.value = null;
};

const isImage = computed(() => {
    if (!previewDoc.value) return false;
    const ext = previewDoc.value.file_name.split('.').pop()?.toLowerCase();
    return ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext || '');
});
</script>
