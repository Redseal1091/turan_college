<template>
    <div
        ref="divRef"
        @mousemove="handleMouseMove"
        @focus="handleFocus"
        @blur="handleBlur"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
        :class="['relative overflow-hidden transition-transform duration-300 hover:-translate-y-1', className]"
    >
        <div
            class="pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-500 ease-in-out z-0"
            :style="{
                opacity,
                background: `radial-gradient(circle at ${position.x}px ${position.y}px, ${spotlightColor}, transparent 80%)`
            }"
        />

        <div class="relative z-10 w-full h-full">
            <slot />
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface Position {
    x: number;
    y: number;
}

interface SpotlightCardProps {
    className?: string;
    spotlightColor?: string;
}

const props = withDefaults(defineProps<SpotlightCardProps>(), {
    className: '',
    spotlightColor: 'rgba(0, 130, 201, 0.12)',
});

const divRef = ref<HTMLDivElement | null>(null);
const isFocused = ref<boolean>(false);
const position = ref<Position>({ x: 0, y: 0 });
const opacity = ref<number>(0);

const handleMouseMove = (e: MouseEvent) => {
    if (!divRef.value || isFocused.value) return;
    const rect = divRef.value.getBoundingClientRect();
    position.value = { x: e.clientX - rect.left, y: e.clientY - rect.top };
};

const handleFocus = () => {
    isFocused.value = true;
    opacity.value = 0.8;
};

const handleBlur = () => {
    isFocused.value = false;
    opacity.value = 0;
};

const handleMouseEnter = () => {
    opacity.value = 0.8;
};

const handleMouseLeave = () => {
    opacity.value = 0;
};
</script>
