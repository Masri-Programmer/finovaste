<script setup lang="ts">
import { PropType, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import '../../../css/filepond.css';

import type { FilePondFile } from 'filepond';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
// import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
// import FilePondPluginMediaPreview from 'filepond-plugin-media-preview';
import vueFilePond from 'vue-filepond';
import Label from '../ui/label/Label.vue';

// Feature ++
const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    // FilePondPluginImagePreview,
    // FilePondPluginMediaPreview,
);

const { t } = useI18n();

// --- PROPS ---
// We use v-model for a 3-way binding with the parent form
defineProps({
    images: {
        type: Array as PropType<File[]>,
        default: () => [],
    },
    documents: {
        type: Array as PropType<File[]>,
        default: () => [],
    },
    videos: {
        type: Array as PropType<File[]>,
        default: () => [],
    },
    // Server-side errors
    imageError: {
        type: String,
        default: '',
    },
    documentError: {
        type: String,
        default: '',
    },
    videoError: {
        type: String,
        default: '',
    },
});

// --- EMITS ---
const emit = defineEmits<{
    (e: 'update:images', files: File[]): void;
    (e: 'update:documents', files: File[]): void;
    (e: 'update:videos', files: File[]): void;
}>();

// --- FILEPOND REFS ---
const imagePond = ref<InstanceType<typeof FilePond> | null>(null);
const documentPond = ref<InstanceType<typeof FilePond> | null>(null);
const videoPond = ref<InstanceType<typeof FilePond> | null>(null);

// --- FILE HANDLERS ---
const handleImagesUpdate = (fileItems: FilePondFile[]) => {
    const files = fileItems.map((fileItem) => fileItem.file as File);
    emit('update:images', files);
};

const handleDocumentsUpdate = (fileItems: FilePondFile[]) => {
    const files = fileItems.map((fileItem) => fileItem.file as File);
    emit('update:documents', files);
};

const handleVideosUpdate = (fileItems: FilePondFile[]) => {
    const files = fileItems.map((fileItem) => fileItem.file as File);
    emit('update:videos', files);
};

const reset = () => {
    imagePond.value?.removeFiles();
    documentPond.value?.removeFiles();
    videoPond.value?.removeFiles();
};
defineExpose({
    reset,
});
</script>

<template>
    <div class="space-y-6">
        <div class="space-y-2">
            <Label>
                {{ t('listing.createListing.fields.media.images') }}
            </Label>
            <FilePond
                ref="imagePond"
                name="images"
                :label-idle="t('listing.createListing.fields.media.dropzone')"
                :allow-multiple="true"
                :accepted-file-types="'image/jpeg, image/png, image/webp'"
                @updatefiles="handleImagesUpdate"
                class="rounded-lg border border-dashed border-input bg-secondary text-secondary-foreground"
            />
            <span v-if="imageError" class="text-sm text-destructive">
                {{ imageError }}
            </span>
        </div>

        <div class="space-y-2">
            <Label>
                {{ t('listing.createListing.fields.media.documents') }}
            </Label>
            <FilePond
                ref="documentPond"
                name="documents"
                :label-idle="t('listing.createListing.fields.media.dropzone')"
                :allow-multiple="true"
                :accepted-file-types="'application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document'"
                @updatefiles="handleDocumentsUpdate"
                class="rounded-lg border border-dashed border-input bg-secondary text-secondary-foreground"
            />
            <span v-if="documentError" class="text-sm text-destructive">
                {{ documentError }}
            </span>
        </div>

        <div class="space-y-2">
            <Label>
                {{ t('listing.createListing.fields.media.videos') }}
            </Label>
            <FilePond
                ref="videoPond"
                name="videos"
                :label-idle="t('listing.createListing.fields.media.dropzone')"
                :allow-multiple="true"
                :accepted-file-types="'video/mp4, video/quicktime'"
                @updatefiles="handleVideosUpdate"
                class="rounded-lg border border-dashed border-input bg-secondary text-secondary-foreground"
            />
            <span v-if="videoError" class="text-sm text-destructive">
                {{ videoError }}
            </span>
        </div>
    </div>
</template>
