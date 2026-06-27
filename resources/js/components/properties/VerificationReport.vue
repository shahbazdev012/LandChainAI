<script setup lang="ts">
import { CheckCircle2, XCircle } from '@lucide/vue';
import { computed } from 'vue';
import PropertyStatusBadge from '@/components/PropertyStatusBadge.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import type { PropertyVerification } from '@/types';

const props = defineProps<{ verification: PropertyVerification }>();

const barColor = computed(
    () =>
        ({
            green: 'bg-emerald-500',
            orange: 'bg-orange-500',
            red: 'bg-red-500',
            amber: 'bg-amber-500',
            gray: 'bg-muted-foreground',
        })[props.verification.status.color] ?? 'bg-primary',
);
</script>

<template>
    <div class="space-y-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm text-muted-foreground">AI confidence score</p>
                <p class="text-3xl font-bold tabular-nums">
                    {{ verification.score
                    }}<span class="text-lg text-muted-foreground">/100</span>
                </p>
            </div>
            <PropertyStatusBadge :status="verification.status" />
        </div>

        <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
            <div
                :class="['h-full rounded-full transition-all', barColor]"
                :style="{ width: `${verification.score}%` }"
            />
        </div>

        <ul class="divide-y rounded-lg border">
            <li
                v-for="check in verification.checks"
                :key="check.key"
                class="flex items-start gap-3 p-3"
            >
                <CheckCircle2
                    v-if="check.passed"
                    class="mt-0.5 size-5 shrink-0 text-emerald-500"
                />
                <XCircle
                    v-else
                    class="mt-0.5 size-5 shrink-0 text-muted-foreground/60"
                />
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-medium">{{ check.label }}</p>
                        <span
                            class="shrink-0 text-xs text-muted-foreground tabular-nums"
                            >{{ check.points }}/{{ check.max_points }}</span
                        >
                    </div>
                    <p class="text-sm text-muted-foreground">
                        {{ check.message }}
                    </p>
                </div>
            </li>
        </ul>

        <Alert v-if="verification.notes" variant="default">
            <AlertTitle>Reviewer note</AlertTitle>
            <AlertDescription>{{ verification.notes }}</AlertDescription>
        </Alert>
    </div>
</template>
