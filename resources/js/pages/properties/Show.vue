<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Check,
    ClipboardCheck,
    Download,
    FileText,
    Image as ImageIcon,
    Pencil,
    ScanLine,
    Trash2,
    Upload,
    X,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import EmptyState from '@/components/EmptyState.vue';
import InputError from '@/components/InputError.vue';
import ChainPanel from '@/components/properties/ChainPanel.vue';
import VerificationReport from '@/components/properties/VerificationReport.vue';
import PropertyStatusBadge from '@/components/PropertyStatusBadge.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { formatDate, formatDateTime } from '@/lib/format';
import { dashboard } from '@/routes';
import {
    approve,
    destroy as destroyProperty,
    edit,
    index,
    reject,
    verify,
} from '@/routes/properties';
import {
    destroy as destroyDocument,
    store as storeDocument,
} from '@/routes/properties/documents';
import type { ChainReport, EnumOption, Property } from '@/types';

const props = defineProps<{
    property: Property;
    chain: ChainReport;
    blockValid: boolean | null;
    documentTypeOptions: EnumOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Properties', href: index() },
        ],
    },
});

const page = usePage();
const isAdmin = computed(() => page.props.auth.roles.includes('admin'));

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
    { label: 'Province', value: props.property.province ?? '—' },
    {
        label: 'Registered by',
        value: props.property.registered_by?.name ?? '—',
    },
]);

const verifyForm = useForm<{ document_id: number | null }>({
    document_id: null,
});

function runVerification(): void {
    verifyForm.post(verify(props.property.id).url, { preserveScroll: true });
}

const isAwaitingApproval = computed(() => props.property.status.value === 'awaiting_approval');

const decisionForm = useForm({});

function approveProperty(): void {
    decisionForm.post(approve(props.property.id).url, { preserveScroll: true });
}

function rejectProperty(): void {
    decisionForm.post(reject(props.property.id).url, { preserveScroll: true });
}

const uploadForm = useForm<{ type: string; file: File | null }>({
    type: props.documentTypeOptions[0]?.value ?? '',
    file: null,
});

function onFile(event: Event): void {
    const target = event.target as HTMLInputElement;
    uploadForm.file = target.files?.[0] ?? null;
}

function uploadDocument(): void {
    uploadForm.post(storeDocument(props.property.id).url, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => uploadForm.reset('file'),
    });
}

