<script setup lang="ts">
import { Cookie, Settings, X } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

// Assuming you are using shadcn-vue, the Vue port of shadcn/ui
// The import paths might be slightly different based on your project setup
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';

const showBanner = ref(false);
const showSettings = ref(false);
const preferences = ref({
    necessary: true,
    analytics: false,
    marketing: false,
    preferences: false,
});

onMounted(() => {
    const consent = localStorage.getItem('finovaste-cookie-consent');
    if (!consent) {
        showBanner.value = true;
    }
});

const handleAcceptAll = () => {
    const allAccepted = {
        necessary: true,
        analytics: true,
        marketing: true,
        preferences: true,
    };
    preferences.value = allAccepted;
    localStorage.setItem(
        'finovaste-cookie-consent',
        JSON.stringify(allAccepted),
    );
    showBanner.value = false;
};

const handleAcceptNecessary = () => {
    const necessaryOnly = {
        necessary: true,
        analytics: false,
        marketing: false,
        preferences: false,
    };
    preferences.value = necessaryOnly;
    localStorage.setItem(
        'finovaste-cookie-consent',
        JSON.stringify(necessaryOnly),
    );
    showBanner.value = false;
};

const handleSavePreferences = () => {
    localStorage.setItem(
        'finovaste-cookie-consent',
        JSON.stringify(preferences.value),
    );
    showSettings.value = false;
    showBanner.value = false;
};
</script>

<template>
    <template v-if="showBanner">
        <div class="fixed right-0 bottom-0 left-0 z-50 p-4 md:p-6">
            <Card class="mx-auto max-w-4xl border-2 shadow-xl">
                <CardContent class="p-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900"
                        >
                            <Cookie
                                class="h-6 w-6 text-blue-600 dark:text-blue-400"
                            />
                        </div>

                        <div class="flex-1">
                            <h3 class="mb-2">
                                {{ $t('cookieConsent.banner.title') }}
                            </h3>
                            <p
                                class="mb-4 text-sm text-gray-600 dark:text-gray-400"
                            >
                                {{ $t('cookieConsent.banner.description') }}
                            </p>

                            <div class="flex flex-wrap gap-3">
                                <Button
                                    @click="handleAcceptAll"
                                    class="bg-blue-600 hover:bg-blue-700"
                                >
                                    {{ $t('cookieConsent.banner.acceptAll') }}
                                </Button>
                                <Button
                                    @click="handleAcceptNecessary"
                                    variant="outline"
                                >
                                    {{
                                        $t('cookieConsent.banner.necessaryOnly')
                                    }}
                                </Button>
                                <Button
                                    @click="showSettings = true"
                                    variant="outline"
                                    class="gap-2"
                                >
                                    <Settings class="h-4 w-4" />
                                    {{ $t('cookieConsent.banner.customize') }}
                                </Button>
                            </div>
                        </div>

                        <Button
                            variant="ghost"
                            size="icon"
                            @click="showBanner = false"
                            class="flex-shrink-0"
                        >
                            <X class="h-4 w-4" />
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <Dialog :open="showSettings" @update:open="showSettings = $event">
            <DialogContent class="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>{{
                        $t('cookieConsent.modal.title')
                    }}</DialogTitle>
                    <DialogDescription>
                        {{ $t('cookieConsent.modal.description') }}
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-6 py-4">
                    <!-- Necessary Cookies -->
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="mb-2 flex items-center gap-2">
                                <Label class="font-semibold">{{
                                    $t('cookieConsent.modal.necessary.title')
                                }}</Label>
                                <span
                                    class="rounded bg-gray-200 px-2 py-1 text-xs dark:bg-gray-700"
                                    >Immer aktiv</span
                                >
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{
                                    $t(
                                        'cookieConsent.modal.necessary.description',
                                    )
                                }}
                            </p>
                        </div>
                        <Switch
                            v-model:checked="preferences.necessary"
                            disabled
                        />
                    </div>

                    <!-- Analytics Cookies -->
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <Label class="mb-2 block font-semibold">{{
                                $t('cookieConsent.modal.analytics.title')
                            }}</Label>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{
                                    $t(
                                        'cookieConsent.modal.analytics.description',
                                    )
                                }}
                            </p>
                        </div>
                        <Switch v-model:checked="preferences.analytics" />
                    </div>

                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <Label class="mb-2 block font-semibold">{{
                                $t('cookieConsent.modal.marketing.title')
                            }}</Label>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{
                                    $t(
                                        'cookieConsent.modal.marketing.description',
                                    )
                                }}
                            </p>
                        </div>
                        <Switch v-model:checked="preferences.marketing" />
                    </div>

                    <!-- Preference Cookies -->
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <Label class="mb-2 block font-semibold">{{
                                $t('cookieConsent.modal.preference.title')
                            }}</Label>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{
                                    $t(
                                        'cookieConsent.modal.preference.description',
                                    )
                                }}
                            </p>
                        </div>
                        <Switch v-model:checked="preferences.preferences" />
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <Button variant="outline" @click="showSettings = false">
                        {{ $t('cookieConsent.modal.cancel') }}
                    </Button>
                    <Button
                        @click="handleSavePreferences"
                        class="bg-blue-600 hover:bg-blue-700"
                    >
                        {{ $t('cookieConsent.modal.save') }}
                    </Button>
                </div>
            </DialogContent>
        </Dialog>
    </template>
</template>
