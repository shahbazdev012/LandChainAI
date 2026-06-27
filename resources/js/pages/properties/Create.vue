<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Plus, Trash2, FileText } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { dashboard } from '@/routes';
import { index, store } from '@/routes/properties';
import type { EnumOption } from '@/types';

const props = defineProps<{
    typeOptions: EnumOption[];
    areaUnitOptions: EnumOption[];
    documentTypeOptions: EnumOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Properties', href: index() },
            { title: 'Register', href: '' },
        ],
    },
});

const form = useForm<{
    property_number: string;
    title: string;
    type: string;
    description: string;
    owner_name: string;
    owner_cnic: string;
    owner_contact: string;
    address: string;
    city: string;
    province: string;
    area_value: string;
    area_unit: string;
    documents: { type: string; file: File | null }[];
}>({
    property_number: '',
    title: '',
    type: props.typeOptions[0]?.value ?? '',
    description: '',
    owner_name: '',
    owner_cnic: '',
    owner_contact: '',
    address: '',
    city: '',
    province: '',
    area_value: '',
    area_unit: props.areaUnitOptions[0]?.value ?? '',
    documents: [],
});

function addDocument(): void {
    form.documents.push({
        type: props.documentTypeOptions[0]?.value ?? '',
        file: null,
    });
}

function removeDocument(index: number): void {
    form.documents.splice(index, 1);
}

function onFile(event: Event, index: number): void {
    const target = event.target as HTMLInputElement;
    form.documents[index].file = target.files?.[0] ?? null;
}

function submit(): void {
    form.post(store().url, { forceFormData: true });
}
</script>

<template>
    <Head title="Register property" />

    <form
        class="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4"
        @submit.prevent="submit"
    >
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Register property</h1>
            <p class="text-muted-foreground">
                Record a property and seal it into the verification chain.
            </p>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Property details</CardTitle>
                <CardDescription
                    >The official parcel number must match the uploaded
                    documents.</CardDescription
                >
            </CardHeader>
            <CardContent class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="property_number"
                        >Property / parcel number</Label
                    >
                    <Input
                        id="property_number"
                        v-model="form.property_number"
                        placeholder="e.g. LHR-1234-56789"
                    />
                    <InputError :message="form.errors.property_number" />
                </div>
                <div class="grid gap-2">
                    <Label for="title">Title</Label>
                    <Input
                        id="title"
                        v-model="form.title"
                        placeholder="e.g. Residential Plot 12-A"
                    />
                    <InputError :message="form.errors.title" />
                </div>
                <div class="grid gap-2">
                    <Label for="type">Type</Label>
                    <Select v-model="form.type">
                        <SelectTrigger id="type"><SelectValue /></SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="o in typeOptions"
                                :key="o.value"
                                :value="o.value"
                                >{{ o.label }}</SelectItem
                            >
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.type" />
                </div>
                <div class="grid gap-2 sm:col-span-2">
                    <Label for="description"
                        >Description
                        <span class="text-muted-foreground"
                            >(optional)</span
                        ></Label
                    >
                    <Textarea
                        id="description"
                        v-model="form.description"
                        placeholder="Any additional details…"
                    />
                    <InputError :message="form.errors.description" />
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader><CardTitle>Owner details</CardTitle></CardHeader>
            <CardContent class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="owner_name">Owner name</Label>
                    <Input
                        id="owner_name"
                        v-model="form.owner_name"
                        placeholder="Full legal name"
                    />
                    <InputError :message="form.errors.owner_name" />
                </div>
                <div class="grid gap-2">
                    <Label for="owner_cnic">Owner CNIC</Label>
                    <Input
                        id="owner_cnic"
                        v-model="form.owner_cnic"
                        placeholder="e.g. 35201-1234567-8"
                    />
                    <InputError :message="form.errors.owner_cnic" />
                </div>
                <div class="grid gap-2">
                    <Label for="owner_contact"
                        >Contact
                        <span class="text-muted-foreground"
                            >(optional)</span
                        ></Label
                    >
                    <Input
                        id="owner_contact"
                        v-model="form.owner_contact"
                        placeholder="Phone or email"
                    />
                    <InputError :message="form.errors.owner_contact" />
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader><CardTitle>Location &amp; area</CardTitle></CardHeader>
            <CardContent class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2 sm:col-span-2">
                    <Label for="address">Address</Label>
                    <Input
                        id="address"
                        v-model="form.address"
                        placeholder="Street address"
                    />
                    <InputError :message="form.errors.address" />
                </div>
                <div class="grid gap-2">
                    <Label for="city">City</Label>
                    <Input id="city" v-model="form.city" />
                    <InputError :message="form.errors.city" />
                </div>
                <div class="grid gap-2">
                    <Label for="province">Province</Label>
                    <Input id="province" v-model="form.province" />
                    <InputError :message="form.errors.province" />
                </div>
                <div class="grid gap-2">
                    <Label for="area_value">Area</Label>
                    <Input
                        id="area_value"
                        v-model="form.area_value"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="e.g. 10"
                    />
                    <InputError :message="form.errors.area_value" />
                </div>
                <div class="grid gap-2">
                    <Label for="area_unit">Unit</Label>
                    <Select v-model="form.area_unit">
                        <SelectTrigger id="area_unit"
                            ><SelectValue
                        /></SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="o in areaUnitOptions"
                                :key="o.value"
                                :value="o.value"
                                >{{ o.label }}</SelectItem
                            >
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.area_unit" />
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle
                    >Documents
                    <span class="text-muted-foreground"
                        >(optional)</span
                    ></CardTitle
                >
                <CardDescription
                    >Upload ownership documents. Image scans (JPG/PNG) give the
                    best AI verification results.</CardDescription
                >
            </CardHeader>
            <CardContent class="space-y-3">
                <div
                    v-for="(doc, i) in form.documents"
                    :key="i"
                    class="flex flex-col gap-3 rounded-lg border p-3 sm:flex-row sm:items-end"
                >
                    <div class="grid gap-2 sm:w-48">
                        <Label :for="`doc-type-${i}`">Document type</Label>
                        <Select v-model="doc.type">
                            <SelectTrigger :id="`doc-type-${i}`"
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
                        <Label :for="`doc-file-${i}`">File</Label>
                        <input
                            :id="`doc-file-${i}`"
                            type="file"
                            accept=".jpg,.jpeg,.png,.pdf"
                            class="w-full rounded-md border border-input bg-transparent text-sm text-muted-foreground file:mr-3 file:cursor-pointer file:border-0 file:bg-muted file:px-3 file:py-2 file:text-sm file:font-medium file:text-foreground hover:file:bg-muted/80"
                            @input="onFile($event, i)"
                        />
                        <InputError
                            :message="
                                (form.errors as Record<string, string>)[
                                    `documents.${i}.file`
                                ]
                            "
                        />
                    </div>
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        class="text-muted-foreground"
                        @click="removeDocument(i)"
                    >
                        <Trash2 class="size-4" />
                    </Button>
                </div>

                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addDocument"
                >
                    <Plus class="size-4" /> Add document
                </Button>
                <p
                    v-if="form.documents.length === 0"
                    class="flex items-center gap-2 text-sm text-muted-foreground"
                >
                    <FileText class="size-4" /> No documents added yet.
                </p>
            </CardContent>
        </Card>

        <div class="flex items-center justify-end gap-3">
            <Button type="button" variant="ghost" as-child
                ><Link :href="index()">Cancel</Link></Button
            >
            <Button type="submit" :disabled="form.processing"
                >Register property</Button
            >
        </div>
    </form>
</template>
