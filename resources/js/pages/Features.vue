<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Building2,
    FileSearch,
    ScanLine,
    ShieldCheck,
    Link2,
    FileLock2,
    Search,
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';

const features = [
    {
        icon: Building2,
        title: 'Property registration',
        description:
            'Capture parcel details, owner identity and location in a clean, validated workflow.',
    },
    {
        icon: FileLock2,
        title: 'Secure document upload',
        description:
            'Ownership documents are stored privately and never exposed to the public.',
    },
    {
        icon: ScanLine,
        title: 'AI-assisted verification',
        description:
            'OCR extracts document text and validates it against the registry record automatically.',
    },
    {
        icon: ShieldCheck,
        title: 'Verified / Suspicious / Rejected',
        description:
            'Every check produces a transparent confidence score and a clear outcome.',
    },
    {
        icon: Link2,
        title: 'Blockchain-inspired hash chain',
        description:
            'Each property is sealed with a SHA-256 hash linked to the previous record.',
    },
    {
        icon: Search,
        title: 'Public verification',
        description:
            'Anyone can confirm a property’s authenticity using its number or hash.',
    },
];
</script>

<template>
    <PublicLayout>
        <Head title="Features - LandChain AI" />

        <section
            class="bg-primary px-6 py-20 text-center text-primary-foreground md:px-12"
        >
            <h1 class="mb-6 text-4xl font-bold md:text-5xl">Features</h1>
            <p class="mx-auto max-w-3xl text-xl text-primary-foreground/90">
                Everything you need to register, verify and trust property
                records — in one platform.
            </p>
        </section>

        <section class="mx-auto max-w-7xl px-6 py-20 md:px-12">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="feature in features"
                    :key="feature.title"
                    class="rounded-2xl border border-border bg-card p-8 shadow-sm"
                >
                    <div
                        class="mb-6 flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary"
                    >
                        <component :is="feature.icon" class="h-6 w-6" />
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-foreground">
                        {{ feature.title }}
                    </h3>
                    <p class="leading-relaxed text-muted-foreground">
                        {{ feature.description }}
                    </p>
                </div>
            </div>
        </section>

        <section class="border-t border-border px-6 py-16 text-center md:px-12">
            <div
                class="mx-auto flex max-w-3xl flex-col items-center gap-4 rounded-2xl bg-primary/5 p-10"
            >
                <FileSearch class="size-10 text-primary" />
                <h2 class="text-2xl font-bold">Verify a property now</h2>
                <p class="max-w-xl text-muted-foreground">
                    Check the authenticity of any registered property using its
                    registration number or blockchain hash.
                </p>
                <Button size="lg" as-child
                    ><Link href="/verify-property"
                        >Verify a property</Link
                    ></Button
                >
            </div>
        </section>
    </PublicLayout>
</template>
