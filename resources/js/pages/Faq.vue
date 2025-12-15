<script setup lang="ts">
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/Components/ui/accordion';
import { Button } from '@/Components/ui/button';
import Layout from '@/components/layout/Layout.vue';
import { Link } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import { trans } from 'laravel-vue-i18n';
import { useToast } from 'vue-toastification';

// --- Interface & Data ---
interface FaqItem {
    value: string;
    questionKey: string;
    answerKey: string;
}

// Defining the FAQ structure based on the requirements.
// The actual text content is delegated to the i18n JSON file (see below).
const faqItems: FaqItem[] = [
    {
        value: 'item-1',
        questionKey: 'faq.items.why.question',
        answerKey: 'faq.items.why.answer',
    },
    {
        value: 'item-2',
        questionKey: 'faq.items.unique.question',
        answerKey: 'faq.items.unique.answer',
    },
    {
        value: 'item-3',
        questionKey: 'faq.items.cost.question',
        answerKey: 'faq.items.cost.answer',
    },
    {
        value: 'item-4',
        questionKey: 'faq.items.trust.question',
        answerKey: 'faq.items.trust.answer',
    },
    {
        value: 'item-5',
        questionKey: 'faq.items.start.question',
        answerKey: 'faq.items.start.answer',
    },
    {
        value: 'item-6',
        questionKey: 'faq.items.support.question',
        answerKey: 'faq.items.support.answer',
    },
    {
        value: 'item-7',
        questionKey: 'faq.items.forbidden.question',
        answerKey: 'faq.items.forbidden.answer',
    },
];

// --- Composables ---
const toast = useToast();
const { copy, isSupported } = useClipboard();

// --- Methods ---
/**
 * Simulates copying a support email address using VueUse
 */
const copySupportEmail = async () => {
    if (isSupported.value) {
        await copy('support@ranalp.de');
        toast.success(trans('faq.toast.email_copied'));
    }
};

/**
 * Handle navigation to campaign creation via Inertia
 */
const navigateToCreate = () => {
    // Using Laravel Typed Wayfinder convention
    // Inertia.visit(route('web.campaign.create'));
    // For this example, we use a standard Link component in the template,
    // but this function demonstrates imperative navigation if needed.
};
</script>

<template>
    <Layout :title="$t('faq.meta_title')">
        <div
            class="min-h-screen bg-background font-sans text-foreground selection:bg-primary selection:text-primary-foreground"
        >
            <header
                class="border-b border-border bg-secondary/30 px-4 py-12 text-center md:px-8 md:py-20"
            >
                <div class="mx-auto max-w-4xl space-y-4">
                    <h1
                        class="text-3xl font-bold tracking-tight text-primary md:text-5xl"
                    >
                        {{ $t('faq.title') }}
                    </h1>
                    <p
                        class="mx-auto max-w-2xl text-lg text-muted-foreground md:text-xl"
                    >
                        {{ $t('faq.subtitle') }}
                    </p>
                </div>
            </header>

            <main class="mx-auto max-w-3xl px-4 py-12">
                <Accordion type="single" collapsible class="w-full space-y-4">
                    <AccordionItem
                        v-for="item in faqItems"
                        :key="item.value"
                        :value="item.value"
                        class="rounded-lg border border-border bg-card px-4"
                    >
                        <AccordionTrigger
                            class="py-4 text-left text-lg font-semibold hover:text-primary hover:no-underline"
                        >
                            {{ $t(item.questionKey) }}
                        </AccordionTrigger>

                        <AccordionContent
                            class="pb-4 text-base leading-relaxed text-muted-foreground"
                        >
                            <div v-html="$t(item.answerKey)"></div>
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>

                <div
                    class="mt-16 space-y-6 rounded-xl border border-border bg-secondary/20 p-8 text-center"
                >
                    <h3 class="text-2xl font-bold">
                        {{ $t('faq.cta.title') }}
                    </h3>
                    <p class="text-muted-foreground">
                        {{ $t('faq.cta.description') }}
                    </p>

                    <div
                        class="flex flex-col items-center justify-center gap-4 sm:flex-row"
                    >
                        <Link href="#">
                            <Button
                                size="lg"
                                class="w-full font-bold shadow-lg shadow-primary/20 sm:w-auto"
                            >
                                {{ $t('faq.cta.button_start') }}
                            </Button>
                        </Link>

                        <Button
                            variant="outline"
                            size="lg"
                            class="w-full sm:w-auto"
                            @click="copySupportEmail"
                        >
                            {{ $t('faq.cta.button_contact') }}
                        </Button>
                    </div>
                </div>
            </main>
        </div>
    </Layout>
</template>

<style scoped>
:deep(ul) {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}

:deep(li) {
    margin-bottom: 0.25rem;
}

:deep(strong) {
    color: var(--foreground);
    font-weight: 600;
}
</style>
