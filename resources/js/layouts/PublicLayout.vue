<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import { dashboard, login, register } from '@/routes';

defineProps<{
    title?: string;
}>();
</script>

<template>
    <div class="min-h-screen flex flex-col bg-background text-foreground overflow-x-hidden font-sans">
        <!-- Header -->
        <header class="py-4 px-6 md:px-12 flex items-center justify-between border-b border-border bg-card/80 backdrop-blur-md sticky top-0 z-50">
            <Link href="/" class="flex items-center gap-2">
                <AppLogoIcon class="w-8 h-8 text-primary" />
                <span class="font-bold text-xl text-primary drop-shadow-sm">LandChain AI</span>
            </Link>
            
            <nav class="hidden md:flex items-center gap-6">
                <Link href="/" class="text-sm font-medium hover:text-primary transition-colors">Home</Link>
                <Link href="/about" class="text-sm font-medium hover:text-primary transition-colors">About</Link>
                <Link href="/contact" class="text-sm font-medium hover:text-primary transition-colors">Contact</Link>
                <Link href="/privacy-policy" class="text-sm font-medium hover:text-primary transition-colors">Privacy</Link>
            </nav>

            <div class="flex items-center gap-3">
                <template v-if="$page.props.auth.user">
                    <Link :href="dashboard()">
                        <Button variant="outline" size="sm">Dashboard</Button>
                    </Link>
                </template>
                <template v-else>
                    <Link :href="login()">
                        <Button variant="ghost" size="sm" class="hidden sm:inline-flex">Log in</Button>
                    </Link>
                    <Link :href="register()">
                        <Button size="sm" class="bg-primary text-primary-foreground hover:bg-primary/90 shadow-sm">Get Started</Button>
                    </Link>
                </template>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow flex flex-col">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-card border-t border-border py-12 px-6 md:px-12 mt-auto">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <AppLogoIcon class="w-6 h-6 text-primary" />
                        <span class="font-bold text-lg text-primary">LandChain AI</span>
                    </div>
                    <p class="text-muted-foreground text-sm max-w-sm mb-4">
                        Smart Property Registration & Verification System powered by AI and Blockchain technology.
                    </p>
                    <div class="text-xs text-muted-foreground">
                        &copy; {{ new Date().getFullYear() }} LandChain AI. All rights reserved.
                    </div>
                </div>
                
                <div>
                    <h3 class="font-semibold text-foreground mb-4">Navigation</h3>
                    <ul class="space-y-2 text-sm text-muted-foreground">
                        <li><Link href="/" class="hover:text-primary transition-colors">Home</Link></li>
                        <li><Link href="/about" class="hover:text-primary transition-colors">About</Link></li>
                        <li><Link href="/contact" class="hover:text-primary transition-colors">Contact</Link></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-semibold text-foreground mb-4">Legal</h3>
                    <ul class="space-y-2 text-sm text-muted-foreground">
                        <li><Link href="/privacy-policy" class="hover:text-primary transition-colors">Privacy Policy</Link></li>
                        <li><Link href="/terms" class="hover:text-primary transition-colors">Terms and Conditions</Link></li>
                    </ul>
                </div>
            </div>
        </footer>
    </div>
</template>
