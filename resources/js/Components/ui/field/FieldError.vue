<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

interface Props {
    class?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <p :class="cn('text-xs font-medium text-red-500 font-sans', $props.class)">
        <slot />
    </p>
</template>
