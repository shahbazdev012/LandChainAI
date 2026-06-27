<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import { dashboard, login } from '@/routes';

defineProps<{
    title?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-screen flex-col overflow-x-hidden bg-background font-sans text-foreground"
    >
        <!-- Header -->
        <header
            class="sticky top-0 z-50 flex items-center justify-between border-b border-border bg-card/80 px-6 py-4 backdrop-blur-md md:px-12"
        >
            <Link href="/" class="flex items-center gap-2">
                <AppLogoIcon class="h-8 w-8 text-primary" />
                <span class="text-xl font-bold text-primary drop-shadow-sm"
                    >LandChain AI</span
                >
            </Link>

            <nav class="hidden items-center gap-6 md:flex">
                <Link
                    href="/"
                    class="text-sm font-medium transition-colors hover:text-primary"
                    >Home</Link
                >
                <Link
                    href="/about"
                    class="text-sm font-medium transition-colors hover:text-primary"
                    >About</Link
                >
                <Link
                    href="/features"
                    class="text-sm font-medium transition-colors hover:text-primary"
                    >Features</Link
                >
                <Link
                    href="/verify-property"
                    class="text-sm font-medium transition-colors hover:text-primary"
                    >Verify</Link
                >
                <Link
                    href="/contact"
                    class="text-sm font-medium transition-colors hover:text-primary"
                    >Contact</Link
                >
            </nav>

            <div class="flex items-center gap-3">
                <Link href="/verify-property" class="hidden sm:inline-flex">
                    <Button variant="outline" size="sm"
                        >Verify a property</Button
                    >
                </Link>
                <template v-if="$page.props.auth.user">
                    <Link :href="dashboard()">
                        <Button size="sm">Dashboard</Button>
                    </Link>
                </template>
                <template v-else>
                    <Link :href="login()">
                        <Button
                            variant="ghost"
                            size="sm"
                            class="hidden sm:inline-flex"
                            >Log in</Button
                        >
                    </Link>
                </template>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex flex-grow flex-col">
            <slot />
        </main>

        <!-- Footer -->
        <footer
            class="mt-auto border-t border-border bg-card px-6 py-12 md:px-12"
        >
            <div
                class="mx-auto grid max-w-7xl grid-cols-1 gap-8 md:grid-cols-4"
            >
                <div class="col-span-1 md:col-span-2">
                    <div class="mb-4 flex items-center gap-2">
                        <AppLogoIcon class="h-6 w-6 text-primary" />
                        <span class="text-lg font-bold text-primary"
                            >LandChain AI</span
                        >
                    </div>
                    <p class="mb-4 max-w-sm text-sm text-muted-foreground">
                        Smart Property Registration & Verification System
                        powered by AI and Blockchain technology.
                    </p>
                    <div class="text-xs text-muted-foreground">
                        &copy; {{ new Date().getFullYear() }} LandChain AI. All
                        rights reserved.
                    </div>
                </div>

                <div>
                    <h3 class="mb-4 font-semibold text-foreground">
                        Navigation
                    </h3>
                    <ul class="space-y-2 text-sm text-muted-foreground">
                        <li>
                            <Link
                                href="/"
                                class="transition-colors hover:text-primary"
                                >Home</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/about"
                                class="transition-colors hover:text-primary"
                                >About</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/features"
                                class="transition-colors hover:text-primary"
                                >Features</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/verify-property"
                                class="transition-colors hover:text-primary"
                                >Verify a property</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/contact"
                                class="transition-colors hover:text-primary"
                                >Contact</Link
                            >
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="mb-4 font-semibold text-foreground">Legal</h3>
                    <ul class="space-y-2 text-sm text-muted-foreground">
                        <li>
                            <Link
                                href="/privacy-policy"
                                class="transition-colors hover:text-primary"
                                >Privacy Policy</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/terms"
                                class="transition-colors hover:text-primary"
                                >Terms and Conditions</Link
                            >
                        </li>
                    </ul>
                </div>
            </div>
        </footer>
    </div>
</template>
