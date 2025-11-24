<script setup lang="ts">
import { Media } from '@/types/listings';
import type { FilePondFile } from 'filepond';
import { PropType, ref, watch } from 'vue';
import vueFilePond from 'vue-filepond';

// 1. CSS Imports
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import 'filepond-plugin-media-preview/dist/filepond-plugin-media-preview.min.css';
import 'filepond-plugin-pdf-preview/dist/filepond-plugin-pdf-preview.min.css';
import 'filepond/dist/filepond.min.css';

// 2. Plugins
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
    images: { type: Array as PropType<File[]>, default: () => [] },
    documents: { type: Array as PropType<File[]>, default: () => [] },
    videos: { type: Array as PropType<File[]>, default: () => [] },
    existingMedia: {
        type: Object as PropType<{
            images: Media[];
            documents: Media[];
            videos: Media[];
        }>,
        default: () => ({ images: [], documents: [], videos: [] }),
    },
    imageError: { type: String, default: '' },
    documentError: { type: String, default: '' },
    videoError: { type: String, default: '' },
});

const emit = defineEmits<{
    (e: 'update:images', files: File[]): void;
    (e: 'update:documents', files: File[]): void;
    (e: 'update:videos', files: File[]): void;
    (e: 'media-delete', ids: number[]): void;
}>();

const imagePond = ref<InstanceType<typeof FilePond> | null>(null);
const documentPond = ref<InstanceType<typeof FilePond> | null>(null);
const videoPond = ref<InstanceType<typeof FilePond> | null>(null);

const myImages = ref<any[]>([]);
const myDocuments = ref<any[]>([]);
const myVideos = ref<any[]>([]);

const serverConfig = {
    load: (source, load, error, progress, abort, headers) => {
        // 1. Find the media item
        const allMedia = [
            ...props.existingMedia.images,
            ...props.existingMedia.documents,
            ...props.existingMedia.videos,
        ];
        const item = allMedia.find((m) => m.id === source);

        if (item) {
            // 2. FETCH WITH CORS MODE
            fetch(item.original_url, {
                mode: 'cors', // Explicitly request CORS
                cache: 'no-cache',
            })
                .then((res) => {
                    if (!res.ok)
                        throw new Error(`Server returned ${res.status}`);
                    return res.blob();
                })
                .then((blob) => {
                    // Success: Pass the blob to FilePond
                    load(blob);
                })
                .catch((err) => {
                    console.error('FilePond Load Error:', err);
                    // Tell FilePond the load failed
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

// --- MAPPING LOGIC ---
const mapToFiles = (
    mediaItems: Media[],
    fileType: 'image' | 'document' | 'video',
) => {
    return mediaItems.map((item) => {
        return {
            source: item.id, // We keep ID as source for easy deletion tracking
            options: {
                type: 'local', // Tells FilePond to call server.load(source)
                file: {
                    name: item.file_name || 'Existing File',
                    size: item.size || 0,
                    type: item.mime_type,
                },
                metadata: {
                    poster: item.original_url, // Helper for Image Preview
                },
            },
        };
    });
};

watch(
    () => props.existingMedia,
    (newVal) => {
        if (newVal) {
            myImages.value = mapToFiles(newVal.images, 'image');
            myDocuments.value = mapToFiles(newVal.documents, 'document');
            myVideos.value = mapToFiles(newVal.videos, 'video');
        }
    },
    { immediate: true, deep: true },
);

// --- HANDLERS ---
const handleImagesUpdate = (fileItems: FilePondFile[]) => {
    const newFiles = fileItems
        .filter((item) => item.origin !== 3) // 3 = Local
        .map((fileItem) => fileItem.file as File);
    emit('update:images', newFiles);
};

const handleDocumentsUpdate = (fileItems: FilePondFile[]) => {
    const newFiles = fileItems
        .filter((item) => item.origin !== 3)
        .map((fileItem) => fileItem.file as File);
    emit('update:documents', newFiles);
};

const handleVideosUpdate = (fileItems: FilePondFile[]) => {
    const newFiles = fileItems
        .filter((item) => item.origin !== 3)
        .map((fileItem) => fileItem.file as File);
    emit('update:videos', newFiles);
};

const deletedMediaIds = ref<number[]>([]);

const handleRemoveFile = (error: any, fileItem: FilePondFile) => {
    if (error) return;
    // Check if it's a local file (origin 3) or has a numeric source (our ID)
    if (fileItem.origin === 3 || typeof fileItem.source === 'number') {
        const id = Number(fileItem.source);
        if (!deletedMediaIds.value.includes(id)) {
            deletedMediaIds.value.push(id);
            emit('media-delete', deletedMediaIds.value);
        }
    }
};

const reset = () => {
    imagePond.value?.removeFiles();
    documentPond.value?.removeFiles();
    videoPond.value?.removeFiles();
    deletedMediaIds.value = [];
};

defineExpose({ reset });
</script>

<template>
    <div class="space-y-6">
        <div class="space-y-2">
            <Label>{{ $t('createListing.fields.media.images') }}</Label>
            <FilePond
                ref="imagePond"
                name="images"
                :files="myImages"
                :server="serverConfig"
                :label-idle="$t('createListing.fields.media.dropzone')"
                :allow-multiple="true"
                :accepted-file-types="'image/jpeg, image/png, image/webp'"
                image-preview-height="150"
                @updatefiles="handleImagesUpdate"
                @removefile="handleRemoveFile"
                class="rounded-lg border border-dashed border-input bg-secondary text-secondary-foreground"
            />
            <span v-if="imageError" class="text-sm text-destructive">{{
                imageError
            }}</span>
        </div>

        <div class="space-y-2">
            <Label>{{ $t('createListing.fields.media.documents') }}</Label>
            <FilePond
                ref="documentPond"
                name="documents"
                :files="myDocuments"
                :server="serverConfig"
                :label-idle="$t('createListing.fields.media.dropzone')"
                :allow-multiple="true"
                :accepted-file-types="'application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document'"
                pdf-preview-height="150"
                pdf-component-extra-params="toolbar=0&navpanes=0&scrollbar=0"
                @updatefiles="handleDocumentsUpdate"
                @removefile="handleRemoveFile"
                class="rounded-lg border border-dashed border-input bg-secondary text-secondary-foreground"
            />
            <span v-if="documentError" class="text-sm text-destructive">{{
                documentError
            }}</span>
        </div>

        <div class="space-y-2">
            <Label>{{ $t('createListing.fields.media.videos') }}</Label>
            <FilePond
                ref="videoPond"
                name="videos"
                :files="myVideos"
                :server="serverConfig"
                :label-idle="$t('createListing.fields.media.dropzone')"
                :allow-multiple="true"
                :accepted-file-types="'video/mp4, video/quicktime'"
                @updatefiles="handleVideosUpdate"
                @removefile="handleRemoveFile"
                class="rounded-lg border border-dashed border-input bg-secondary text-secondary-foreground"
            />
            <span v-if="videoError" class="text-sm text-destructive">{{
                videoError
            }}</span>
        </div>
    </div>
</template>
