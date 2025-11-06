<script setup lang="ts">
import googleIcon from '@/images/googleIcon.webp';
import { Link } from '@inertiajs/vue3';
import { Github, Heart, Linkedin } from 'lucide-vue-next';
import { computed } from 'vue';

import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { useScrollSpy } from '@/composables/useScrollSpy';
import { navigationLinks } from '@/lib/navigation';
import {
    contact,
    faq,
    imprint,
    privacyPolicy,
    termsConditions,
} from '@/routes';
import { trans } from 'laravel-vue-i18n';

const { handleScroll } = useScrollSpy();
defineProps<{
    menuSections: Array<any>;
}>();

const year = new Date().getFullYear();

const footerSections = computed(() => ({
    product: navigationLinks
        .filter((link) =>
            ['features', 'services', 'pricing', 'testimonials'].includes(
                link.id,
            ),
        )
        .map((link) => ({
            type: 'scroll',
            target: link.id,
            text: trans(link.i18nKey as string),
            href: link.href,
        })),

    community: [
        {
            text: trans('footer.links.github'),
            href: 'https://github.com/Masri-Programmer',
            isExternal: true,
        },
        {
            text: trans('footer.links.blogPortfolio'),
            href: 'https://masri.blog',
            isExternal: true,
        },
        {
            text: trans('footer.links.bookMeeting'),
            href: 'https://masri.blog/Book-a-Meeting',
            isExternal: true,
        },
        {
            text: trans('footer.links.contact'),
            href: contact.url(),
            isExternal: false,
        },
        { text: trans('footer.links.faq'), href: faq.url(), isExternal: false },
    ],

    legal: [
        {
            text: trans('footer.links.imprint'),
            href: imprint.url(),
            isExternal: false,
        },
        {
            text: trans('footer.links.privacyPolicy'),
            href: privacyPolicy.url(),
            isExternal: false,
        },
        {
            text: trans('footer.links.termsOfService'),
            href: termsConditions.url(),
            isExternal: false,
        },
    ],
}));

const socialLinks = computed(() => [
    {
        name: 'GitHub',
        href: 'https://github.com/Masri-Programmer',
        ariaLabel: trans('footer.githubAriaLabel'),
        icon: Github,
        isSvg: false,
    },
    {
        name: 'LinkedIn',
        href: 'https://www.linkedin.com/in/mohamad-masri-89778915a/',
        ariaLabel: trans('footer.linkedinAriaLabel'),
        icon: Linkedin,
        isSvg: false,
    },
    {
        name: 'Google Maps',
        href: 'https://share.google/6xMW6IuA4AZ6yzrNu',
        ariaLabel: trans('footer.googleAriaLabel'),
        icon: googleIcon,
        isSvg: true,
    },
]);
</script>

<template>
    <footer
        id="footer"
        class="relative z-10 w-full border-t border-border bg-background"
    >
        <div class="container-custom container-custom-y relative">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                <div
                    class="col-span-2 flex flex-col items-start gap-4 md:col-span-1"
                >
                    <div class="hidden flex-col items-center gap-2 sm:block">
                        <Link href="/">
                            <AppLogoIcon
                                class-name="cursor-pointer m-auto h-60 w-65 relative -top-3 left-0"
                            />
                        </Link>
                    </div>
                </div>
                <div class="space-y-4 sm:hidden">
                    <Link href="/">
                        <AppLogoIcon
                            class-name="cursor-pointer m-auto h-60 w-65 relative -top-3 left-0  "
                        />
                    </Link>
                </div>
                <div class="space-y-4">
                    <h4 class="font-semibold tracking-tighter">
                        {{ $t('footer.headings.product') }}
                    </h4>
                    <ul class="space-y-2">
                        <li
                            v-for="link in footerSections.product"
                            :key="link.text"
                        >
                            <button
                                @click="handleScroll(link.target)"
                                class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                            >
                                {{ link.text }}
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="space-y-4">
                    <h4 class="font-semibold tracking-tighter">
                        {{ $t('footer.headings.community') }}
                    </h4>
                    <ul class="space-y-2">
                        <li
                            v-for="link in footerSections.community"
                            :key="link.text"
                        >
                            <a
                                v-if="link.isExternal"
                                :href="link.href"
                                class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                                target="_blank"
                                rel="noopener noreferrer"
                                >{{ link.text }}</a
                            >
                            <Link
                                v-else
                                :href="link.href"
                                class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                                >{{ link.text }}</Link
                            >
                        </li>
                    </ul>
                </div>

                <div class="space-y-4">
                    <h4 class="font-semibold tracking-tighter">
                        {{ $t('footer.headings.legal') }}
                    </h4>
                    <ul class="space-y-2">
                        <li
                            v-for="link in footerSections.legal"
                            :key="link.text"
                        >
                            <Link
                                :href="link.href"
                                class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                                >{{ link.text }}</Link
                            >
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="container-custom-y border-t border-border">
            <div
                class="container-custom flex flex-col-reverse items-center justify-between gap-6 sm:flex-row"
            >
                <p
                    class="text-center text-sm text-muted-foreground sm:text-left"
                >
                    {{ $t('footer.copyright', { year }) }}
                </p>

                <i18n-t
                    keypath="footer.madeWith"
                    tag="p"
                    class="hidden items-center gap-1.5 text-center text-sm text-muted-foreground md:flex"
                >
                    <template #heartIcon>
                        <Heart class="h-4 w-4 fill-red-500 text-red-500" />
                    </template>
                    <template #authorLink>
                        <a
                            href="https://masri.blog"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-medium tracking-tighter text-foreground hover:underline"
                            >Masri Programmer</a
                        >
                    </template>
                </i18n-t>

                <div class="flex items-center gap-1">
                    <a
                        v-for="social in socialLinks"
                        :key="social.name"
                        :href="social.href"
                        target="_blank"
                        rel="noopener noreferrer"
                        :aria-label="social.ariaLabel"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-muted-foreground ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                    >
                        <component
                            v-if="!social.isSvg"
                            :is="social.icon as any"
                            class="h-4 w-4"
                        />
                        <img
                            v-else
                            :src="social.icon as any"
                            class="h-4 w-4"
                            :alt="social.name"
                            loading="lazy"
                            decoding="async"
                        />
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>
