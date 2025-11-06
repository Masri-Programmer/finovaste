<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import { type BreadcrumbItem } from '@/types';

import Layout from '@/components/layout/Layout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import LocaleSelector from '@/components/LocaleSelector.vue';
import { edit } from '@/routes/languages';
import { trans } from 'laravel-vue-i18n';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: trans('settings.languages.breadcrumb'),
        href: edit().url,
    },
];
</script>

<template>
    <Layout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('settings.languages.headTitle')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="$t('settings.languages.title')"
                    :description="$t('settings.languages.description')"
                />
                <LocaleSelector />

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
