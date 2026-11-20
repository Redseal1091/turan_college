<script setup lang="ts">
import { ref, computed, watch, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import Container from '@/Components/shared/Container.vue';
import { useUiStrings } from '@/composables/useUiStrings';
import { Menu, X, ArrowRight, Mail, Phone, Search, ChevronDown } from 'lucide-vue-next';

interface NavSubItem {
    title: string;
    href: string;
}

interface MobileNavItem {
    title: string;
    href: string;
    subitems?: NavSubItem[];
}

const props = withDefaults(
    defineProps<{
        darkText?: boolean;
    }>(),
    {
        darkText: false,
    }
);

const isMobileMenuOpen = ref(false);
const searchQuery = ref('');
const expandedItems = ref<Record<string, boolean>>({});

// Desktop sliding submenu state
const activeMenuIndex = ref<number | null>(null);
const triggerElements = ref<(HTMLElement | null)[]>([]);
const navRootRef = ref<HTMLElement | null>(null);
const popoverLeft = ref(0);
let closeTimer: ReturnType<typeof setTimeout> | null = null;

const setTriggerRef = (el: any, index: number) => {
    if (el) {
        triggerElements.value[index] = (el as any).$el || el;
    }
};

const openSubmenu = (index: number) => {
    if (closeTimer) {
        clearTimeout(closeTimer);
        closeTimer = null;
    }
    const item = detailedNavItems.value[index];
    if (!item?.subitems || !item.subitems.length) {
        activeMenuIndex.value = null;
        return;
    }

    activeMenuIndex.value = index;

    const triggerEl = triggerElements.value[index];
    const navEl = navRootRef.value;
    if (triggerEl && navEl) {
        const triggerRect = triggerEl.getBoundingClientRect();
        const navRect = navEl.getBoundingClientRect();
        const popoverWidth = 460;
        let left = triggerRect.left - navRect.left + (triggerRect.width / 2) - (popoverWidth / 2);
        if (left < 0) left = 0;
        const maxLeft = navRect.width - popoverWidth;
        if (left > maxLeft && maxLeft > 0) left = maxLeft;
        popoverLeft.value = Math.round(left);
    }
};

const scheduleCloseSubmenu = () => {
    if (closeTimer) clearTimeout(closeTimer);
    closeTimer = setTimeout(() => {
        activeMenuIndex.value = null;
    }, 180);
};

const cancelCloseSubmenu = () => {
    if (closeTimer) {
        clearTimeout(closeTimer);
        closeTimer = null;
    }
};

const closeSubmenuImmediately = () => {
    if (closeTimer) clearTimeout(closeTimer);
    activeMenuIndex.value = null;
};

const page = usePage();
const { t, locale } = useUiStrings();

const languages = [
    { code: 'ru', label: 'Рус' },
    { code: 'kz', label: 'Қаз' },
    { code: 'en', label: 'Eng' },
];

const switchLocale = (newLocale: string) => {
    if (newLocale === locale.value) return;
    router.get(`/locale/${newLocale}`, {}, {
        preserveScroll: true,
        preserveState: false,
    });
};

const handleSearch = () => {
    if (!searchQuery.value.trim()) return;
    isMobileMenuOpen.value = false;
    router.get('/news', { q: searchQuery.value.trim() }, {
        preserveScroll: true,
    });
};

const toggleExpand = (title: string) => {
    expandedItems.value[title] = !expandedItems.value[title];
};

const navItems = computed(() => [
    { title: t('nav_about', 'Колледж'), href: '/about' },
    { title: t('nav_specialties', 'Специальности'), href: '/specialties' },
    { title: t('nav_admission', 'Абитуриенту'), href: '/admission' },
    { title: t('nav_student', 'Студенту'), href: '/student' },
    { title: t('nav_career', 'Карьера'), href: '/career' },
    { title: t('nav_news', 'Новости'), href: '/news' },
    { title: t('nav_contacts', 'Контакты'), href: '/contacts' },
]);

const detailedNavItems = computed<MobileNavItem[]>(() => [
    {
        title: t('nav_about', 'Колледж'),
        href: '/about',
        subitems: [
            { title: 'Про колледж', href: '/about' },
            { title: 'Педагогический состав', href: '/about/teachers' },
            { title: 'Руководство и администрация', href: '/about#leadership' },
            { title: 'Лицензии и документы', href: '/about#documents' },
        ],
    },
    {
        title: t('nav_specialties', 'Специальности'),
        href: '/specialties',
        subitems: [
            { title: 'Все специальности', href: '/specialties' },
            { title: 'Программное обеспечение (IT)', href: '/specialties#it' },
            { title: 'Маркетинг и экономика', href: '/specialties#marketing' },
            { title: 'Туризм и сервис', href: '/specialties#tourism' },
            { title: 'Правоведение', href: '/specialties#law' },
            { title: 'Переводческое дело', href: '/specialties#translation' },
        ],
    },
    {
        title: t('nav_admission', 'Абитуриенту'),
        href: '/admission',
        subitems: [
            { title: 'Правила приема 2026', href: '/admission' },
            { title: 'Перечень документов', href: '/admission#documents' },
            { title: 'Государственные гранты', href: '/admission#grants' },
            { title: 'Стоимость обучения', href: '/admission#tuition' },
            { title: 'Общежитие', href: '/admission#campus' },
        ],
    },
    {
        title: t('nav_student', 'Студенту'),
        href: '/student',
        subitems: [
            { title: 'Студенческая жизнь', href: '/student' },
            { title: 'Расписание занятий', href: '/student#schedule' },
            { title: 'Сессия и аттестация', href: '/student#session' },
        ],
    },
    {
        title: t('nav_career', 'Карьера'),
        href: '/career',
        subitems: [
            { title: 'Базы практики', href: '/career' },
            { title: 'Партнеры и работодатели', href: '/career#partners' },
        ],
    },
    {
        title: t('nav_news', 'Новости'),
        href: '/news',
    },
    {
        title: t('nav_contacts', 'Контакты'),
        href: '/contacts',
        subitems: [
            { title: 'Контакты и адрес', href: '/contacts' },
            { title: 'Приемная комиссия', href: '/contacts#admission' },
        ],
    },
]);

const isActive = (href: string) => {
    if (href === '/') return page.url === '/';
    return page.url.startsWith(href);
};

// Body scroll lock on mobile popup open
watch(isMobileMenuOpen, (isOpen) => {
    if (typeof window === 'undefined' || typeof document === 'undefined') return;
    if (isOpen) {
        const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        if (scrollbarWidth > 0) {
            document.body.style.paddingRight = `${scrollbarWidth}px`;
        }
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }
});

onUnmounted(() => {
    if (typeof document !== 'undefined') {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }
});
</script>

<template>
    <div class="w-full relative z-30 pt-4 sm:pt-6 lg:pt-8">
        <!-- Brand + Nav wrapped in site-wide standard Container -->
        <Container class="flex items-center justify-between gap-3 sm:gap-4">
            <!-- Brand Logo: Square box with logo_college_turan.svg, Gotham Title, PTsans Subtitle -->
            <Link href="/" class="flex items-center gap-2.5 sm:gap-3.5 group shrink-0">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 lg:w-[64px] lg:h-[64px] bg-white flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-200 aspect-square"
                    :class="darkText ? 'border border-slate-200/90 shadow-sm' : ''"
                >
                    <img
                        src="/images/logo_college_turan.svg"
                        alt="Turan College Logo"
                        class="w-full h-full object-contain"
                    />
                </div>
                <div>
                    <div
                        class="font-gotham font-black text-base sm:text-xl lg:text-2xl tracking-wider uppercase leading-none transition-colors"
                        :class="darkText ? 'text-slate-900' : 'text-white'"
                    >
                        TURAN COLLEGE
                    </div>
                    <div
                        class="font-['PT_Sans',sans-serif] text-[10px] sm:text-[11px] lg:text-[12px] italic mt-0.5 sm:mt-1 font-normal tracking-tight line-clamp-1 transition-colors"
                        :class="darkText ? 'text-slate-500' : 'text-slate-300'"
                    >
                        {{ t('brand_slogan', 'Для тех, кто верит в себя и стремится к успеху!') }}
                    </div>
                </div>
            </Link>

            <!-- Desktop Navigation Menu with Smooth Sliding Submenu -->
            <div
                ref="navRootRef"
                class="hidden xl:flex items-center relative"
                @mouseleave="scheduleCloseSubmenu"
            >
                <nav class="flex items-center gap-1">
                    <div
                        v-for="(item, idx) in detailedNavItems"
                        :key="item.href"
                        :ref="el => setTriggerRef(el, idx)"
                        class="relative"
                        @mouseenter="openSubmenu(idx)"
                    >
                        <!-- Item with Sub-items: Click navigates, Hover opens/slides submenu -->
                        <template v-if="item.subitems && item.subitems.length">
                            <Link
                                :href="item.href"
                                prefetch="hover"
                                :class="[
                                    'group inline-flex h-9 w-max items-center justify-center rounded-lg px-3.5 py-1.5 text-sm font-heading font-medium transition-all duration-200 select-none cursor-pointer',
                                    darkText
                                        ? (isActive(item.href) || activeMenuIndex === idx
                                            ? 'text-slate-900 bg-slate-100 font-bold'
                                            : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100')
                                        : (isActive(item.href) || activeMenuIndex === idx
                                            ? 'text-white bg-white/15 font-bold'
                                            : 'text-white/90 hover:text-white hover:bg-white/10')
                                ]"
                            >
                                <span>{{ item.title }}</span>
                                <ChevronDown
                                    class="relative top-[1px] ml-1.5 h-3.5 w-3.5 transition-transform duration-200"
                                    :class="activeMenuIndex === idx ? 'rotate-180' : ''"
                                    aria-hidden="true"
                                />
                            </Link>
                        </template>

                        <!-- Single Direct Link without Sub-items -->
                        <template v-else>
                            <Link
                                :href="item.href"
                                prefetch="hover"
                                @mouseenter="closeSubmenuImmediately"
                                :class="[
                                    'group inline-flex h-9 w-max items-center justify-center rounded-lg px-3.5 py-1.5 text-sm font-heading font-medium transition-colors select-none',
                                    darkText
                                        ? (isActive(item.href) ? 'text-[#0082C9] font-bold bg-slate-100' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-100')
                                        : (isActive(item.href) ? 'text-white font-bold bg-white/15' : 'text-white/90 hover:text-white hover:bg-white/10')
                                ]"
                            >
                                {{ item.title }}
                            </Link>
                        </template>
                    </div>
                </nav>

                <!-- Sliding Submenu Popover (Smooth gliding between items) -->
                <div
                    v-if="activeMenuIndex !== null && detailedNavItems[activeMenuIndex]?.subitems"
                    class="absolute top-full left-0 pt-2 z-50 transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]"
                    :style="{
                        transform: `translateX(${popoverLeft}px)`,
                    }"
                    @mouseenter="cancelCloseSubmenu"
                    @mouseleave="scheduleCloseSubmenu"
                >
                    <div class="w-[460px] overflow-hidden rounded-2xl border border-slate-200/90 bg-white p-2.5 text-slate-900 shadow-xl transition-all duration-300">
                        <ul class="grid grid-cols-2 gap-1">
                            <li v-for="sub in detailedNavItems[activeMenuIndex].subitems" :key="sub.href">
                                <Link
                                    :href="sub.href"
                                    prefetch="hover"
                                    @click="closeSubmenuImmediately"
                                    class="block select-none rounded-xl p-2.5 leading-none no-underline outline-none transition-colors hover:bg-slate-100 hover:text-slate-900 group"
                                >
                                    <div class="text-sm font-heading font-semibold text-slate-800 group-hover:text-[#0082C9] transition-colors leading-tight">
                                        {{ sub.title }}
                                    </div>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Mobile Burger Button -->
            <div class="xl:hidden flex items-center gap-2">
                <button
                    @click="isMobileMenuOpen = true"
                    type="button"
                    class="p-2 sm:p-2.5 rounded-xl active:scale-95 transition-all cursor-pointer"
                    :class="darkText ? 'text-slate-800 bg-slate-100 hover:bg-slate-200' : 'text-white bg-white/10 hover:bg-white/20'"
                    aria-label="Открыть мобильное меню"
                >
                    <Menu class="w-6 h-6" />
                </button>
            </div>
        </Container>

        <!-- =================================================================
             MOBILE MENU: Fullscreen Popup Modal (Попапом, а не дропдауном)
             ================================================================= -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 translate-y-3"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-y-3"
            >
                <div
                    v-if="isMobileMenuOpen"
                    class="fixed inset-0 z-[100] bg-[#0c1219]/96 backdrop-blur-2xl text-white flex flex-col justify-between overflow-hidden"
                >
                    <!-- 1. Top Bar -->
                    <div class="p-4 sm:p-6 flex items-center justify-between gap-4 shrink-0">
                        <Link href="/" @click="isMobileMenuOpen = false" class="flex items-center gap-3 group">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white flex items-center justify-center shrink-0 aspect-square rounded-xl">
                                <img
                                    src="/images/logo_college_turan.svg"
                                    alt="Turan Logo"
                                    class="w-full h-full object-contain"
                                />
                            </div>
                            <div>
                                <div class="font-gotham font-black text-base sm:text-lg tracking-wider text-white uppercase leading-none">
                                    TURAN COLLEGE
                                </div>
                                <div class="font-['PT_Sans',sans-serif] text-[10px] sm:text-[11px] text-slate-300 italic mt-0.5 font-normal tracking-tight line-clamp-1">
                                    {{ t('brand_slogan', 'Для тех, кто верит в себя и стремится к успеху!') }}
                                </div>
                            </div>
                        </Link>

                        <button
                            type="button"
                            @click="isMobileMenuOpen = false"
                            class="p-2 sm:px-4 sm:py-2 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center gap-1.5 cursor-pointer active:scale-95 transition-all"
                            aria-label="Закрыть меню"
                        >
                            <span class="text-xs font-heading font-semibold uppercase hidden sm:inline">Закрыть</span>
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- 2. Scrollable Content Area -->
                    <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4">
                        <!-- 2.1 Search Input (Строка поиска без бордеров) -->
                        <form @submit.prevent="handleSearch" class="relative">
                            <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <input
                                v-model="searchQuery"
                                type="search"
                                placeholder="Поиск по сайту..."
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white/[0.07] text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#0082C9] focus:bg-white/10 transition-all border-0"
                            />
                        </form>

                        <!-- 2.2 Language Selector (Компактный свитчер языков) -->
                        <div class="flex items-center justify-between px-1">
                            <span class="text-xs font-heading text-slate-400 uppercase tracking-wider">Язык</span>
                            <div class="inline-flex items-center p-0.5 bg-white/[0.07] rounded-full">
                                <button
                                    v-for="lang in languages"
                                    :key="lang.code"
                                    type="button"
                                    @click="switchLocale(lang.code)"
                                    class="py-1 px-3 rounded-full text-xs font-heading font-semibold transition-all duration-200 cursor-pointer text-center"
                                    :class="locale === lang.code ? 'bg-[#0082C9] text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                                >
                                    {{ lang.label }}
                                </button>
                            </div>
                        </div>

                        <!-- 2.3 Menu items with sub-items (Пункты меню с подпунктами) -->
                        <nav class="space-y-1.5 pt-1">
                            <div
                                v-for="item in detailedNavItems"
                                :key="item.href"
                                class="rounded-2xl overflow-hidden bg-white/[0.04]"
                            >
                                <div class="flex items-center justify-between">
                                    <Link
                                        :href="item.href"
                                        prefetch="hover"
                                        @click="isMobileMenuOpen = false"
                                        :class="[
                                            'flex-1 py-3 px-4 text-base font-heading font-medium transition-colors',
                                            isActive(item.href)
                                                ? 'text-[#0082C9] font-bold'
                                                : 'text-white hover:text-slate-200'
                                        ]"
                                    >
                                        {{ item.title }}
                                    </Link>

                                    <!-- Accordion Toggle Arrow if subitems exist -->
                                    <button
                                        v-if="item.subitems && item.subitems.length"
                                        type="button"
                                        @click="toggleExpand(item.title)"
                                        class="p-3 text-slate-400 hover:text-white transition-colors cursor-pointer"
                                        :aria-expanded="expandedItems[item.title]"
                                    >
                                        <ChevronDown
                                            class="w-4 h-4 transition-transform duration-300"
                                            :class="expandedItems[item.title] ? 'rotate-180 text-[#FDA685]' : ''"
                                        />
                                    </button>
                                </div>

                                <!-- Sub-items Accordion Drop с увеличенным размером шрифта -->
                                <div
                                    v-if="item.subitems && item.subitems.length && expandedItems[item.title]"
                                    class="px-2 pb-2 pt-0.5 space-y-1 bg-white/[0.03] animate-in slide-in-from-top-1"
                                >
                                    <Link
                                        v-for="sub in item.subitems"
                                        :key="sub.href"
                                        :href="sub.href"
                                        prefetch="hover"
                                        @click="isMobileMenuOpen = false"
                                        class="block py-2.5 px-3.5 text-base sm:text-[17px] text-slate-200 hover:text-white hover:bg-white/[0.08] rounded-xl transition-colors font-sans"
                                    >
                                        {{ sub.title }}
                                    </Link>
                                </div>
                            </div>
                        </nav>

                        <!-- 2.4 Admissions Contacts (Контакты без бордеров и без заголовка) -->
                        <div class="pt-3 space-y-2.5 font-sans">
                            <a
                                href="tel:+77272604035"
                                class="flex items-center gap-3 text-white hover:text-[#FDA685] transition-colors p-2.5 rounded-2xl bg-white/[0.04] hover:bg-white/[0.08] group"
                            >
                                <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center shrink-0 group-hover:bg-white/20">
                                    <Phone class="w-4 h-4 text-[#FDA685]" />
                                </div>
                                <div>
                                    <div class="font-heading font-semibold text-sm sm:text-base">+7 (727) 260-40-35</div>
                                    <div class="text-xs text-slate-400">Пн-Пт: 09:00 - 18:00</div>
                                </div>
                            </a>
                            <a
                                href="mailto:turan_college@mail.ru"
                                class="flex items-center gap-3 text-white hover:text-[#0082C9] transition-colors p-2.5 rounded-2xl bg-white/[0.04] hover:bg-white/[0.08] group"
                            >
                                <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center shrink-0 group-hover:bg-white/20">
                                    <Mail class="w-4 h-4 text-[#0082C9]" />
                                </div>
                                <div>
                                    <div class="font-heading font-semibold text-sm sm:text-base">turan_college@mail.ru</div>
                                    <div class="text-xs text-slate-400">Официальная почта</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
