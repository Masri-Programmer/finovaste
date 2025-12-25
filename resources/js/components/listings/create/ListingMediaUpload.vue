<script setup lang="ts">
import { Media } from '@/types/listings';
import type { FilePondFile } from 'filepond';
import { PropType, ref, watch } from 'vue';
import vueFilePond from 'vue-filepond';

// 1. CSS Imports (Keep as is)
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import 'filepond-plugin-media-preview/dist/filepond-plugin-media-preview.min.css';
import 'filepond-plugin-pdf-preview/dist/filepond-plugin-pdf-preview.min.css';
import 'filepond/dist/filepond.min.css';

// 2. Plugins (Keep as is)
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginMediaPreview from 'filepond-plugin-media-preview';
import FilePondPluginPdfPreview from 'filepond-plugin-pdf-preview';

import Label from '@/components/ui/label/Label.vue';

// 3. Register Plugins
const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview,
    FilePondPluginMediaPreview,
    FilePondPluginPdfPreview,
);

const props = defineProps({
    // We still accept them separately if needed, or you could merge props
    // but keeping strict typing is good.
    existingMedia: {
        type: Object as PropType<{
            images: Media[];
            documents: Media[];
            videos: Media[];
        }>,
        default: () => ({ images: [], documents: [], videos: [] }),
    },
    // We can merge errors or just show the first one
    errors: { type: Object, default: () => ({}) },
});

const emit = defineEmits<{
    (e: 'update:images', files: File[]): void;
    (e: 'update:documents', files: File[]): void;
    (e: 'update:videos', files: File[]): void;
    (e: 'media-delete', ids: number[]): void;
}>();

const pond = ref<InstanceType<typeof FilePond> | null>(null);
const myFiles = ref<any[]>([]); // Single source for the UI
const deletedMediaIds = ref<number[]>([]);

// --- COMBINED SERVER CONFIG ---
const serverConfig = {
    load: (source, load, error, progress, abort, headers) => {
        // 1. Flatten all media to find the item by ID
        const allMedia = [
            ...props.existingMedia.images,
            ...props.existingMedia.documents,
            ...props.existingMedia.videos,
        ];

        // The source passed by FilePond is the ID we set in mapToFiles
        const item = allMedia.find((m) => m.id === source);

        if (item) {
            fetch(item.original_url, { mode: 'cors', cache: 'no-cache' })
                .then((res) => {
                    if (!res.ok) throw new Error(`Status ${res.status}`);
                    return res.blob();
                })
                .then((blob) => load(blob))
                .catch((err) => {
                    console.error('Load Error:', err);
                    error('Could not load file');
                });
        } else {
            error('File object not found');
        }
    },
    process: null,
    revert: null,
    restore: null,
    fetch: null,
};

// --- MAPPING HELPER ---
const mapToFiles = (mediaItems: Media[]) => {
    return mediaItems.map((item) => ({
        source: item.id,
        options: {
            type: 'local',
            file: {
                name: item.file_name || 'Existing File',
                size: item.size || 0,
                type: item.mime_type,
            },
            metadata: { poster: item.original_url },
        },
    }));
};

// --- WATCHER: LOAD INITIAL DATA ---
watch(
    () => props.existingMedia,
    (newVal) => {
        if (newVal) {
            // Merge all backend media into one array for the single Dropzone
            myFiles.value = [
                ...mapToFiles(newVal.images),
                ...mapToFiles(newVal.documents),
                ...mapToFiles(newVal.videos),
            ];
        }
    },
    { immediate: true, deep: true },
);

// --- UNIFIED UPDATE HANDLER ---
const handleGlobalUpdate = (fileItems: FilePondFile[]) => {
    // 1. Filter out 'local' files (already on server)
    const newFiles = fileItems
        .filter((item) => item.origin !== 3)
        .map((fileItem) => fileItem.file as File);

    // 2. Buckets for sorting
    const images: File[] = [];
    const documents: File[] = [];
    const videos: File[] = [];

    // 3. Sort based on MIME type
    newFiles.forEach((file) => {
        const type = file.type;
        if (type.startsWith('image/')) {
            images.push(file);
        } else if (type.startsWith('video/')) {
            videos.push(file);
        } else {
            // Assume everything else is a document (pdf, doc, etc)
            documents.push(file);
        }
    });

    // 4. Emit separate events so Backend Service works without changes
    emit('update:images', images);
    emit('update:documents', documents);
    emit('update:videos', videos);
};

const handleRemoveFile = (error: any, fileItem: FilePondFile) => {
    if (error) return;
    if (fileItem.origin === 3 || typeof fileItem.source === 'number') {
        const id = Number(fileItem.source);
        if (!deletedMediaIds.value.includes(id)) {
            deletedMediaIds.value.push(id);
            emit('media-delete', deletedMediaIds.value);
        }
    }
};

const reset = () => {
    pond.value?.removeFiles();
    deletedMediaIds.value = [];
};

defineExpose({ reset });
</script>

<template>
    <div class="space-y-2">
        <Label>{{ $t('createListing.sections.media') }}</Label>

        <FilePond
            ref="pond"
            name="attachments"
            :files="myFiles"
            :server="serverConfig"
            :label-idle="$t('createListing.fields.media.dropzone_unified')"
            :allow-multiple="true"
            :accepted-file-types="
                [
                    'image/jpeg',
                    'image/png',
                    'image/webp', // Images
                    'video/mp4',
                    'video/quicktime', // Videos
                    'application/pdf',
                    'application/msword', // Docs
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ].join(',')
            "
            image-preview-height="150"
            pdf-preview-height="150"
            @updatefiles="handleGlobalUpdate"
            @removefile="handleRemoveFile"
            class="rounded-lg border border-dashed border-input bg-secondary text-secondary-foreground"
        />

        <span v-if="errors.images" class="block text-sm text-destructive">
            Images: {{ errors.images }}
        </span>
        <span v-if="errors.documents" class="block text-sm text-destructive">
            Docs: {{ errors.documents }}
        </span>
        <span v-if="errors.videos" class="block text-sm text-destructive">
            Videos: {{ errors.videos }}
        </span>
    </div>
</template>
