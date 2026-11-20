<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue';
import { DialogTitle, type DialogTitleProps, useForwardProps } from 'reka-ui';
import { cn } from '@/lib/utils';

interface Props extends /* @vue-ignore */ DialogTitleProps {
    class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;
    return delegated;
});

const forwarded = useForwardProps(delegatedProps);
</script>

<template>
    <DialogTitle
        v-bind="forwarded"
        :class="cn('text-xl font-heading font-semibold leading-tight text-slate-900', $props.class)"
    >
        <slot />
    </DialogTitle>
</template>
