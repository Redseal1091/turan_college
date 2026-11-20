<script setup lang="ts">
import { type HTMLAttributes } from 'vue';
import { cva, type VariantProps } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const buttonVariants = cva(
    'group inline-flex items-center justify-center gap-2.5 whitespace-nowrap rounded-full font-heading font-semibold text-[14px] leading-[14px] transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 cursor-pointer active:scale-[0.98] [&_.lucide-arrow-right]:transition-transform [&_.lucide-arrow-right]:duration-200 group-hover:[&_.lucide-arrow-right]:translate-x-1.5 hover:[&_.lucide-arrow-right]:translate-x-1.5 [&_svg:last-child]:transition-transform [&_svg:last-child]:duration-200 group-hover:[&_svg:last-child]:translate-x-1.5 hover:[&_svg:last-child]:translate-x-1.5',
    {
        variants: {
            variant: {
                default:
                    'bg-[#448AC4] text-white hover:bg-[#3776A9] focus-visible:ring-[#448AC4]',
                accent:
                    'bg-[#FDA685] text-slate-900 font-semibold hover:bg-[#EB8E6A] focus-visible:ring-[#FDA685]',
                outline:
                    'border-2 border-[#448AC4] text-[#448AC4] bg-transparent hover:bg-[#448AC4]/10 focus-visible:ring-[#448AC4]',
                secondary:
                    'bg-slate-100 text-slate-800 hover:bg-slate-200 focus-visible:ring-slate-400',
                ghost:
                    'text-slate-700 hover:bg-slate-100 hover:text-slate-900',
                link:
                    'text-[#448AC4] underline-offset-4 hover:underline',
            },
            size: {
                default: 'h-12 px-7 py-3 text-[14px]',
                sm: 'h-10 px-5 py-2 text-[14px]',
                lg: 'h-14 px-8 py-3.5 text-[14px]',
                icon: 'h-10 w-10',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    }
);

type ButtonVariants = VariantProps<typeof buttonVariants>;

interface Props {
    variant?: ButtonVariants['variant'];
    size?: ButtonVariants['size'];
    class?: HTMLAttributes['class'];
    as?: string;
}

withDefaults(defineProps<Props>(), {
    as: 'button',
});
</script>

<template>
    <component
        :is="as"
        :class="cn(buttonVariants({ variant, size }), $props.class)"
    >
        <slot />
    </component>
</template>
