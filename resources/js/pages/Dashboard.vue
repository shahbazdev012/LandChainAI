<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Building2,
    CheckCircle2,
    Clock,
    ShieldAlert,
    ShieldCheck,
    Plus,
    ArrowRight,
} from '@lucide/vue';
import EmptyState from '@/components/EmptyState.vue';
import PropertyStatusBadge from '@/components/PropertyStatusBadge.vue';
import StatCard from '@/components/StatCard.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { formatDate } from '@/lib/format';
import { dashboard } from '@/routes';
import { create, index, show } from '@/routes/properties';
import type { ChainReport, Property } from '@/types';

defineProps<{
    stats: {
        total: number;
        verified: number;
        pending: number;
        flagged: number;
    };
    chain: ChainReport;
    recent: { data: Property[] };
}>();

defineOptions({
    layout: { breadcrumbs: [{ title: 'Dashboard', href: dashboard() }] },
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">
                    Registry Console
                </h1>
                <p class="text-muted-foreground">
                    Overview of registered properties and chain integrity.
                </p>
            </div>
            <Button as-child>
                <Link :href="create()"
                    ><Plus class="size-4" /> Register property</Link
                >
            </Button>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard
                label="Total properties"
                :value="stats.total"
                :icon="Building2"
            />
            <StatCard
                label="Verified"
                :value="stats.verified"
                :icon="CheckCircle2"
                accent="bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"
            />
            <StatCard
                label="In review"
                :value="stats.pending"
                :icon="Clock"
                accent="bg-amber-500/10 text-amber-600 dark:text-amber-400"
            />
            <StatCard
                label="Flagged"
                :value="stats.flagged"
                :icon="ShieldAlert"
                accent="bg-red-500/10 text-red-600 dark:text-red-400"
            />
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <Card class="lg:col-span-2">
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>Recent properties</CardTitle>
                    <Button variant="ghost" size="sm" as-child>
                        <Link :href="index()"
                            >View all <ArrowRight class="size-4"
                        /></Link>
                    </Button>
                </CardHeader>
                <CardContent>
                    <EmptyState
                        v-if="recent.data.length === 0"
                        :icon="Building2"
                        title="No properties yet"
                        description="Register the first property to seal it into the chain."
                    >
                        <Button as-child
                            ><Link :href="create()"
                                ><Plus class="size-4" /> Register property</Link
                            ></Button
                        >
                    </EmptyState>

                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Property</TableHead>
                                <TableHead>Owner</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Registered</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="property in recent.data"
                                :key="property.id"
                                class="cursor-pointer"
                                @click="$inertia.visit(show(property.id).url)"
                            >
                                <TableCell>
                                    <div class="font-medium">
                                        {{ property.title }}
                                    </div>
                                    <div
                                        class="font-mono text-xs text-muted-foreground"
                                    >
                                        {{ property.property_number }}
                                    </div>
                                </TableCell>
                                <TableCell>{{ property.owner_name }}</TableCell>
                                <TableCell
                                    ><PropertyStatusBadge
                                        :status="property.status"
                                /></TableCell>
                                <TableCell class="text-muted-foreground">{{
                                    formatDate(property.created_at)
                                }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Chain integrity</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div
                        class="flex items-center gap-3 rounded-lg border p-4"
                        :class="
                            chain.intact
                                ? 'border-emerald-200 bg-emerald-50/50 dark:border-emerald-500/20 dark:bg-emerald-500/5'
                                : 'border-red-200 bg-red-50/50 dark:border-red-500/20 dark:bg-red-500/5'
                        "
                    >
                        <ShieldCheck
                            v-if="chain.intact"
                            class="size-8 shrink-0 text-emerald-500"
                        />
                        <ShieldAlert
                            v-else
                            class="size-8 shrink-0 text-red-500"
                        />
                        <div>
                            <p class="font-semibold">
                                {{
                                    chain.intact
                                        ? 'Chain intact'
                                        : 'Chain compromised'
                                }}
                            </p>
                            <p class="text-sm text-muted-foreground">
                                {{
                                    chain.intact
                                        ? 'All blocks verified successfully.'
                                        : `Tampering detected at block #${chain.broken_at_sequence}.`
                                }}
                            </p>
                        </div>
                    </div>
                    <dl class="grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-lg border p-3">
                            <dt class="text-muted-foreground">Blocks sealed</dt>
                            <dd class="text-xl font-bold tabular-nums">
                                {{ chain.blocks }}
                            </dd>
                        </div>
                        <div class="rounded-lg border p-3">
                            <dt class="text-muted-foreground">Algorithm</dt>
                            <dd class="text-xl font-bold">SHA-256</dd>
                        </div>
                    </dl>
                    <ul
                        v-if="chain.issues.length"
                        class="space-y-1 text-sm text-red-600 dark:text-red-400"
                    >
                        <li v-for="(issue, i) in chain.issues" :key="i">
                            • {{ issue }}
                        </li>
                    </ul>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
