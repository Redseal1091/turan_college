<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cva, type VariantProps } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const badgeVariants = cva(
    'inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
    {
        variants: {
            variant: {
                default:
                    'border-transparent bg-slate-900 text-white shadow-xs hover:bg-slate-900/80',
                secondary:
                    'border-transparent bg-slate-100 text-slate-900 hover:bg-slate-100/80',
                destructive:
                    'border-transparent bg-red-500 text-white shadow-xs hover:bg-red-500/80',
                outline:
                    'text-slate-950 border-slate-200',
                brand:
                    'border-transparent bg-[#0082C9] text-white shadow-xs hover:bg-[#0082C9]/85',
                accent:
                    'border-transparent bg-[#FDA685] text-slate-900 hover:bg-[#FDA685]/85',
                success:
                    'border-transparent bg-emerald-100 text-emerald-800',
            },
        },
        defaultVariants: {
            variant: 'default',
        },
    }
);

type BadgeVariants = VariantProps<typeof badgeVariants>;

interface Props {
    variant?: BadgeVariants['variant'];
    class?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <div :class="cn(badgeVariants({ variant }), $props.class)">
        <slot />
    </div>
</template>
