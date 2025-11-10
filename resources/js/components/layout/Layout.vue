<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { defineAsyncComponent } from 'vue';
import Breadcrumbs from '../Breadcrumbs.vue';
import Cookies from './Cookies.vue';
import Header from './header/Header.vue';
const Footer = defineAsyncComponent(
    () => import('@/components/layout/Footer.vue'),
);
defineProps<{
    head?: string;
    keywords?: string;
    description?: string;
    link?: string;
    image?: string;
    schema?: Record<string, any> | Record<string, any>[];
    breadcrumbs?: BreadcrumbItem[];
    flash?: {
        success?: string;
        error?: string;
    };
}>();

const menuSections = [];
</script>

<template>
    <Head>
        <!-- <title>{{ head ?? t('meta_title') }}</title>
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
            :content="description ?? t('meta_description')"
        />
        <meta
            name="twitter:description"
            :content="description ?? t('meta_description')"
        />
        <meta
            property="og:description"
            :content="description ?? t('meta_description')"
        />
        <meta
            name="Keywords"
            :content="keywords ?? t('meta_keywords')"
        />
        <component :is="'script'" type="application/ld+json">
            {{ jsonLdSchema }}
        </component> -->
    </Head>
    <Header :menuSections="menuSections" />
    <main
        class="container-custom mt-16 grid min-h-screen items-center gap-4 pt-4 sm:gap-6 sm:pt-6 md:gap-8 md:pt-8 lg:gap-10 lg:pt-10 xl:gap-12 xl:pt-12"
    >
        <div class="mt-3" v-if="breadcrumbs && breadcrumbs.length > 0">
            <Breadcrumbs :breadcrumbs="breadcrumbs" />
        </div>
        <!-- <div v-if="flash?.success" class="alert alert-success">
            {{ flash?.success }}
        </div>

        <div v-if="flash?.error" class="alert alert-danger">
            {{ flash?.error }}
        </div>

        <div v-if="Object.keys(($page.props.errors as any)).length > 0" class="alert alert-danger">
            Please correct the errors below.
        </div> -->
        <slot />
        <Footer :menuSections="menuSections" />
        <Cookies />
    </main>
    <!--
    <RightSideNav />
    <ScrollTop />
     -->
</template>
