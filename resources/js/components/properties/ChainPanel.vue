<script setup lang="ts">
import { ShieldCheck, ShieldAlert, Link2 } from '@lucide/vue';
import CopyableHash from '@/components/CopyableHash.vue';
import type { ChainReport, PropertyBlock } from '@/types';

defineProps<{
    block: PropertyBlock | null | undefined;
    blockValid: boolean | null;
    chain: ChainReport;
}>();
</script>

<template>
    <div class="space-y-4">
        <div
            v-if="block"
            class="flex items-center gap-3 rounded-lg border p-3"
            :class="
                blockValid
                    ? 'border-emerald-200 bg-emerald-50/50 dark:border-emerald-500/20 dark:bg-emerald-500/5'
                    : 'border-red-200 bg-red-50/50 dark:border-red-500/20 dark:bg-red-500/5'
            "
        >
            <ShieldCheck
                v-if="blockValid"
                class="size-5 shrink-0 text-emerald-500"
            />
            <ShieldAlert v-else class="size-5 shrink-0 text-red-500" />
            <div class="text-sm">
                <p class="font-medium">
                    {{
                        blockValid
                            ? 'Block integrity verified'
                            : 'Block integrity compromised'
                    }}
                </p>
                <p class="text-muted-foreground">
                    {{
                        blockValid
                            ? `Sealed as block #${block.sequence}. Hash matches the recorded contents.`
                            : 'The stored hash no longer matches this record.'
                    }}
                </p>
            </div>
        </div>

        <dl v-if="block" class="space-y-3 text-sm">
            <div>
                <dt
                    class="mb-1 flex items-center gap-1.5 text-xs font-medium tracking-wide text-muted-foreground uppercase"
                >
                    Block hash (SHA-256)
                </dt>
                <dd><CopyableHash :value="block.hash" /></dd>
            </div>
            <div>
                <dt
                    class="mb-1 flex items-center gap-1.5 text-xs font-medium tracking-wide text-muted-foreground uppercase"
                >
                    <Link2 class="size-3.5" /> Previous block hash
                </dt>
                <dd><CopyableHash :value="block.previous_hash" /></dd>
            </div>
        </dl>

        <p v-else class="text-sm text-muted-foreground">
            This property has not been sealed into the chain.
        </p>

        <div class="border-t pt-3 text-xs text-muted-foreground">
            Ledger status:
            <span
                v-if="chain.intact"
                class="font-medium text-emerald-600 dark:text-emerald-400"
                >intact</span
            >
            <span v-else class="font-medium text-red-600 dark:text-red-400"
                >broken at block #{{ chain.broken_at_sequence }}</span
            >
            · {{ chain.blocks }} block{{ chain.blocks === 1 ? '' : 's' }} total
        </div>
    </div>
</template>
