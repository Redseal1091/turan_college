<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

interface SkeletonProps {
    class?: HTMLAttributes['class'];
}

defineProps<SkeletonProps>();
</script>

<template>
    <div
        :class="cn('animate-pulse rounded-md bg-slate-200/80 dark:bg-slate-800/80', $props.class)"
    />
</template>
