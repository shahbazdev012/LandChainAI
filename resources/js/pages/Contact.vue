<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Mail, MapPin, Phone } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <PublicLayout>
        <Head title="Contact Us - LandChain AI" />

        <section
            class="mx-auto flex max-w-7xl flex-col gap-16 px-6 py-20 md:flex-row md:px-12"
        >
            <!-- Contact Info -->
            <div class="w-full md:w-1/3">
                <h1 class="mb-6 text-4xl font-bold text-foreground">
                    Get in Touch
                </h1>
                <p class="mb-10 leading-relaxed text-muted-foreground">
                    Have questions about our smart verification process or want
                    to integrate LandChain AI into your government registry?
                    Reach out to our team.
                </p>

                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            <Mail class="h-5 w-5" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-foreground">Email</h3>
                            <p class="text-sm text-muted-foreground">
                                inquiries@LandChain.ai
                            </p>
                            <p class="text-sm text-muted-foreground">
                                support@LandChain.ai
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            <Phone class="h-5 w-5" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-foreground">Phone</h3>
                            <p class="text-sm text-muted-foreground">
                                (+92) 323-2155122
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            <MapPin class="h-5 w-5" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-foreground">
                                Office
                            </h3>
                            <p class="text-sm text-muted-foreground">
                                Old Civil Lines,<br />
                                Sahiwal, Punjab, Pakistan
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div
                class="w-full rounded-2xl border border-border bg-card p-8 shadow-sm md:w-2/3"
            >
                <form @submit.prevent="" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="name">Full Name</Label>
                            <Input id="name" placeholder="John Doe" required />
                        </div>
                        <div class="space-y-2">
                            <Label for="email">Email Address</Label>
                            <Input
                                id="email"
                                type="email"
                                placeholder="john@example.com"
                                required
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="subject">Subject</Label>
                        <Input
                            id="subject"
                            placeholder="How can we help you?"
                            required
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="message">Message</Label>
                        <textarea
                            id="message"
                            rows="5"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="Write your message here..."
                            required
                        ></textarea>
                    </div>

                    <Button type="submit" class="w-full px-8 md:w-auto">
                        Send Message
                    </Button>
                </form>
            </div>
        </section>
    </PublicLayout>
</template>
