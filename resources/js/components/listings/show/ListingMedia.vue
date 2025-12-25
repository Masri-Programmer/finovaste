<template>
    <div class="flex flex-col gap-4">
        <div
            class="relative aspect-video w-full overflow-hidden rounded-lg bg-muted"
        >
            <Carousel
                ref="carousel"
                v-model="currentSlide"
                :items-to-show="1"
                :wrap-around="true"
            >
                <Slide
                    v-for="(slide, index) in allSlides"
                    :key="slide.uniqueId"
                    class="h-full w-full"
                >
                    <div
                        v-if="slide.type === 'video'"
                        class="relative flex h-full w-full items-center justify-center bg-black"
                    >
                        <video
                            class="h-full w-full object-contain"
                            :src="slide.url"
                            :poster="slide.thumbnail || fallbackImage"
                            muted
                            autoplay
                            loop
                            playsinline
                        >
                            Your browser does not support the video tag.
                        </video>

                        <div
                            class="group absolute inset-0 z-10 flex cursor-pointer items-center justify-center bg-transparent"
                            @click="showLightbox(index)"
                        >
                            <div
                                class="rounded-full bg-black/40 p-4 text-white backdrop-blur-sm transition-all group-hover:scale-110 group-hover:bg-black/60"
                            >
                                <MaximizeIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="group relative h-full w-full cursor-zoom-in"
                        @click="showLightbox(index)"
                    >
                        <img
                            :src="slide.url || fallbackImage"
                            alt="Listing Media"
                            class="h-full w-full object-cover"
                            @error="handleImageError"
                        />
                        <div
                            class="absolute inset-0 flex items-center justify-center bg-black/0 opacity-0 transition-all group-hover:bg-black/10 group-hover:opacity-100"
                        >
                            <MaximizeIcon
                                class="h-10 w-10 text-white drop-shadow-lg"
                            />
                        </div>
                    </div>
                </Slide>

                <template #addons>
                    <Navigation />
                </template>
            </Carousel>

            <div class="absolute top-4 left-4 z-10">
                <slot name="tags"></slot>
            </div>
        </div>

        <div
            v-if="
                allSlides.length > 1 ||
                (allSlides.length === 1 && allSlides[0].id !== -1)
            "
            class="grid grid-cols-4 gap-2 sm:grid-cols-6"
        >
            <button
                v-for="(slide, index) in allSlides"
                :key="slide.uniqueId"
                type="button"
                @click="slideTo(index)"
                class="relative aspect-video w-full overflow-hidden rounded-md bg-muted transition-all hover:opacity-80 focus:outline-none"
                :class="
                    currentSlide === index
                        ? 'ring-2 ring-primary ring-offset-2'
                        : 'opacity-60'
                "
            >
                <img
                    :src="
                        slide.type === 'video'
                            ? slide.thumbnail ||
                              'https://placehold.co/1920x1080?text=Video'
                            : slide.url || fallbackImage
                    "
                    class="h-full w-full object-cover"
                    :class="{ 'scale-110': currentSlide === index }"
                    @error="handleImageError"
                />

                <div
                    v-if="slide.type === 'video'"
                    class="absolute inset-0 flex items-center justify-center bg-black/30"
                >
                    <VideoIcon class="h-6 w-6 text-white drop-shadow-md" />
                </div>
            </button>
        </div>

        <VueEasyLightbox
            :visible="visibleRef"
            :imgs="lightboxSources"
            :index="indexRef"
            @hide="onHide"
            @on-index-change="onIndexChange"
        >
            <template #item="{ index }">
                <div class="flex h-full w-full items-center justify-center">
                    <video
                        v-if="allSlides[index].type === 'video'"
                        :key="allSlides[index].uniqueId"
                        controls
                        autoplay
                        playsinline
                        class="max-h-[90vh] max-w-[90vw] object-contain"
                        :src="allSlides[index].url"
                        :poster="allSlides[index].thumbnail"
                    >
                        Your browser does not support the video tag.
                    </video>

                    <img
                        v-else
                        :src="allSlides[index].url || fallbackImage"
                        class="max-h-[90vh] max-w-[90vw] object-contain"
                    />
                </div>
            </template>
        </VueEasyLightbox>
    </div>
</template>

<script setup lang="ts">
import { Maximize as MaximizeIcon, Video as VideoIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import VueEasyLightbox from 'vue-easy-lightbox';
import { Carousel, Navigation, Slide } from 'vue3-carousel';
import 'vue3-carousel/dist/carousel.css';

const fallbackImage = 'https://placehold.co/600x400?text=No+Media+Available';

const props = defineProps<{
    media?: {
        images: Array<{
            id: number;
            url: string;
            thumbnail: string;
            mime_type: string;
        }>;
        videos: Array<{
            id: number;
            url: string;
            thumbnail: string;
            mime_type: string;
        }>;
    };
}>();

const currentSlide = ref(0);
const carousel = ref(null);
const visibleRef = ref(false);
const indexRef = ref(0);

const handleImageError = (e: Event) => {
    const target = e.target as HTMLImageElement;
    target.src = fallbackImage;
};

const allSlides = computed(() => {
    const images =
        props.media?.images?.map((img) => ({
            ...img,
            type: 'image',
            uniqueId: `img-${img.id}`,
        })) || [];

    const videos =
        props.media?.videos?.map((vid) => ({
            ...vid,
            type: 'video',
            uniqueId: `vid-${vid.id}`,
        })) || [];

    // You can choose to put videos first or last.
    // Putting videos first is often better for engagement if present.
    const combined = [...images, ...videos];

    if (combined.length === 0) {
        return [
            {
                id: -1,
                url: fallbackImage,
                thumbnail: fallbackImage,
                mime_type: 'image/png',
                type: 'image',
                uniqueId: 'placeholder-no-media',
            },
        ];
    }

    return combined;
});

const lightboxSources = computed(() =>
    allSlides.value.map((s) => s.url || fallbackImage),
);

const slideTo = (index: number) => {
    currentSlide.value = index;
};

// --- LIGHTBOX METHODS ---
const showLightbox = (index: number) => {
    indexRef.value = index;
    visibleRef.value = true;
};

const onHide = () => {
    visibleRef.value = false;
};
const onIndexChange = (oldIndex: number, newIndex: number) => {
    indexRef.value = newIndex;
    currentSlide.value = newIndex;
};
</script>

<style scoped>
:deep(.carousel__prev),
:deep(.carousel__next) {
    background-color: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    color: #000;
}
:deep(.carousel__prev:hover),
:deep(.carousel__next:hover) {
    background-color: white;
}

:deep(.vel-modal) {
    z-index: 9999;
}
</style>
