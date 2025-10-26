<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

import Pagination from '@/components/ui/pagination/Pagination.vue';
import PaginationContent from '@/components/ui/pagination/PaginationContent.vue';
import PaginationEllipsis from '@/components/ui/pagination/PaginationEllipsis.vue';
import PaginationItem from '@/components/ui/pagination/PaginationItem.vue';
import PaginationNext from '@/components/ui/pagination/PaginationNext.vue';
import PaginationPrevious from '@/components/ui/pagination/PaginationPrevious.vue';

interface PaginationLinkData {
    url: string | null;
    label: string;
    active: boolean;
}

// defineProps<{
//     links: PaginationLinkData[];
// }>();
const { t } = useI18n();
const isPrevious = (label: string) =>
    label.includes(t('layout.pagination.previous'));
const isNext = (label: string) => label.includes(t('layout.pagination.next'));
const isEllipsis = (label: string) => label.includes('...');
const fakePaginator = {
    links: [
        { url: null, label: t('layout.pagination.previous'), active: false },
        { url: 'http://example.com/items?page=1', label: '1', active: true },
        { url: 'http://example.com/items?page=2', label: '2', active: false },
        { url: 'http://example.com/items?page=3', label: '3', active: false },
        {
            url: 'http://example.com/items?page=2',
            label: t('layout.pagination.next'),
            active: true,
        },
    ],
};
const links = computed(() => fakePaginator.links as PaginationLinkData[]);
</script>

<template>
    <Pagination
        v-if="links.length > 3"
        class="my-6"
        :items-per-page="10"
        :total="100"
        :aria-label="t('layout.pagination.navigationAriaLabel')"
    >
        <PaginationContent>
            <template v-for="(link, key) in links" :key="key">
                <PaginationPrevious
                    v-if="isPrevious(link.label)"
                    :as-child="!!link.url"
                    :disabled="!link.url"
                >
                    <Link v-if="link.url" :href="link.url" preserve-scroll />
                </PaginationPrevious>

                <PaginationNext
                    v-else-if="isNext(link.label)"
                    :as-child="!!link.url"
                    :disabled="!link.url"
                >
                    <Link v-if="link.url" :href="link.url" preserve-scroll />
                </PaginationNext>

                <PaginationEllipsis v-else-if="isEllipsis(link.label)" />

                <PaginationItem
                    v-else
                    :as-child="!!link.url"
                    :value="Number(link.label)"
                    :is-active="link.active"
                    :disabled="!link.url"
                >
                    <Link v-if="link.url" :href="link.url" preserve-scroll>{{
                        link.label
                    }}</Link>
                    <span v-else v-html="link.label" />
                </PaginationItem>
            </template>
        </PaginationContent>
    </Pagination>
</template>
