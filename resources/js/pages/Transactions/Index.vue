<script setup lang="ts">
import Layout from '@/components/layout/Layout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { formatCurrency } from '@/composables/useCurrency';
import { show } from '@/routes/listings';
import { index } from '@/routes/transactions';
import { Link, router } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { debounce } from 'lodash';
import { ref, watch } from 'vue';

const props = defineProps<{
    transactions: {
        data: Array<{
            id: number;
            uuid: string;
            type: string;
            amount: number;
            status: string;
            created_at: string;
            payable: {
                listing: {
                    id: number;
                    title: string;
                };
            } | null;
        }>;
        links: Array<any>;
    };
    filters: {
        type?: string;
        search?: string;
    };
}>();

const search = ref(props.filters.search || '');

watch(
    search,
    debounce((value: string) => {
        router.get(
            index(),
            { ...props.filters, search: value },
            { preserveState: true, replace: true },
        );
    }, 300),
);

const getTypeLabel = (type: string) => {
    switch (type) {
        case 'buy_now':
            return 'transactions.types.purchase';
        case 'auction_buy_now':
            return 'transactions.types.auction_buy_now';
        case 'investment':
            return 'transactions.types.investment';
        case 'donation':
            return 'transactions.types.donation';
        case 'auction':
            return 'transactions.types.auction';
        default:
            return type;
    }
};

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'completed':
            return 'default';
        case 'pending':
            return 'secondary';
        case 'failed':
            return 'destructive';
        default:
            return 'outline';
    }
};
</script>

<template>
    <Layout>
        <div class="flex h-full min-h-screen grow flex-col gap-3">
            <h2 class="text-3xl font-bold tracking-tight">
                {{ $t('transactions.title') }}
            </h2>
            <p class="text-muted-foreground">
                {{ $t('transactions.description') }}
            </p>
            <!-- <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2 text-sm">
                    <Link :href="index()" :class="{'font-bold text-primary': !filters.type, 'text-muted-foreground hover:text-primary': filters.type}">
                        {{ $t('transactions.filters.all') }}
                    </Link>
                    <Link :href="index({ query: { type: 'buy_now' } })" :class="{'font-bold text-primary': filters.type === 'buy_now', 'text-muted-foreground hover:text-primary': filters.type !== 'buy_now'}">
                        {{ $t('transactions.filters.purchases') }}
                    </Link>
                    <Link :href="index({ query: { type: 'investment' } })" :class="{'font-bold text-primary': filters.type === 'investment', 'text-muted-foreground hover:text-primary': filters.type !== 'investment'}">
                        {{ $t('transactions.filters.investments') }}
                    </Link>
                    <Link :href="index({ query: { type: 'donation' } })" :class="{'font-bold text-primary': filters.type === 'donation', 'text-muted-foreground hover:text-primary': filters.type !== 'donation'}">
                        {{ $t('transactions.filters.donations') }}
                    </Link>
                    <Link :href="index({ query: { type: 'auction' } })" :class="{'font-bold text-primary': filters.type === 'auction', 'text-muted-foreground hover:text-primary': filters.type !== 'auction'}">
                        {{ $t('transactions.filters.auctions') }}
                    </Link>
                </div>
            
                <div class="relative w-full sm:w-64">
                    <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input v-model="search" :placeholder="$t('transactions.search_placeholder')" class="pl-8 bg-background" />
                </div>
            </div> -->
            <div
                class="h-full rounded-md border bg-card text-card-foreground shadow-sm"
            >
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>{{
                                $t('transactions.columns.date')
                            }}</TableHead>
                            <TableHead>{{
                                $t('transactions.columns.type')
                            }}</TableHead>
                            <TableHead>{{
                                $t('transactions.columns.item')
                            }}</TableHead>
                            <TableHead>{{
                                $t('transactions.columns.amount')
                            }}</TableHead>
                            <TableHead>{{
                                $t('transactions.columns.status')
                            }}</TableHead>
                            <TableHead class="text-right">{{
                                $t('transactions.columns.action')
                            }}</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="transaction in transactions.data"
                            :key="transaction.id"
                        >
                            <TableCell class="font-medium">
                                {{
                                    format(
                                        new Date(transaction.created_at),
                                        'MMM dd, yyyy',
                                    )
                                }}
                            </TableCell>
                            <TableCell>
                                {{ $t(getTypeLabel(transaction.type)) }}
                            </TableCell>
                            <TableCell>
                                <span
                                    v-if="
                                        transaction.payable &&
                                        transaction.payable.listing
                                    "
                                    class="block max-w-[200px] truncate"
                                >
                                    {{
                                        transaction.payable.listing.title[
                                            $page.props.locale
                                        ]
                                    }}
                                </span>
                                <span
                                    v-else
                                    class="text-muted-foreground italic"
                                    >{{
                                        $t('transactions.item_unavailable')
                                    }}</span
                                >
                            </TableCell>
                            <TableCell>
                                {{ formatCurrency(transaction.amount) }}
                            </TableCell>
                            <TableCell>
                                <Badge
                                    :variant="
                                        getStatusVariant(transaction.status)
                                    "
                                >
                                    {{
                                        $t(
                                            'transactions.status.' +
                                                transaction.status,
                                        )
                                    }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-right">
                                <Link
                                    v-if="
                                        transaction.payable &&
                                        transaction.payable.listing
                                    "
                                    :href="
                                        show.url(transaction.payable.listing.id)
                                    "
                                >
                                    <Button variant="ghost" size="sm">{{
                                        $t('transactions.actions.view')
                                    }}</Button>
                                </Link>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="transactions.data.length === 0">
                            <TableCell
                                colspan="6"
                                class="h-24 text-center text-muted-foreground"
                            >
                                {{ $t('transactions.empty_state') }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </Layout>
</template>
