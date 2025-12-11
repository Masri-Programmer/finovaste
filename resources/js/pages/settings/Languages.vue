<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import Layout from '@/components/layout/Layout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import LanguageSelector from '@/components/LanguageSelector.vue';
</script>

<template>
    <Layout>
        <Head :title="$t('languages.headTitle')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="$t('languages.title')"
                    :description="$t('languages.description')"
                />
                <LanguageSelector />

                <!-- <div
                    v-for="lang in page.props.supported_languages"
                    :key="lang.code"
                    class="flex w-full items-center justify-start gap-2"
                    @click="setLocale(lang.code)"
                >
                    <Check v-if="locale === lang.code" class="h-4 w-4" />
                    {{ $t(lang.nameKey) }}
                </div> -->
            </div>
        </SettingsLayout>
    </Layout>
</template>
