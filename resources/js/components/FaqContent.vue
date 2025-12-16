<script setup lang="ts">
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { contact } from '@/routes';
import { create } from '@/routes/listings';
import { Link, router } from '@inertiajs/vue3';
import { ArrowRight, HelpCircle, Mail, MessageCircle } from 'lucide-vue-next';

// --- Data & Configuration ---

interface FaqItem {
    value: string;
    questionKey: string;
    answerKey: string;
}

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
</script>

<template>
    <div class="min-h-screen bg-background font-sans text-foreground">
        <header
            class="relative overflow-hidden border-b border-border bg-secondary/20 px-4 py-16 text-center md:py-24"
        >
            <div
                class="absolute -top-24 -left-24 h-64 w-64 rounded-full bg-primary/5 blur-3xl"
            ></div>
            <div
                class="absolute right-0 bottom-0 h-64 w-64 rounded-full bg-accent/10 blur-3xl"
            ></div>

            <div class="relative mx-auto max-w-4xl space-y-6">
                <Badge
                    variant="outline"
                    class="border-primary/20 bg-primary/5 px-3 py-1 text-primary"
                >
                    <HelpCircle class="mr-2 h-3.5 w-3.5" />
                    {{ $t('faq.badge') }}
                </Badge>

                <h1
                    class="text-4xl font-extrabold tracking-tight text-foreground md:text-5xl lg:text-6xl"
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

        <main class="mx-auto max-w-3xl px-4 py-16 md:py-20">
            <Accordion type="single" collapsible class="w-full space-y-4">
                <AccordionItem
                    v-for="item in faqItems"
                    :key="item.value"
                    :value="item.value"
                    class="overflow-hidden rounded-xl border border-border bg-card px-2 transition-all hover:border-primary/30 hover:shadow-sm"
                >
                    <AccordionTrigger
                        class="px-4 py-5 text-left text-lg font-medium hover:no-underline [&[data-state=open]]:text-primary"
                    >
                        {{ $t(item.questionKey) }}
                    </AccordionTrigger>

                    <AccordionContent
                        class="px-4 pb-6 text-base leading-relaxed text-muted-foreground"
                    >
                        <div
                            class="prose-sm dark:prose-invert"
                            v-html="$t(item.answerKey)"
                        ></div>
                    </AccordionContent>
                </AccordionItem>
            </Accordion>

            <div
                class="mt-20 overflow-hidden rounded-2xl border border-border bg-gradient-to-br from-secondary/50 to-card p-8 text-center shadow-lg md:p-12"
            >
                <div
                    class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-primary"
                >
                    <MessageCircle class="h-8 w-8" />
                </div>

                <h3
                    class="mb-3 text-2xl font-bold tracking-tight text-foreground"
                >
                    {{ $t('faq.cta.title') }}
                </h3>
                <p class="mx-auto mb-8 max-w-md text-muted-foreground">
                    {{ $t('faq.cta.description') }}
                </p>

                <div
                    class="flex flex-col items-center justify-center gap-4 sm:flex-row"
                >
                    <Link :href="create()">
                        <Button
                            size="lg"
                            class="group h-12 min-w-[160px] font-semibold shadow-md transition-all hover:scale-105"
                        >
                            {{ $t('faq.cta.button_start') }}
                            <ArrowRight
                                class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1"
                            />
                        </Button>
                    </Link>

                    <Button
                        variant="outline"
                        size="lg"
                        class="h-12 min-w-[160px] bg-background font-semibold hover:bg-secondary/50"
                        @click="router.visit(contact.url())"
                    >
                        <Mail class="mr-2 h-4 w-4" />
                        {{ $t('faq.cta.button_contact') }}
                    </Button>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
:deep(ul) {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-top: 0.75rem;
    margin-bottom: 0.75rem;
}

:deep(li) {
    margin-bottom: 0.5rem;
}

:deep(strong) {
    color: var(--foreground);
    font-weight: 600;
}

:deep(a) {
    color: var(--primary);
    text-decoration: underline;
    text-underline-offset: 4px;
}

:deep(a:hover) {
    color: var(--primary-foreground);
    background-color: var(--primary);
    text-decoration: none;
    border-radius: 0.25rem;
    padding: 0 0.25rem;
}
</style>
