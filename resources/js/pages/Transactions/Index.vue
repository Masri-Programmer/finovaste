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
import { useMoney } from '@/composables/useMoney';
import { show } from '@/routes/listings';
import { index, receipt } from '@/routes/transactions';
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
            currency: string;
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

const { formatMoney } = useMoney();

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
        case 'auction_purchase':
            return 'transactions.types.auction_purchase';
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
                                        typeof transaction.payable.listing
                                            .title === 'object'
                                            ? transaction.payable.listing.title[
                                                  $page.props.locale
                                              ] ||
                                              transaction.payable.listing.title[
                                                  'en'
                                              ]
                                            : transaction.payable.listing.title
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
                                {{
                                    formatMoney(
                                        transaction.amount,
                                        transaction.currency,
                                    )
                                }}
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
                                <a
                                    v-if="transaction.status === 'completed'"
                                    :href="receipt.url(transaction.uuid)"
                                    :download="`receipt-${transaction.uuid}.pdf`"
                                    target="_blank"
                                >
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="ml-2"
                                    >
                                        {{
                                            $t('transactions.actions.download')
                                        }}
                                    </Button>
                                </a>
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
