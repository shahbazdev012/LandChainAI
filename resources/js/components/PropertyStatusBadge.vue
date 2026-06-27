<script setup lang="ts">
import { computed } from 'vue';
import { cn } from '@/lib/utils';
import type { StatusBadge } from '@/types';

const props = defineProps<{ status: StatusBadge; class?: string }>();

const colorClasses: Record<string, string> = {
    gray: 'bg-muted text-muted-foreground ring-border',
    amber: 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20',
    blue: 'bg-blue-50 text-blue-700 ring-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20',
    green: 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
    orange: 'bg-orange-50 text-orange-700 ring-orange-200 dark:bg-orange-500/10 dark:text-orange-400 dark:ring-orange-500/20',
    red: 'bg-red-50 text-red-700 ring-red-200 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20',
};

const classes = computed(
    () => colorClasses[props.status.color] ?? colorClasses.gray,
);
</script>

<template>
    <span
        :class="
            cn(
                'inline-flex w-fit items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium whitespace-nowrap ring-1 ring-inset',
                classes,
                props.class,
            )
        "
    >
        <span class="size-1.5 rounded-full bg-current opacity-80" />
        {{ status.label }}
    </span>
</template>
