<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Container from '@/Components/shared/Container.vue';
import AccessibilityPanel from '@/Components/layout/AccessibilityPanel.vue';
import { useUiStrings } from '@/composables/useUiStrings';
import { Mail } from 'lucide-vue-next';

const { locale } = useUiStrings();
const isLangOpen = ref(false);
const langDropdownRef = ref<HTMLElement | null>(null);

const topEcosystem = [
    { title: 'Университет', href: 'https://turan-edu.kz', external: true },
    { title: 'Туран-Астана', href: '#', external: false },
    { title: 'Колледж', href: '/', external: false, current: true },
    { title: 'Колледж-Астана', href: '#', external: false },
    { title: 'Школа-лицей', href: '#', external: false },
    { title: 'Тау-Туран', href: '#', external: false },
];

const languages = [
    { code: 'ru', label: 'Рус' },
    { code: 'kz', label: 'Қаз' },
    { code: 'en', label: 'Eng' },
];

const currentLanguage = computed(() => {
    return languages.find((l) => l.code === locale.value) || languages[0];
});

const switchLocale = (newLocale: string) => {
    isLangOpen.value = false;
    if (newLocale === locale.value) return;
    router.get(`/locale/${newLocale}`, {}, {
        preserveScroll: true,
        preserveState: false,
    });
};

