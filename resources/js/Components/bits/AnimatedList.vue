<template>
    <div
        ref="containerRef"
        :class="[
            'relative w-full',
            className
        ]"
        tabindex="0"
        @keydown="handleKeyDown"
    >
        <!-- Top Gradient Fade (if enabled) -->
        <div
            v-if="showGradients"
            class="pointer-events-none absolute top-0 left-0 right-0 h-10 bg-gradient-to-b from-black/40 to-transparent z-10 transition-opacity duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
            :class="canScrollTop ? 'opacity-100' : 'opacity-0'"
        />

        <!-- List Items Container -->
        <div
            ref="listRef"
            @scroll="onScroll"
            :class="[
                'w-full space-y-3.5',
                maxHeight ? 'overflow-y-auto' : '',
                displayScrollbar ? 'scrollbar-thin scrollbar-thumb-white/20' : 'scrollbar-none'
            ]"
            :style="maxHeight ? { maxHeight } : {}"
        >
            <div
                v-for="(item, index) in items"
                :key="getItemKey(item, index)"
                :ref="(el) => setItemRef(el as HTMLElement, index)"
                @mouseenter="selectedIndex = index"
                :class="[
                    'animated-list-item relative transition-all duration-700 ease-[cubic-bezier(0.16,1,0.3,1)]',
                    itemClassName
                ]"
                :style="{
                    opacity: isVisible ? 1 : 0,
                    transform: isVisible ? 'translateY(0)' : 'translateY(24px)',
                    transitionDelay: `${Math.min(index * (staggerDelay * 1000), 500)}ms`
                }"
            >
                <slot
                    name="item"
                    :item="item"
                    :index="index"
                    :is-selected="selectedIndex === index"
                    :select="() => selectIndex(index)"
                >
                    <div
                        class="p-4 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors cursor-pointer"
                        @click="selectIndex(index)"
                    >
                        {{ typeof item === 'object' && item !== null ? item.title || item.label || JSON.stringify(item) : item }}
                    </div>
                </slot>
            </div>
        </div>

        <!-- Bottom Gradient Fade (if enabled) -->
        <div
            v-if="showGradients"
            class="pointer-events-none absolute bottom-0 left-0 right-0 h-10 bg-gradient-to-t from-black/40 to-transparent z-10 transition-opacity duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
            :class="canScrollBottom ? 'opacity-100' : 'opacity-0'"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue';

interface AnimatedListProps {
    items: any[];
    showGradients?: boolean;
    enableArrowNavigation?: boolean;
    displayScrollbar?: boolean;
    maxHeight?: string;
    className?: string;
    itemClassName?: string;
    staggerDelay?: number;
    keyField?: string;
}

const props = withDefaults(defineProps<AnimatedListProps>(), {
    showGradients: false,
    enableArrowNavigation: true,
    displayScrollbar: false,
    maxHeight: '',
    className: '',
    itemClassName: '',
    staggerDelay: 0.06,
    keyField: 'id',
});

const emit = defineEmits<{
    (e: 'select', item: any, index: number): void;
}>();

const containerRef = ref<HTMLDivElement | null>(null);
const listRef = ref<HTMLDivElement | null>(null);
const itemRefs = ref<HTMLElement[]>([]);
const selectedIndex = ref<number>(-1);
const isVisible = ref(false);
const canScrollTop = ref(false);
const canScrollBottom = ref(false);

const setItemRef = (el: HTMLElement | null, index: number) => {
    if (el) {
        itemRefs.value[index] = el;
    }
};

const getItemKey = (item: any, index: number) => {
    if (item && typeof item === 'object' && props.keyField in item) {
        return item[props.keyField];
    }
    return index;
};

const selectIndex = (index: number) => {
    if (index >= 0 && index < props.items.length) {
        selectedIndex.value = index;
        emit('select', props.items[index], index);
    }
};

const onScroll = () => {
    if (!listRef.value) return;
    const { scrollTop, scrollHeight, clientHeight } = listRef.value;
    canScrollTop.value = scrollTop > 10;
    canScrollBottom.value = scrollTop + clientHeight < scrollHeight - 10;
};

const handleKeyDown = (e: KeyboardEvent) => {
    if (!props.enableArrowNavigation || !props.items.length) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        const next = selectedIndex.value < props.items.length - 1 ? selectedIndex.value + 1 : 0;
        selectIndex(next);
        itemRefs.value[next]?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prev = selectedIndex.value > 0 ? selectedIndex.value - 1 : props.items.length - 1;
        selectIndex(prev);
        itemRefs.value[prev]?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    } else if (e.key === 'Home') {
        e.preventDefault();
        selectIndex(0);
        itemRefs.value[0]?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    } else if (e.key === 'End') {
        e.preventDefault();
        const last = props.items.length - 1;
        selectIndex(last);
        itemRefs.value[last]?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }
};

onMounted(() => {
    nextTick(() => {
        isVisible.value = true;
        onScroll();
    });
});
</script>
