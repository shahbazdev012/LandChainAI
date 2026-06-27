<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Target, Shield, Zap } from '@lucide/vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <PublicLayout>
        <Head title="About Us - LandChain AI" />

        <!-- Header Section -->
        <section
            class="bg-primary px-6 py-20 text-center text-primary-foreground md:px-12"
        >
            <h1 class="mb-6 text-4xl font-bold md:text-5xl">
                About LandChain AI
            </h1>
            <p class="mx-auto max-w-3xl text-xl text-primary-foreground/90">
                Pioneering a secure, transparent, and intelligent era for
                property registration worldwide.
            </p>
        </section>

        <!-- The Problem & Solution -->
        <section class="mx-auto max-w-7xl px-6 py-20 md:px-12">
            <div class="grid grid-cols-1 items-center gap-16 md:grid-cols-2">
                <div>
                    <div
                        class="mb-6 flex h-12 w-12 items-center justify-center rounded-xl bg-destructive/10 text-destructive"
                    >
                        <Zap class="h-6 w-6" />
                    </div>
                    <h2 class="mb-4 text-3xl font-bold text-foreground">
                        The Problem: Property Fraud
                    </h2>
                    <p
                        class="mb-6 text-lg leading-relaxed text-muted-foreground"
                    >
                        For decades, real estate markets have been plagued by
                        disputes, forged documents, and agonizingly slow
                        bureaucratic verification. Paper-based records are
                        highly susceptible to loss and corruption, putting
                        billions of dollars and millions of families at risk
                        every year.
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-border bg-card p-8 shadow-sm"
                >
                    <div
                        class="mb-6 flex h-12 w-12 items-center justify-center rounded-xl bg-success/10 text-success"
                    >
                        <Shield class="h-6 w-6" />
                    </div>
                    <h2 class="mb-4 text-3xl font-bold text-foreground">
                        The Solution: LandChain AI
                    </h2>
                    <p
                        class="mb-6 text-lg leading-relaxed text-muted-foreground"
                    >
                        We replace outdated ledgers with immutable Blockchain
                        technology and power our verification process with
                        cutting-edge AI. LandChain ensures that property titles
                        are indisputable, completely transparent, and
                        transferable with just a few clicks rather than weeks of
                        paperwork.
                    </p>
                </div>
            </div>
        </section>

        <!-- Mission & Vision -->
        <section class="bg-secondary/30 px-6 py-20 md:px-12">
            <div class="mx-auto max-w-5xl text-center">
                <Target class="mx-auto mb-6 h-12 w-12 text-primary" />
                <h2 class="mb-6 text-3xl font-bold text-foreground">
                    Our Mission & Vision
                </h2>
                <div
                    class="mx-auto max-w-3xl space-y-6 text-left text-lg leading-relaxed text-muted-foreground"
                >
                    <p>
                        <strong>Our Mission:</strong> To standardize property
                        registration globally using decentralized ledger
                        technology, eradicating corruption and guaranteeing true
                        ownership for everyone.
                    </p>
                    <p>
                        <strong>Our Vision:</strong> A world where buying,
                        selling, and verifying property is as simple, secure,
                        and instantaneous as sending an email—fostering ultimate
                        trust in international and local real estate markets.
                    </p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
