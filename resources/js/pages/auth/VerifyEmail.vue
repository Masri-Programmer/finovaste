<script setup lang="ts">
import EmailVerificationNotificationController from '@/actions/Laravel/Fortify/Http/Controllers/EmailVerificationNotificationController';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout
        :title="$t('verifyemail.title')"
        :description="$t('verifyemail.description')"
    >
        <Head :title="$t('verifyemail.headTitle')" />

        <div
            v-if="status === 'verification-link-sent'"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ $t('verifyemail.successMessage') }}
        </div>

        <Form
            v-bind="EmailVerificationNotificationController.store.form()"
            class="space-y-6 text-center"
            v-slot="{ processing }"
        >
            <Button :disabled="processing" variant="secondary">
                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                {{ $t('verifyemail.button') }}
            </Button>

            <TextLink
                :href="logout()"
                as="button"
                class="mx-auto block text-sm"
            >
                {{ $t('verifyemail.logoutLink') }}
            </TextLink>
        </Form>
    </AuthLayout>
</template>
