<script lang="ts" setup>
import { computed } from 'vue';
import type { Transition, Variant } from 'motion-v';
import { Motion } from 'motion-v';
import { cn } from '@/lib/utils';

interface CircularTextProps {
    text: string;
    duration?: number;
    class?: string;
    reverse?: boolean;
    radius?: number | string;
    transition?: Transition;
    variants?: {
        container?: Variant;
        item?: Variant;
    };
}

const props = withDefaults(defineProps<CircularTextProps>(), {
    duration: 16,
    radius: 54,
    reverse: false,
});

const BASE_TRANSITION = {
    repeat: Infinity,
    ease: 'linear',
};

const BASE_ITEM_VARIANTS = {
    hidden: { opacity: 1 },
    visible: { opacity: 1 },
};

const letters = computed(() => {
    // Preserve non-breaking spaces so words and separators never collapse
    return props.text.split('').map((char) => (char === ' ' ? '\u00A0' : char));
});

const radiusCss = computed(() => {
    if (typeof props.radius === 'number') {
        return `${props.radius}px`;
    }
    return props.radius || '54px';
});

const finalTransition = computed(() => ({
    ...BASE_TRANSITION,
    ...props.transition,
    duration: props.transition?.duration ?? props.duration,
}));

const containerVariants = computed(() => ({
    visible: { rotate: props.reverse ? -360 : 360 },
}));

const itemVariants = computed(() => ({
    ...BASE_ITEM_VARIANTS,
    ...props?.variants?.item,
}));
</script>

<template>
    <div :class="cn('relative inline-flex items-center justify-center select-none', props.class)">
        <!-- Rotating Text Ring using Inspira UI Motion -->
        <Motion
            as="div"
            initial="hidden"
            animate="visible"
            :variants="containerVariants"
            :transition="finalTransition"
            class="absolute inset-0 flex items-center justify-center pointer-events-none"
        >
            <span
                v-for="(letter, index) in letters"
                :key="`${letter}-${index}`"
                class="absolute left-1/2 top-1/2 pointer-events-none inline-block whitespace-pre text-center origin-center"
                :variants="itemVariants"
                :style="{
                    transform: `translate(-50%, -50%) rotate(${index * (360 / letters.length)}deg) translateY(calc(-1 * ${radiusCss}))`,
                }"
            >
                {{ letter }}
            </span>
        </Motion>

        <!-- Centered Slot for the Play Button / Icon (remains upright while text rotates around it) -->
        <div class="relative z-10 flex items-center justify-center">
            <slot />
        </div>
    </div>
</template>
