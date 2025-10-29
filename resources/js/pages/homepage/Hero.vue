<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { login } from '@/routes';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toastification';

const { t } = useI18n();
const toast = useToast();

function handleGetStarted() {
    router.visit(login(), {
        method: 'get',
        onStart: () => {
            toast.info(t('homepage.hero.notifications.navigating'));
        },
    });
}
function handleLearnMore() {
    toast.success(t('homepage.hero.notifications.learnMoreClicked'));
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
                    {{ t('homepage.hero.headline') }}
                </h1>

                <p class="max-w-xl text-lg text-muted-foreground md:text-xl">
                    {{ t('homepage.hero.subheading') }}
                </p>

                <div class="flex flex-col gap-4 sm:flex-row">
                    <Button size="lg" @click="handleGetStarted">
                        {{ t('homepage.hero.cta.primary') }}
                    </Button>
                    <Button
                        size="lg"
                        variant="outline"
                        @click="handleLearnMore"
                    >
                        {{ t('homepage.hero.cta.secondary') }}
                    </Button>
                </div>
            </div>
        </div>
    </section>
</template>
