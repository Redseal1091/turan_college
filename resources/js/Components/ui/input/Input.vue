<script setup lang="ts">
import type { InputHTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

interface Props extends /* @vue-ignore */ InputHTMLAttributes {
    modelValue?: string | number;
    class?: string;
    borderless?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    borderless: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number): void;
}>();

const handleInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    emit('update:modelValue', target.value);
};
</script>

<template>
    <input
        :value="modelValue"
        @input="handleInput"
        v-bind="$attrs"
        :class="cn(
            'flex h-10 w-full rounded-md px-3 py-2 text-sm text-slate-900 ring-offset-white file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-slate-400 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 transition-all duration-200',
            borderless
                ? 'border-0 bg-slate-200/60 hover:bg-slate-200/80 focus-visible:bg-white focus-visible:ring-2 focus-visible:ring-[#0082C9]'
                : 'border border-slate-200 bg-white focus-visible:ring-2 focus-visible:ring-[#0082C9]/25 focus-visible:border-[#0082C9]',
            $props.class
        )"
    />
</template>
