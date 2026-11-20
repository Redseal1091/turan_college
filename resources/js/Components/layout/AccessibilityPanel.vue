<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Eye, RotateCcw, X, Check } from 'lucide-vue-next';
import { useUiStrings } from '@/composables/useUiStrings';
import {
    useAccessibility,
    type AccessibilityFontSize,
    type AccessibilityTheme,
    type AccessibilitySpacing,
    type AccessibilityImages,
} from '@/composables/useAccessibility';

const props = withDefaults(
    defineProps<{
        iconOnly?: boolean;
    }>(),
    {
        iconOnly: false,
    }
);

const { t } = useUiStrings();
const isOpen = ref(false);
const panelRef = ref<HTMLElement | null>(null);

const {
    settings,
    isEnabled,
    setEnabled,
    setFontSize,
    setTheme,
    setSpacing,
    setImages,
    reset,
} = useAccessibility();

const fontSizes: { label: string; value: AccessibilityFontSize; title: string }[] = [
    { label: 'A', value: 'normal', title: 'Обычный' },
    { label: 'A+', value: 'large', title: 'Увеличенный' },
    { label: 'A++', value: 'xlarge', title: 'Максимальный' },
];

const themes: { id: AccessibilityTheme; label: string; previewClass: string }[] = [
    { id: 'normal', label: 'Стандартная', previewClass: 'bg-white text-slate-800 border-slate-300' },
    { id: 'contrast', label: 'Черно-белая', previewClass: 'bg-black text-white border-white' },
    { id: 'dark', label: 'Темная', previewClass: 'bg-slate-900 text-slate-100 border-slate-700' },
    { id: 'yellow-blue', label: 'Сине-желтая', previewClass: 'bg-[#0A192F] text-[#FFD700] border-[#FFD700]' },
];

const handleOutsideClick = (event: MouseEvent) => {
    if (!panelRef.value) return;
    if (!panelRef.value.contains(event.target as Node)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleOutsideClick);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleOutsideClick);
});
</script>

