<script setup lang="ts">
import { type HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <div :class="cn('rounded-3xl glass-card glass-card-hover border border-white/60 text-slate-800 transition-all duration-300', $props.class)">
        <slot />
    </div>
</template>
