<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Target, Shield, Zap } from '@lucide/vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <PublicLayout>
        <Head title="About Us - LanChain AI" />

        <!-- Header Section -->
        <section class="bg-primary text-primary-foreground py-20 px-6 md:px-12 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">About LanChain AI</h1>
            <p class="text-xl max-w-3xl mx-auto text-primary-foreground/90">
                Pioneering a secure, transparent, and intelligent era for property registration worldwide.
            </p>
        </section>

        <!-- The Problem & Solution -->
        <section class="max-w-7xl mx-auto px-6 md:px-12 py-20">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="h-12 w-12 bg-destructive/10 text-destructive flex items-center justify-center rounded-xl mb-6">
                        <Zap class="w-6 h-6" />
                    </div>
                    <h2 class="text-3xl font-bold text-foreground mb-4">The Problem: Property Fraud</h2>
                    <p class="text-muted-foreground text-lg mb-6 leading-relaxed">
                        For decades, real estate markets have been plagued by disputes, forged documents, and agonizingly slow bureaucratic verification. Paper-based records are highly susceptible to loss and corruption, putting billions of dollars and millions of families at risk every year.
                    </p>
                </div>
                
                <div class="bg-card border border-border p-8 rounded-2xl shadow-sm">
                    <div class="h-12 w-12 bg-success/10 text-success flex items-center justify-center rounded-xl mb-6">
                        <Shield class="w-6 h-6" />
                    </div>
                    <h2 class="text-3xl font-bold text-foreground mb-4">The Solution: LanChain AI</h2>
                    <p class="text-muted-foreground text-lg mb-6 leading-relaxed">
                        We replace outdated ledgers with immutable Blockchain technology and power our verification process with cutting-edge AI. LanChain ensures that property titles are indisputable, completely transparent, and transferable with just a few clicks rather than weeks of paperwork.
                    </p>
                </div>
            </div>
        </section>

        <!-- Mission & Vision -->
        <section class="bg-secondary/30 py-20 px-6 md:px-12">
            <div class="max-w-5xl mx-auto text-center">
                <Target class="w-12 h-12 text-primary mx-auto mb-6" />
                <h2 class="text-3xl font-bold text-foreground mb-6">Our Mission & Vision</h2>
                <div class="space-y-6 text-lg text-muted-foreground leading-relaxed text-left max-w-3xl mx-auto">
                    <p>
                        <strong>Our Mission:</strong> To standardize property registration globally using decentralized ledger technology, eradicating corruption and guaranteeing true ownership for everyone.
                    </p>
                    <p>
                        <strong>Our Vision:</strong> A world where buying, selling, and verifying property is as simple, secure, and instantaneous as sending an email—fostering ultimate trust in international and local real estate markets.
                    </p>
                </div>
            </div>
        </section>

    </PublicLayout>
</template>