<template>
    <div ref="panelRef" class="relative inline-block text-left">
        <!-- Trigger Button -->
        <button
            v-if="iconOnly"
            type="button"
            @click="isOpen = !isOpen"
            class="a11y-trigger-btn flex items-center justify-center p-1 rounded-md text-[#111111] hover:text-[#0082C9] transition-colors cursor-pointer"
            :class="isEnabled ? 'text-[#0082C9]' : 'text-[#111111]'"
            :aria-expanded="isOpen"
            :title="t('a11y_btn', 'Версия для слабовидящих')"
            aria-label="Версия для слабовидящих"
        >
            <Eye class="w-5 h-5 a11y-keep" />
        </button>

        <button
            v-else
            type="button"
            @click="isOpen = !isOpen"
            class="a11y-trigger-btn flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium transition-all duration-200 cursor-pointer border"
            :class="[
                isEnabled
                    ? 'bg-[#0F4C3A] text-white border-[#0F4C3A] ring-2 ring-[#0F4C3A]/30'
                    : 'bg-white/80 hover:bg-white text-slate-700 hover:text-slate-900 border-slate-200/80 backdrop-blur-md'
            ]"
            :aria-expanded="isOpen"
            title="Версия для слабовидящих"
        >
            <Eye class="w-3.5 h-3.5 a11y-keep" :class="isEnabled ? 'text-emerald-300' : 'text-[#0F4C3A]'" />
            <span class="hidden md:inline">{{ t('a11y_btn', 'Версия для слабовидящих') }}</span>
            <span class="md:hidden">A11y</span>
        </button>

        <!-- Dropdown / Popover -->
        <div
            v-if="isOpen"
            class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl glass-card bg-white/95 backdrop-blur-2xl border border-slate-200/90 p-5 z-50 text-slate-900 font-sans animate-in fade-in-50 zoom-in-95"
            style="color-scheme: light;"
        >
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-[#0F4C3A]/10 text-[#0F4C3A] flex items-center justify-center">
                        <Eye class="w-4 h-4 a11y-keep" />
                    </div>
                    <span class="font-heading font-bold text-sm text-slate-900">
                        {{ t('a11y_title', 'Панель доступности') }}
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="reset"
                        class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
                        title="Сбросить настройки"
                    >
                        <RotateCcw class="w-4 h-4 a11y-keep" />
                    </button>
                    <button
                        type="button"
                        @click="isOpen = false"
                        class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
                    >
                        <X class="w-4 h-4 a11y-keep" />
                    </button>
                </div>
            </div>

            <!-- Master Toggle -->
            <div class="mt-4 flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/60">
                <div>
                    <div class="text-xs font-semibold text-slate-900">Режим доступности</div>
                    <div class="text-[11px] text-slate-500">Увеличенные шрифты и контраст</div>
                </div>
                <button
                    type="button"
                    @click="setEnabled(!settings.enabled)"
                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden"
                    :class="settings.enabled ? 'bg-[#0F4C3A]' : 'bg-slate-300'"
                >
                    <span
                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white border border-slate-200 ring-0 transition duration-200 ease-in-out"
                        :class="settings.enabled ? 'translate-x-5' : 'translate-x-0'"
                    />
                </button>
            </div>

            <div class="mt-4 space-y-4">
                <!-- Font Size -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">
                        {{ t('a11y_font_size', 'Размер шрифта') }}
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <button
                            v-for="opt in fontSizes"
                            :key="opt.value"
                            type="button"
                            @click="setFontSize(opt.value)"
                            class="py-2 px-3 rounded-lg border text-center font-bold transition-all cursor-pointer"
                            :class="[
                                settings.fontSize === opt.value
                                    ? 'bg-[#0F4C3A] text-white border-[#0F4C3A]'
                                    : 'bg-white hover:bg-slate-50 border-slate-200 text-slate-700'
                            ]"
                        >
                            {{ opt.label }}
                        </button>
                    </div>
                </div>

                <!-- Theme / Contrast -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">
                        {{ t('a11y_theme', 'Цветовая схема') }}
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            v-for="th in themes"
                            :key="th.id"
                            type="button"
                            @click="setTheme(th.id)"
                            class="p-2.5 rounded-xl border text-left flex items-center justify-between text-xs font-medium transition-all cursor-pointer"
                            :class="[
                                settings.theme === th.id
                                    ? 'ring-2 ring-[#0F4C3A] border-transparent font-bold'
                                    : 'border-slate-200',
                                th.previewClass
                            ]"
                        >
                            <span>{{ th.label }}</span>
                            <Check v-if="settings.theme === th.id" class="w-3.5 h-3.5 a11y-keep" />
                        </button>
                    </div>
                </div>

                <!-- Letter Spacing -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">
                        {{ t('a11y_spacing', 'Интервал между буквами') }}
                    </label>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <button
                            type="button"
                            @click="setSpacing('normal')"
                            class="py-2 px-3 rounded-lg border font-medium transition-all cursor-pointer"
                            :class="settings.spacing === 'normal' ? 'bg-[#0F4C3A] text-white border-[#0F4C3A]' : 'bg-white hover:bg-slate-50 border-slate-200 text-slate-700'"
                        >
                            {{ t('a11y_spacing_normal', 'Обычный') }}
                        </button>
                        <button
                            type="button"
                            @click="setSpacing('increased')"
                            class="py-2 px-3 rounded-lg border font-medium tracking-wider transition-all cursor-pointer"
                            :class="settings.spacing === 'increased' ? 'bg-[#0F4C3A] text-white border-[#0F4C3A]' : 'bg-white hover:bg-slate-50 border-slate-200 text-slate-700'"
                        >
                            {{ t('a11y_spacing_increased', 'Увеличенный') }}
                        </button>
                    </div>
                </div>

                <!-- Images Toggle -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">
                        {{ t('a11y_images', 'Изображения') }}
                    </label>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <button
                            type="button"
                            @click="setImages('on')"
                            class="py-2 px-3 rounded-lg border font-medium transition-all cursor-pointer"
                            :class="settings.images === 'on' ? 'bg-[#0F4C3A] text-white border-[#0F4C3A]' : 'bg-white hover:bg-slate-50 border-slate-200 text-slate-700'"
                        >
                            {{ t('a11y_images_on', 'Включены') }}
                        </button>
                        <button
                            type="button"
                            @click="setImages('off')"
                            class="py-2 px-3 rounded-lg border font-medium transition-all cursor-pointer"
                            :class="settings.images === 'off' ? 'bg-[#0F4C3A] text-white border-[#0F4C3A]' : 'bg-white hover:bg-slate-50 border-slate-200 text-slate-700'"
                        >
                            {{ t('a11y_images_off', 'Отключены') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