function deleteDocument(documentId: number): void {
    router.delete(destroyDocument([props.property.id, documentId]).url, {
        preserveScroll: true,
    });
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
                <Button variant="outline" as-child
                    ><Link :href="edit(property.id)"
                        ><Pencil class="size-4" /> Edit</Link
                    ></Button
                >
                <Button
                    v-if="isAdmin"
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
                <!-- Details -->
                <Card>
                    <CardHeader
                        ><CardTitle>Property details</CardTitle></CardHeader
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

                <!-- Documents -->
                <Card>
                    <CardHeader>
                        <CardTitle>Documents</CardTitle>
                        <CardDescription
                            >Ownership documents stored privately and used for
                            AI verification.</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <ul
                            v-if="
                                property.documents && property.documents.length
                            "
                            class="divide-y rounded-lg border"
                        >
                            <li
                                v-for="doc in property.documents"
                                :key="doc.id"
                                class="flex items-center gap-3 p-3"
                            >
                                <component
                                    :is="doc.is_image ? ImageIcon : FileText"
                                    class="size-5 shrink-0 text-muted-foreground"
                                />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium">
                                        {{ doc.original_name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ doc.type.label }} ·
                                        {{ doc.size_label }}
                                    </p>
                                </div>
                                <a
                                    :href="doc.download_url"
                                    class="text-muted-foreground hover:text-foreground"
                                    :aria-label="`Download ${doc.original_name}`"
                                >
                                    <Download class="size-4" />
                                </a>
                                <button
                                    type="button"
                                    class="text-muted-foreground hover:text-destructive"
                                    :aria-label="`Delete document ${doc.original_name}`"
                                    @click="deleteDocument(doc.id)"
                                >
                                    <Trash2 class="size-4" />
                                </button>
                            </li>
                        </ul>
                        <p v-else class="text-sm text-muted-foreground">
                            No documents uploaded yet.
                        </p>

                        <form
                            class="flex flex-col gap-3 rounded-lg border border-dashed p-3 sm:flex-row sm:items-end"
                            @submit.prevent="uploadDocument"
                        >
                            <div class="grid gap-2 sm:w-48">
                                <Label for="upload-type">Type</Label>
                                <Select v-model="uploadForm.type">
                                    <SelectTrigger id="upload-type"
                                        ><SelectValue
                                    /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="o in documentTypeOptions"
                                            :key="o.value"
                                            :value="o.value"
                                            >{{ o.label }}</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="grid flex-1 gap-2">
                                <Label for="upload-file">File</Label>
                                <input
                                    id="upload-file"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="w-full rounded-md border border-input bg-transparent text-sm text-muted-foreground file:mr-3 file:cursor-pointer file:border-0 file:bg-muted file:px-3 file:py-2 file:text-sm file:font-medium file:text-foreground hover:file:bg-muted/80"
                                    @input="onFile"
                                />
                                <InputError :message="uploadForm.errors.file" />
                            </div>
                            <Button
                                type="submit"
                                :disabled="
                                    uploadForm.processing || !uploadForm.file
                                "
                            >
                                <Upload class="size-4" /> Upload
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <!-- Verification -->
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between"
                    >
                        <div>
                            <CardTitle>AI verification</CardTitle>
                            <CardDescription
                                >OCR-based document validation against the
                                registry record.</CardDescription
                            >
                        </div>
                        <Button
                            :disabled="verifyForm.processing"
                            @click="runVerification"
                        >
                            <Spinner
                                v-if="verifyForm.processing"
                                class="size-4"
                            />
                            <ScanLine v-else class="size-4" />
                            Run verification
                        </Button>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <VerificationReport
                            v-if="property.latest_verification"
                            :verification="property.latest_verification"
                        />
                        <EmptyState
                            v-else
                            :icon="ScanLine"
                            title="Not yet verified"
                            description="Upload a document and run AI verification to validate this property."
                        />

                        <!-- Human approval gate (only after the automated check passes) -->
                        <div
                            v-if="isAwaitingApproval"
                            class="rounded-lg border border-blue-200 bg-blue-50/50 p-4 dark:border-blue-500/20 dark:bg-blue-500/5"
                        >
                            <div class="flex items-start gap-3">
                                <ClipboardCheck
                                    class="mt-0.5 size-5 shrink-0 text-blue-600 dark:text-blue-400"
                                />
                                <div class="flex-1">
                                    <p class="font-medium">
                                        Automated check passed — awaiting your
                                        approval
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        Review the document, then approve to mark
                                        this property as verified, or reject it.
                                    </p>
                                    <div class="mt-3 flex gap-2">
                                        <Button
                                            size="sm"
                                            :disabled="decisionForm.processing"
                                            @click="approveProperty"
                                        >
                                            <Check class="size-4" /> Approve
                                            &amp; verify
                                        </Button>
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            class="text-destructive hover:text-destructive"
                                            :disabled="decisionForm.processing"
                                            @click="rejectProperty"
                                        >
                                            <X class="size-4" /> Reject
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="
                                property.verifications &&
                                property.verifications.length > 1
                            "
                            class="border-t pt-4"
                        >
                            <h4
                                class="mb-2 text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                History
                            </h4>
                            <ul class="space-y-1 text-sm">
                                <li
                                    v-for="v in property.verifications"
                                    :key="v.id"
                                    class="flex items-center justify-between gap-2"
                                >
                                    <span class="flex items-center gap-2">
                                        <PropertyStatusBadge
                                            :status="v.status"
                                        />
                                        <span class="text-muted-foreground"
                                            >score {{ v.score }}</span
                                        >
                                    </span>
                                    <span class="text-muted-foreground">{{
                                        formatDateTime(v.created_at)
                                    }}</span>
                                </li>
                            </ul>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Sidebar -->
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
                            <span class="text-muted-foreground"
                                >Registered</span
                            >
                            <span>{{ formatDate(property.created_at) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Verified</span>
                            <span>{{ formatDate(property.verified_at) }}</span>
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
                        registry. The sealed block remains in the ledger for
                        audit integrity.
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
