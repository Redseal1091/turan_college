<script setup lang="ts">
import type { LabelHTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

interface Props extends /* @vue-ignore */ LabelHTMLAttributes {
    class?: string;
    for?: string;
}

defineProps<Props>();
</script>

<template>
    <label
        :for="$props.for"
        :class="cn(
            'text-sm font-medium leading-none text-slate-900 peer-disabled:cursor-not-allowed peer-disabled:opacity-70 select-none',
            $props.class
        )"
    >
        <slot />
    </label>
</template>
