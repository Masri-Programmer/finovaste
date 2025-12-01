<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
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

function handlePerPageChange(value: string | number | null | undefined) {
    if (!value) return;

    const newPerPage = Number(value);
    selectedPerPage.value = newPerPage;

    router.get(
        props.paginator.path,
        {
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
                <!-- Iterate with index to safely identify First (Prev) and Last (Next) -->
                <template v-for="(link, index) in links" :key="index">
                    <!-- Previous Button (Always the first item) -->
                    <PaginationItem v-if="index === 0">
                        <PaginationPrevious
                            :as-child="!!link.url"
                            :class="{
                                'pointer-events-none opacity-50': !link.url,
                            }"
                        >
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                preserve-scroll
                                :aria-label="$t('pagination.previous')"
                            >
                                <ChevronLeft class="h-4 w-4" />
                            </Link>
                            <span
                                v-else
                                class="flex h-9 w-9 items-center justify-center"
                            >
                                <ChevronLeft class="h-4 w-4" />
                            </span>
                        </PaginationPrevious>
                    </PaginationItem>

                    <!-- Next Button (Always the last item) -->
                    <PaginationItem v-else-if="index === links.length - 1">
                        <PaginationNext
                            :as-child="!!link.url"
                            :class="{
                                'pointer-events-none opacity-50': !link.url,
                            }"
                        >
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                preserve-scroll
                                :aria-label="$t('pagination.next')"
                            >
                                <ChevronRight class="h-4 w-4" />
                            </Link>
                            <span
                                v-else
                                class="flex h-9 w-9 items-center justify-center"
                            >
                                <ChevronRight class="h-4 w-4" />
                            </span>
                        </PaginationNext>
                    </PaginationItem>

                    <!-- Ellipsis -->
                    <PaginationEllipsis v-else-if="link.label === '...'" />

                    <!-- Numbered Page Links -->
                    <PaginationItem v-else>
                        <!-- Using as-child pattern to avoid nesting buttons/links improperly -->
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-scroll
                            class="inline-flex h-9 w-9 items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50"
                            :class="
                                link.active
                                    ? 'bg-primary text-primary-foreground hover:bg-primary/90'
                                    : 'border border-input bg-background hover:bg-accent hover:text-accent-foreground'
                            "
                        >
                            {{ link.label }}
                        </Link>
                        <!-- Current/Active Page (if URL is null/active) -->
                        <span
                            v-else
                            class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-input bg-background text-sm font-medium opacity-50"
                            v-html="link.label"
                        />
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
                    <SelectValue :placeholder="String(selectedPerPage)" />
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
            <span class="whitespace-nowrap">
                {{
                    $t('pagination.perPageName', {
                        name: $t(props.name),
                    })
                }}
            </span>
        </div>
    </div>
</template>
