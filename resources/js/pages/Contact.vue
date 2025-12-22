<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import { trans } from 'laravel-vue-i18n';
import {
    Check,
    Copy,
    Loader2,
    Mail,
    MapPin,
    Phone,
    Send,
} from 'lucide-vue-next';
import { useToast } from 'vue-toastification';

// UI Components (Shadcn/ui)
import Layout from '@/components/layout/Layout.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

interface ContactForm {
    name: string;
    email: string;
    subject: string;
    message: string;
}

const toast = useToast();
const { copy, copied, isSupported } = useClipboard();

const form = useForm<ContactForm>({
    name: '',
    email: '',
    subject: '',
    message: '',
});

// Logic
const copyEmail = async () => {
    const supportEmail = 'ranalp@ranalp.de';
    await copy(supportEmail);

    if (copied.value) {
        toast.success(trans('support.contact.email_copied'));
    }
};

const submitContactForm = () => {
    //   form.post(route('web.support.contact.store'), {
    //     preserveScroll: true,
    //     onSuccess: () => {
    //       form.reset();
    //       toast.success(trans('support.contact.success_message'));
    //     },
    //     onError: (errors) => {
    //       // Toastification handles general error feedback
    //       toast.error(trans('support.contact.error_message'));
    //       console.error(errors);
    //     },
    //   });
};
</script>

<template>
    <Layout :title="$t('support.contact.title')">
        <div
            class="mx-auto grid max-w-7xl grid-cols-1 items-start gap-12 lg:grid-cols-2"
        >
            <div class="space-y-8">
                <div>
                    <h1 class="text-4xl font-bold tracking-tight text-primary">
                        {{ $t('support.contact.hero_title') }}
                    </h1>
                    <p class="mt-4 text-lg text-muted-foreground">
                        {{ $t('support.contact.hero_subtitle') }}
                    </p>
                </div>
                <div class="space-y-6">
                    <Card class="border-border/50 shadow-sm">
                        <CardContent class="flex items-center gap-4 p-4">
                            <div
                                class="rounded-full bg-primary/10 p-3 text-primary"
                            >
                                <Mail class="h-6 w-6" />
                            </div>
                            <div class="flex-1">
                                <p
                                    class="text-sm font-medium text-muted-foreground"
                                >
                                    {{ $t('support.contact.email_label') }}
                                </p>
                                <p class="text-lg font-semibold">
                                    ranalp@ranalp.de
                                </p>
                            </div>
                            <Button
                                v-if="isSupported"
                                variant="ghost"
                                size="icon"
                                @click="copyEmail"
                                :title="$t('support.contact.copy_tooltip')"
                            >
                                <Check
                                    v-if="copied"
                                    class="h-4 w-4 text-green-500"
                                />
                                <Copy
                                    v-else
                                    class="h-4 w-4 text-muted-foreground"
                                />
                            </Button>
                        </CardContent>
                    </Card>
                    <Card class="border-border/50 shadow-sm">
                        <CardContent class="flex items-center gap-4 p-4">
                            <div
                                class="rounded-full bg-primary/10 p-3 text-primary"
                            >
                                <MapPin class="h-6 w-6" />
                            </div>
                            <div>
                                <p
                                    class="text-sm font-medium text-muted-foreground"
                                >
                                    {{ $t('support.contact.office_label') }}
                                </p>
                                <p class="text-base font-semibold">
                                    Musterstraße 42, 26122 Oldenburg
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                    <Card class="border-border/50 shadow-sm">
                        <CardContent class="flex items-center gap-4 p-4">
                            <div
                                class="rounded-full bg-primary/10 p-3 text-primary"
                            >
                                <Phone class="h-6 w-6" />
                            </div>
                            <div>
                                <p
                                    class="text-sm font-medium text-muted-foreground"
                                >
                                    {{ $t('support.contact.phone_label') }}
                                </p>
                                <p class="text-base font-semibold">
                                    +49 441 1234567
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
            <Card class="w-full border-border bg-card shadow-md">
                <CardHeader>
                    <CardTitle class="text-2xl text-card-foreground">
                        {{ $t('support.contact.form_title') }}
                    </CardTitle>
                    <CardDescription class="text-muted-foreground">
                        {{ $t('support.contact.form_description') }}
                    </CardDescription>
                </CardHeader>
                <form @submit.prevent="submitContactForm">
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Label for="name">{{
                                $t('support.contact.name_label')
                            }}</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                :placeholder="
                                    $t('support.contact.name_placeholder')
                                "
                                :class="{
                                    'border-destructive ring-destructive':
                                        form.errors.name,
                                }"
                                required
                            />
                            <span
                                v-if="form.errors.name"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.name }}
                            </span>
                        </div>
                        <div class="space-y-2">
                            <Label for="email">{{
                                $t('support.contact.email_input_label')
                            }}</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                :placeholder="
                                    $t('support.contact.email_placeholder')
                                "
                                :class="{
                                    'border-destructive ring-destructive':
                                        form.errors.email,
                                }"
                                required
                            />
                            <span
                                v-if="form.errors.email"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.email }}
                            </span>
                        </div>
                        <div class="space-y-2">
                            <Label for="subject">{{
                                $t('support.contact.subject_label')
                            }}</Label>
                            <Input
                                id="subject"
                                v-model="form.subject"
                                type="text"
                                :placeholder="
                                    $t('support.contact.subject_placeholder')
                                "
                                :class="{
                                    'border-destructive ring-destructive':
                                        form.errors.subject,
                                }"
                                required
                            />
                            <span
                                v-if="form.errors.subject"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.subject }}
                            </span>
                        </div>
                        <div class="space-y-2">
                            <Label for="message">{{
                                $t('support.contact.message_label')
                            }}</Label>
                            <Textarea
                                id="message"
                                v-model="form.message"
                                :placeholder="
                                    $t('support.contact.message_placeholder')
                                "
                                class="min-h-[150px]"
                                :class="{
                                    'border-destructive ring-destructive':
                                        form.errors.message,
                                }"
                                required
                            />
                            <span
                                v-if="form.errors.message"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.message }}
                            </span>
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-end pt-4">
                        <Button
                            type="submit"
                            class="w-full bg-primary text-primary-foreground hover:bg-primary/90 sm:w-auto"
                            :disabled="form.processing"
                        >
                            <Loader2
                                v-if="form.processing"
                                class="mr-2 h-4 w-4 animate-spin"
                            />
                            <Send v-else class="mr-2 h-4 w-4" />
                            {{ $t('support.contact.submit_button') }}
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </Layout>
</template>
