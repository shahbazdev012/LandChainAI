<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ShieldCheck,
    FileCheck,
    Search,
    Link as LinkIcon,
    CheckCircle2,
} from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <PublicLayout>
        <Head
            title="LandChain AI - Smart Property Registration & Verification"
        />

        <!-- Hero Section -->
        <section
            class="relative flex flex-col items-center justify-center overflow-hidden px-6 py-20 text-center md:px-12 md:py-32"
        >
            <div
                class="absolute inset-0 -z-10 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:100px_100px]"
            ></div>

            <Badge
                class="mb-6 border-primary/20 bg-primary/10 text-primary hover:bg-primary/20"
                variant="outline"
            >
                <span class="flex items-center gap-1.5"
                    ><ShieldCheck class="h-4 w-4" /> Secure & Transparent</span
                >
            </Badge>

            <h1
                class="mb-6 max-w-4xl text-5xl font-extrabold tracking-tight text-foreground md:text-7xl"
            >
                The Future of
                <span class="text-primary">Property Registration</span> &
                Verification
            </h1>

            <p
                class="mb-10 max-w-2xl text-xl leading-relaxed text-muted-foreground md:text-2xl"
            >
                LandChain AI leverages Blockchain and AI to eliminate fraud,
                ensuring smart, transparent, and secure property transactions.
            </p>

            <div class="flex flex-col items-center gap-4 sm:flex-row">
                <Link href="/register-property">
                    <Button
                        size="lg"
                        class="h-14 w-full bg-primary px-8 text-lg text-white shadow-xl shadow-primary/20 hover:bg-primary/95 sm:w-auto"
                    >
                        Register Property
                    </Button>
                </Link>
                <Link href="/verify-property">
                    <Button
                        size="lg"
                        variant="outline"
                        class="h-14 w-full border-2 px-8 text-lg sm:w-auto"
                    >
                        Verify Property
                    </Button>
                </Link>
            </div>
        </section>

        <!-- Features Section -->
        <section class="bg-secondary/30 px-6 py-20 md:px-12">
            <div class="mx-auto max-w-7xl">
                <div class="mb-16 text-center">
                    <h2
                        class="mb-4 text-3xl font-bold text-foreground md:text-4xl"
                    >
                        Core Capabilities
                    </h2>
                    <p class="mx-auto max-w-2xl text-lg text-muted-foreground">
                        Our platform provides end-to-end security and ease of
                        use for governments, buyers, and sellers.
                    </p>
                </div>

                <div
                    class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4"
                >
                    <!-- Feature 1 -->
                    <div
                        class="group rounded-2xl border border-border bg-card p-8 shadow-sm transition-all hover:shadow-md"
                    >
                        <div
                            class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-primary/10 text-primary transition-transform group-hover:scale-110"
                        >
                            <FileCheck class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-semibold">
                            Smart Registration
                        </h3>
                        <p
                            class="text-sm leading-relaxed text-muted-foreground"
                        >
                            Digitize the entire property registration workflow,
                            removing bottlenecks and excessive paperwork.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div
                        class="group rounded-2xl border border-border bg-card p-8 shadow-sm transition-all hover:shadow-md"
                    >
                        <div
                            class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-success/10 text-success transition-transform group-hover:scale-110"
                        >
                            <Search class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-semibold">
                            AI Verification (Concept)
                        </h3>
                        <p
                            class="text-sm leading-relaxed text-muted-foreground"
                        >
                            AI-driven document analysis to detect
                            inconsistencies and forgery in real-time before
                            approval.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div
                        class="group rounded-2xl border border-border bg-card p-8 shadow-sm transition-all hover:shadow-md"
                    >
                        <div
                            class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-primary/10 text-primary transition-transform group-hover:scale-110"
                        >
                            <LinkIcon class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-semibold">
                            Blockchain Security (Concept)
                        </h3>
                        <p
                            class="text-sm leading-relaxed text-muted-foreground"
                        >
                            Records are hashed and placed on an immutable
                            ledger, ensuring data integrity forever.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div
                        class="group rounded-2xl border border-border bg-card p-8 shadow-sm transition-all hover:shadow-md"
                    >
                        <div
                            class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-warning/10 text-warning transition-transform group-hover:scale-110"
                        >
                            <ShieldCheck class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-semibold">
                            Fraud Prevention
                        </h3>
                        <p
                            class="text-sm leading-relaxed text-muted-foreground"
                        >
                            Eradicate property scams through biometric checks
                            and multi-party cryptographic signatures.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="px-6 py-20 md:px-12 md:py-28">
            <div class="mx-auto max-w-7xl">
                <div class="flex flex-col items-center gap-16 md:flex-row">
                    <div class="w-full md:w-1/2">
                        <h2
                            class="mb-6 text-3xl font-bold text-foreground md:text-4xl"
                        >
                            How LandChain AI Works
                        </h2>
                        <p
                            class="mb-10 text-lg leading-relaxed text-muted-foreground"
                        >
                            A simple, secure, and modern approach to property
                            management. Follow these steps to register or verify
                            a property natively on our platform.
                        </p>

                        <div class="space-y-8">
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-white"
                                    >
                                        1
                                    </div>
                                    <div
                                        class="mt-2 h-full w-0.5 bg-border"
                                    ></div>
                                </div>
                                <div class="pb-2">
                                    <h4 class="mb-2 text-xl font-semibold">
                                        Submit Property Details
                                    </h4>
                                    <p class="text-sm text-muted-foreground">
                                        Upload ownership documents, ID proofs,
                                        and structural details via our secure
                                        portal.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-white"
                                    >
                                        2
                                    </div>
                                    <div
                                        class="mt-2 h-full w-0.5 bg-border"
                                    ></div>
                                </div>
                                <div class="pb-2">
                                    <h4 class="mb-2 text-xl font-semibold">
                                        AI Document Verification
                                    </h4>
                                    <p class="text-sm text-muted-foreground">
                                        Our AI models automatically
                                        cross-reference data against national
                                        databases and check for anomalies.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-white"
                                    >
                                        3
                                    </div>
                                    <div
                                        class="mt-2 h-full w-0.5 bg-border"
                                    ></div>
                                </div>
                                <div class="pb-2">
                                    <h4 class="mb-2 text-xl font-semibold">
                                        Blockchain Hash & Store
                                    </h4>
                                    <p class="text-sm text-muted-foreground">
                                        Once approved, details are
                                        cryptographically hashed and committed
                                        to our ledger.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-white"
                                    >
                                        4
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-2 text-xl font-semibold">
                                        Public Verification Access
                                    </h4>
                                    <p class="text-sm text-muted-foreground">
                                        Anyone can instantly verify the
                                        authenticity of a property title by
                                        searching its unique ID.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="w-full rounded-3xl border border-border bg-card p-8 shadow-xl md:w-1/2"
                    >
                        <!-- Mockup UI representation -->
                        <div
                            class="mb-6 flex items-center gap-3 border-b border-border pb-4"
                        >
                            <div
                                class="h-3 w-3 rounded-full bg-destructive"
                            ></div>
                            <div class="h-3 w-3 rounded-full bg-warning"></div>
                            <div class="h-3 w-3 rounded-full bg-success"></div>
                            <div
                                class="ml-4 h-6 w-3/4 rounded-md bg-secondary"
                            ></div>
                        </div>
                        <div class="space-y-4">
                            <div
                                class="flex h-10 w-full items-center rounded-lg bg-secondary px-4"
                            >
                                <Search
                                    class="mr-2 h-4 w-4 text-muted-foreground"
                                />
                                <span class="text-sm text-muted-foreground"
                                    >Enter Property Hash ID...</span
                                >
                            </div>
                            <div
                                class="mt-6 flex h-32 flex-col items-center justify-center rounded-lg border border-primary/20 bg-primary/5"
                            >
                                <CheckCircle2
                                    class="mb-2 h-10 w-10 text-success"
                                />
                                <span class="font-semibold text-primary"
                                    >Property Verified Authentic</span
                                >
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-4">
                                <div class="h-20 rounded-lg bg-secondary"></div>
                                <div class="h-20 rounded-lg bg-secondary"></div>
                                <div class="h-20 rounded-lg bg-secondary"></div>
                                <div class="h-20 rounded-lg bg-secondary"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section
            class="bg-primary px-6 py-20 text-center text-primary-foreground md:px-12"
        >
            <h2 class="mb-6 text-3xl font-bold md:text-5xl">
                Ready to secure your assets?
            </h2>
            <p
                class="mx-auto mb-10 max-w-2xl text-xl text-primary-foreground/80"
            >
                Join thousands who have already migrated to the most transparent
                and secure property registration standard.
            </p>
            <div class="flex justify-center gap-4">
                <!-- <Link href="/register">
                    <Button size="lg" class="bg-white text-primary hover:bg-white/90 h-14 px-8 text-lg font-semibold">
                        Create an Account
                    </Button>
                </Link> -->
            </div>
        </section>
    </PublicLayout>
</template>
