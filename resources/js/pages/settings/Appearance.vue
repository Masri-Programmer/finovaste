<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';

import Layout from '@/components/layout/Layout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
</script>

<template>
    <Layout>
        <Head :title="$t('appearance.headTitle')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="$t('appearance.title')"
                    :description="$t('appearance.description')"
                />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </Layout>
</template>
