<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ScanSearch, ShieldAlert, ShieldCheck, Upload } from '@lucide/vue';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import LoadingOverlay from '@/components/LoadingOverlay.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { check } from '@/routes/fraud-check';

type Extracted = {
    owner_name: string | null;
    owner_cnic: string | null;
    property_number: string | null;
    source: string;
    fraud_hint: string | null;
    confidence: string | null;
};

type Fraud = {
    fraud_hint: string | null;
    confidence: string | null;
};

type Match = {
    id: number;
    property_number: string;
    title: string;
    owner_name: string;
    city: string;
    province: string | null;
};

const props = defineProps<{
    checked: boolean;
    extracted: Extracted | null;
    fraud: Fraud | null;
    matches: Match[];
}>();

const form = useForm<{ document: File | null }>({ document: null });

function onFile(event: Event): void {
    const target = event.target as HTMLInputElement;
    form.document = target.files?.[0] ?? null;
}

function submit(): void {
    form.post(check().url, { forceFormData: true });
}

const genuine = computed(() => props.fraud?.fraud_hint === 'Document appears genuine');
const hasVerdict = computed(() => !!props.fraud?.fraud_hint);
</script>

<template>
    <PublicLayout>
        <Head title="Document fraud check - LandChain AI" />

        <LoadingOverlay
            :show="form.processing"
            message="Analyzing your document for fraud indicators — this can take a few seconds…"
        />

        <section class="bg-primary px-6 py-16 text-center text-primary-foreground md:px-12">
            <h1 class="mb-3 text-3xl font-bold md:text-4xl">Document fraud check</h1>
            <p class="mx-auto max-w-2xl text-primary-foreground/90">
                Upload a property document — our AI inspects it for tampering signs and cross-checks it against the official registry.
            </p>
        </section>

        <section class="mx-auto w-full max-w-3xl px-6 py-12 md:px-12">
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2"><ScanSearch class="size-5 text-primary" /> Upload document to check</CardTitle>
                    <CardDescription>
                        We look for blurring, inconsistent fonts, cut-paste signs and tampered text, and verify the details exist in the registry.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form class="flex flex-col gap-3 sm:flex-row sm:items-end" @submit.prevent="submit">
                        <div class="grid flex-1 gap-2">
                            <Label for="fraud-doc">Property document (JPG, PNG or PDF)</Label>
                            <input
                                id="fraud-doc"
                                type="file"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="w-full rounded-md border border-input bg-transparent text-sm text-muted-foreground file:mr-3 file:cursor-pointer file:border-0 file:bg-muted file:px-3 file:py-2 file:text-sm file:font-medium file:text-foreground hover:file:bg-muted/80"
                                @input="onFile"
                            />
                            <InputError :message="form.errors.document" />
                        </div>
                        <Button type="submit" :disabled="form.processing || !form.document">
                            <Spinner v-if="form.processing" class="size-4" />
                            <Upload v-else class="size-4" />
                            {{ form.processing ? 'Analyzing…' : 'Check for fraud' }}
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <!-- Verdict -->
            <template v-if="checked">
                <Card
                    v-if="hasVerdict"
                    class="mt-6"
                    :class="genuine ? 'border-emerald-300 dark:border-emerald-500/30' : 'border-red-300 dark:border-red-500/30'"
                >
                    <CardContent class="flex items-start gap-4 p-6">
                        <ShieldCheck v-if="genuine" class="mt-1 size-8 shrink-0 text-emerald-500" />
                        <ShieldAlert v-else class="mt-1 size-8 shrink-0 text-red-500" />
                        <div>
                            <p class="text-lg font-bold" :class="genuine ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400'">
                                {{ fraud?.fraud_hint }}
                            </p>
                            <p v-if="fraud?.confidence" class="text-sm text-muted-foreground">
                                Confidence: <span class="font-semibold">{{ fraud.confidence }}</span>
                            </p>
                            <p class="mt-2 text-sm text-muted-foreground">
                                {{
                                    genuine
                                        ? 'No obvious tampering indicators were found and the document details match a registered property.'
                                        : matches.length === 0
                                          ? 'The document details do not match any approved property in the registry.'
                                          : 'The document matched a registry record, but the image shows possible tampering indicators.'
                                }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card v-else class="mt-6 border-amber-300 dark:border-amber-500/30">
                    <CardContent class="p-6 text-sm text-muted-foreground">
                        A registry match was found, but AI image analysis was unavailable — the document image could not be checked for tampering.
                    </CardContent>
                </Card>

                <!-- What we read -->
                <Card v-if="extracted" class="mt-6">
                    <CardContent class="p-5">
                        <p class="mb-2 text-sm font-semibold">
                            Read from your document
                            <span class="ml-1 text-xs font-normal text-muted-foreground">({{ extracted.source === 'ai' ? 'AI read' : 'OCR read' }})</span>
                        </p>
                        <div class="flex flex-wrap gap-x-8 gap-y-1 text-sm">
                            <span>Owner: <span class="font-medium">{{ extracted.owner_name ?? '—' }}</span></span>
                            <span>CNIC: <span class="font-mono font-medium">{{ extracted.owner_cnic ?? '—' }}</span></span>
                            <span>Plot no.: <span class="font-mono font-medium">{{ extracted.property_number ?? '—' }}</span></span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Registry matches -->
                <div v-if="matches.length > 0" class="mt-6">
                    <p class="mb-3 text-sm text-muted-foreground">Matched {{ matches.length }} registered propert{{ matches.length === 1 ? 'y' : 'ies' }}</p>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div v-for="m in matches" :key="m.id" class="rounded-xl border bg-card p-4">
                            <p class="truncate font-semibold">{{ m.title }}</p>
                            <p class="font-mono text-xs text-muted-foreground">{{ m.property_number }}</p>
                            <p class="mt-2 text-sm">{{ m.owner_name }} · {{ m.city }}{{ m.province ? `, ${m.province}` : '' }}</p>
                        </div>
                    </div>
                </div>
            </template>
        </section>
    </PublicLayout>
</template>
