<script setup lang="ts">
import { cn } from '@/lib/utils';

defineProps<{
    class?: string;
}>();
</script>

<template>
    <div :class="cn('container-tc', $props.class)">
        <slot />
    </div>
</template>
