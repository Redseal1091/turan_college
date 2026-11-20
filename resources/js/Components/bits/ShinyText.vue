<template>
    <span
        :class="['inline-block bg-clip-text text-transparent', className]"
        :style="{
            backgroundImage: `linear-gradient(120deg, transparent 20%, ${shimmerColor} 50%, transparent 80%)`,
            backgroundSize: '200% 100%',
            animation: `vueBitsShine ${speed}s linear infinite`,
        }"
    >
        <slot />
    </span>
</template>

<script setup lang="ts">
interface ShinyTextProps {
    className?: string;
    shimmerColor?: string;
    speed?: number;
}

withDefaults(defineProps<ShinyTextProps>(), {
    className: '',
    shimmerColor: 'rgba(255, 255, 255, 0.8)',
    speed: 3,
});
</script>

<style scoped>
@keyframes vueBitsShine {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}
</style>

