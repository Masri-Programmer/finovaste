<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { login } from '@/routes';
import { router } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { useToast } from 'vue-toastification';

const toast = useToast();

function handleGetStarted() {
    router.visit(login(), {
        method: 'get',
        onStart: () => {
            toast.info(trans('hero.notifications.navigating'));
        },
    });
}
function handleLearnMore() {
    toast.success(trans('hero.notifications.learnMoreClicked'));
}
</script>

<template>
    <section class="mx-auto mt-8 sm:mt-10 md:mt-12">
        <div
            class="grid grid-cols-1 items-center gap-12 md:grid-cols-2 md:gap-16"
        >
            <div
                class="flex flex-col items-center gap-6 text-center md:items-start md:text-left"
            >
                <h1
                    class="text-4xl font-extrabold tracking-tighter md:text-5xl lg:text-6xl"
                >
                    {{ $t('hero.headline') }}
                </h1>

                <p class="max-w-xl text-lg text-muted-foreground md:text-xl">
                    {{ $t('hero.subheading') }}
                </p>

                <div class="flex flex-col gap-4 sm:flex-row">
                    <Button size="lg" @click="handleGetStarted">
                        {{ $t('hero.cta.primary') }}
                    </Button>
                    <Button
                        size="lg"
                        variant="outline"
                        @click="handleLearnMore"
                    >
                        {{ $t('hero.cta.secondary') }}
                    </Button>
                </div>
            </div>
        </div>
    </section>
</template>
