<script setup lang="ts">
import { ref, onMounted } from 'vue';
import type { ImgHTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';
import Skeleton from '@/Components/ui/skeleton/Skeleton.vue';

interface Props extends /* @vue-ignore */ ImgHTMLAttributes {
    src?: string;
    alt?: string;
    class?: string;
    wrapperClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
    src: '',
    alt: '',
    class: '',
    wrapperClass: '',
});

const isLoaded = ref(false);
const hasError = ref(false);
const imgRef = ref<HTMLImageElement | null>(null);

const handleLoad = () => {
    isLoaded.value = true;
};

const handleError = () => {
    hasError.value = true;
    isLoaded.value = true;
};

onMounted(() => {
    if (imgRef.value?.complete && imgRef.value?.naturalWidth > 0) {
        isLoaded.value = true;
    }
});
</script>

<template>
    <div :class="cn('relative overflow-hidden', wrapperClass)">
        <!-- Skeleton Placeholder -->
        <Skeleton
            v-if="!isLoaded"
            class="absolute inset-0 w-full h-full rounded-[inherit] z-10"
        />

        <!-- Actual Image with smooth fade-in -->
        <img
            ref="imgRef"
            :src="src"
            :alt="alt"
            v-bind="$attrs"
            @load="handleLoad"
            @error="handleError"
            :class="cn(
                'transition-opacity duration-300 ease-out',
                isLoaded ? 'opacity-100' : 'opacity-0',
                $props.class
            )"
        />
    </div>
</template>
