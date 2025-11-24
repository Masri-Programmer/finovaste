<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold">
                {{ $t('listing.faq.title', 'Häufig gestellte Fragen') }}
            </h3>
            <Button
                v-if="!isOwner && !showAskForm"
                @click="showAskForm = true"
                variant="outline"
            >
                {{ $t('listing.faq.ask_question', 'Ask a Question') }}
            </Button>
        </div>

        <Card v-if="showAskForm" class="bg-slate-50">
            <CardContent class="space-y-4 pt-6">
                <div class="space-y-2">
                    <Label>{{ $t('listing.faq.your_question') }}</Label>
                    <Textarea v-model="newQuestion" rows="2" />
                </div>
                <div class="flex justify-end gap-2">
                    <Button variant="ghost" @click="showAskForm = false">
                        {{ $t('common.cancel') }}
                    </Button>
                    <Button @click="submitQuestion" :disabled="processing">
                        {{ $t('common.submit') }}
                    </Button>
                </div>
            </CardContent>
        </Card>

        <Accordion type="single" collapsible class="w-full">
            <AccordionItem
                v-for="faq in faqs"
                :key="faq.id"
                :value="'item-' + faq.id"
            >
                <AccordionTrigger class="text-left hover:no-underline">
                    <div class="flex flex-col items-start gap-1 text-left">
                        <span class="font-medium">
                            {{ getTranslation(faq.question) }}
                        </span>
                        <span
                            v-if="isOwner && !faq.is_visible"
                            class="rounded bg-yellow-100 px-2 py-0.5 text-xs text-yellow-800"
                        >
                            {{ $t('status.pending_approval') }}
                        </span>
                    </div>
                </AccordionTrigger>

                <AccordionContent class="space-y-4 text-muted-foreground">
                    <div
                        v-if="faq.answer && !editingId"
                        class="prose dark:prose-invert"
                    >
                        {{ getTranslation(faq.answer) }}
                    </div>
                    <div v-else-if="!isOwner" class="text-sm italic">
                        {{ $t('listing.faq.waiting_for_answer') }}
                    </div>

                    <div v-if="isOwner" class="mt-2 border-t pt-2">
                        <div v-if="editingId === faq.id" class="space-y-3">
                            <Textarea
                                v-model="editAnswerForm.answer"
                                :placeholder="$t('listing.faq.write_answer')"
                            />
                            <div class="flex gap-2">
                                <Button size="sm" @click="saveAnswer(faq)">
                                    {{ $t('common.save') }}
                                </Button>
                                <Button
                                    size="sm"
                                    variant="ghost"
                                    @click="editingId = null"
                                >
                                    {{ $t('common.cancel') }}
                                </Button>
                            </div>
                        </div>

                        <div v-else class="flex items-center gap-2">
                            <Button
                                size="sm"
                                variant="outline"
                                @click="startEdit(faq)"
                            >
                                <Pencil class="mr-1 h-3 w-3" />
                                {{
                                    faq.answer
                                        ? $t('common.edit')
                                        : $t('listing.faq.answer')
                                }}
                            </Button>

                            <Button
                                size="sm"
                                :variant="faq.is_visible ? 'ghost' : 'default'"
                                @click="toggleVisibility(faq)"
                            >
                                <Eye class="mr-1 h-3 w-3" />
                                {{
                                    faq.is_visible
                                        ? $t('common.hide')
                                        : $t('common.approve')
                                }}
                            </Button>

                            <Button
                                size="sm"
                                variant="destructive"
                                @click="deleteFaq(faq)"
                            >
                                <Trash2 class="h-3 w-3" />
                            </Button>
                        </div>
                    </div>
                </AccordionContent>
            </AccordionItem>
        </Accordion>

        <div
            v-if="faqs.length === 0"
            class="py-8 text-center text-muted-foreground"
        >
            {{ $t('listing.faq.no_questions_yet') }}
        </div>
    </div>
</template>

<script setup lang="ts">
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { destroy, store, update } from '@/routes/listings/faq';
import type { ListingFaq } from '@/types';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { Eye, Pencil, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    listingId: number;
    faqs: ListingFaq[];
    isOwner: boolean;
}>();

const page = usePage();
const currentLocale = page.props.locale || 'de';

const getTranslation = (field: any) => {
    if (typeof field === 'string') return field;
    if (!field) return '';
    return field[currentLocale] || field['en'] || Object.values(field)[0];
};

const showAskForm = ref(false);
const newQuestion = ref('');
const processing = ref(false);

const submitQuestion = () => {
    processing.value = true;

    // 1. URL Generation: store.url(listingId)
    // 2. Data: { question: ... }
    router.post(
        store.url(props.listingId),
        {
            question: newQuestion.value,
        },
        {
            onSuccess: () => {
                showAskForm.value = false;
                newQuestion.value = '';
                processing.value = false;
            },
            onError: () => (processing.value = false),
        },
    );
};

// --- Owner Actions ---
const editingId = ref<number | null>(null);
const editAnswerForm = useForm({
    answer: '',
});

const startEdit = (faq: ListingFaq) => {
    editingId.value = faq.id;
    editAnswerForm.answer = getTranslation(faq.answer);
};

const saveAnswer = (faq: ListingFaq) => {
    // URL Generation: update.url([listingId, faqId])
    editAnswerForm.patch(update.url([props.listingId, faq.id]), {
        onSuccess: () => {
            editingId.value = null;
            // Auto approve on answer if currently hidden
            if (!faq.is_visible) toggleVisibility(faq, true);
        },
    });
};

const toggleVisibility = (faq: ListingFaq, forceState?: boolean) => {
    // URL Generation: update.url([listingId, faqId])
    router.patch(update.url([props.listingId, faq.id]), {
        is_visible: forceState !== undefined ? forceState : !faq.is_visible,
    });
};

const deleteFaq = (faq: ListingFaq) => {
    if (confirm('Are you sure?')) {
        // URL Generation: destroy.url([listingId, faqId])
        router.delete(destroy.url([props.listingId, faq.id]));
    }
};
</script>
