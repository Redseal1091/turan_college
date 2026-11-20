<script setup lang="ts">
import { DialogTrigger, type DialogTriggerProps } from 'reka-ui';

interface Props extends /* @vue-ignore */ DialogTriggerProps {}

const props = defineProps<Props>();
</script>

<template>
    <DialogTrigger v-bind="props">
        <slot />
    </DialogTrigger>
</template>
