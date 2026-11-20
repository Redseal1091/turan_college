<script setup lang="ts">
import { DialogRoot, useForwardPropsEmits } from 'reka-ui';

interface Props extends /* @vue-ignore */ Record<string, any> {
    open?: boolean;
    defaultOpen?: boolean;
    modal?: boolean;
}

const props = defineProps<Props>();
const emits = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
    <DialogRoot v-bind="forwarded">
        <slot />
    </DialogRoot>
</template>
