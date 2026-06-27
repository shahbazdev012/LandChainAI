<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
import { index, show, update } from '@/routes/properties';
import type { EnumOption, Property } from '@/types';

const props = defineProps<{
    property: Property;
    typeOptions: EnumOption[];
    areaUnitOptions: EnumOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Properties', href: index() },
            { title: 'Edit', href: '' },
        ],
    },
});

const form = useForm({
    property_number: props.property.property_number,
    title: props.property.title,
    type: props.property.type.value,
    description: props.property.description ?? '',
    owner_name: props.property.owner_name,
    owner_cnic: props.property.owner_cnic,
    owner_contact: props.property.owner_contact ?? '',
    address: props.property.address,
    city: props.property.city,
    province: props.property.province,
    area_value: props.property.area_value,
    area_unit: props.property.area_unit.value,
});

function submit(): void {
    form.put(update(props.property.id).url);
}
</script>

<template>
    <Head :title="`Edit ${property.property_number}`" />

    <form
        class="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4"
        @submit.prevent="submit"
    >
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Edit property</h1>
            <p class="text-muted-foreground">
                Update the registry record for {{ property.property_number }}.
            </p>
        </div>

        <Card>
            <CardHeader><CardTitle>Property details</CardTitle></CardHeader>
            <CardContent class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="property_number"
                        >Property / parcel number</Label
                    >
                    <Input
                        id="property_number"
                        v-model="form.property_number"
                    />
                    <InputError :message="form.errors.property_number" />
                </div>
                <div class="grid gap-2">
                    <Label for="title">Title</Label>
                    <Input id="title" v-model="form.title" />
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
                    <Label for="description">Description</Label>
                    <Textarea id="description" v-model="form.description" />
                    <InputError :message="form.errors.description" />
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader><CardTitle>Owner details</CardTitle></CardHeader>
            <CardContent class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="owner_name">Owner name</Label>
                    <Input id="owner_name" v-model="form.owner_name" />
                    <InputError :message="form.errors.owner_name" />
                </div>
                <div class="grid gap-2">
                    <Label for="owner_cnic">Owner CNIC</Label>
                    <Input id="owner_cnic" v-model="form.owner_cnic" />
                    <InputError :message="form.errors.owner_cnic" />
                </div>
                <div class="grid gap-2">
                    <Label for="owner_contact">Contact</Label>
                    <Input id="owner_contact" v-model="form.owner_contact" />
                    <InputError :message="form.errors.owner_contact" />
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader><CardTitle>Location &amp; area</CardTitle></CardHeader>
            <CardContent class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2 sm:col-span-2">
                    <Label for="address">Address</Label>
                    <Input id="address" v-model="form.address" />
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
                ><Link :href="show(property.id)">Cancel</Link></Button
            >
            <Button type="submit" :disabled="form.processing"
                >Save changes</Button
            >
        </div>
    </form>
</template>
