<script setup lang="ts">
import type { Component } from 'vue';
import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';

defineProps<{
    label: string;
    value: number | string;
    icon?: Component;
    accent?: string;
}>();
</script>

<template>
    <Card>
        <CardContent class="flex items-center gap-4 p-5">
            <div
                v-if="icon"
                :class="
                    cn(
                        'flex size-11 shrink-0 items-center justify-center rounded-xl',
                        accent ?? 'bg-primary/10 text-primary',
                    )
                "
            >
                <component :is="icon" class="size-5" />
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-muted-foreground">
                    {{ label }}
                </p>
                <p class="text-2xl font-bold tracking-tight tabular-nums">
                    {{ value }}
                </p>
            </div>
        </CardContent>
    </Card>
</template>
