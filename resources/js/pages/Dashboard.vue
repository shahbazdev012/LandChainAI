<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div class="mb-2">
            <h1 class="text-2xl font-bold text-[#0F172A] dark:text-white">LanChain AI Console</h1>
            <p class="text-[#64748B] dark:text-[#94A3B8]">Welcome to the LanChain AI Property Verification System.</p>
        </div>
        
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
                class="flex flex-col justify-center p-6 rounded-xl border border-sidebar-border/70 bg-white dark:bg-[#161615] dark:border-sidebar-border shadow-sm"
            >
                <h3 class="text-lg font-semibold text-[#0F172A] dark:text-white">Total Properties</h3>
                <p class="text-3xl font-bold text-[#0F4C81] mt-2">0</p>
            </div>
            <div
                class="flex flex-col justify-center p-6 rounded-xl border border-sidebar-border/70 bg-white dark:bg-[#161615] dark:border-sidebar-border shadow-sm"
            >
                <h3 class="text-lg font-semibold text-[#0F172A] dark:text-white">Verified</h3>
                <p class="text-3xl font-bold text-[#16A34A] mt-2">0</p>
            </div>
            <div
                class="flex flex-col justify-center p-6 rounded-xl border border-sidebar-border/70 bg-white dark:bg-[#161615] dark:border-sidebar-border shadow-sm"
            >
                <h3 class="text-lg font-semibold text-[#0F172A] dark:text-white">Pending Verification</h3>
                <p class="text-3xl font-bold text-[#F59E0B] mt-2">0</p>
            </div>
        </div>
        <div
            class="flex flex-1 items-center justify-center rounded-xl p-8 border border-sidebar-border/70 bg-white md:min-h-min dark:bg-[#161615] dark:border-sidebar-border shadow-sm"
        >
            <div class="text-center">
                <h2 class="text-xl font-semibold text-[#0F172A] dark:text-white mb-2">Recent Activity</h2>
                <p class="text-[#64748B] dark:text-[#94A3B8]">No properties have been registered yet.</p>
            </div>
        </div>
    </div>
</template>
