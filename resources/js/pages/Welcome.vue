<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ShieldCheck, FileCheck, Search, Link as LinkIcon, CheckCircle2 } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';

</script>

<template>
    <PublicLayout>
        <Head title="LanChain AI - Smart Property Registration & Verification" />
        
        <!-- Hero Section -->
        <section class="relative px-6 md:px-12 py-20 md:py-32 flex flex-col items-center justify-center text-center overflow-hidden">
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:100px_100px]"></div>
            
            <Badge class="mb-6 bg-primary/10 text-primary border-primary/20 hover:bg-primary/20" variant="outline">
                <span class="flex items-center gap-1.5"><ShieldCheck class="w-4 h-4" /> Secure & Transparent</span>
            </Badge>
            
            <h1 class="text-5xl md:text-7xl font-extrabold text-foreground tracking-tight mb-6 max-w-4xl">
                The Future of <span class="text-primary">Property Registration</span> & Verification
            </h1>
            
            <p class="text-xl md:text-2xl text-muted-foreground mb-10 max-w-2xl leading-relaxed">
                LanChain AI leverages Blockchain and AI to eliminate fraud, ensuring smart, transparent, and secure property transactions.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <Link href="/register-property">
                    <Button size="lg" class="w-full sm:w-auto h-14 px-8 text-lg bg-primary hover:bg-primary/95 text-white shadow-xl shadow-primary/20">
                        Register Property
                    </Button>
                </Link>
                <Link href="/verify">
                    <Button size="lg" variant="outline" class="w-full sm:w-auto h-14 px-8 text-lg border-2">
                        Verify Property
                    </Button>
                </Link>
            </div>
        </section>

        <!-- Features Section -->
        <section class="px-6 md:px-12 py-20 bg-secondary/30">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-4">Core Capabilities</h2>
                    <p class="text-lg text-muted-foreground max-w-2xl mx-auto">Our platform provides end-to-end security and ease of use for governments, buyers, and sellers.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-card p-8 rounded-2xl border border-border shadow-sm hover:shadow-md transition-all group">
                        <div class="w-14 h-14 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <FileCheck class="w-7 h-7" />
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Smart Registration</h3>
                        <p class="text-muted-foreground text-sm leading-relaxed">Digitize the entire property registration workflow, removing bottlenecks and excessive paperwork.</p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="bg-card p-8 rounded-2xl border border-border shadow-sm hover:shadow-md transition-all group">
                        <div class="w-14 h-14 rounded-xl bg-success/10 text-success flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <Search class="w-7 h-7" />
                        </div>
                        <h3 class="text-xl font-semibold mb-3">AI Verification (Concept)</h3>
                        <p class="text-muted-foreground text-sm leading-relaxed">AI-driven document analysis to detect inconsistencies and forgery in real-time before approval.</p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="bg-card p-8 rounded-2xl border border-border shadow-sm hover:shadow-md transition-all group">
                        <div class="w-14 h-14 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <LinkIcon class="w-7 h-7" />
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Blockchain Security (Concept)</h3>
                        <p class="text-muted-foreground text-sm leading-relaxed">Records are hashed and placed on an immutable ledger, ensuring data integrity forever.</p>
                    </div>
                    
                    <!-- Feature 4 -->
                    <div class="bg-card p-8 rounded-2xl border border-border shadow-sm hover:shadow-md transition-all group">
                        <div class="w-14 h-14 rounded-xl bg-warning/10 text-warning flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <ShieldCheck class="w-7 h-7" />
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Fraud Prevention</h3>
                        <p class="text-muted-foreground text-sm leading-relaxed">Eradicate property scams through biometric checks and multi-party cryptographic signatures.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="px-6 md:px-12 py-20 md:py-28">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row items-center gap-16">
                    <div class="w-full md:w-1/2">
                        <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-6">How LanChain AI Works</h2>
                        <p class="text-lg text-muted-foreground mb-10 leading-relaxed">
                            A simple, secure, and modern approach to property management. Follow these steps to register or verify a property natively on our platform.
                        </p>
                        
                        <div class="space-y-8">
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm shrink-0">1</div>
                                    <div class="w-0.5 h-full bg-border mt-2"></div>
                                </div>
                                <div class="pb-2">
                                    <h4 class="text-xl font-semibold mb-2">Submit Property Details</h4>
                                    <p class="text-muted-foreground text-sm">Upload ownership documents, ID proofs, and structural details via our secure portal.</p>
                                </div>
                            </div>
                            
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm shrink-0">2</div>
                                    <div class="w-0.5 h-full bg-border mt-2"></div>
                                </div>
                                <div class="pb-2">
                                    <h4 class="text-xl font-semibold mb-2">AI Document Verification</h4>
                                    <p class="text-muted-foreground text-sm">Our AI models automatically cross-reference data against national databases and check for anomalies.</p>
                                </div>
                            </div>
                            
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm shrink-0">3</div>
                                    <div class="w-0.5 h-full bg-border mt-2"></div>
                                </div>
                                <div class="pb-2">
                                    <h4 class="text-xl font-semibold mb-2">Blockchain Hash & Store</h4>
                                    <p class="text-muted-foreground text-sm">Once approved, details are cryptographically hashed and committed to our ledger.</p>
                                </div>
                            </div>
                            
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm shrink-0">4</div>
                                </div>
                                <div>
                                    <h4 class="text-xl font-semibold mb-2">Public Verification Access</h4>
                                    <p class="text-muted-foreground text-sm">Anyone can instantly verify the authenticity of a property title by searching its unique ID.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-1/2 bg-card border border-border p-8 rounded-3xl shadow-xl">
                        <!-- Mockup UI representation -->
                        <div class="flex items-center gap-3 border-b border-border pb-4 mb-6">
                            <div class="w-3 h-3 rounded-full bg-destructive"></div>
                            <div class="w-3 h-3 rounded-full bg-warning"></div>
                            <div class="w-3 h-3 rounded-full bg-success"></div>
                            <div class="ml-4 h-6 w-3/4 bg-secondary rounded-md"></div>
                        </div>
                        <div class="space-y-4">
                            <div class="h-10 bg-secondary rounded-lg w-full flex items-center px-4">
                                <Search class="w-4 h-4 text-muted-foreground mr-2" />
                                <span class="text-sm text-muted-foreground">Enter Property Hash ID...</span>
                            </div>
                            <div class="h-32 bg-primary/5 border border-primary/20 rounded-lg flex flex-col items-center justify-center mt-6">
                                <CheckCircle2 class="w-10 h-10 text-success mb-2" />
                                <span class="font-semibold text-primary">Property Verified Authentic</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div class="h-20 bg-secondary rounded-lg"></div>
                                <div class="h-20 bg-secondary rounded-lg"></div>
                                <div class="h-20 bg-secondary rounded-lg"></div>
                                <div class="h-20 bg-secondary rounded-lg"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-primary text-primary-foreground text-center px-6 md:px-12">
            <h2 class="text-3xl md:text-5xl font-bold mb-6">Ready to secure your assets?</h2>
            <p class="text-xl text-primary-foreground/80 mb-10 max-w-2xl mx-auto">
                Join thousands who have already migrated to the most transparent and secure property registration standard.
            </p>
            <div class="flex justify-center gap-4">
                <Link href="/register">
                    <Button size="lg" class="bg-white text-primary hover:bg-white/90 h-14 px-8 text-lg font-semibold">
                        Create an Account
                    </Button>
                </Link>
            </div>
        </section>

    </PublicLayout>
</template>
