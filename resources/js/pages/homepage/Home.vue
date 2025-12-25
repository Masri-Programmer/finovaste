<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { useClipboard, useWindowScroll } from '@vueuse/core';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import { useToast } from 'vue-toastification';

// UI Components (Shadcn/ui)
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

// Icons (Lucide Vue)
import {
    ArrowRight,
    CheckCircle2,
    Gavel,
    Gift,
    Heart,
    Rocket,
    Sparkles,
    Star,
} from 'lucide-vue-next';

// Routes
import { register } from '@/routes';
import { create } from '@/routes/listings';
import Marketplace from './Marketplace.vue';

// --- Composables ---
const toast = useToast();
const { copy, isSupported } = useClipboard();
const { y } = useWindowScroll();

// --- Data & Configuration ---

/**
 * Feature Grid Data
 * Mapped to i18n keys for strict localization.
 */
const features = [
    {
        id: 'private',
        icon: Gift,
        color: 'text-pink-500',
        bg: 'bg-pink-500/10',
        i18nKey: 'home.features.private',
        route: 'web.campaign.create.private',
    },
    {
        id: 'founders',
        icon: Rocket,
        color: 'text-purple-500',
        bg: 'bg-purple-500/10',
        i18nKey: 'home.features.founders',
        route: 'web.campaign.create.startup',
    },
    {
        id: 'volunteer',
        icon: Heart,
        color: 'text-red-500',
        bg: 'bg-red-500/10',
        i18nKey: 'home.features.volunteer',
        route: 'web.campaign.create.volunteer',
    },
    {
        id: 'charity',
        icon: Gavel,
        color: 'text-blue-500',
        bg: 'bg-blue-500/10',
        i18nKey: 'home.features.charity',
        route: 'web.marketplace.index',
    },
];

/**
 * Social Proof / Trust Indicators (Static Example)
 */
const stats = [
    { label: 'home.stats.users', value: '10k+' },
    { label: 'home.stats.campaigns', value: '500+' },
    { label: 'home.stats.donated', value: '€2M+' },
];

// --- Actions ---

const navigateTo = (url: string) => {
    router.visit(url);
};

const handleShare = async () => {
    if (!isSupported.value) {
        toast.error(trans('validation.clipboard_unsupported'));
        return;
    }
    await copy(window.location.href);
    toast.success(trans('home.hero.share_success'));
};

// Dynamic visual calculation for hero opacity on scroll
const heroOpacity = computed(() => {
    return Math.max(0, 1 - y.value / 600);
});
</script>

