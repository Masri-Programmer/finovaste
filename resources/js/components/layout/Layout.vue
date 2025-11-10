<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { defineAsyncComponent } from 'vue';
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
        class="container-custom grid min-h-screen items-center gap-4 pt-4 sm:mt-20 sm:gap-6 sm:pt-6 md:mt-22 md:gap-8 md:pt-8 lg:mt-24 lg:gap-10 lg:pt-10 xl:mt-26 xl:gap-12 xl:pt-12"
    >
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
    </main>
    <div
        class="mt-4 border-t border-border bg-background pt-4 text-foreground sm:mt-6 md:mt-8 lg:mt-10 xl:mt-12"
    >
        <Footer :menuSections="menuSections" />
    </div>
    <Cookies />
    <!--
    <ScrollTop />
     -->
</template>
