<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue';
import {
    NavigationMenuTrigger,
    type NavigationMenuTriggerProps,
    useForwardProps,
} from 'reka-ui';
import { ChevronDown } from 'lucide-vue-next';
import { cn } from '@/lib/utils';

interface Props extends /* @vue-ignore */ NavigationMenuTriggerProps {
    class?: HTMLAttributes['class'];
    darkText?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    darkText: false,
});

const delegatedProps = computed(() => {
    const { class: _, darkText: __, ...delegated } = props;
    return delegated;
});

const forwarded = useForwardProps(delegatedProps);
</script>

<template>
    <NavigationMenuTrigger
        v-bind="forwarded"
        :class="cn(
            'group inline-flex h-9 w-max items-center justify-center rounded-lg px-3.5 py-1.5 text-sm font-heading font-medium transition-all duration-200 focus:outline-none disabled:pointer-events-none disabled:opacity-50 cursor-pointer select-none',
            darkText
                ? 'text-slate-700 hover:text-slate-900 hover:bg-slate-100 data-[state=open]:bg-slate-100 data-[state=open]:text-slate-900'
                : 'text-white/90 hover:text-white hover:bg-white/10 data-[state=open]:bg-white/15 data-[state=open]:text-white',
            $props.class
        )"
    >
        <slot />
        <ChevronDown
            class="relative top-[1px] ml-1.5 h-3.5 w-3.5 transition-transform duration-200 group-data-[state=open]:rotate-180"
            aria-hidden="true"
        />
    </NavigationMenuTrigger>
</template>
