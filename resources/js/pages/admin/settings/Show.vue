<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { GeneralSettings as GeneralSettingsType } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { useToggle } from '@vueuse/core';
import { useToast } from 'vue-toastification';

// --- Component Library Imports (shadcn/ui) ---
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
import { Switch } from '@/components/ui/switch';
import { trans } from 'laravel-vue-i18n';

interface Props {
    settings: {
        general: GeneralSettingsType;
    };
}

const props = defineProps<Props>();

const toast = useToast();
const [isSubmitting, toggleSubmitting] = useToggle(false);

// Initialize form with current settings data
const form = useForm({
    site_name: props.settings.general.site_name,
    site_active: props.settings.general.site_active,
    logo_url: props.settings.general.logo_url || '',
    per_page: props.settings.general.per_page,
    contact_email: props.settings.general.contact_email || '',
});

const submitGeneralSettings = () => {
    toggleSubmitting(true);

    // form.post(route('web.admin.settings.update.general'), { // Assuming a route: web.admin.settings.update.general
    //     onSuccess: (page) => {
    //         // Success notification via vue-toastification
    //         const message = page.props.flash.message || trans('admin.settings.general.success_message');
    //         toast.success(message as string);
    //     },
    //     onError: (errors) => {
    //         // Error notification via vue-toastification
    //         const errorMessage = trans('admin.settings.general.error_message');
    //         toast.error(errorMessage);
    //         console.error('Validation Errors:', errors);
    //     },
    //     onFinish: () => {
    //         // End loading state
    //         toggleSubmitting(false);
    //     },
    // });
};

const pageTitle = trans('admin.settings.page.title');
const generalTitle = trans('admin.settings.general.title');
const generalDescription = trans('admin.settings.general.description');
const saveButtonLabel = trans('admin.settings.general.save_button');
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-8 p-4 md:p-8">
            <form
                @submit.prevent="submitGeneralSettings"
                class="w-full max-w-4xl"
            >
                <Card>
                    <CardHeader>
                        <CardTitle>{{ generalTitle }}</CardTitle>
                        <CardDescription>{{
                            generalDescription
                        }}</CardDescription>
                    </CardHeader>

                    <CardContent class="grid gap-6">
                        <div class="space-y-2">
                            <Label for="site_name">{{
                                $t('admin.settings.general.site_name_label')
                            }}</Label>
                            <Input
                                id="site_name"
                                type="text"
                                v-model="form.site_name"
                                :placeholder="
                                    $t(
                                        'admin.settings.general.site_name_placeholder',
                                    )
                                "
                                :disabled="isSubmitting"
                                :class="{
                                    'border-destructive': form.errors.site_name,
                                }"
                            />
                            <p
                                v-if="form.errors.site_name"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.site_name }}
                            </p>
                        </div>

                        <div
                            class="flex items-center justify-between space-x-4 rounded-lg border p-2"
                        >
                            <Label
                                for="site_active"
                                class="flex flex-col space-y-1"
                            >
                                <span>{{
                                    $t(
                                        'admin.settings.general.site_active_label',
                                    )
                                }}</span>
                                <span
                                    class="leading-snug font-normal text-muted-foreground"
                                    >{{
                                        $t(
                                            'admin.settings.general.site_active_description',
                                        )
                                    }}</span
                                >
                            </Label>
                            <Switch
                                id="site_active"
                                v-model:checked="form.site_active"
                                :disabled="isSubmitting"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="contact_email">{{
                                $t('admin.settings.general.contact_email_label')
                            }}</Label>
                            <Input
                                id="contact_email"
                                type="email"
                                v-model="form.contact_email"
                                :placeholder="
                                    $t(
                                        'admin.settings.general.contact_email_placeholder',
                                    )
                                "
                                :disabled="isSubmitting"
                                :class="{
                                    'border-destructive':
                                        form.errors.contact_email,
                                }"
                            />
                            <p
                                v-if="form.errors.contact_email"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.contact_email }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="per_page">{{
                                $t('admin.settings.general.per_page_label')
                            }}</Label>
                            <Input
                                id="per_page"
                                type="number"
                                v-model="form.per_page"
                                :placeholder="
                                    $t(
                                        'admin.settings.general.per_page_placeholder',
                                    )
                                "
                                :disabled="isSubmitting"
                                :class="{
                                    'border-destructive': form.errors.per_page,
                                }"
                            />
                            <p
                                v-if="form.errors.per_page"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.per_page }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="logo_url">{{
                                $t('admin.settings.general.logo_url_label')
                            }}</Label>
                            <Input
                                id="logo_url"
                                type="url"
                                v-model="form.logo_url"
                                :placeholder="
                                    $t(
                                        'admin.settings.general.logo_url_placeholder',
                                    )
                                "
                                :disabled="isSubmitting"
                                :class="{
                                    'border-destructive': form.errors.logo_url,
                                }"
                            />
                            <p
                                v-if="form.errors.logo_url"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.logo_url }}
                            </p>
                        </div>
                    </CardContent>

                    <CardFooter class="border-t pt-6">
                        <Button
                            type="submit"
                            :disabled="form.processing || isSubmitting"
                        >
                            <template v-if="isSubmitting">
                                {{ $t('global.loading') }}
                            </template>
                            <template v-else>
                                {{ saveButtonLabel }}
                            </template>
                        </Button>
                    </CardFooter>
                </Card>
            </form>
        </div>
    </AppLayout>
</template>