const handleOutsideClick = (event: MouseEvent) => {
    if (langDropdownRef.value && !langDropdownRef.value.contains(event.target as Node)) {
        isLangOpen.value = false;
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
    <div class="hidden lg:block w-full bg-transparent text-[#111111] py-3.5 z-40 relative">
        <Container class="flex items-center justify-between">
            <!-- 1. Ecosystem / Corporate Links (Left Side) -->
            <!-- Montserrat Regular, color #111111, font height 16px, gap 34px -->
            <nav
                class="hidden lg:flex items-center gap-[34px] font-['Montserrat',sans-serif] font-normal text-[16px] leading-[16px] text-[#111111]"
                aria-label="Корпоративное меню"
            >
                <template v-for="(item, idx) in topEcosystem" :key="idx">
                    <a
                        v-if="item.external"
                        :href="item.href"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hover:text-[#0082C9] transition-colors"
                    >
                        {{ item.title }}
                    </a>
                    <Link
                        v-else-if="item.current"
                        :href="item.href"
                        class="hover:text-[#0082C9] transition-colors"
                    >
                        {{ item.title }}
                    </Link>
                    <a
                        v-else
                        :href="item.href"
                        class="hover:text-[#0082C9] transition-colors"
                    >
                        {{ item.title }}
                    </a>
                </template>
            </nav>

            <!-- Mobile fallback for ecosystem (collapsed or hidden, keeping clean layout) -->
            <div class="lg:hidden text-xs text-slate-500 font-['Montserrat',sans-serif]">
                <span>Туран</span>
            </div>

            <!-- 2. Right Side Controls -->
            <!-- Order: Blue Mail (PT Sans) -> Lang Switcher (Flag box + Locale) -> Eye Icon only -->
            <div class="flex items-center gap-5 sm:gap-7">
                <!-- 2.1 Blue Mail Link (PT Sans) -->
                <a
                    href="mailto:turan_college@mail.ru"
                    class="flex items-center gap-2 text-[#0082C9] hover:underline font-['PT_Sans',sans-serif] text-[15px] sm:text-[16px] transition-colors"
                >
                    <Mail class="w-4 h-4 text-[#0082C9] shrink-0" />
                    <span>turan_college@mail.ru</span>
                </a>

                <!-- 2.2 Language Switcher with Flag Square and Locale -->
                <div ref="langDropdownRef" class="relative inline-block text-left">
                    <button
                        type="button"
                        @click="isLangOpen = !isLangOpen"
                        class="flex items-center gap-2 cursor-pointer font-['Montserrat',sans-serif] text-[16px] leading-[16px] text-[#111111] hover:text-[#0082C9] transition-colors py-1"
                        :aria-expanded="isLangOpen"
                        title="Выбрать язык"
                    >
                        <!-- Flag Box (Square 25x25, border 1 #E5E5E5, rounded 3) -->
                        <span class="inline-flex items-center justify-center w-[25px] h-[25px] rounded-[3px] overflow-hidden border border-[#E5E5E5] shrink-0 bg-white">
                            <!-- Russian Flag -->
                            <svg v-if="currentLanguage.code === 'ru'" class="w-full h-full" viewBox="0 0 25 25" preserveAspectRatio="none">
                                <rect width="25" height="8.33" fill="#ffffff" />
                                <rect y="8.33" width="25" height="8.34" fill="#0039A6" />
                                <rect y="16.67" width="25" height="8.33" fill="#D52B1E" />
                            </svg>
                            <!-- Kazakh Flag -->
                            <svg v-else-if="currentLanguage.code === 'kz'" class="w-full h-full" viewBox="0 0 25 25" preserveAspectRatio="xMidYMid slice">
                                <rect width="25" height="25" fill="#00AFCA" />
                                <circle cx="12.5" cy="12.5" r="4.5" fill="#FEC50C" />
                                <path d="M7 16 Q12.5 14 18 16 Q12.5 17.5 7 16 Z" fill="#FEC50C" />
                                <rect x="1" y="2" width="2.5" height="21" fill="#FEC50C" rx="0.5" />
                            </svg>
                            <!-- English Flag -->
                            <svg v-else class="w-full h-full" viewBox="0 0 25 25" preserveAspectRatio="none">
                                <rect width="25" height="25" fill="#012169" />
                                <path d="M0 0 L25 25 M25 0 L0 25" stroke="#fff" stroke-width="3" />
                                <path d="M0 0 L25 25 M25 0 L0 25" stroke="#C8102E" stroke-width="1.5" />
                                <path d="M12.5 0 V25 M0 12.5 H25" stroke="#fff" stroke-width="5" />
                                <path d="M12.5 0 V25 M0 12.5 H25" stroke="#C8102E" stroke-width="3" />
                            </svg>
                        </span>

                        <!-- Locale text (Semibold) -->
                        <span class="font-semibold">{{ currentLanguage.label }}</span>
                    </button>

                    <!-- Language Dropdown -->
                    <div
                        v-if="isLangOpen"
                        class="absolute right-0 mt-1.5 w-32 rounded-xl bg-white/95 backdrop-blur-xl border border-slate-200/90 py-1 z-50 font-['Montserrat',sans-serif] animate-in fade-in-50 zoom-in-95"
                    >
                        <button
                            v-for="l in languages"
                            :key="l.code"
                            type="button"
                            @click="switchLocale(l.code)"
                            class="w-full flex items-center gap-2.5 px-3 py-2 text-left hover:bg-slate-50 transition-colors cursor-pointer text-sm"
                            :class="locale === l.code ? 'text-[#0082C9] font-bold bg-sky-50/60' : 'text-[#111111]'"
                        >
                            <span class="inline-flex items-center justify-center w-[25px] h-[25px] rounded-[3px] overflow-hidden border border-[#E5E5E5] shrink-0 bg-white">
                                <svg v-if="l.code === 'ru'" class="w-full h-full" viewBox="0 0 25 25" preserveAspectRatio="none">
                                    <rect width="25" height="8.33" fill="#ffffff" />
                                    <rect y="8.33" width="25" height="8.34" fill="#0039A6" />
                                    <rect y="16.67" width="25" height="8.33" fill="#D52B1E" />
                                </svg>
                                <svg v-else-if="l.code === 'kz'" class="w-full h-full" viewBox="0 0 25 25" preserveAspectRatio="xMidYMid slice">
                                    <rect width="25" height="25" fill="#00AFCA" />
                                    <circle cx="12.5" cy="12.5" r="4.5" fill="#FEC50C" />
                                    <path d="M7 16 Q12.5 14 18 16 Q12.5 17.5 7 16 Z" fill="#FEC50C" />
                                    <rect x="1" y="2" width="2.5" height="21" fill="#FEC50C" rx="0.5" />
                                </svg>
                                <svg v-else class="w-full h-full" viewBox="0 0 25 25" preserveAspectRatio="none">
                                    <rect width="25" height="25" fill="#012169" />
                                    <path d="M0 0 L25 25 M25 0 L0 25" stroke="#fff" stroke-width="3" />
                                    <path d="M0 0 L25 25 M25 0 L0 25" stroke="#C8102E" stroke-width="1.5" />
                                    <path d="M12.5 0 V25 M0 12.5 H25" stroke="#fff" stroke-width="5" />
                                    <path d="M12.5 0 V25 M0 12.5 H25" stroke="#C8102E" stroke-width="3" />
                                </svg>
                            </span>
                            <span :class="locale === l.code ? 'font-bold' : 'font-semibold'">{{ l.label }}</span>
                        </button>
                    </div>
                </div>

                <!-- 2.3 Eye Icon for visually impaired (Only icon!) -->
                <AccessibilityPanel :icon-only="true" />
            </div>
        </Container>
    </div>
</template>

