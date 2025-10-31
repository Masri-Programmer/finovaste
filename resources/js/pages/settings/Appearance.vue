<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import Layout from '@/components/layout/Layout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/appearance';

const { t } = useI18n();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('settings.appearance.breadcrumb'),
        href: edit().url,
    },
];
</script>

<template>
    <Layout :breadcrumbs="breadcrumbItems">
        <Head :title="t('settings.appearance.headTitle')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="t('settings.appearance.title')"
                    :description="t('settings.appearance.description')"
                />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </Layout>
</template>
