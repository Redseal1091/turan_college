<script setup lang="ts">
import { DialogClose, type DialogCloseProps } from 'reka-ui';

interface Props extends /* @vue-ignore */ DialogCloseProps {}

const props = defineProps<Props>();
</script>

<template>
    <DialogClose v-bind="props">
        <slot />
    </DialogClose>
</template>
