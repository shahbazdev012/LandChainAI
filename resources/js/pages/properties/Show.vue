<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Check,
    ClipboardCheck,
    Pencil,
    Trash2,
    X,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import ChainPanel from '@/components/properties/ChainPanel.vue';
import PropertyStatusBadge from '@/components/PropertyStatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { formatDate } from '@/lib/format';
import { dashboard } from '@/routes';
import {
    approve,
    destroy as destroyProperty,
    edit,
    index,
    reject,
} from '@/routes/properties';
import type { ChainReport, Property, PropertyPermissions } from '@/types';

const props = defineProps<{
    property: Property;
    chain: ChainReport;
    blockValid: boolean | null;
    can: PropertyPermissions;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Properties', href: index() },
        ],
    },
});

const isPending = computed(
    () => props.property.status.value === 'pending_approval',
);

const details = computed(() => [
    {
        label: 'Property number',
        value: props.property.property_number,
        mono: true,
    },
    { label: 'Type', value: props.property.type.label },
    { label: 'Owner name', value: props.property.owner_name },
    { label: 'Owner CNIC', value: props.property.owner_cnic },
    { label: 'Contact', value: props.property.owner_contact ?? '—' },
    { label: 'Area', value: props.property.area_label },
    { label: 'Address', value: props.property.address },
    { label: 'City', value: props.property.city },
    { label: 'Province', value: props.property.province },
    { label: 'Created by', value: props.property.created_by ?? '—' },
    { label: 'Approved by', value: props.property.approved_by ?? '—' },
]);

const decisionForm = useForm({});

function approveProperty(): void {
    decisionForm.post(approve(props.property.id).url, { preserveScroll: true });
}

function rejectProperty(): void {
    decisionForm.post(reject(props.property.id).url, { preserveScroll: true });
}

const showDeleteDialog = ref(false);

function deleteProperty(): void {
    router.delete(destroyProperty(props.property.id).url);
}
</script>

<template>
    <Head :title="property.title" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-1">
                <Button
                    variant="ghost"
                    size="sm"
                    class="mb-1 -ml-2 text-muted-foreground"
                    as-child
                >
                    <Link :href="index()"
                        ><ArrowLeft class="size-4" /> Back to properties</Link
                    >
                </Button>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-bold tracking-tight">
                        {{ property.title }}
                    </h1>
                    <PropertyStatusBadge :status="property.status" />
                </div>
                <p class="font-mono text-sm text-muted-foreground">
                    {{ property.property_number }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Button v-if="can.update" variant="outline" as-child>
                    <Link :href="edit(property.id)"
                        ><Pencil class="size-4" /> Edit</Link
                    >
                </Button>
                <Button
                    v-if="can.delete"
                    variant="outline"
                    class="text-destructive hover:text-destructive"
                    @click="showDeleteDialog = true"
                >
                    <Trash2 class="size-4" /> Delete
                </Button>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <!-- Approval gate -->
                <Card
                    v-if="isPending && can.approve"
                    class="border-amber-200 dark:border-amber-500/30"
                >
                    <CardContent
                        class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="flex items-start gap-3">
                            <ClipboardCheck
                                class="mt-0.5 size-5 shrink-0 text-amber-600 dark:text-amber-400"
                            />
                            <div>
                                <p class="font-medium">
                                    Awaiting your approval
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    Review the record below, then approve (seals
                                    it into the chain) or reject it.
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <Button
                                size="sm"
                                :disabled="decisionForm.processing"
                                @click="approveProperty"
                                ><Check class="size-4" /> Approve</Button
                            >
                            <Button
                                size="sm"
                                variant="outline"
                                class="text-destructive hover:text-destructive"
                                :disabled="decisionForm.processing"
                                @click="rejectProperty"
                                ><X class="size-4" /> Reject</Button
                            >
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        ><CardTitle>Property record</CardTitle></CardHeader
                    >
                    <CardContent>
                        <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
                            <div v-for="item in details" :key="item.label">
                                <dt
                                    class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    {{ item.label }}
                                </dt>
                                <dd
                                    class="mt-0.5 text-sm"
                                    :class="item.mono ? 'font-mono' : ''"
                                >
                                    {{ item.value }}
                                </dd>
                            </div>
                        </dl>
                        <div
                            v-if="property.description"
                            class="mt-4 border-t pt-4"
                        >
                            <dt
                                class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Description
                            </dt>
                            <dd class="mt-1 text-sm">
                                {{ property.description }}
                            </dd>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="space-y-6">
                <Card>
                    <CardHeader
                        ><CardTitle>Blockchain record</CardTitle></CardHeader
                    >
                    <CardContent>
                        <ChainPanel
                            :block="property.block"
                            :block-valid="blockValid"
                            :chain="chain"
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>Timeline</CardTitle></CardHeader>
                    <CardContent class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Created</span>
                            <span>{{ formatDate(property.created_at) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Approved</span>
                            <span>{{ formatDate(property.approved_at) }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <Dialog v-model:open="showDeleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete this property?</DialogTitle>
                    <DialogDescription>
                        This removes
                        <strong>{{ property.property_number }}</strong> from the
                        registry. A sealed block remains in the ledger for audit
                        integrity.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <DialogClose as-child
                        ><Button variant="ghost">Cancel</Button></DialogClose
                    >
                    <Button variant="destructive" @click="deleteProperty"
                        >Delete property</Button
                    >
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
