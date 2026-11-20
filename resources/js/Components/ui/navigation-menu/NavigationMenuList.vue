<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue';
import { NavigationMenuList, type NavigationMenuListProps, useForwardProps } from 'reka-ui';
import { cn } from '@/lib/utils';

interface Props extends /* @vue-ignore */ NavigationMenuListProps {
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
    <NavigationMenuList
        v-bind="forwarded"
        :class="cn('group flex flex-1 list-none items-center justify-center space-x-1', $props.class)"
    >
        <slot />
    </NavigationMenuList>
</template>