<template>
    <div
        class="relative min-h-screen w-full bg-background font-sans text-foreground selection:bg-primary/20 selection:text-primary"
    >
        <Head :title="$t('home.meta.title')" />

        <section class="relative overflow-hidden pt-32 pb-20 lg:pt-48 lg:pb-32">
            <div
                class="pointer-events-none absolute inset-0 -z-10 overflow-hidden"
            >
                <div
                    class="absolute -top-[20%] -right-[10%] h-[600px] w-[600px] rounded-full bg-primary/5 blur-[100px] dark:bg-primary/10"
                ></div>
                <div
                    class="absolute top-[20%] -left-[10%] h-[500px] w-[500px] rounded-full bg-accent/10 blur-[100px] dark:bg-accent/5"
                ></div>
            </div>

            <div class="container mx-auto px-4 text-center">
                <div
                    class="animate-fade-in-up mx-auto mb-8 flex w-fit items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-4 py-1.5 shadow-sm transition-colors hover:bg-primary/10"
                >
                    <Star class="h-4 w-4 fill-primary text-primary" />
                    <span
                        class="text-sm font-semibold tracking-wide text-primary uppercase"
                    >
                        {{ $t('home.hero.badge') }}
                    </span>
                </div>

                <h1
                    class="mx-auto mb-6 max-w-5xl text-5xl font-extrabold tracking-tight text-foreground sm:text-6xl md:text-7xl lg:leading-[1.1]"
                >
                    {{ $t('home.hero.title_start') }}
                    <span
                        class="relative inline-block whitespace-nowrap text-primary"
                    >
                        <span
                            class="absolute -inset-1 -rotate-1 bg-primary/10 blur-sm"
                        ></span>
                        <span
                            class="relative z-10 bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent"
                        >
                            Ranalp
                        </span>
                    </span>
                </h1>

                <p
                    class="mx-auto mb-10 max-w-2xl text-xl leading-relaxed text-muted-foreground md:text-2xl"
                >
                    {{ $t('home.hero.subtitle') }}
                </p>

                <div
                    class="flex flex-col items-center justify-center gap-4 sm:flex-row"
                >
                    <Button
                        size="lg"
                        class="group h-14 rounded-full px-8 text-base font-bold shadow-lg shadow-primary/25 transition-all hover:scale-105 hover:shadow-primary/40"
                        @click="navigateTo(create.url())"
                    >
                        <Sparkles
                            class="mr-2 h-5 w-5 transition-transform group-hover:rotate-12"
                        />
                        {{ $t('home.hero.cta_start') }}
                    </Button>

                    <Button
                        variant="outline"
                        size="lg"
                        class="h-14 rounded-full border-2 px-8 text-base font-semibold hover:bg-secondary/50"
                        @click="navigateTo(register.url())"
                    >
                        {{ $t('auth.register.cta') }}
                    </Button>
                </div>

                <div
                    class="mt-20 grid grid-cols-1 gap-8 border-y border-border/50 bg-card/50 py-8 backdrop-blur-sm sm:grid-cols-3"
                >
                    <div
                        v-for="(stat, index) in stats"
                        :key="index"
                        class="flex flex-col items-center justify-center gap-1"
                    >
                        <span class="text-3xl font-bold text-foreground">{{
                            stat.value
                        }}</span>
                        <span
                            class="text-sm font-medium tracking-wider text-muted-foreground uppercase"
                            >{{ $t(stat.label) }}</span
                        >
                    </div>
                </div>
            </div>
        </section>

        <Marketplace />

        <section class="container mx-auto max-w-4xl px-4 py-24 text-center">
            <h2
                class="mb-6 text-3xl font-bold tracking-tight text-foreground sm:text-4xl"
            >
                {{ $t('home.mission.title') }}
            </h2>
            <div class="relative">
                <p
                    class="text-lg leading-loose text-muted-foreground md:text-xl"
                >
                    {{ $t('home.mission.description') }}
                </p>
                <span
                    class="absolute -top-4 -left-8 font-serif text-6xl text-primary/10"
                    >"</span
                >
                <span
                    class="absolute -right-8 -bottom-8 font-serif text-6xl text-primary/10"
                    >"</span
                >
            </div>
        </section>

        <section class="bg-secondary/30 py-24">
            <div class="container mx-auto px-4">
                <div class="mb-16 text-center">
                    <h2
                        class="text-3xl font-bold tracking-tight text-foreground md:text-5xl"
                    >
                        {{ $t('home.features.main_title') }}
                    </h2>
                    <p class="mt-4 text-lg text-muted-foreground">
                        {{ $t('home.features.main_subtitle') }}
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 xl:gap-10">
                    <Card
                        v-for="feature in features"
                        :key="feature.id"
                        class="group relative overflow-hidden border-border/60 bg-card transition-all duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-xl hover:shadow-primary/5"
                    >
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <div
                                    :class="[
                                        'flex h-14 w-14 items-center justify-center rounded-2xl transition-colors',
                                        feature.bg,
                                        feature.color,
                                    ]"
                                >
                                    <component
                                        :is="feature.icon"
                                        class="h-7 w-7"
                                    />
                                </div>
                                <ArrowRight
                                    class="h-5 w-5 text-muted-foreground opacity-0 transition-all group-hover:-translate-x-1 group-hover:opacity-100"
                                />
                            </div>
                            <CardTitle class="mt-6 text-2xl font-bold">
                                {{ $t(`${feature.i18nKey}.title`) }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="mb-2 font-semibold text-foreground/90">
                                {{ $t(`${feature.i18nKey}.tagline`) }}
                            </p>
                            <p class="text-base text-muted-foreground">
                                {{ $t(`${feature.i18nKey}.description`) }}
                            </p>
                        </CardContent>
                        <div
                            class="absolute inset-0 cursor-pointer"
                            @click="navigateTo(feature.route)"
                        ></div>
                    </Card>
                </div>
            </div>
        </section>

        <section class="relative isolate overflow-hidden py-24">
            <div class="absolute inset-0 -z-10 bg-primary"></div>
            <svg
                class="absolute inset-0 -z-10 h-full w-full [mask-image:radial-gradient(100%_100%_at_top_right,white,transparent)] stroke-white/10"
                aria-hidden="true"
            >
                <defs>
                    <pattern
                        id="983e3e4c-de6d-4c3f-8d64-b9761d1534cc"
                        width="200"
                        height="200"
                        x="50%"
                        y="-1"
                        patternUnits="userSpaceOnUse"
                    >
                        <path d="M.5 200V.5H200" fill="none" />
                    </pattern>
                </defs>
                <rect
                    width="100%"
                    height="100%"
                    stroke-width="0"
                    fill="url(#983e3e4c-de6d-4c3f-8d64-b9761d1534cc)"
                />
            </svg>

            <div
                class="container mx-auto px-4 text-center text-primary-foreground"
            >
                <h2 class="mb-6 text-4xl font-bold tracking-tight md:text-5xl">
                    {{ $t('home.footer.title') }}
                </h2>
                <p class="mx-auto mb-10 max-w-2xl text-xl opacity-90">
                    {{ $t('home.footer.subtitle') }}
                </p>
                <div
                    class="flex flex-col items-center justify-center gap-4 sm:flex-row"
                >
                    <Button
                        size="lg"
                        variant="secondary"
                        class="h-16 rounded-full px-12 text-lg font-bold text-foreground shadow-2xl transition-transform hover:scale-105 active:scale-95"
                        @click="navigateTo(register.url())"
                    >
                        {{ $t('auth.register.cta') }}
                        <ArrowRight class="ml-2 h-5 w-5" />
                    </Button>
                </div>

                <div class="mt-12 flex justify-center gap-8 opacity-60">
                    <div class="flex items-center gap-2">
                        <CheckCircle2 class="h-5 w-5" />
                        <span class="text-sm font-medium">{{
                            $t('home.trust.secure')
                        }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <CheckCircle2 class="h-5 w-5" />
                        <span class="text-sm font-medium">{{
                            $t('home.trust.transparent')
                        }}</span>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
}
</style>
