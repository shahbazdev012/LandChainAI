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

        <section class="max-w-7xl mx-auto px-6 md:px-12 py-20 flex flex-col md:flex-row gap-16">
            <!-- Contact Info -->
            <div class="w-full md:w-1/3">
                <h1 class="text-4xl font-bold text-foreground mb-6">Get in Touch</h1>
                <p class="text-muted-foreground mb-10 leading-relaxed">
                    Have questions about our smart verification process or want to integrate LandChain AI into your government registry? Reach out to our team.
                </p>

                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-primary/10 text-primary rounded-lg flex items-center justify-center shrink-0">
                            <Mail class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-foreground">Email</h3>
                            <p class="text-sm text-muted-foreground">inquiries@LandChain.ai</p>
                            <p class="text-sm text-muted-foreground">support@LandChain.ai</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-primary/10 text-primary rounded-lg flex items-center justify-center shrink-0">
                            <Phone class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-foreground">Phone</h3>
                            <p class="text-sm text-muted-foreground">(+92) 323-2155122</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-primary/10 text-primary rounded-lg flex items-center justify-center shrink-0">
                            <MapPin class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-foreground">Office</h3>
                            <p class="text-sm text-muted-foreground">
                                Old Civil Lines,<br/>
                                Sahiwal, Punjab, Pakistan
                            </p>                        
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="w-full md:w-2/3 bg-card border border-border p-8 rounded-2xl shadow-sm">
                <form @submit.prevent="" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="name">Full Name</Label>
                            <Input id="name" placeholder="John Doe" required />
                        </div>
                        <div class="space-y-2">
                            <Label for="email">Email Address</Label>
                            <Input id="email" type="email" placeholder="john@example.com" required />
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <Label for="subject">Subject</Label>
                        <Input id="subject" placeholder="How can we help you?" required />
                    </div>
                    
                    <div class="space-y-2">
                        <Label for="message">Message</Label>
                        <textarea 
                            id="message" 
                            rows="5" 
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="Write your message here..."
                            required
                        ></textarea>
                    </div>
                    
                    <Button type="submit" class="w-full md:w-auto px-8">
                        Send Message
                    </Button>
                </form>
            </div>
        </section>

    </PublicLayout>
</template>
