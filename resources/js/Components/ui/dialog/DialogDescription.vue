<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue';
import { DialogDescription, type DialogDescriptionProps, useForwardProps } from 'reka-ui';
import { cn } from '@/lib/utils';

interface Props extends /* @vue-ignore */ DialogDescriptionProps {
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
    <DialogDescription
        v-bind="forwarded"
        :class="cn('text-sm text-slate-500 font-sans', $props.class)"
    >
        <slot />
    </DialogDescription>
</template>
