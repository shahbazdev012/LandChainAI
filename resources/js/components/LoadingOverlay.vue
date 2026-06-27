<script setup lang="ts">
import { Spinner } from '@/components/ui/spinner';

defineProps<{ show: boolean; message?: string }>();
</script>

<template>
    <Transition
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="opacity-0"
        leave-active-class="transition duration-150 ease-in"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex flex-col items-center justify-center gap-4 bg-background/75 backdrop-blur-sm"
            role="status"
            aria-live="polite"
        >
            <Spinner class="size-9 text-primary" />
            <p class="text-sm font-medium text-foreground">{{ message ?? 'Please wait a moment…' }}</p>
        </div>
    </Transition>
</template>
