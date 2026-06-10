<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login } from '@/routes';
import { register } from '@/routes';
</script>

<template>
    <Head title="Welcome to LanChain AI">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#F8FAFC] p-6 text-[#0F172A] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]"
    >
        <header
            class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl"
        >
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="register()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>
        <div
            class="flex flex-1 w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0"
        >
            <main
                class="flex w-full flex-col items-center text-center justify-center lg:max-w-4xl"
            >
                <div class="mb-10">
                    <div class="h-16 w-16 bg-[#0F4C81] text-white rounded-2xl flex items-center justify-center font-bold text-3xl mx-auto mb-6 shadow-lg">L</div>
                    <h1 class="text-4xl font-bold text-[#0F172A] dark:text-white mb-4">LanChain AI</h1>
                    <p class="text-xl text-[#64748B] dark:text-[#94A3B8] max-w-2xl mx-auto">
                        Smart Property Registration and Verification System
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full text-left">
                    <div class="p-8 bg-white dark:bg-[#161615] rounded-xl border border-[#19140035] dark:border-[#3E3E3A] shadow-sm flex flex-col items-start hover:shadow-md transition-shadow">
                        <div class="h-12 w-12 bg-[#16A34A]/10 text-[#16A34A] rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-[#0F172A] dark:text-[#EDEDEC] mb-3">Secure Verification</h3>
                        <p class="text-[#64748B] dark:text-[#A1A09A] leading-relaxed">Blockchain-backed property records ensure authenticity and prevent fraud in real estate transactions.</p>
                    </div>
                    <div class="p-8 bg-white dark:bg-[#161615] rounded-xl border border-[#19140035] dark:border-[#3E3E3A] shadow-sm flex flex-col items-start hover:shadow-md transition-shadow">
                        <div class="h-12 w-12 bg-[#0F4C81]/10 text-[#0F4C81] rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-[#0F172A] dark:text-[#EDEDEC] mb-3">Smart Registration</h3>
                        <p class="text-[#64748B] dark:text-[#A1A09A] leading-relaxed">Streamlined digital property registration process saving time and eliminating cumbersome paperwork.</p>
                    </div>
                </div>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
