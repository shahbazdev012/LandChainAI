<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <PublicLayout>
        <Head title="Privacy Policy - LandChain AI" />

        <!-- Header Section -->
        <section
            class="bg-[#1E293B] px-6 py-16 text-center text-white md:px-12"
        >
            <h1 class="mb-4 text-3xl font-bold md:text-5xl">Privacy Policy</h1>
            <p class="text-lg text-[#94A3B8]">Last Updated: May 11, 2026</p>
        </section>

        <!-- Content Section -->
        <section class="mx-auto max-w-4xl px-6 py-20 md:px-12">
            <div class="prose prose-slate dark:prose-invert max-w-none">
                <p>
                    LandChain AI operates the Smart Property Registration and
                    Verification System. This page informs you of our policies
                    regarding the collection, use, and disclosure of personal
                    and property data when you use our Service and the choices
                    you have associated with that data.
                </p>

                <h2 class="mt-10 mb-4 text-2xl font-semibold text-foreground">
                    1. Data Handling & Security
                </h2>
                <p>
                    We use state-of-the-art cryptographic hashing to secure
                    property records. Sensitive details uploaded to our platform
                    are encrypted in transit and at rest. The information stored
                    on our immutable blockchain ledger consists of secure
                    hashes, not raw personal unencrypted data.
                </p>

                <h2 class="mt-10 mb-4 text-2xl font-semibold text-foreground">
                    2. Information Collection And Use
                </h2>
                <p>
                    We collect several different types of information for
                    various purposes to provide and improve our Service:
                </p>
                <ul class="my-4 list-disc space-y-2 pl-6">
                    <li>
                        <strong>Personal Data:</strong> Name, email address,
                        phone number, government-issued IDs for KYC/AML
                        regulations.
                    </li>
                    <li>
                        <strong>Property Data:</strong> Ownership deeds,
                        structural information, and taxation documents.
                    </li>
                    <li>
                        <strong>Usage Data:</strong> Information on how the
                        Service is accessed and used.
                    </li>
                </ul>

                <h2 class="mt-10 mb-4 text-2xl font-semibold text-foreground">
                    3. User Responsibility
                </h2>
                <p>
                    While we provide a secure platform and AI-driven anomaly
                    detection, users are solely responsible for the accuracy of
                    the data they submit. Fraudulent submissions will be
                    reported to the relevant government authorities. LandChain
                    AI provides tools for verification but does not legally
                    adjudicate property disputes.
                </p>

                <h2 class="mt-10 mb-4 text-2xl font-semibold text-foreground">
                    4. Changes To This Privacy Policy
                </h2>
                <p>
                    We may update our Privacy Policy from time to time. We will
                    notify you of any changes by posting the new Privacy Policy
                    on this page and updating the "Last Updated" date. You are
                    advised to review this Privacy Policy periodically for any
                    changes.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
