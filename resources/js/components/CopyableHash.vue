<script setup lang="ts">
import { Check, Copy } from '@lucide/vue';
import { useClipboard } from '@vueuse/core';
import { toast } from 'vue-sonner';
import { cn } from '@/lib/utils';

const props = defineProps<{
    value: string;
    truncate?: boolean;
    class?: string;
}>();

const { copy, copied } = useClipboard({
    source: () => props.value,
    copiedDuring: 1500,
});

function onCopy(): void {
    copy(props.value);
    toast.success('Hash copied to clipboard');
}
</script>

<template>
    <div
        :class="
            cn(
                'flex items-center gap-2 rounded-md border bg-muted/60 px-2.5 py-1.5',
                props.class,
            )
        "
    >
        <code
            :class="
                cn(
                    'font-mono text-xs text-foreground/80',
                    truncate ? 'truncate' : 'break-all',
                )
            "
            >{{ value }}</code
        >
        <button
            type="button"
            class="ml-auto shrink-0 text-muted-foreground transition-colors hover:text-foreground"
            :aria-label="copied ? 'Copied' : 'Copy hash'"
            @click="onCopy"
        >
            <Check v-if="copied" class="size-3.5 text-emerald-500" />
            <Copy v-else class="size-3.5" />
        </button>
    </div>
</template>
