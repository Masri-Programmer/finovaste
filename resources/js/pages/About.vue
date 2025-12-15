<script setup lang="ts">
import Layout from '@/components/layout/Layout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Gift,
    HeartHandshake,
    Rocket,
    ShoppingBag,
    Users,
} from 'lucide-vue-next';

const features = [
    {
        key: 'private',
        icon: Gift,
        route: home(),
    },
    {
        key: 'founders',
        icon: Rocket,
        route: home(),
    },
    {
        key: 'volunteer',
        icon: HeartHandshake,
        route: home(),
    },
    {
        key: 'charity',
        icon: ShoppingBag,
        route: home(),
    },
];
</script>

<template>
    <Layout :title="$t('about.meta_title')">
        <div
            class="relative w-full overflow-hidden bg-background py-16 md:py-24"
        >
            <div
                class="absolute top-0 left-1/2 -z-10 h-[600px] w-[600px] -translate-x-1/2 rounded-full bg-primary/5 blur-3xl"
            ></div>

            <div class="container-custom mx-auto px-4">
                <section class="mb-20 text-center">
                    <h1
                        class="mb-6 text-4xl font-extrabold tracking-tight text-foreground lg:text-5xl"
                    >
                        {{ $t('about.hero.title') }}
                    </h1>
                    <p
                        class="mx-auto mb-8 max-w-2xl text-lg leading-relaxed text-muted-foreground"
                    >
                        {{ $t('about.hero.subtitle') }}
                    </p>
                    <div
                        class="mx-auto flex max-w-3xl flex-col items-center gap-4 rounded-xl border border-border bg-card/50 p-6 backdrop-blur-sm md:p-8"
                    >
                        <Users class="mb-2 h-10 w-10 text-primary" />
                        <p class="text-center font-medium text-foreground">
                            {{ $t('about.mission.text') }}
                        </p>
                    </div>
                </section>

                <section class="mb-20">
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-2">
                        <Card
                            v-for="feature in features"
                            :key="feature.key"
                            class="group relative overflow-hidden transition-all hover:border-primary/50 hover:shadow-md"
                        >
                            <CardHeader>
                                <div
                                    class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-secondary text-primary transition-colors group-hover:bg-primary group-hover:text-primary-foreground"
                                >
                                    <component
                                        :is="feature.icon"
                                        class="h-6 w-6"
                                    />
                                </div>
                                <CardTitle class="text-xl">
                                    {{ $t(`about.${feature.key}.title`) }}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p class="mb-4 text-muted-foreground">
                                    {{ $t(`about.${feature.key}.description`) }}
                                </p>
                                <p class="text-sm font-medium text-foreground">
                                    {{ $t(`about.${feature.key}.highlight`) }}
                                </p>
                            </CardContent>
                        </Card>
                    </div>
                </section>

                <section class="text-center">
                    <div
                        class="rounded-3xl bg-primary px-6 py-16 text-primary-foreground shadow-2xl md:px-12"
                    >
                        <h2 class="mb-4 text-3xl font-bold">
                            {{ $t('about.cta.title') }}
                        </h2>
                        <p
                            class="mx-auto mb-8 max-w-xl text-primary-foreground/90"
                        >
                            {{ $t('about.cta.subtitle') }}
                        </p>
                        <div
                            class="flex flex-col items-center justify-center gap-4 sm:flex-row"
                        >
                            <Link :href="home()" as="button">
                                <Button
                                    size="lg"
                                    variant="secondary"
                                    class="font-semibold"
                                >
                                    {{ $t('about.cta.button_start') }}
                                    <Rocket class="ml-2 h-4 w-4" />
                                </Button>
                            </Link>
                            <Link :href="home()" as="button">
                                <Button
                                    size="lg"
                                    variant="outline"
                                    class="border-primary-foreground/20 bg-primary-foreground/10 text-primary-foreground hover:bg-primary-foreground/20 hover:text-white"
                                >
                                    {{ $t('about.cta.button_support') }}
                                    <ArrowRight class="ml-2 h-4 w-4" />
                                </Button>
                            </Link>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </Layout>
</template>
