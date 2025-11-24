<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'; // Import Icons
import { computed, ref } from 'vue';

import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';

import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Paginator } from '@/types';
import { trans } from 'laravel-vue-i18n';

const props = withDefaults(
    defineProps<{
        paginator: Paginator;
        name?: string;
        steps?: number[];
    }>(),
    {
        name: 'pagination.defaultName',
        steps: () => [12, 25, 50, 100],
    },
);

const selectedPerPage = ref(props.paginator.per_page);

const perPageSteps = computed(() => {
    const stepsSet = new Set(props.steps);
    stepsSet.add(props.paginator.per_page);
    return Array.from(stepsSet).sort((a, b) => a - b);
});

const links = computed(() => props.paginator.links);

const paginationInfo = computed(() => {
    const translatedName = trans(props.name);
    if (
        !props.paginator.from ||
        !props.paginator.to ||
        props.paginator.total === 0
    ) {
        return trans('pagination.noResults', { name: translatedName });
    }

    return trans('pagination.showing', {
        from: String(props.paginator.from),
        to: String(props.paginator.to),
        total: String(props.paginator.total),
        name: translatedName,
    });
});

const isPrevious = (label: string) =>
    label.includes('&laquo;') || label.toLowerCase().includes('previous');

const isNext = (label: string) =>
    label.includes('&raquo;') || label.toLowerCase().includes('next');

const isEllipsis = (label: string) => label === '...';

function handlePerPageChange(value: string | number | null | undefined) {
    if (!value) {
        return;
    }

    const newPerPage = Number(value);
    selectedPerPage.value = newPerPage;

    const currentParams = {};

    router.get(
        props.paginator.path,
        {
            ...currentParams,
            per_page: newPerPage,
            page: 1,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}
</script>

<template>
    <div
        class="flex flex-col flex-wrap items-center justify-between gap-4 py-4 md:flex-row"
    >
        <div class="text-sm text-muted-foreground">
            {{ paginationInfo }}
        </div>

        <Pagination
            v-if="links.length > 3"
            :total="paginator.total"
            :items-per-page="Number(paginator.per_page)"
            :page="paginator.current_page"
            :aria-label="$t('pagination.ariaLabel')"
            class="flex-1 justify-center"
        >
            <PaginationContent>
                <template v-for="(link, key) in links" :key="key">
                    <PaginationPrevious
                        v-if="isPrevious(link.label)"
                        :as-child="!!link.url"
                        :disabled="!link.url"
                    >
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-scroll
                            :aria-label="$t('pagination.previous')"
                        >
                            <ChevronLeft class="h-4 w-4" />
                        </Link>
                        <ChevronLeft v-else class="h-4 w-4" />
                    </PaginationPrevious>

                    <PaginationNext
                        v-else-if="isNext(link.label)"
                        :as-child="!!link.url"
                        :disabled="!link.url"
                    >
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-scroll
                            :aria-label="$t('pagination.next')"
                        >
                            <ChevronRight class="h-4 w-4" />
                        </Link>
                        <ChevronRight v-else class="h-4 w-4" />
                    </PaginationNext>

                    <PaginationEllipsis v-else-if="isEllipsis(link.label)" />

                    <PaginationItem
                        v-else
                        :as-child="!!link.url"
                        :value="Number(link.label)"
                        :is-active="link.active"
                        :disabled="!link.url"
                    >
                        <Link v-if="link.url" :href="link.url" preserve-scroll>
                            {{ link.label }}
                        </Link>
                        <span v-else v-html="link.label" />
                    </PaginationItem>
                </template>
            </PaginationContent>
        </Pagination>

        <div class="flex items-center gap-2 text-sm text-muted-foreground">
            <Select
                :model-value="String(selectedPerPage)"
                @update:model-value="handlePerPageChange"
            >
                <SelectTrigger class="w-[80px] bg-background">
                    <SelectValue :placeholder="selectedPerPage" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem
                        v-for="step in perPageSteps"
                        :key="step"
                        :value="String(step)"
                    >
                        {{ step }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <span>
                {{
                    $t('pagination.perPageName', {
                        name: $t(props.name),
                    })
                }}
            </span>
        </div>
    </div>
</template>
