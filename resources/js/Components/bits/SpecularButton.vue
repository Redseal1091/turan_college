<template>
    <component
        :is="asComponent"
        :href="href"
        ref="buttonRef"
        @mousemove="handleMouseMove"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
        :class="[
            'relative inline-flex items-center justify-center gap-2.5 overflow-hidden rounded-full font-heading font-semibold text-[14px] leading-[14px] uppercase tracking-wider select-none cursor-pointer group active:scale-[0.97]',
            'transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]',
            className
        ]"
        :style="buttonStyle"
    >
        <!-- Glass Surface Background with Backdrop Blur -->
        <span
            class="absolute inset-0 rounded-full bg-white/10 backdrop-blur-xl border border-white/40 transition-colors duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:bg-white/20 group-hover:border-white/80"
        />

        <!-- Specular Highlight Layer tracking cursor -->
        <span
            class="pointer-events-none absolute inset-0 rounded-full transition-opacity duration-500 ease-out"
            :style="{
                opacity: isHovered ? 1 : 0,
                background: `radial-gradient(circle at ${position.x}px ${position.y}px, rgba(255, 255, 255, 0.45) 0%, rgba(255, 255, 255, 0.15) 35%, transparent 70%)`
            }"
        />

        <!-- Specular Rim Glare -->
        <span
            class="pointer-events-none absolute inset-[-1px] rounded-full transition-opacity duration-500 ease-out"
            :style="{
                opacity: isHovered ? 0.9 : 0,
                background: `radial-gradient(circle at ${position.x}px ${position.y}px, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0.2) 20%, transparent 60%)`,
                WebkitMask: 'linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0)',
                WebkitMaskComposite: 'xor',
                maskComposite: 'exclude',
                padding: '1px'
            }"
        />

        <!-- Content Slot with smooth arrow transition -->
        <span class="relative z-10 inline-flex items-center gap-2.5 text-white transition-transform duration-300 group-hover:scale-[1.02]">
            <slot />
        </span>
    </component>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

interface SpecularButtonProps {
    href?: string;
    className?: string;
    as?: 'button' | 'a' | 'Link';
}

const props = withDefaults(defineProps<SpecularButtonProps>(), {
    className: '',
    as: 'button',
});

const asComponent = computed(() => {
    if (props.href) {
        return Link;
    }
    return props.as === 'Link' ? Link : props.as;
});

const buttonRef = ref<HTMLElement | null>(null);
const isHovered = ref(false);
const position = ref({ x: 0, y: 0 });

const handleMouseMove = (e: MouseEvent) => {
    if (!buttonRef.value) return;
    const target = (buttonRef.value as any).$el || buttonRef.value;
    if (!target.getBoundingClientRect) return;
    const rect = target.getBoundingClientRect();
    position.value = {
        x: e.clientX - rect.left,
        y: e.clientY - rect.top,
    };
};

const handleMouseEnter = () => {
    isHovered.value = true;
};

const handleMouseLeave = () => {
    isHovered.value = false;
};

const buttonStyle = computed(() => ({
    boxShadow: 'none',
}));
</script>

