<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <PublicLayout>
        <Head title="Terms and Conditions - LandChain AI" />

        <!-- Header Section -->
        <section
            class="bg-[#1E293B] px-6 py-16 text-center text-white md:px-12"
        >
            <h1 class="mb-4 text-3xl font-bold md:text-5xl">
                Terms and Conditions
            </h1>
            <p class="text-lg text-[#94A3B8]">Last Updated: June 11, 2026</p>
        </section>

        <!-- Content Section -->
        <section class="mx-auto max-w-4xl px-6 py-20 md:px-12">
            <div class="prose prose-slate dark:prose-invert max-w-none">
                <p>
                    Welcome to LandChain AI. These Terms and Conditions govern
                    your use of our Smart Property Registration and Verification
                    System. By accessing or using our Service, you agree to be
                    bound by these Terms.
                </p>

                <h2 class="mt-10 mb-4 text-2xl font-semibold text-foreground">
                    1. User Accounts and Responsibilities
                </h2>
                <p>
                    When you create an account with us, you must provide
                    accurate, complete, and current information. You are
                    responsible for safeguarding your account credentials and
                    for any activities or actions under your password.
                </p>

                <h2 class="mt-10 mb-4 text-2xl font-semibold text-foreground">
                    2. Fake Documents and Account Termination
                </h2>
                <p>
                    Integrity and trust are the foundation of LandChain AI.
                    Under no circumstances are users permitted to upload,
                    publish, or submit false, forged, or fraudulent property
                    documents.
                </p>
                <div
                    class="my-4 rounded-md border border-destructive/20 bg-destructive/10 p-4"
                >
                    <strong class="mb-2 block text-destructive"
                        >Zero Tolerance Policy for Fraud</strong
                    >
                    <span class="text-destructive/90">
                        If we detect the submission or publication of fake
                        documents through our AI verification process or manual
                        review,
                        <strong
                            >we reserve the right to instantly disable and
                            permanently ban your account</strong
                        >
                        without prior notice.
                    </span>
                </div>
                <p>
                    Furthermore, instances of deliberate fraud will be reported
                    to relevant law enforcement and government regulatory bodies
                    for further investigation.
                </p>

                <h2 class="mt-10 mb-4 text-2xl font-semibold text-foreground">
                    3. Reservation of Rights
                </h2>
                <p>
                    We reserve all rights, title, and interest in and to the
                    LandChain AI platform, its underlying blockchain technology,
                    artificial intelligence models, algorithms, and intellectual
                    property. Nothing in these Terms grants you a right or
                    license to use any of our trademarks, logos, or brand
                    features.
                </p>
                <p>
                    We reserve the right to refuse service, terminate accounts,
                    or remove or edit content at our sole discretion at any
                    time.
                </p>

                <h2 class="mt-10 mb-4 text-2xl font-semibold text-foreground">
                    4. Limitation of Liability
                </h2>
                <p>
                    While our platform strives to provide accurate and secure
                    property verification, LandChain AI acts strictly as a
                    technological facilitator. We are not liable for any
                    financial losses, property disputes, or damages arising from
                    the use or inability to use our Service.
                </p>

                <h2 class="mt-10 mb-4 text-2xl font-semibold text-foreground">
                    5. Modifications to Terms
                </h2>
                <p>
                    We reserve the right, at our sole discretion, to modify or
                    replace these Terms at any time. We will notify users of any
                    substantial changes, and continued use of the platform after
                    changes constitutes acceptance of the new terms.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
