<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Building2, Plus, Search } from '@lucide/vue';
import { watchDebounced } from '@vueuse/core';
import { reactive, watch } from 'vue';
import EmptyState from '@/components/EmptyState.vue';
import PropertyStatusBadge from '@/components/PropertyStatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
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
import type { EnumOption, Paginated, Property } from '@/types';

const props = defineProps<{
    properties: Paginated<Property>;
    filters: { search: string; status: string; type: string };
    statusOptions: EnumOption[];
    typeOptions: EnumOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Properties', href: index() },
        ],
    },
});

const ALL = 'all';

const form = reactive({
    search: props.filters.search ?? '',
    status: props.filters.status || ALL,
    type: props.filters.type || ALL,
});

function applyFilters(): void {
    router.get(
        index().url,
        {
            search: form.search || undefined,
            status: form.status === ALL ? undefined : form.status,
            type: form.type === ALL ? undefined : form.type,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

watchDebounced(() => form.search, applyFilters, { debounce: 350 });
watch([() => form.status, () => form.type], applyFilters);

function goToPage(page: number): void {
    router.get(
        index().url,
        {
            search: form.search || undefined,
            status: form.status === ALL ? undefined : form.status,
            type: form.type === ALL ? undefined : form.type,
            page,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}
</script>

<template>
    <Head title="Properties" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Properties</h1>
                <p class="text-muted-foreground">
                    Search, filter and manage registered properties.
                </p>
            </div>
            <Button as-child>
                <Link :href="create()"
                    ><Plus class="size-4" /> Register property</Link
                >
            </Button>
        </div>

        <Card>
            <CardContent
                class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center"
            >
                <div class="relative flex-1">
                    <Search
                        class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="form.search"
                        placeholder="Search by number, owner, CNIC or city…"
                        class="pl-9"
                    />
                </div>
                <Select v-model="form.status">
                    <SelectTrigger class="sm:w-48"
                        ><SelectValue placeholder="Status"
                    /></SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="ALL">All statuses</SelectItem>
                        <SelectItem
                            v-for="o in statusOptions"
                            :key="o.value"
                            :value="o.value"
                            >{{ o.label }}</SelectItem
                        >
                    </SelectContent>
                </Select>
                <Select v-model="form.type">
                    <SelectTrigger class="sm:w-44"
                        ><SelectValue placeholder="Type"
                    /></SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="ALL">All types</SelectItem>
                        <SelectItem
                            v-for="o in typeOptions"
                            :key="o.value"
                            :value="o.value"
                            >{{ o.label }}</SelectItem
                        >
                    </SelectContent>
                </Select>
            </CardContent>
        </Card>

        <Card>
            <CardContent class="p-0">
                <EmptyState
                    v-if="properties.data.length === 0"
                    :icon="Building2"
                    title="No properties found"
                    description="Try adjusting your search or filters, or register a new property."
                    class="m-4 border-none"
                />

                <Table v-else>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Property</TableHead>
                            <TableHead>Type</TableHead>
                            <TableHead>Owner</TableHead>
                            <TableHead>City</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Registered</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="property in properties.data"
                            :key="property.id"
                            class="cursor-pointer"
                            @click="router.visit(show(property.id).url)"
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
                            <TableCell>{{ property.type.label }}</TableCell>
                            <TableCell>{{ property.owner_name }}</TableCell>
                            <TableCell>{{ property.city }}</TableCell>
                            <TableCell
                                ><PropertyStatusBadge :status="property.status"
                            /></TableCell>
                            <TableCell class="text-muted-foreground">{{
                                formatDate(property.created_at)
                            }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>

        <div
            v-if="properties.meta.total > 0"
            class="flex flex-wrap items-center justify-between gap-3"
        >
            <p class="text-sm text-muted-foreground">
                Showing {{ properties.meta.from ?? 0 }}–{{
                    properties.meta.to ?? 0
                }}
                of {{ properties.meta.total }}
            </p>
            <div class="flex items-center gap-2">
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="properties.meta.current_page <= 1"
                    @click="goToPage(properties.meta.current_page - 1)"
                >
                    Previous
                </Button>
                <span class="text-sm text-muted-foreground"
                    >Page {{ properties.meta.current_page }} of
                    {{ properties.meta.last_page }}</span
                >
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="
                        properties.meta.current_page >=
                        properties.meta.last_page
                    "
                    @click="goToPage(properties.meta.current_page + 1)"
                >
                    Next
                </Button>
            </div>
        </div>
    </div>
</template>
