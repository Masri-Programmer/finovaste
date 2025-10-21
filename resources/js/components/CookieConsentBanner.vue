<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { imprint, privacyPolicy } from '@/routes';
import { StorageSerializers, useScroll, useStorage } from '@vueuse/core';
import { Cookie } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

declare function gtag(command: 'consent', action: 'update', params: Record<string, 'granted' | 'denied'>): void;

const { t } = useI18n();

const isMounted = ref(false);
const userHasScrolled = ref(false);

onMounted(() => {
    isMounted.value = true;
    const { y } = useScroll(window);
    const stopWatch = watch(y, (newY) => {
        if (newY > 100) {
            userHasScrolled.value = true;
            stopWatch();
        }
    });
});

interface ConsentState {
    status: 'accepted' | 'declined';
    expires?: number;
}

const cookieConsent = useStorage<ConsentState | null>('cookie-consent', null, undefined, { serializer: StorageSerializers.object });
const savePreference = useStorage<boolean>('cookie-consent-save-preference', false);

watch(
    cookieConsent,
    (val) => {
        if (val) {
            if (val.expires && val.expires < Date.now()) {
                cookieConsent.value = null;
                return;
            }

            const consentState = val.status === 'accepted' ? 'granted' : 'denied';
            gtag('consent', 'update', {
                ad_storage: consentState,
                ad_user_data: consentState,
                ad_personalization: consentState,
                analytics_storage: consentState,
            });
        }
    },
    { immediate: true },
);

const showBanner = computed(() => {
    if (!cookieConsent.value) {
        return true;
    }
    if (cookieConsent.value.expires && cookieConsent.value.expires < Date.now()) {
        return true;
    }
    return false;
});

function acceptCookies() {
    if (savePreference.value) {
        const expiry = new Date();
        expiry.setDate(expiry.getDate() + 365);
        cookieConsent.value = {
            status: 'accepted',
            expires: expiry.getTime(),
        };
    } else {
        cookieConsent.value = {
            status: 'accepted',
        };
    }
}

function declineCookies() {
    if (savePreference.value) {
        const expiry = new Date();
        expiry.setDate(expiry.getDate() + 365);
        cookieConsent.value = {
            status: 'declined',
            expires: expiry.getTime(),
        };
    } else {
        cookieConsent.value = {
            status: 'declined',
        };
    }
}
</script>

<template>
    <transition enter-active-class="animate-duration-500 animate-fade-in-up" leave-active-class="animate-duration-300 animate-fade-out-down">
        <div
            v-if="isMounted && showBanner && userHasScrolled"
            class="fixed right-0 bottom-0 left-0 z-50 p-4 sm:right-8 sm:bottom-4 sm:left-auto"
            role="dialog"
            aria-live="polite"
            aria-label="Cookie Consent Banner"
        >
            <Card class="w-full max-w-md shadow-2xl">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle>{{ t('cookieBanner.title') }}</CardTitle>
                        <Cookie class="h-6 w-6" />
                    </div>
                </CardHeader>
                <CardContent class="space-y-4">
                    <i18n-t keypath="cookieBanner.description" tag="p" class="text-sm text-muted-foreground">
                        <template #privacyLink>
                            <a :href="privacyPolicy.url()" class="underline transition-colors hover:text-foreground">
                                {{ t('cookieBanner.privacyPolicy') }}
                            </a>
                        </template>
                    </i18n-t>
                    <div class="flex items-center space-x-2">
                        <Checkbox id="save-preference" :checked="savePreference" @click="savePreference = !savePreference" />
                        <label
                            for="save-preference"
                            class="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                        >
                            {{ t('cookieBanner.save') }}
                        </label>
                    </div>
                </CardContent>
                <CardFooter class="flex flex-col items-stretch gap-3 sm:flex-row sm:justify-between">
                    <div class="flex justify-end gap-x-3">
                        <Button variant="link" as-child class="h-auto grow p-0 text-start underline">
                            <a :href="imprint.url()" target="_blank" rel="noopener noreferrer">{{ t('cookieBanner.imprint') }}</a>
                        </Button>
                        <Button variant="outline" @click="declineCookies">
                            {{ t('cookieBanner.decline') }}
                        </Button>
                        <Button @click="acceptCookies">
                            {{ t('cookieBanner.accept') }}
                        </Button>
                    </div>
                </CardFooter>
            </Card>
        </div>
    </transition>
</template>
