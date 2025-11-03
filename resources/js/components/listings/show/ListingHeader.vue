<template>
    <header class="mb-6 flex items-center justify-between">
        <Link
            :href="home.url()"
            class="flex items-center text-sm font-medium text-muted-foreground hover:text-primary"
        >
            <ArrowLeft class="mr-2 h-4 w-4" />
            {{ t('listings_show.backToMarketplace') }}
        </Link>
        <div class="flex items-center space-x-2">
            <Button variant="ghost" size="icon">
                <Share2 class="h-5 w-5" />
            </Button>
            <Button variant="ghost" size="icon">
                <Heart class="h-5 w-5" />
            </Button>
        </div>
    </header>
</template>

<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, Heart, Share2 } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<style scoped></style>
