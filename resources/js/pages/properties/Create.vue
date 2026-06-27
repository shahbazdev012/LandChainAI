<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
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
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Properties', href: index() },
            { title: 'New property', href: '' },
        ],
    },
});

const form = useForm({
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
});

function submit(): void {
    form.post(store().url);
}
</script>

<template>
    <Head title="New property" />

    <form
        class="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4"
        @submit.prevent="submit"
    >
        <div>
            <h1 class="text-2xl font-bold tracking-tight">
                New property record
            </h1>
            <p class="text-muted-foreground">
                Enter the ground-truth data. It will be sent to an Officer for
                approval.
            </p>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Property details</CardTitle>
                <CardDescription
                    >The plot/parcel number must be unique in the
                    registry.</CardDescription
                >
            </CardHeader>
            <CardContent class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="property_number">Plot / property number</Label>
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

        <div class="flex items-center justify-end gap-3">
            <Button type="button" variant="ghost" as-child
                ><Link :href="index()">Cancel</Link></Button
            >
            <Button type="submit" :disabled="form.processing"
                >Create &amp; submit for approval</Button
            >
        </div>
    </form>
</template>
