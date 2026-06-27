<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    CheckCircle2,
    ShieldAlert,
    ShieldCheck,
    Sparkles,
    Upload,
    XCircle,
} from '@lucide/vue';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import LoadingOverlay from '@/components/LoadingOverlay.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { index, verify } from '@/routes/verify-property';
import type { PublicProperty, VerificationResult } from '@/types';

const props = defineProps<{
    property: PublicProperty;
    result: VerificationResult | null;
}>();

const form = useForm<{ document: File | null }>({ document: null });

function onFile(event: Event): void {
    const target = event.target as HTMLInputElement;
    form.document = target.files?.[0] ?? null;
}

function submit(): void {
    form.post(verify(props.property.id).url, { forceFormData: true });
}

const isVerified = computed(
    () => props.result?.final_status.value === 'verified',
);
const aiConfidence = computed(() =>
    props.result?.ai?.confidence != null
        ? Math.round(props.result.ai.confidence * 100)
        : null,
);

const details = computed(() => [
    {
        label: 'Plot / property number',
        value: props.property.property_number,
        mono: true,
    },
    { label: 'Type', value: props.property.type },
    { label: 'Owner', value: props.property.owner_name },
    { label: 'Owner CNIC', value: props.property.owner_cnic, mono: true },
    { label: 'Area', value: props.property.area_label },
    {
        label: 'Location',
        value: `${props.property.city}${props.property.province ? ', ' + props.property.province : ''}`,
    },
    { label: 'Address', value: props.property.address },
]);
</script>

<template>
    <PublicLayout>
        <Head :title="`Verify ${property.property_number}`" />

        <LoadingOverlay :show="form.processing" message="Verifying your document — this can take a few seconds…" />

        <section class="mx-auto w-full max-w-3xl px-6 py-10 md:px-12">
            <Button
                variant="ghost"
                size="sm"
                class="mb-3 -ml-2 text-muted-foreground"
                as-child
            >
                <Link :href="index().url"
                    ><ArrowLeft class="size-4" /> Back to search</Link
                >
            </Button>

            <Card>
                <CardHeader
                    ><CardTitle>{{ property.title }}</CardTitle></CardHeader
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
                </CardContent>
            </Card>

            <!-- Upload -->
            <Card class="mt-6">
                <CardHeader>
                    <CardTitle>Upload your ownership document</CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="flex flex-col gap-4" @submit.prevent="submit">
                        <div class="grid gap-2">
                            <Label for="document">Document image or PDF</Label>
                            <input
                                id="document"
                                type="file"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="w-full rounded-md border border-input bg-transparent text-sm text-muted-foreground file:mr-3 file:cursor-pointer file:border-0 file:bg-muted file:px-3 file:py-2 file:text-sm file:font-medium file:text-foreground hover:file:bg-muted/80"
                                @input="onFile"
                            />
                            <InputError :message="form.errors.document" />
                            <p class="text-xs text-muted-foreground">
                                An image scan that shows the owner name, CNIC
                                and plot number gives the best result.
                            </p>
                        </div>
                        <div>
                            <Button
                                type="submit"
                                :disabled="form.processing || !form.document"
                            >
                                <Spinner v-if="form.processing" class="size-4" />
                                <Upload v-else class="size-4" />
                                {{
                                    form.processing
                                        ? 'Verifying…'
                                        : 'Verify ownership'
                                }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <!-- Result -->
            <Card
                v-if="result"
                class="mt-6"
                :class="
                    isVerified
                        ? 'border-emerald-300 dark:border-emerald-500/30'
                        : 'border-red-300 dark:border-red-500/30'
                "
            >
                <CardContent class="space-y-5 p-6">
                    <div class="flex items-center gap-3">
                        <ShieldCheck
                            v-if="isVerified"
                            class="size-8 shrink-0 text-emerald-500"
                        />
                        <ShieldAlert
                            v-else
                            class="size-8 shrink-0 text-red-500"
                        />
                        <div>
                            <p
                                class="text-xl font-bold"
                                :class="
                                    isVerified
                                        ? 'text-emerald-600 dark:text-emerald-400'
                                        : 'text-red-600 dark:text-red-400'
                                "
                            >
                                {{ result.final_status.label }}
                            </p>
                            <p class="text-sm text-muted-foreground">
                                {{
                                    isVerified
                                        ? 'Your document matches the official registry record.'
                                        : 'This document was not accepted — it does not match the official record, or it is not a valid ownership document. See the checks below.'
                                }}
                            </p>
                        </div>
                    </div>

                    <ul class="divide-y rounded-lg border">
                        <li
                            v-for="check in result.checks"
                            :key="check.key"
                            class="flex items-start gap-3 p-3"
                        >
                            <CheckCircle2
                                v-if="check.passed"
                                class="mt-0.5 size-5 shrink-0 text-emerald-500"
                            />
                            <XCircle
                                v-else
                                class="mt-0.5 size-5 shrink-0 text-muted-foreground/60"
                            />
                            <div>
                                <p class="text-sm font-medium">
                                    {{ check.label }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    {{ check.message }}
                                </p>
                            </div>
                        </li>
                    </ul>

                    <div
                        v-if="result.ai"
                        class="rounded-lg border border-violet-200 bg-violet-50/50 p-4 dark:border-violet-500/20 dark:bg-violet-500/5"
                    >
                        <div class="flex items-center gap-2">
                            <Sparkles
                                class="size-4 text-violet-600 dark:text-violet-400"
                            />
                            <p class="text-sm font-semibold">
                                AI document check
                            </p>
                        </div>
                        <div
                            class="mt-2 flex flex-wrap items-center gap-4 text-sm"
                        >
                            <span class="inline-flex items-center gap-1.5">
                                <CheckCircle2
                                    v-if="result.ai.match"
                                    class="size-4 text-emerald-500"
                                />
                                <XCircle v-else class="size-4 text-red-500" />
                                {{
                                    result.ai.match
                                        ? 'Looks authentic & matching'
                                        : 'Mismatch / not authentic'
                                }}
                            </span>
                            <span
                                v-if="aiConfidence !== null"
                                class="text-muted-foreground"
                                >Confidence:
                                <span class="font-medium text-foreground"
                                    >{{ aiConfidence }}%</span
                                ></span
                            >
                        </div>
                        <p
                            v-if="result.ai.notes"
                            class="mt-2 text-sm text-muted-foreground"
                        >
                            {{ result.ai.notes }}
                        </p>
                        <ul
                            v-if="result.ai.issues.length"
                            class="mt-2 list-inside list-disc text-sm text-red-600 dark:text-red-400"
                        >
                            <li v-for="(issue, i) in result.ai.issues" :key="i">
                                {{ issue }}
                            </li>
                        </ul>
                    </div>
                </CardContent>
            </Card>
        </section>
    </PublicLayout>
</template>
