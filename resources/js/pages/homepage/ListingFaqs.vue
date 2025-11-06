<script setup lang="ts">
// 🛠 Added VueUse for modal/visibility toggle
import { useToggle } from '@vueuse/core';

import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { MessageSquare } from 'lucide-vue-next';

const props = defineProps<{
    faqs: {
        questionKey: string;
        answerKey: string;
    }[];
}>();

const [isDialogOpen, toggleDialog] = useToggle(false);
const displayedFaqs = props.faqs.slice(0, 3);
</script>

<template>
    <div v-if="faqs.length > 0" class="mt-4 border-t border-border pt-4">
        <h4 class="mb-2 text-base font-semibold text-foreground">
            {{ $t('faqs.title') }}
        </h4>

        <Accordion type="single" class="w-full" collapsible>
            <AccordionItem
                v-for="(faq, index) in displayedFaqs"
                :key="index"
                :value="`item-${index}`"
            >
                <AccordionTrigger class="py-2 text-left text-sm">
                    {{ $t(`${faq.questionKey}`) }}
                </AccordionTrigger>
                <AccordionContent class="pb-2 text-sm text-muted-foreground">
                    {{ $t(`${faq.answerKey}`) }}
                </AccordionContent>
            </AccordionItem>
        </Accordion>

        <Dialog :open="isDialogOpen" @update:open="toggleDialog">
            <DialogTrigger as-child>
                <Button
                    v-if="faqs.length > displayedFaqs.length"
                    variant="link"
                    size="sm"
                    class="mt-2 pl-0 text-xs"
                    @click="toggleDialog(true)"
                >
                    <MessageSquare class="mr-1 h-3 w-3" />
                    {{ $t('faqs.showAll') }} ({{ faqs.length }})
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]">
                <DialogTitle>{{ $t('faqs.allFaqsTitle') }}</DialogTitle>
                <div class="max-h-[70vh] overflow-y-auto">
                    <Accordion type="single" class="w-full" collapsible>
                        <AccordionItem
                            v-for="(faq, index) in faqs"
                            :key="index"
                            :value="`full-item-${index}`"
                        >
                            <AccordionTrigger class="text-left text-sm">
                                {{ $t(`${faq.questionKey}`) }}
                            </AccordionTrigger>
                            <AccordionContent
                                class="text-sm text-muted-foreground"
                            >
                                {{ $t(`${faq.answerKey}`) }}
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>
