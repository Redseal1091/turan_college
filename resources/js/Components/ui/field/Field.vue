<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

interface Props {
    class?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <div :class="cn('group flex flex-col gap-1.5', $props.class)">
        <slot />
    </div>
</template>
