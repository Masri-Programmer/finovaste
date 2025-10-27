<script setup lang="ts">
import { useLocalStorage } from '@vueuse/core';
import { Cookie } from 'lucide-vue-next';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

// Import shadcn/ui components
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';

const { t } = useI18n();
// const toast = useToast();

const consent = useLocalStorage('cookie_consent', null);

const isCustomizeModalOpen = ref(false);
const analyticsCookies = ref(false);
const marketingCookies = ref(false);
const preferenceCookies = ref(false);

const savePreferences = (preferences: object) => {
    // const saveRoute = 'layout.cookies.preferences.save';
    isCustomizeModalOpen.value = false;
    consent.value = preferences;
    // toast.success(t('layout.cookieConsent.toast.success'));
    // router.post(saveRoute, preferences, {
    //     preserveState: true,
    //     preserveScroll: true,
    //     onSuccess: () => {
    //         consent.value = preferences;
    //         isCustomizeModalOpen.value = false; // Close modal if open
    //         toast.success(t('layout.cookieConsent.toast.success'));
    //     },
    //     onError: (errors) => {
    //         console.error('Failed to save cookie preferences:', errors);
    //         toast.error(t('layout.cookieConsent.toast.error'));
    //     },
    // });
};

const handleAcceptAll = () => {
    analyticsCookies.value = true;
    marketingCookies.value = true;
    preferenceCookies.value = true;

    savePreferences({
        necessary: true,
        analytics: true,
        marketing: true,
        preference: true,
    });
};

const handleNecessaryOnly = () => {
    savePreferences({
        necessary: true,
        analytics: false,
        marketing: false,
        preference: false,
    });
};

const handleSaveCustom = () => {
    savePreferences({
        necessary: true,
        analytics: analyticsCookies.value,
        marketing: marketingCookies.value,
        preference: preferenceCookies.value,
    });
};
</script>

<template>
    <div v-if="!consent">
        <Dialog v-model:open="isCustomizeModalOpen">
            <Card
                class="fixed right-0 bottom-4 z-50 m-4 max-w-none shadow-lg sm:left-auto sm:max-w-3xl"
            >
                <CardContent class="p-4 sm:p-6">
                    <div class="flex flex-col sm:items-center sm:gap-6">
                        <div class="flex flex-grow items-center gap-4">
                            <Cookie
                                class="h-10 w-10 flex-shrink-0 text-primary"
                            />
                            <div>
                                <h3 class="font-semibold">
                                    {{ t('layout.cookieConsent.banner.title') }}
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    {{
                                        t(
                                            'layout.cookieConsent.banner.description',
                                        )
                                    }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-4 flex w-full flex-shrink-0 flex-col gap-2 sm:mt-0 sm:flex-row sm:items-center"
                        >
                            <Button
                                class="w-full sm:w-auto"
                                @click="handleAcceptAll"
                            >
                                {{ t('layout.cookieConsent.banner.acceptAll') }}
                            </Button>
                            <Button
                                class="w-full sm:w-auto"
                                variant="outline"
                                @click="handleNecessaryOnly"
                            >
                                {{
                                    t(
                                        'layout.cookieConsent.banner.necessaryOnly',
                                    )
                                }}
                            </Button>
                            <DialogTrigger as-child>
                                <Button
                                    class="w-full sm:w-auto"
                                    variant="outline"
                                >
                                    {{
                                        t(
                                            'layout.cookieConsent.banner.customize',
                                        )
                                    }}
                                </Button>
                            </DialogTrigger>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{
                        t('layout.cookieConsent.modal.title')
                    }}</DialogTitle>
                    <DialogDescription>
                        {{ t('layout.cookieConsent.modal.description') }}
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-6 py-2 pr-2">
                    <div class="flex items-center justify-between space-x-4">
                        <Label
                            for="necessary-cookies"
                            class="flex-grow space-y-1"
                        >
                            <span class="font-medium">
                                {{
                                    t(
                                        'layout.cookieConsent.modal.necessary.title',
                                    )
                                }}
                            </span>
                            <p
                                class="text-sm font-normal text-muted-foreground"
                            >
                                {{
                                    t(
                                        'layout.cookieConsent.modal.necessary.description',
                                    )
                                }}
                            </p>
                        </Label>
                        <Switch
                            id="necessary-cookies"
                            :default-checked="true"
                            :disabled="true"
                        />
                    </div>

                    <div class="flex items-center justify-between space-x-4">
                        <Label
                            for="analytics-cookies"
                            class="flex-grow space-y-1"
                        >
                            <span class="font-medium">
                                {{
                                    t(
                                        'layout.cookieConsent.modal.analytics.title',
                                    )
                                }}
                            </span>
                            <p
                                class="text-sm font-normal text-muted-foreground"
                            >
                                {{
                                    t(
                                        'layout.cookieConsent.modal.analytics.description',
                                    )
                                }}
                            </p>
                        </Label>
                        <Switch
                            id="analytics-cookies"
                            v-model:checked="analyticsCookies"
                        />
                    </div>

                    <div class="flex items-center justify-between space-x-4">
                        <Label
                            for="marketing-cookies"
                            class="flex-grow space-y-1"
                        >
                            <span class="font-medium">
                                {{
                                    t(
                                        'layout.cookieConsent.modal.marketing.title',
                                    )
                                }}
                            </span>
                            <p
                                class="text-sm font-normal text-muted-foreground"
                            >
                                {{
                                    t(
                                        'layout.cookieConsent.modal.marketing.description',
                                    )
                                }}
                            </p>
                        </Label>
                        <Switch
                            id="marketing-cookies"
                            v-model:checked="marketingCookies"
                        />
                    </div>

                    <div class="flex items-center justify-between space-x-4">
                        <Label
                            for="preference-cookies"
                            class="flex-grow space-y-1"
                        >
                            <span class="font-medium">
                                {{
                                    t(
                                        'layout.cookieConsent.modal.preference.title',
                                    )
                                }}
                            </span>
                            <p
                                class="text-sm font-normal text-muted-foreground"
                            >
                                {{
                                    t(
                                        'layout.cookieConsent.modal.preference.description',
                                    )
                                }}
                            </p>
                        </Label>
                        <Switch
                            id="preference-cookies"
                            v-model:checked="preferenceCookies"
                        />
                    </div>
                </div>

                <DialogFooter
                    class="mt-4 flex-col-reverse sm:flex-row sm:space-x-2"
                >
                    <Button
                        variant="outline"
                        @click="isCustomizeModalOpen = false"
                    >
                        {{ t('layout.cookieConsent.modal.cancel') }}
                    </Button>
                    <Button @click="handleSaveCustom">
                        {{ t('layout.cookieConsent.modal.save') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
