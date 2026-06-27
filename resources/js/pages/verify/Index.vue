<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    CheckCircle2,
    Search,
    ShieldCheck,
    ShieldAlert,
    XCircle,
} from '@lucide/vue';
import { ref } from 'vue';
import CopyableHash from '@/components/CopyableHash.vue';
import PropertyStatusBadge from '@/components/PropertyStatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { formatDate } from '@/lib/format';
import { verify } from '@/routes';
import type { StatusBadge } from '@/types';

type VerifyResult = {
    found: boolean;
    property?: {
        property_number: string;
        title: string;
        type: string;
        owner_name: string;
        owner_cnic: string;
        city: string;
        province: string | null;
        area_label: string;
        status: StatusBadge;
        verified_at: string | null;
        registered_at: string | null;
    };
    block?: {
        sequence: number;
        hash: string;
        previous_hash: string;
        created_at: string | null;
    } | null;
    block_valid?: boolean | null;
    chain_intact?: boolean;
};

const props = defineProps<{ query: string; result: VerifyResult | null }>();

const term = ref(props.query ?? '');

function submit(): void {
    router.get(
        verify().url,
        { query: term.value },
        { preserveState: true, preserveScroll: true },
    );
}
</script>

<template>
    <PublicLayout>
        <Head title="Verify a property - LandChain AI" />

        <section
            class="bg-primary px-6 py-16 text-center text-primary-foreground md:px-12"
        >
            <h1 class="mb-3 text-3xl font-bold md:text-4xl">
                Verify a property
            </h1>
            <p class="mx-auto max-w-2xl text-primary-foreground/90">
                Confirm a property's authenticity using its registration number
                or blockchain hash.
            </p>
        </section>

        <section class="mx-auto w-full max-w-3xl px-6 py-12 md:px-12">
            <form
                class="flex flex-col gap-3 sm:flex-row"
                @submit.prevent="submit"
            >
                <div class="relative flex-1">
                    <Search
                        class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="term"
                        placeholder="Enter property number or hash…"
                        class="pl-9"
                    />
                </div>
                <Button type="submit" size="lg">Verify</Button>
            </form>

            <!-- Not found -->
            <Card
                v-if="result && !result.found"
                class="mt-8 border-amber-200 dark:border-amber-500/30"
            >
                <CardContent class="flex items-start gap-3 p-5">
                    <XCircle class="mt-0.5 size-5 shrink-0 text-amber-500" />
                    <div>
                        <p class="font-semibold">No matching property found</p>
                        <p class="text-sm text-muted-foreground">
                            We couldn't find a property for “{{ query }}”. Check
                            the number or hash and try again.
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Found -->
            <div
                v-else-if="result && result.found && result.property"
                class="mt-8 space-y-6"
            >
                <Card>
                    <CardContent class="p-6">
                        <div
                            class="flex flex-wrap items-start justify-between gap-3"
                        >
                            <div>
                                <h2 class="text-xl font-bold">
                                    {{ result.property.title }}
                                </h2>
                                <p
                                    class="font-mono text-sm text-muted-foreground"
                                >
                                    {{ result.property.property_number }}
                                </p>
                            </div>
                            <PropertyStatusBadge
                                :status="result.property.status"
                            />
                        </div>

                        <dl class="mt-6 grid gap-x-6 gap-y-4 sm:grid-cols-2">
                            <div>
                                <dt
                                    class="text-xs tracking-wide text-muted-foreground uppercase"
                                >
                                    Owner
                                </dt>
                                <dd class="text-sm">
                                    {{ result.property.owner_name }}
                                </dd>
                            </div>
                            <div>
                                <dt
                                    class="text-xs tracking-wide text-muted-foreground uppercase"
                                >
                                    Owner CNIC
                                </dt>
                                <dd class="font-mono text-sm">
                                    {{ result.property.owner_cnic }}
                                </dd>
                            </div>
                            <div>
                                <dt
                                    class="text-xs tracking-wide text-muted-foreground uppercase"
                                >
                                    Type
                                </dt>
                                <dd class="text-sm">
                                    {{ result.property.type }}
                                </dd>
                            </div>
                            <div>
                                <dt
                                    class="text-xs tracking-wide text-muted-foreground uppercase"
                                >
                                    Area
                                </dt>
                                <dd class="text-sm">
                                    {{ result.property.area_label }}
                                </dd>
                            </div>
                            <div>
                                <dt
                                    class="text-xs tracking-wide text-muted-foreground uppercase"
                                >
                                    Location
                                </dt>
                                <dd class="text-sm">
                                    {{ result.property.city
                                    }}{{
                                        result.property.province
                                            ? `, ${result.property.province}`
                                            : ''
                                    }}
                                </dd>
                            </div>
                            <div>
                                <dt
                                    class="text-xs tracking-wide text-muted-foreground uppercase"
                                >
                                    Registered
                                </dt>
                                <dd class="text-sm">
                                    {{
                                        formatDate(
                                            result.property.registered_at,
                                        )
                                    }}
                                </dd>
                            </div>
                        </dl>
                    </CardContent>
                </Card>

                <Card v-if="result.block">
                    <CardContent class="space-y-4 p-6">
                        <div class="flex items-center gap-3">
                            <ShieldCheck
                                v-if="result.block_valid && result.chain_intact"
                                class="size-6 text-emerald-500"
                            />
                            <ShieldAlert v-else class="size-6 text-red-500" />
                            <div>
                                <p class="font-semibold">
                                    {{
                                        result.block_valid &&
                                        result.chain_intact
                                            ? 'Authenticity confirmed'
                                            : 'Integrity warning'
                                    }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    {{
                                        result.block_valid &&
                                        result.chain_intact
                                            ? `Sealed as block #${result.block.sequence} in an intact chain.`
                                            : 'This record could not be fully verified against the chain.'
                                    }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <p
                                    class="mb-1 text-xs tracking-wide text-muted-foreground uppercase"
                                >
                                    Block hash (SHA-256)
                                </p>
                                <CopyableHash :value="result.block.hash" />
                            </div>
                            <div>
                                <p
                                    class="mb-1 text-xs tracking-wide text-muted-foreground uppercase"
                                >
                                    Previous block hash
                                </p>
                                <CopyableHash
                                    :value="result.block.previous_hash"
                                />
                            </div>
                        </div>

                        <div
                            class="flex items-center gap-2 border-t pt-3 text-sm text-muted-foreground"
                        >
                            <CheckCircle2
                                v-if="result.chain_intact"
                                class="size-4 text-emerald-500"
                            />
                            <XCircle v-else class="size-4 text-red-500" />
                            Ledger
                            {{ result.chain_intact ? 'intact' : 'compromised' }}
                        </div>
                    </CardContent>
                </Card>
            </div>
        </section>
    </PublicLayout>
</template>
