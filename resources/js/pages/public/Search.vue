<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowRight, Building2, ScanLine, Search, ShieldCheck, Upload } from '@lucide/vue';
import { reactive } from 'vue';
import EmptyState from '@/components/EmptyState.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { index, scan, show, verifyScan } from '@/routes/verify-property';
import type { PublicPropertySummary } from '@/types';

type Extracted = {
    owner_name: string | null;
    owner_cnic: string | null;
    property_number: string | null;
    source: string;
};

const props = defineProps<{
    filters: {
        owner_cnic: string | null;
        owner_name: string | null;
        property_number: string | null;
        city: string | null;
        province: string | null;
    };
    hasSearched: boolean;
    results: PublicPropertySummary[];
    extracted: Extracted | null;
    scanned: boolean;
}>();

// Upload-driven scan
const scanForm = useForm<{ document: File | null }>({ document: null });

function onScanFile(event: Event): void {
    const target = event.target as HTMLInputElement;
    scanForm.document = target.files?.[0] ?? null;
}

function submitScan(): void {
    scanForm.post(scan().url, { forceFormData: true });
}

function verifyMatch(id: number): void {
    router.post(verifyScan(id).url);
}

// Manual fallback search
const form = reactive({
    owner_cnic: props.filters.owner_cnic ?? '',
    owner_name: props.filters.owner_name ?? '',
    property_number: props.filters.property_number ?? '',
    city: props.filters.city ?? '',
    province: props.filters.province ?? '',
});

function submitManual(): void {
    router.get(index().url, { ...form }, { preserveState: true, preserveScroll: true });
}
</script>

<template>
    <PublicLayout>
        <Head title="Verify a property - LandChain AI" />

        <section class="bg-primary px-6 py-16 text-center text-primary-foreground md:px-12">
            <h1 class="mb-3 text-3xl font-bold md:text-4xl">Verify property ownership</h1>
            <p class="mx-auto max-w-2xl text-primary-foreground/90">
                Upload your ownership document — we read the details and find your record automatically.
            </p>
        </section>

        <section class="mx-auto w-full max-w-5xl px-6 py-12 md:px-12">
            <!-- Primary: upload document -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2"><ScanLine class="size-5 text-primary" /> Upload your document</CardTitle>
                    <CardDescription>We extract the owner name, CNIC and plot number (typed or handwritten) and match it to the registry.</CardDescription>
                </CardHeader>
                <CardContent>
                    <form class="flex flex-col gap-3 sm:flex-row sm:items-end" @submit.prevent="submitScan">
                        <div class="grid flex-1 gap-2">
                            <Label for="scan-doc">Ownership document (JPG, PNG or PDF)</Label>
                            <input
                                id="scan-doc"
                                type="file"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="w-full rounded-md border border-input bg-transparent text-sm text-muted-foreground file:mr-3 file:cursor-pointer file:border-0 file:bg-muted file:px-3 file:py-2 file:text-sm file:font-medium file:text-foreground hover:file:bg-muted/80"
                                @input="onScanFile"
                            />
                            <InputError :message="scanForm.errors.document" />
                        </div>
                        <Button type="submit" :disabled="scanForm.processing || !scanForm.document">
                            <Upload class="size-4" /> {{ scanForm.processing ? 'Reading…' : 'Find my property' }}
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <!-- Detected fields -->
            <Card v-if="scanned && extracted" class="mt-6 border-violet-200 dark:border-violet-500/20">
                <CardContent class="p-5">
                    <p class="mb-2 text-sm font-semibold">
                        Detected from your document
                        <span class="ml-1 text-xs font-normal text-muted-foreground">({{ extracted.source === 'ai' ? 'AI read' : 'OCR read' }})</span>
                    </p>
                    <div class="flex flex-wrap gap-x-8 gap-y-1 text-sm">
                        <span>Owner: <span class="font-medium">{{ extracted.owner_name ?? '—' }}</span></span>
                        <span>CNIC: <span class="font-mono font-medium">{{ extracted.owner_cnic ?? '—' }}</span></span>
                        <span>Plot no.: <span class="font-mono font-medium">{{ extracted.property_number ?? '—' }}</span></span>
                    </div>
                </CardContent>
            </Card>

            <!-- Results -->
            <div v-if="hasSearched" class="mt-6">
                <p class="mb-3 text-sm text-muted-foreground">{{ results.length }} matching propert{{ results.length === 1 ? 'y' : 'ies' }}</p>

                <EmptyState
                    v-if="results.length === 0"
                    :icon="Building2"
                    title="No approved property matched"
                    description="We couldn't match an approved record. Try a clearer image, or search manually below."
                />

                <div v-else class="grid gap-3 sm:grid-cols-2">
                    <div v-for="p in results" :key="p.id" class="rounded-xl border bg-card p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-semibold">{{ p.title }}</p>
                                <p class="font-mono text-xs text-muted-foreground">{{ p.property_number }}</p>
                            </div>
                        </div>
                        <dl class="mt-3 grid grid-cols-2 gap-2 text-sm">
                            <div><dt class="text-xs text-muted-foreground">Owner</dt><dd>{{ p.owner_name }}</dd></div>
                            <div><dt class="text-xs text-muted-foreground">CNIC</dt><dd class="font-mono">{{ p.owner_cnic }}</dd></div>
                            <div><dt class="text-xs text-muted-foreground">Location</dt><dd>{{ p.city }}{{ p.province ? `, ${p.province}` : '' }}</dd></div>
                        </dl>
                        <div class="mt-3">
                            <Button v-if="scanned" size="sm" class="w-full" @click="verifyMatch(p.id)">
                                <ShieldCheck class="size-4" /> Verify ownership with my document
                            </Button>
                            <Button v-else size="sm" variant="outline" class="w-full" as-child>
                                <Link :href="show(p.id).url">Open <ArrowRight class="size-4" /></Link>
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manual fallback -->
            <Card class="mt-8">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base"><Search class="size-4" /> Or search manually</CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" @submit.prevent="submitManual">
                        <div class="grid gap-2">
                            <Label for="owner_cnic">Owner CNIC</Label>
                            <Input id="owner_cnic" v-model="form.owner_cnic" placeholder="e.g. 35201-1234567-8" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="owner_name">Owner name</Label>
                            <Input id="owner_name" v-model="form.owner_name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="property_number">Plot / property number</Label>
                            <Input id="property_number" v-model="form.property_number" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="city">City</Label>
                            <Input id="city" v-model="form.city" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="province">Province</Label>
                            <Input id="province" v-model="form.province" />
                        </div>
                        <div class="flex items-end">
                            <Button type="submit" variant="outline" class="w-full sm:w-auto"><Search class="size-4" /> Search</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </section>
    </PublicLayout>
</template>
