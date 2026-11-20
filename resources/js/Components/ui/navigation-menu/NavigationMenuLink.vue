<script setup lang="ts">
import { NavigationMenuLink, useForwardPropsEmits } from 'reka-ui';

interface Props extends /* @vue-ignore */ Record<string, any> {}

const props = defineProps<Props>();
const emits = defineEmits<{
    (e: string, ...args: any[]): void;
}>();

const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
    <NavigationMenuLink v-bind="forwarded">
        <slot />
    </NavigationMenuLink>
</template>
