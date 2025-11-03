<template>
    <section>
        <div
            class="relative aspect-video w-full overflow-hidden rounded-lg bg-muted"
        >
            <img
                src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600"
                alt="Luxury Apartment"
                class="h-full w-full object-cover"
            />
            <Button
                variant="secondary"
                class="absolute right-4 bottom-4 shadow-md"
            >
                <Video class="mr-2 h-4 w-4" />
                {{ t('listings_show.watchVideo') }}
            </Button>
            <span
                class="absolute top-4 left-4 rounded-full bg-primary/80 px-3 py-1 text-xs font-medium text-primary-foreground backdrop-blur-sm"
            >
                {{ t('listings_show.investmentTag') }}
            </span>
        </div>
        <div class="mt-2 grid grid-cols-4 gap-2">
            <div
                v-for="i in 4"
                :key="i"
                class="aspect-video w-full cursor-pointer overflow-hidden rounded-md bg-muted ring-offset-background transition-opacity hover:opacity-80 focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:outline-none"
                :class="{ 'ring-2 ring-primary': i === 1 }"
            >
                <img
                    src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=300"
                    alt="Thumbnail"
                    class="h-full w-full object-cover"
                />
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<style scoped></style>
