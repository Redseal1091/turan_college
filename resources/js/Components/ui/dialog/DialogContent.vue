<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue';
import {
    DialogClose,
    DialogContent,
    DialogOverlay,
    DialogPortal,
    useForwardPropsEmits,
} from 'reka-ui';
import { X } from 'lucide-vue-next';
import { cn } from '@/lib/utils';

interface Props extends /* @vue-ignore */ Record<string, any> {
    class?: HTMLAttributes['class'];
    overlayClass?: HTMLAttributes['class'];
    showClose?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showClose: true,
});
const emits = defineEmits<{
    (e: string, ...args: any[]): void;
}>();

const delegatedProps = computed(() => {
    const { class: _, overlayClass: __, showClose: ___, ...delegated } = props;
    return delegated;
});

const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <DialogPortal>
        <DialogOverlay
            :class="cn(
                'fixed inset-0 z-50 bg-black/80 backdrop-blur-sm data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0',
                overlayClass
            )"
        />
        <DialogContent
            v-bind="forwarded"
            :class="cn(
                'fixed left-[50%] top-[50%] z-50 grid w-full max-w-lg translate-x-[-50%] translate-y-[-50%] gap-4 border border-slate-200 bg-white p-6 shadow-xl duration-200 sm:rounded-2xl rounded-xl data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[state=closed]:slide-out-to-left-1/2 data-[state=closed]:slide-out-to-top-[48%] data-[state=open]:slide-in-from-left-1/2 data-[state=open]:slide-in-from-top-[48%] max-h-[90vh] overflow-y-auto',
                $props.class
            )"
        >
            <slot />

            <DialogClose
                v-if="showClose"
                class="absolute right-4 top-4 sm:right-5 sm:top-5 z-20 rounded-md p-1.5 text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors focus:outline-none focus:ring-2 focus:ring-[#0082C9] cursor-pointer disabled:pointer-events-none"
                aria-label="Закрыть"
            >
                <X class="h-4 w-4 sm:h-5 sm:w-5" />
            </DialogClose>
        </DialogContent>
    </DialogPortal>
</template>
