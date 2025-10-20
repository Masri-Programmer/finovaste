<script setup lang="ts">
// import CookieConsentBanner from '@/components/CookieConsentBanner.vue';
// import Header from '@/components/layout/header/Header.vue';

import { AppPageProps } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const page = usePage();
const locale = computed(() => page.props.locale as string);

const customProps = page.props as AppPageProps;
// const Footer = defineAsyncComponent(() => import('@/components/layout/Footer.vue'));
const props = defineProps<{
    head?: string;
    keywords?: string;
    description?: string;
    link: string;
    image?: string;
    schema?: Record<string, any> | Record<string, any>[];
}>();

const jsonLdSchema = computed(() => {
    const baseGraph = [];

    if (props.schema) {
        if (Array.isArray(props.schema)) {
            baseGraph.push(...props.schema);
        } else {
            baseGraph.push(props.schema as any);
        }
    }

    return JSON.stringify({
        '@context': 'https://schema.org',
        '@graph': baseGraph,
    });
});
</script>

<template>
    <Head>
        <!-- <title>{{ head ?? t('homepage.meta_title') }}</title>
        <link rel="preconnect" :href="customProps.app.url + link" />
        <link rel="canonical" :href="customProps.app.url + link" />
        <link
            rel="alternate"
            :hreflang="locale"
            href="https://www.masriprogrammer.de"
        />
        <meta
            head-key="description"
            name="description"
            :content="description ?? t('homepage.meta_description')"
        />
        <meta
            name="twitter:description"
            :content="description ?? t('homepage.meta_description')"
        />
        <meta
            property="og:description"
            :content="description ?? t('homepage.meta_description')"
        />
        <meta
            name="Keywords"
            :content="keywords ?? t('homepage.meta_keywords')"
        />
        <component :is="'script'" type="application/ld+json">
            {{ jsonLdSchema }}
        </component> -->
    </Head>
    <!-- <Header :menuSections="menuSections" />
    <RightSideNav />
    <main
        class="grid gap-4 overflow-hidden sm:gap-6 md:gap-8 lg:gap-10 xl:gap-12"
    >
        <slot />
    </main>
    <Footer :menuSections="menuSections" />
    <ScrollTop />
    <CookieConsentBanner /> -->
</template>
