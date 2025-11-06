<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import Layout from '@/components/layout/Layout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/appearance';
import { trans } from 'laravel-vue-i18n';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: trans('settings.appearance.breadcrumb'),
        href: edit().url,
    },
];
</script>

<template>
    <Layout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('settings.appearance.headTitle')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="$t('settings.appearance.title')"
                    :description="$t('settings.appearance.description')"
                />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </Layout>
</template>
