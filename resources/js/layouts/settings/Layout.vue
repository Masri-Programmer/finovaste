<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { toUrl, urlIsActive } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editLanguages } from '@/routes/languages';
import { edit as editPassword } from '@/routes/password';
import { edit as editProfile } from '@/routes/profile';
import { show } from '@/routes/two-factor';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';

const sidebarNavItems = computed<NavItem[]>(() => [
    {
        title: trans('layout.profile'),
        href: editProfile(),
    },
    {
        title: trans('layout.password'),
        href: editPassword(),
    },
    {
        title: trans('layout.twoFactor'),
        href: show(),
    },
    {
        title: trans('layout.appearance'),
        href: editAppearance(),
    },
    {
        title: trans('layout.languages'),
        href: editLanguages(),
    },
]);

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <Heading
        :title="$t('layout.title')"
        :description="$t('layout.description')"
    />

    <div class="flex min-h-[100vh] flex-col lg:flex-row lg:space-x-12">
        <aside class="w-full max-w-xl lg:w-48">
            <nav class="flex flex-col space-y-1 space-x-0">
                <Button
                    v-for="item in sidebarNavItems"
                    :key="toUrl(item.href)"
                    variant="ghost"
                    :class="[
                        'w-full justify-start',
                        { 'bg-muted': urlIsActive(item.href, currentPath) },
                    ]"
                    as-child
                >
                    <Link :href="item.href" class="p-1 text-wrap!">
                        <component :is="item.icon" class="h-4 w-4" />
                        {{ item.title }}
                    </Link>
                </Button>
            </nav>
        </aside>

        <Separator class="my-6 lg:hidden" />

        <div class="flex-1 md:max-w-2xl">
            <section class="max-w-xl space-y-12">
                <slot />
            </section>
        </div>
    </div>
</template>
