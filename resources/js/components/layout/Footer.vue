<template>
    <footer class="container-custom mx-auto px-4 py-12 md:px-6">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-5">
            <div class="col-span-1 sm:col-span-2 lg:col-span-2">
                <div class="flex items-center space-x-3">
                    <AppLogoIcon class="size-6 fill-current" />
                    <span class="text-2xl font-bold text-foreground">
                        {{ $page.props.name }}
                    </span>
                </div>

                <p class="mt-4 max-w-xs text-base text-muted-foreground">
                    {{ $t('footer.description') }}
                </p>

                <div class="mt-6 space-y-4">
                    <div
                        class="flex cursor-pointer items-center space-x-3 text-muted-foreground transition-colors hover:text-primary"
                        @click="
                            copyToClipboard(
                                'xxxx',
                                $t('footer.contact.emailLabel'),
                            )
                        "
                    >
                        <Mail class="h-5 w-5 flex-shrink-0" />
                        <span>{{ $t('footer.contact.email') }}</span>
                    </div>
                    <div
                        class="flex cursor-pointer items-center space-x-3 text-muted-foreground transition-colors hover:text-primary"
                        @click="
                            copyToClipboard(
                                '+1 (555) 123-4567',
                                $t('footer.contact.phoneLabel'),
                            )
                        "
                    >
                        <Phone class="h-5 w-5 flex-shrink-0" />
                        <span>{{ $t('footer.contact.phone') }}</span>
                    </div>
                    <div
                        class="flex items-center space-x-3 text-muted-foreground"
                    >
                        <MapPin class="h-5 w-5 flex-shrink-0" />
                        <span>{{ $t('footer.contact.address') }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="font-semibold text-foreground">
                    {{ $t('footer.company.title') }}
                </h4>
                <ul class="mt-4 space-y-3">
                    <li>
                        <Link
                            :href="footerRoutes.company.about"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            {{ $t('menu.marketplace') }}
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="footerRoutes.company.about"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            {{ $t('menu.about') }}
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="footerRoutes.company.contact"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            {{ $t('menu.contact') }}
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="footerRoutes.company.contact"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            {{ $t('menu.faq') }}
                        </Link>
                    </li>
                    <!-- <li>
                        <Link
                            :href="footerRoutes.company.careers"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            {{ $t('menu.careers') }}
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="footerRoutes.company.press"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            {{ $t('menu.press') }}
                        </Link>
                    </li> -->
                </ul>
            </div>

            <!-- <div>
                <h4 class="font-semibold text-foreground">
                    {{ $t('footer.support.title') }}
                </h4>
                <ul class="mt-4 space-y-3">
                    <li>
                        <Link
                            :href="footerRoutes.support.faq"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            {{ $t('footer.support.faq') }}
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="footerRoutes.support.help"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            {{ $t('footer.support.help') }}
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="footerRoutes.support.safety"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            {{ $t('footer.support.safety') }}
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="footerRoutes.support.community"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            {{ $t('footer.support.community') }}
                        </Link>
                    </li>
                </ul>
            </div> -->

            <div>
                <h4 class="font-semibold text-foreground">
                    {{ $t('footer.legal.title') }}
                </h4>
                <ul class="mt-4 space-y-3">
                    <li>
                        <Link
                            :href="footerRoutes.legal.privacy"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            {{ $t('footer.legal.privacy') }}
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="footerRoutes.legal.termsService"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            {{ $t('footer.legal.terms') }}
                        </Link>
                    </li>
                    <li>
                        <Link
                            :href="footerRoutes.legal.imprint"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            {{ $t('footer.legal.imprint') }}
                        </Link>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-foreground">
                    {{ $t('menu.marketplace') }}
                </h4>
                <ul class="mt-4 space-y-3">
                    <li v-for="item in $page.props.categories" :key="item.id">
                        <Link
                            :href="`${index.url()}/?category=${item.slug}`"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            {{
                                item.name[$page.props.locale] ?? item.name['en']
                            }}
                        </Link>
                    </li>
                </ul>
            </div>
        </div>

        <div
            class="mt-12 flex flex-col-reverse items-center justify-between gap-6 border-t border-border pt-8 sm:flex-row"
        >
            <p class="text-center text-sm text-muted-foreground sm:text-left">
                © {{ year }} {{ $page.props.name }}
                {{ $t('footer.copyright') }}
            </p>

            <div class="flex space-x-5">
                <Link
                    :href="footerRoutes.socials.facebook"
                    class="text-muted-foreground transition-colors hover:text-primary"
                >
                    <span class="sr-only">Facebook</span>
                    <Facebook class="h-5 w-5" />
                </Link>
                <Link
                    :href="footerRoutes.socials.twitter"
                    class="text-muted-foreground transition-colors hover:text-primary"
                >
                    <span class="sr-only">Twitter</span>
                    <Twitter class="h-5 w-5" />
                </Link>
                <Link
                    :href="footerRoutes.socials.instagram"
                    class="text-muted-foreground transition-colors hover:text-primary"
                >
                    <span class="sr-only">Instagram</span>
                    <Instagram class="h-5 w-5" />
                </Link>
                <Link
                    :href="footerRoutes.socials.linkedin"
                    class="text-muted-foreground transition-colors hover:text-primary"
                >
                    <span class="sr-only">LinkedIn</span>
                    <Linkedin class="h-5 w-5" />
                </Link>
                <Link
                    :href="footerRoutes.socials.youtube"
                    class="text-muted-foreground transition-colors hover:text-primary"
                >
                    <span class="sr-only">YouTube</span>
                    <Youtube class="h-5 w-5" />
                </Link>
            </div>
        </div>
    </footer>
</template>

<script setup lang="ts">
import { index } from '@/routes/listings';
import { Link } from '@inertiajs/vue3';
import { useClipboard, useDateFormat, useNow } from '@vueuse/core';
import { trans } from 'laravel-vue-i18n';
import {
    Facebook,
    Instagram,
    Linkedin,
    Mail,
    MapPin,
    Phone,
    Twitter,
    Youtube,
} from 'lucide-vue-next';
import { useToast } from 'vue-toastification';
import AppLogoIcon from '../AppLogoIcon.vue';

const footerRoutes = {
    company: {
        about: '/about-us',
        contact: '/contact',
        careers: '/careers',
        press: '/press',
    },
    support: {
        faq: '/faq',
        help: '/help-center',
        safety: '/safety-tips',
        community: '/community',
    },
    legal: {
        termsGeneral: '/terms-and-conditions',
        privacy: '/privacy-policy',
        termsService: '/terms-of-service',
        imprint: '/imprint',
    },
    marketplace: {
        properties: '/properties',
        vehicles: '/vehicles',
        auctions: '/auctions',
    },
    socials: {
        facebook: 'https://facebook.com',
        twitter: 'https://twitter.com',
        instagram: 'https://instagram.com',
        linkedin: 'https://linkedin.com',
        youtube: 'https://youtube.com',
    },
};

const toast = useToast();
const { copy } = useClipboard();

const year = useDateFormat(useNow(), 'YYYY');
const copyToClipboard = (text: string, type: string) => {
    copy(text)
        .then(() => {
            toast.success(trans('footer.copied', { type: type }));
        })
        .catch(() => {
            toast.error(trans('footer.copyFailed'));
        });
};
</script>
