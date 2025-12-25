<script setup lang="ts">
import { useGlobalToast } from '@/composables/useGlobalToast';
import { home } from '@/routes';
import { index, liked } from '@/routes/listings';
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

interface NavItem {
    label: string;
    href: string | any;
    activePath: string;
}

const mainNavItems: NavItem[] = [
    {
        label: 'menu.home',
        href: home(),
        activePath: home.url(),
    },
    {
        label: 'menu.marketplace',
        href: index(),
        activePath: index.url(),
    },
    // {
    //     label: 'menu.about',
    //     href: about(),
    //     activePath: about.url(),
    // },
    {
        label: 'menu.favorites',
        href: liked(),
        activePath: liked.url(),
    },
];

const { enableGlobalHandling } = useGlobalToast();
enableGlobalHandling();
</script>

<template>
    <Head>
        <title>{{ head }}</title>
        <!-- <link rel="preconnect" :href="customProps.app.url + link" />
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
    <Header :menuSections="mainNavItems" />
    <main
        class="container-custom mt-30 grid min-h-screen items-center gap-4 pt-4 sm:mt-20 sm:gap-6 sm:pt-6 md:mt-22 md:gap-8 md:pt-8 lg:mt-24 lg:gap-10 lg:pt-10 xl:mt-26 xl:gap-12 xl:pt-12"
    >
        <!-- <ToastOverlay /> -->
        <slot />
    </main>
    <div
        class="mt-4 border-t border-border bg-background pt-4 text-foreground sm:mt-6 md:mt-8 lg:mt-10 xl:mt-12"
    >
        <Footer :menuSections="mainNavItems" />
    </div>
    <Cookies />
    <!--
    <ScrollTop />
     -->
</template>
