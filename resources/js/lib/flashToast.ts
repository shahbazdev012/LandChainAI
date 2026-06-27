import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import type { FlashToast } from '@/types/ui';

/**
 * Surface server-side flash messages as toasts after every successful Inertia
 * visit. The server normalises session flash keys (success/error/warning/info)
 * into a single `flash.toast` shared prop (see HandleInertiaRequests).
 */
export function initializeFlashToast(): void {
    router.on('success', (event) => {
        const page = (event as CustomEvent).detail?.page;
        const data = page?.props?.flash?.toast as FlashToast | undefined | null;

        if (!data) {
            return;
        }

        toast[data.type](data.message);
    });
}
