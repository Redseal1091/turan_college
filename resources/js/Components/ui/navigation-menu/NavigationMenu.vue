<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue';
import {
    NavigationMenuRoot,
    NavigationMenuViewport,
    useForwardPropsEmits,
} from 'reka-ui';
import { cn } from '@/lib/utils';

interface Props extends /* @vue-ignore */ Record<string, any> {
    class?: HTMLAttributes['class'];
    viewportClass?: HTMLAttributes['class'];
}

const props = defineProps<Props>();
const emits = defineEmits<{
    (e: string, ...args: any[]): void;
}>();

const delegatedProps = computed(() => {
    const { class: _, viewportClass: __, ...delegated } = props;
    return delegated;
});

const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <NavigationMenuRoot
        v-bind="forwarded"
        :class="cn('relative z-50 flex max-w-max flex-1 items-center justify-center', $props.class)"
    >
        <slot />
        <div class="absolute left-0 top-full flex justify-center">
            <NavigationMenuViewport
                :class="cn(
                    'origin-top-center relative mt-2 h-[var(--reka-navigation-menu-viewport-height)] w-full overflow-hidden rounded-2xl border border-slate-200/90 bg-white text-slate-900 shadow-xl duration-200 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 md:w-[var(--reka-navigation-menu-viewport-width)]',
                    viewportClass
                )"
            />
        </div>
    </NavigationMenuRoot>
</template>
