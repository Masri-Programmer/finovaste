<script setup lang="ts">
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { DollarSign, Package, TrendingUp, Users } from 'lucide-vue-next';
import { computed } from 'vue';

const stats = computed(() => ({
    totalListings: 'XQ',
    totalUsers: 'XY',
    totalRevenue: '1000€',
    activeListings: '44',
}));
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex flex-1 flex-col gap-4 p-4">
                <div class="mb-4">
                    <h1 class="mb-2 text-3xl font-bold text-foreground">
                        {{ $t('admin.dashboard.title') }}
                    </h1>
                    <p class="text-muted-foreground">
                        {{ $t('admin.dashboard.subtitle') }}
                    </p>
                </div>

                <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                    <Card
                        class="rounded-xl border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <CardHeader
                            class="flex flex-row items-center justify-between pb-2"
                        >
                            <CardTitle class="text-sm font-medium">
                                {{ $t('admin.dashboard.stats_total_listings') }}
                            </CardTitle>
                            <Package class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">
                                {{ stats.totalListings }}
                            </div>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ $t('admin.dashboard.stats_listings_desc') }}
                            </p>
                        </CardContent>
                    </Card>

                    <Card
                        class="rounded-xl border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <CardHeader
                            class="flex flex-row items-center justify-between pb-2"
                        >
                            <CardTitle class="text-sm font-medium">
                                {{ $t('admin.dashboard.stats_total_users') }}
                            </CardTitle>
                            <Users class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">
                                {{ stats.totalUsers }}
                            </div>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ $t('admin.dashboard.stats_users_desc') }}
                            </p>
                        </CardContent>
                    </Card>

                    <Card
                        class="rounded-xl border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <CardHeader
                            class="flex flex-row items-center justify-between pb-2"
                        >
                            <CardTitle class="text-sm font-medium">
                                {{ $t('admin.dashboard.stats_total_revenue') }}
                            </CardTitle>
                            <DollarSign class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">
                                ${{ stats.totalRevenue.toLocaleString() }}
                            </div>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ $t('admin.dashboard.stats_revenue_desc') }}
                            </p>
                        </CardContent>
                    </Card>

                    <Card
                        class="rounded-xl border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <CardHeader
                            class="flex flex-row items-center justify-between pb-2"
                        >
                            <CardTitle class="text-sm font-medium">
                                {{
                                    $t('admin.dashboard.stats_active_listings')
                                }}
                            </CardTitle>
                            <TrendingUp class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">
                                {{ stats.activeListings }}
                            </div>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ $t('admin.dashboard.stats_active_desc') }}
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <div
                    class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
