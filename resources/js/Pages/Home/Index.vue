<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/shared/Container.vue';
import IntroCard from '@/Components/layout/IntroCard.vue';
import Button from '@/Components/ui/button/Button.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import LazyImage from '@/Components/ui/image/LazyImage.vue';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
} from '@/Components/ui/dialog';
import { Field, FieldLabel } from '@/Components/ui/field';
import Input from '@/Components/ui/input/Input.vue';
import PhoneInput from '@/Components/ui/input/PhoneInput.vue';
import { isPhoneComplete } from '@/composables/usePhoneMask';
import SpinningText from '@/Components/ui/spinning-text/SpinningText.vue';
import { SpotlightCard, CountUp, SpecularButton, AnimatedList } from '@/Components/bits';
import { getSpecialtyImage } from '@/lib/specialtyImages';
import { useUiStrings } from '@/composables/useUiStrings';
import { useApplyModal } from '@/composables/useApplyModal';
import {
    GraduationCap,
    Award,
    Building2,
    Users,
    ArrowRight,
    ArrowUpRight,
    ArrowUp,
    ArrowDown,
    Play,
    X,
    Send,
    CheckCircle2,
    ChevronDown,
    ChevronUp,
    ChevronLeft,
    ChevronRight,
    Plus,
    Minus,
    HelpCircle
} from 'lucide-vue-next';

interface Specialty {
    id: number;
    code: string;
    title: string;
    slug: string;
    qualification: string;
    department: string;
    duration: string;
    tuition_fee: number;
    grants_count: number;
    description: string;
    skills: string[];
}

interface NewsItem {
    id: number;
    title: string;
    slug: string;
    category: string;
    published_at: string;
    excerpt: string;
    image_url: string;
}

interface PartnerItem {
    id: number;
    name: string;
    industry: string;
    logo_url?: string;
}

interface FaqItem {
    id: number;
    question: string;
    answer: string;
    category: string;
}

const props = defineProps<{
    featuredSpecialties: Specialty[];
    latestNews: NewsItem[];
    partners: PartnerItem[];
    faqs: FaqItem[];
    stats: {
        employment_rate: string;
        graduates_count: string;
        grants_yearly: string;
        partner_companies: string;
    };
}>();

const { t, locale } = useUiStrings();

const videoTourText = computed(() => {
    if (locale.value === 'kz') {
        return t('video_tour_spinning', 'БЕЙНЕТУРДЫ КӨРУ • БЕЙНЕТУРДЫ КӨРУ • ');
    }
    if (locale.value === 'en') {
        return t('video_tour_spinning', 'WATCH VIDEO TOUR • WATCH VIDEO TOUR • ');
    }
    return t('video_tour_spinning', 'ПОСМОТРЕТЬ ВИДЕО-ТУР • ПОСМОТРЕТЬ ВИДЕО-ТУР • ');
});

const videoTourBtnTitle = computed(() => {
    if (locale.value === 'kz') {
        return t('video_tour_btn_title', 'Колледждің бейнетуын көру');
    }
    if (locale.value === 'en') {
        return t('video_tour_btn_title', 'Watch college video tour');
    }
    return t('video_tour_btn_title', 'Посмотреть видеотур колледжа');
});

const videoTourModalTitle = computed(() => {
    if (locale.value === 'kz') {
        return t('video_tour_modal_title', '«Тұран» колледжі бойынша 360° виртуалды бейнетур');
    }
    if (locale.value === 'en') {
        return t('video_tour_modal_title', 'Virtual 360° Video Tour of Turan College');
    }
    return t('video_tour_modal_title', 'Виртуальный 360° видеотур по Колледжу «Туран»');
});

const formatNewsDate = (dateStr?: string) => {
    if (!dateStr) return '29 октября 2024';
    try {
        const d = new Date(dateStr);
        return d.toLocaleDateString('ru-RU', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    } catch {
        return dateStr;
    }
};

// FAQ accordion state: first question open by default
const activeFaqId = ref<number | null>(props.faqs[0]?.id || 1);

const toggleFaq = (id: number) => {
    activeFaqId.value = activeFaqId.value === id ? null : id;
};

// Modal for Video / Virtual Tour
const isVideoModalOpen = ref(false);

// Unified Online Application Modal Composable
const { openApplyModal } = useApplyModal();

// =============================================================================
// Specialties Carousel / Slider State & Interactions
// =============================================================================
const sliderRef = ref<HTMLDivElement | null>(null);
const activeSlideIndex = ref(0);
const isAtStart = ref(true);
const isAtEnd = ref(false);
const isHoveringSlider = ref(false);

let isMouseDown = false;
let startXCoord = 0;
let initialScroll = 0;
let lastXCoord = 0;
let lastTimestamp = 0;
let dragVelocity = 0;
let momentumAnimId: number | null = null;
let autoScrollAnimId: number | null = null;
let pauseAutoScrollUntil = 0;

const maxSlidesCount = computed(() => {
    const total = props.featuredSpecialties.length;
    // For 3 visible cards on desktop, there are (total - 2) distinct snap stops
    return Math.max(1, total - 2);
});

const updateSliderState = () => {
    if (!sliderRef.value) return;
    const { scrollLeft, scrollWidth, clientWidth } = sliderRef.value;
    isAtStart.value = scrollLeft <= 12;
    isAtEnd.value = scrollLeft + clientWidth >= scrollWidth - 12;

    const firstCard = sliderRef.value.firstElementChild as HTMLElement | null;
    if (firstCard) {
        const cardWidth = firstCard.offsetWidth + 28;
        activeSlideIndex.value = Math.min(
            Math.max(0, Math.round(scrollLeft / cardWidth)),
            maxSlidesCount.value - 1
        );
    }
};

const scrollToSlide = (index: number) => {
    if (!sliderRef.value) return;
    pauseAutoScrollUntil = performance.now() + 3000;
    const firstCard = sliderRef.value.firstElementChild as HTMLElement | null;
    if (!firstCard) return;
    const cardWidth = firstCard.offsetWidth + 28;
    sliderRef.value.scrollTo({
        left: index * cardWidth,
        behavior: 'smooth',
    });
};

const scrollNext = () => {
    if (!sliderRef.value) return;
    pauseAutoScrollUntil = performance.now() + 3000;
    const firstCard = sliderRef.value.firstElementChild as HTMLElement | null;
    if (!firstCard) return;
    const cardWidth = firstCard.offsetWidth + 28;
    sliderRef.value.scrollBy({
        left: cardWidth,
        behavior: 'smooth',
    });
};

const scrollPrev = () => {
    if (!sliderRef.value) return;
    pauseAutoScrollUntil = performance.now() + 3000;
    const firstCard = sliderRef.value.firstElementChild as HTMLElement | null;
    if (!firstCard) return;
    const cardWidth = firstCard.offsetWidth + 28;
    sliderRef.value.scrollBy({
        left: -cardWidth,
        behavior: 'smooth',
    });
};

// Smooth mouse dragging with velocity tracking & viscous inertia
let hasDraggedSpecialty = false;

const onSliderPointerDown = (e: PointerEvent) => {
    if (e.pointerType === 'touch' || !sliderRef.value) return;
    if (momentumAnimId) {
        cancelAnimationFrame(momentumAnimId);
        momentumAnimId = null;
    }
    isMouseDown = true;
    hasDraggedSpecialty = false;
    startXCoord = e.pageX;
    initialScroll = sliderRef.value.scrollLeft;
    lastXCoord = e.pageX;
    lastTimestamp = performance.now();
    dragVelocity = 0;
};

const onSliderPointerMove = (e: PointerEvent) => {
    if (e.pointerType === 'touch' || !isMouseDown || !sliderRef.value) return;
    const now = performance.now();
    const dt = now - lastTimestamp;
    const dx = e.pageX - startXCoord;
    if (Math.abs(dx) > 6) {
        hasDraggedSpecialty = true;
    }
    sliderRef.value.scrollLeft = initialScroll - dx;

    if (dt > 10) {
        dragVelocity = (e.pageX - lastXCoord) / dt;
        lastXCoord = e.pageX;
        lastTimestamp = now;
    }
};

const onSliderPointerUp = (e: PointerEvent) => {
    if (e.pointerType === 'touch') return;
    stopDragWithMomentum();
};

const stopDragWithMomentum = () => {
    if (!isMouseDown) return;
    isMouseDown = false;
    pauseAutoScrollUntil = performance.now() + 2000;

    // Apply soft decaying momentum
    if (Math.abs(dragVelocity) > 0.12 && sliderRef.value) {
        const applyMomentum = () => {
            if (!sliderRef.value || Math.abs(dragVelocity) < 0.02 || isMouseDown) {
                momentumAnimId = null;
                return;
            }
            sliderRef.value.scrollLeft -= dragVelocity * 14;
            dragVelocity *= 0.92;
            momentumAnimId = requestAnimationFrame(applyMomentum);
        };
        momentumAnimId = requestAnimationFrame(applyMomentum);
    }
};

// Touch state for mobile swiping
const isUserTouching = ref(false);
let sliderTouchStartX = 0;
let sliderTouchStartY = 0;

const onTouchStart = (e: TouchEvent) => {
    isUserTouching.value = true;
    if (momentumAnimId) {
        cancelAnimationFrame(momentumAnimId);
        momentumAnimId = null;
    }
    pauseAutoScrollUntil = performance.now() + 8000;
    if (e.touches && e.touches[0]) {
        sliderTouchStartX = e.touches[0].clientX;
        sliderTouchStartY = e.touches[0].clientY;
        hasDraggedSpecialty = false;
    }
};

const onTouchMove = (e: TouchEvent) => {
    if (e.touches && e.touches[0]) {
        const dx = Math.abs(e.touches[0].clientX - sliderTouchStartX);
        if (dx > 8) {
            hasDraggedSpecialty = true;
        }
    }
};

const onTouchEnd = () => {
    isUserTouching.value = false;
    pauseAutoScrollUntil = performance.now() + 6000;
    setTimeout(() => {
        hasDraggedSpecialty = false;
    }, 150);
};

const onSliderClickCapture = (e: MouseEvent) => {
    if (hasDraggedSpecialty) {
        e.preventDefault();
        e.stopPropagation();
    }
};

// Slow continuous auto-scroll loop (delicate, luxurious, continuous drift for desktop only)
const startAutoScroll = () => {
    const step = () => {
        const now = performance.now();
        const isMobileScreen = typeof window !== 'undefined' && (window.innerWidth < 1024 || 'ontouchstart' in window);
        if (
            sliderRef.value &&
            !isMobileScreen &&
            !isHoveringSlider.value &&
            !isMouseDown &&
            !isUserTouching.value &&
            !momentumAnimId &&
            now > pauseAutoScrollUntil
        ) {
            const maxScroll = sliderRef.value.scrollWidth - sliderRef.value.clientWidth;
            if (maxScroll > 10) {
                if (sliderRef.value.scrollLeft >= maxScroll - 1) {
                    sliderRef.value.scrollLeft = 0;
                } else {
                    sliderRef.value.scrollLeft += 0.45;
                }
            }
        }
        autoScrollAnimId = requestAnimationFrame(step);
    };
    autoScrollAnimId = requestAnimationFrame(step);
};

// =============================================================================
// Cooperation Partners Continuous Marquee Ticker & Drag
// =============================================================================
const partnerTickerRef = ref<HTMLDivElement | null>(null);
const isHoveringTicker = ref(false);
const isTickerTouching = ref(false);
let isTickerMouseDown = false;
let tickerStartX = 0;

let hasDraggedTicker = false;
let tickerTouchStartX = 0;

const onTickerTouchStart = (e: TouchEvent) => {
    isTickerTouching.value = true;
    if (tickerMomentumId) {
        cancelAnimationFrame(tickerMomentumId);
        tickerMomentumId = null;
    }
    pauseTickerUntil = performance.now() + 6000;
    if (e.touches && e.touches[0]) {
        tickerTouchStartX = e.touches[0].clientX;
        hasDraggedTicker = false;
    }
};

const onTickerTouchMove = (e: TouchEvent) => {
    if (e.touches && e.touches[0]) {
        const dx = Math.abs(e.touches[0].clientX - tickerTouchStartX);
        if (dx > 8) {
            hasDraggedTicker = true;
        }
    }
};

const onTickerTouchEnd = () => {
    isTickerTouching.value = false;
    pauseTickerUntil = performance.now() + 4000;
    setTimeout(() => {
        hasDraggedTicker = false;
    }, 150);
};

const onTickerClickCapture = (e: MouseEvent) => {
    if (hasDraggedTicker) {
        e.preventDefault();
        e.stopPropagation();
    }
};

let tickerStartScroll = 0;
let tickerLastX = 0;
let tickerLastTime = 0;
let tickerVelocity = 0;
let tickerMomentumId: number | null = null;
let tickerAnimId: number | null = null;
let pauseTickerUntil = 0;

const repeatedPartners = computed(() => {
    if (!props.partners || !props.partners.length) return [];
    // Repeat at least 4 times for infinite seamless loop
    return [...props.partners, ...props.partners, ...props.partners, ...props.partners];
});

const onTickerPointerDown = (e: PointerEvent) => {
    if (e.pointerType === 'touch' || !partnerTickerRef.value) return;
    if (tickerMomentumId) {
        cancelAnimationFrame(tickerMomentumId);
        tickerMomentumId = null;
    }
    isTickerMouseDown = true;
    hasDraggedTicker = false;
    tickerStartX = e.pageX;
    tickerStartScroll = partnerTickerRef.value.scrollLeft;
    tickerLastX = e.pageX;
    tickerLastTime = performance.now();
    tickerVelocity = 0;
};

const onTickerPointerMove = (e: PointerEvent) => {
    if (e.pointerType === 'touch' || !isTickerMouseDown || !partnerTickerRef.value) return;
    const now = performance.now();
    const dt = now - tickerLastTime;
    const dx = e.pageX - tickerStartX;
    if (Math.abs(dx) > 6) {
        hasDraggedTicker = true;
    }
    partnerTickerRef.value.scrollLeft = tickerStartScroll - dx;

    if (dt > 10) {
        tickerVelocity = (e.pageX - tickerLastX) / dt;
        tickerLastX = e.pageX;
        tickerLastTime = now;
    }
};

const onTickerPointerUp = (e: PointerEvent) => {
    if (e.pointerType === 'touch') return;
    stopTickerDragWithMomentum();
};

const stopTickerDragWithMomentum = () => {
    if (!isTickerMouseDown) return;
    isTickerMouseDown = false;
    pauseTickerUntil = performance.now() + 1500;

    if (Math.abs(tickerVelocity) > 0.12 && partnerTickerRef.value) {
        const applyMomentum = () => {
            if (!partnerTickerRef.value || Math.abs(tickerVelocity) < 0.02 || isTickerMouseDown) {
                tickerMomentumId = null;
                return;
            }
            partnerTickerRef.value.scrollLeft -= tickerVelocity * 14;
            tickerVelocity *= 0.92;
            tickerMomentumId = requestAnimationFrame(applyMomentum);
        };
        tickerMomentumId = requestAnimationFrame(applyMomentum);
    }
};

const startTickerScroll = () => {
    const step = () => {
        const now = performance.now();
        const isMobileScreen = typeof window !== 'undefined' && (window.innerWidth < 1024 || 'ontouchstart' in window);
        if (
            partnerTickerRef.value &&
            !isMobileScreen &&
            !isHoveringTicker.value &&
            !isTickerMouseDown &&
            !isTickerTouching.value &&
            !tickerMomentumId &&
            now > pauseTickerUntil
        ) {
            const el = partnerTickerRef.value;
            const singleSetWidth = el.scrollWidth / 4;
            if (el.scrollLeft >= singleSetWidth * 2) {
                el.scrollLeft -= singleSetWidth;
            } else if (el.scrollLeft <= 0) {
                el.scrollLeft += singleSetWidth;
            }
            el.scrollLeft += 0.65; // Soft continuous drift
        }
        tickerAnimId = requestAnimationFrame(step);
    };
    tickerAnimId = requestAnimationFrame(step);
};

onMounted(() => {
    if (sliderRef.value) {
        sliderRef.value.addEventListener('scroll', updateSliderState, { passive: true });
        updateSliderState();
    }
    startAutoScroll();
    startTickerScroll();
});

onUnmounted(() => {
    if (sliderRef.value) {
        sliderRef.value.removeEventListener('scroll', updateSliderState);
    }
    if (autoScrollAnimId) {
        cancelAnimationFrame(autoScrollAnimId);
    }
    if (momentumAnimId) {
        cancelAnimationFrame(momentumAnimId);
    }
    if (tickerAnimId) {
        cancelAnimationFrame(tickerAnimId);
    }
    if (tickerMomentumId) {
        cancelAnimationFrame(tickerMomentumId);
    }
});

// Predefined 3D specialty visual styles matching mockup
const specialtyIcons = [
    {
        bg: 'from-amber-400 to-rose-400',
        badge: 'Маркетинг',
        svg: `<svg viewBox="0 0 64 64" fill="none" class="w-16 h-16"><circle cx="32" cy="32" r="28" fill="#FFF1EB"/><circle cx="32" cy="32" r="20" stroke="#FDA685" stroke-width="6"/><circle cx="32" cy="32" r="10" fill="#EB5757"/><path d="M42 22L48 16M42 42L48 48M22 22L16 16M22 42L16 48" stroke="#FDA685" stroke-width="3" stroke-linecap="round"/></svg>`,
    },
    {
        bg: 'from-sky-400 to-emerald-400',
        badge: 'Туризм',
        svg: `<svg viewBox="0 0 64 64" fill="none" class="w-16 h-16"><circle cx="32" cy="32" r="28" fill="#EBF7F2"/><circle cx="32" cy="32" r="22" stroke="#448AC4" stroke-width="4"/><path d="M32 10C24 18 24 46 32 54M32 10C40 18 40 46 32 54M12 24H52M12 40H52" stroke="#448AC4" stroke-width="3" stroke-linecap="round"/><circle cx="32" cy="32" r="6" fill="#0F4C3A"/></svg>`,
    },
    {
        bg: 'from-blue-500 to-indigo-600',
        badge: 'ПО',
        svg: `<svg viewBox="0 0 64 64" fill="none" class="w-16 h-16"><rect x="6" y="10" width="52" height="40" rx="8" fill="#F0F7FC" stroke="#0082C9" stroke-width="4"/><path d="M22 24L16 30L22 36M42 24L48 30L42 36M35 22L29 38" stroke="#0082C9" stroke-width="3.5" stroke-linecap="round"/><circle cx="16" cy="18" r="2" fill="#FDA685"/><circle cx="22" cy="18" r="2" fill="#448AC4"/><circle cx="28" cy="18" r="2" fill="#0F4C3A"/></svg>`,
    },
];
</script>

<template>
    <AppLayout title="TURAN COLLEGE — Главная">
        <!-- =================================================================
             1. HERO / INTRO SECTION (Main Header sits on intro background)
             ================================================================= -->
        <IntroCard variant="home">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center pt-4 sm:pt-4 pb-2">
                <!-- Left Hero Content with comfortable top offset on mobile -->
                <div class="lg:col-span-8 space-y-4 sm:space-y-7 pt-2 sm:pt-0">
                    <h1 class="font-heading font-black text-2xl sm:text-5xl lg:text-7xl tracking-tight leading-none uppercase text-white">
                        TURAN COLLEGE
                    </h1>

                    <p class="text-slate-200 text-sm sm:text-lg lg:text-xl font-normal max-w-3xl leading-relaxed">
                        Это <CountUp :to="23" :duration="2" class="font-bold text-white inline-block" /> года опыта, современные подходы и реальная подготовка к востребованным профессиям. 
                        Объединяем традиции и инновации, чтобы каждый студент раскрыл свой потенциал.
                    </p>

                    <!-- Action Buttons with Vue Bits Specular Button -->
                    <div class="flex flex-wrap items-center gap-3 sm:gap-4 pt-1 sm:pt-3">
                        <SpecularButton
                            type="button"
                            @click="openApplyModal()"
                            class-name="h-11 sm:h-12 px-6 sm:px-8 text-xs sm:text-sm"
                        >
                            <span>КАК ПОСТУПИТЬ?</span>
                            <ArrowRight class="w-4 h-4 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:translate-x-1" />
                        </SpecularButton>

                        <SpecularButton
                            as="Link"
                            href="/specialties"
                            class-name="h-11 sm:h-12 px-6 sm:px-8 text-xs sm:text-sm"
                        >
                            <span>СТОИМОСТЬ ОБУЧЕНИЯ</span>
                            <ArrowRight class="w-4 h-4 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:translate-x-1" />
                        </SpecularButton>
                    </div>
                </div>

                <!-- Right Hero Widget: Hidden on mobile, shown on desktop -->
                <div class="hidden lg:flex lg:col-span-4 justify-center lg:justify-end">
                    <button
                        type="button"
                        @click="isVideoModalOpen = true"
                        class="relative group cursor-pointer p-2 transition-transform duration-300 hover:scale-105 active:scale-95"
                        :title="videoTourBtnTitle"
                    >
                        <SpinningText
                            :text="videoTourText"
                            :duration="16"
                            :radius="54"
                            class="w-36 h-36 sm:w-44 sm:h-44 text-[10px] sm:text-[11px] uppercase tracking-[0.16em] font-heading font-semibold text-white/90"
                        >
                            <!-- Center Play Icon (Stationary in center while circular text rotates around it) -->
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-white text-slate-900 flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                                <Play class="w-6 h-6 fill-slate-900 text-slate-900 ml-0.5" />
                            </div>
                        </SpinningText>
                    </button>
                </div>
            </div>
        </IntroCard>

        <!-- =================================================================
             2. СПЕЦИАЛЬНОСТИ (Specialties Cards Slider: 3 visible cards)
             ================================================================= -->
        <section class="py-8 sm:py-16 lg:py-24 bg-white">
            <Container>
                <!-- Section Header with Long Editorial Arrow + Slider Navigation Buttons -->
                <div class="flex items-center justify-between gap-4 mb-5 sm:mb-10 lg:mb-12">
                    <div class="flex items-center gap-3 sm:gap-7">
                        <h2 class="font-heading font-bold text-xl sm:text-3xl lg:text-[54px] text-slate-900 uppercase tracking-tight leading-none">
                            СПЕЦИАЛЬНОСТИ
                        </h2>
                        <!-- Long Editorial Thin Arrow link replacing "Посмотреть ещё" -->
                        <Link
                            href="/specialties"
                            class="group inline-flex items-center text-slate-900 hover:text-[#0082C9] transition-all duration-300 cursor-pointer"
                            title="Все специальности"
                            aria-label="Все специальности"
                        >
                            <svg
                                viewBox="0 0 96 24"
                                fill="none"
                                stroke="currentColor"
                                class="w-10 sm:w-22 lg:w-28 h-4 sm:h-7 lg:h-8 transition-transform duration-300 group-hover:translate-x-3 shrink-0"
                                style="stroke-width: 1.3px; stroke-linecap: round; stroke-linejoin: round;"
                            >
                                <line x1="2" y1="12" x2="90" y2="12" />
                                <polyline points="78,4 90,12 78,20" />
                            </svg>
                        </Link>
                    </div>

                    <!-- Slider Controls: Prev / Next Buttons (Hidden on mobile, shown on desktop) -->
                    <div class="hidden sm:flex items-center gap-2.5">
                        <button
                            type="button"
                            @click="scrollPrev"
                            :disabled="isAtStart"
                            class="group relative w-11 h-11 sm:w-12 sm:h-12 rounded-full border border-slate-200/90 bg-white text-slate-800 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] flex items-center justify-center cursor-pointer disabled:opacity-20 disabled:pointer-events-none hover:scale-105 active:scale-90"
                            title="Предыдущая"
                            aria-label="Предыдущая"
                        >
                            <ChevronLeft class="w-5 h-5 sm:w-6 sm:h-6 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:-translate-x-1" :stroke-width="1.8" />
                        </button>
                        <button
                            type="button"
                            @click="scrollNext"
                            :disabled="isAtEnd"
                            class="group relative w-11 h-11 sm:w-12 sm:h-12 rounded-full border border-slate-200/90 bg-white text-slate-800 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] flex items-center justify-center cursor-pointer disabled:opacity-20 disabled:pointer-events-none hover:scale-105 active:scale-90"
                            title="Следующая"
                            aria-label="Следующая"
                        >
                            <ChevronRight class="w-5 h-5 sm:w-6 sm:h-6 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:translate-x-1" :stroke-width="1.8" />
                        </button>
                    </div>
                </div>

                <!-- Specialties Carousel: 3 visible cards on desktop, smooth continuous drift & momentum drag -->
                <div class="relative">
                    <div
                        ref="sliderRef"
                        @mouseenter="isHoveringSlider = true"
                        @mouseleave="() => { isHoveringSlider = false; stopDragWithMomentum(); }"
                        @pointerdown="onSliderPointerDown"
                        @pointermove="onSliderPointerMove"
                        @pointerup="onSliderPointerUp"
                        @pointercancel="onSliderPointerUp"
                        @touchstart.passive="onTouchStart"
                        @touchmove.passive="onTouchMove"
                        @touchend.passive="onTouchEnd"
                        @click.capture="onSliderClickCapture"
                        class="flex gap-3 sm:gap-7 overflow-x-auto scrollbar-none py-2 -mx-[14px] px-[14px] sm:mx-0 sm:px-0 select-none snap-x snap-mandatory scroll-pl-[14px] sm:scroll-pl-0"
                        style="-webkit-overflow-scrolling: touch; touch-action: pan-x pan-y; overscroll-behavior-x: contain;"
                        :class="{
                            'cursor-grab': !isMouseDown,
                            'cursor-grabbing': isMouseDown
                        }"
                    >
                        <div
                            v-for="item in featuredSpecialties"
                            :key="item.id"
                            class="shrink-0 w-[88%] sm:w-[calc((100%-28px)/2)] lg:w-[calc((100%-56px)/3)] snap-start"
                        >
                            <SpotlightCard
                                class-name="rounded-[20px] sm:rounded-[30px] bg-[#EFEFEF] group cursor-pointer border-0 h-full"
                                spotlight-color="rgba(0, 130, 201, 0.12)"
                            >
                                <div class="w-full h-full flex items-stretch justify-between pl-4 sm:pl-[32px] pr-3 sm:pr-[20px] py-2 sm:py-[10px] gap-2.5 sm:gap-[28px] min-h-[130px] sm:min-h-[195px]">
                                    <!-- Content: compact vertical padding on mobile -->
                                    <div class="flex-1 py-1 sm:py-5 flex flex-col justify-center min-w-0">
                                        <!-- Заголовок карточки -->
                                        <h3 class="font-heading font-semibold text-[15px] sm:text-[18px] lg:text-[20px] leading-[115%] text-slate-900 mb-1 sm:mb-3">
                                            <Link :href="`/specialties/${item.slug}`" class="after:absolute after:inset-0">
                                                {{ item.title }}
                                            </Link>
                                        </h3>

                                        <!-- Нижний текст после заголовка -->
                                        <div class="text-[11px] sm:text-[14px] lg:text-[15px] space-y-0.5 sm:space-y-2 text-slate-600 font-sans leading-normal">
                                            <div class="flex items-center gap-1.5 sm:gap-2.5">
                                                <span class="font-semibold text-slate-800">9 класс:</span>
                                                <span class="text-slate-600">2 года 10 мес.</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 sm:gap-2.5">
                                                <span class="font-semibold text-slate-800">11 класс:</span>
                                                <span class="text-slate-600">1 год 10 мес.</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 3D Graphic Visual Card Image with Skeleton -->
                                    <div class="shrink-0 flex items-center justify-center max-h-[115px] sm:max-h-[155px] my-auto w-[85px] sm:w-[125px] lg:w-[135px] xl:w-[150px]">
                                        <LazyImage
                                            :src="getSpecialtyImage(item)"
                                            :alt="item.title"
                                            class="max-h-[105px] sm:max-h-[155px] w-auto h-auto object-contain transform group-hover:scale-105 transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)]"
                                        />
                                    </div>
                                </div>
                            </SpotlightCard>
                        </div>
                    </div>
                </div>
            </Container>
        </section>

        <!-- =================================================================
             3. ПРО КОЛЛЕДЖ (Exact design matching user mockup)
             ================================================================= -->
        <section class="py-8 sm:py-16 lg:py-24 bg-white">
            <Container>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-10 lg:gap-14 items-center">
                    <!-- Left Column: 7/12 width on desktop (Title 54px Black, Subtitle 24px Regular, 3 Feature Blocks <=220px) -->
                    <div class="lg:col-span-7 space-y-4 sm:space-y-8">
                        <div class="space-y-3 sm:space-y-5">
                            <h2 class="font-heading font-black text-xl sm:text-3xl lg:text-[54px] text-slate-900 uppercase tracking-tight leading-none">
                                ПРО КОЛЛЕДЖ
                            </h2>
                            <p class="font-sans font-normal text-sm sm:text-lg lg:text-[24px] text-slate-700 leading-snug sm:leading-relaxed max-w-2xl">
                                Колледж сотрудничает с ведущими предприятиями, что обеспечивает нашим студентам возможность прохождения практики и дальнейшего трудоустройства
                            </p>
                        </div>

                        <!-- 3 Feature Blocks: Montserrat font, 20px radius, max 220px wide on desktop (#F2F2F2) -->
                        <div class="grid grid-cols-3 gap-2 sm:gap-4 max-w-[692px]">
                            <!-- Card 1: Педагогический состав (max 220px) -->
                            <Link
                                href="/about/teachers"
                                class="w-full max-w-[220px] rounded-[16px] sm:rounded-[20px] bg-[#F2F2F2] hover:bg-[#EAEAEA] p-2.5 sm:p-5 text-center flex flex-col items-center justify-center min-h-[115px] sm:min-h-[155px] transition-all duration-300 hover:scale-[1.03] select-none cursor-pointer block group"
                            >
                                <div class="w-7 h-7 sm:w-9 sm:h-9 mb-1.5 sm:mb-3.5 flex items-center justify-center text-[#4A5568] group-hover:text-slate-900 transition-colors">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8" viewBox="0 0 32 32" fill="none">
                                        <!-- Teacher -->
                                        <circle cx="9" cy="8" r="2.8" fill="currentColor" />
                                        <path d="M5.5 17.5v-2a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                        <path d="M11 13.5l3-2.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                        <!-- Presentation board -->
                                        <rect x="14.5" y="4.5" width="13.5" height="11" rx="1.5" fill="currentColor" />
                                        <!-- Chart lines inside board -->
                                        <path d="M17.5 12.5v-3M21.5 12.5v-5M25.5 12.5v-2" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" />
                                        <!-- Audience / Desks -->
                                        <rect x="6" y="22" width="4" height="3" rx="0.8" fill="currentColor" />
                                        <rect x="14" y="22" width="4" height="3" rx="0.8" fill="currentColor" />
                                        <rect x="22" y="22" width="4" height="3" rx="0.8" fill="currentColor" />
                                        <path d="M3.5 28h25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    </svg>
                                </div>
                                <span class="font-heading font-medium text-[11px] sm:text-sm lg:text-[15px] text-slate-800 leading-tight block group-hover:text-slate-900 text-center">
                                    Педагогический<br>состав
                                </span>
                            </Link>

                            <!-- Card 2: Качественное образование (max 220px) -->
                            <div class="w-full max-w-[220px] rounded-[16px] sm:rounded-[20px] bg-[#F2F2F2] p-2.5 sm:p-5 text-center flex flex-col items-center justify-center min-h-[115px] sm:min-h-[155px] transition-transform duration-300 hover:scale-[1.03] select-none">
                                <div class="w-7 h-7 sm:w-9 sm:h-9 mb-1.5 sm:mb-3.5 flex items-center justify-center text-[#4A5568]">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8" viewBox="0 0 32 32" fill="none">
                                        <!-- Pen Body -->
                                        <path d="M21.5 4.5l5.5 5.5-11 11-4.5 1 1-4.5 11-11z" fill="currentColor" />
                                        <!-- White accent line on pen -->
                                        <line x1="20" y1="6" x2="25.5" y2="11.5" stroke="#FFFFFF" stroke-width="1.4" stroke-linecap="round" />
                                        <!-- Wavy line below -->
                                        <path d="M6 26.5c3.5-2.5 6.5 2 10 0s6.5-2.5 10 0" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
                                    </svg>
                                </div>
                                <span class="font-heading font-medium text-[11px] sm:text-sm lg:text-[15px] text-slate-800 leading-tight block text-center">
                                    Качественное<br>образование
                                </span>
                            </div>

                            <!-- Card 3: Сотрудничество (max 220px) -->
                            <div class="w-full max-w-[220px] rounded-[16px] sm:rounded-[20px] bg-[#F2F2F2] p-2.5 sm:p-5 text-center flex flex-col items-center justify-center min-h-[115px] sm:min-h-[155px] transition-transform duration-300 hover:scale-[1.03] select-none">
                                <div class="w-7 h-7 sm:w-9 sm:h-9 mb-1.5 sm:mb-3.5 flex items-center justify-center text-[#4A5568]">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8" viewBox="0 0 32 32" fill="none">
                                        <!-- Head -->
                                        <circle cx="16" cy="8.5" r="3.8" fill="currentColor" />
                                        <!-- Torso -->
                                        <path d="M7 26.5c0-4.8 4-8.5 9-8.5s9 3.7 9 8.5H7z" fill="currentColor" />
                                        <!-- White tie / lapel cutout -->
                                        <polygon points="16,18 14.5,21.5 16,25 17.5,21.5" fill="#FFFFFF" />
                                    </svg>
                                </div>
                                <span class="font-heading font-medium text-[11px] sm:text-sm lg:text-[15px] text-slate-800 leading-tight block text-center">
                                    Сотрудничество
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: 5/12 width on desktop (about_mainpage.png) -->
                    <div class="lg:col-span-5 flex justify-center lg:justify-end">
                        <LazyImage
                            src="/images/about_mainpage.png"
                            alt="Про колледж Туран"
                            class="w-full max-w-[500px] h-auto object-contain select-none transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] hover:scale-[1.02]"
                        />
                    </div>
                </div>
            </Container>
        </section>
        <!-- =================================================================
             4. ВОПРОСЫ И ОТВЕТЫ (FAQ - Stretched Banner Overlapping Cooperation)
             ================================================================= -->
        <section class="w-full px-0 sm:px-[10px] max-w-none sm:max-w-[1820px] mx-auto pt-6 sm:pt-10 lg:pt-14 -mb-20 sm:-mb-32 lg:-mb-44 relative z-20">
            <div
                class="relative overflow-hidden rounded-none sm:rounded-[36px] bg-[#0c1219] text-white bg-cover bg-center w-full py-8 sm:py-16 lg:py-24"
                :style="{
                    backgroundImage: `url('/images/faq_back.jpg')`
                }"
            >
                <!-- Subtle dark ambient overlay matching mockup contrast -->
                <div class="absolute inset-0 pointer-events-none" />

                <Container class="relative z-10 w-full px-4 sm:px-6">
                    <!-- Left-aligned content container (~60% width) leaving the right side open as in mockup -->
                    <div class="max-w-3xl">
                        <div class="mb-6 sm:mb-10 space-y-1.5 sm:space-y-2">
                            <span class="text-xs sm:text-sm text-[#FDA685] font-medium tracking-normal block">
                                Помогаем разобраться во всём легко и быстро
                            </span>
                            <h2 class="font-heading font-black text-xl sm:text-3xl lg:text-[48px] text-white uppercase tracking-tight leading-none">
                                ВОПРОСЫ И ОТВЕТЫ
                            </h2>
                        </div>

                        <!-- Vue Bits AnimatedList: clean rows, no card backgrounds, circle arrows -->
                        <AnimatedList
                            :items="faqs"
                            class-name="space-y-4 sm:space-y-5"
                            :stagger-delay="0.04"
                        >
                            <template #item="{ item }">
                                <div class="transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]">
                                    <!-- Question row with circular arrow button -->
                                    <button
                                        type="button"
                                        @click="toggleFaq(item.id)"
                                        class="w-full text-left flex items-start gap-3.5 sm:gap-4 group cursor-pointer"
                                    >
                                        <!-- Circle arrow icon: Down & white when closed, Up & peach (#FDA685) when open -->
                                        <span
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-full border flex items-center justify-center shrink-0 mt-0.5 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
                                            :class="activeFaqId === item.id 
                                                ? 'border-[#FDA685] text-[#FDA685]' 
                                                : 'border-white/70 text-white/90 group-hover:border-white'"
                                        >
                                            <ArrowUp v-if="activeFaqId === item.id" class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-[2]" />
                                            <ArrowDown v-else class="w-3.5 h-3.5 sm:w-4 sm:h-4 stroke-[2]" />
                                        </span>

                                        <span
                                            class="font-heading font-bold text-[15px] sm:text-[17px] text-white group-hover:text-white transition-colors leading-snug pt-0.5"
                                        >
                                            {{ item.question }}
                                        </span>
                                    </button>

                                    <!-- Smooth accordion expansion with soft easing -->
                                    <div
                                        class="grid transition-[grid-template-rows,opacity] duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
                                        :class="activeFaqId === item.id ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'"
                                    >
                                        <div class="overflow-hidden">
                                            <div class="pl-10.5 sm:pl-12 pt-2.5 pb-2 text-[13px] sm:text-[14px] text-slate-200/90 leading-relaxed font-normal max-w-2xl">
                                                <p>{{ item.answer }}</p>

                                                <!-- Tag pills for first active question -->
                                                <div v-if="item.id === 1" class="flex flex-wrap items-center gap-2.5 mt-4">
                                                    <Link
                                                        href="/specialties"
                                                        class="px-4 py-1.5 rounded-full border border-[#FDA685]/70 text-[#FDA685] hover:bg-[#FDA685]/15 text-xs font-heading font-medium transition-all duration-300"
                                                    >
                                                        Специальности
                                                    </Link>
                                                    <Link
                                                        href="/career"
                                                        class="px-4 py-1.5 rounded-full border border-[#FDA685]/70 text-[#FDA685] hover:bg-[#FDA685]/15 text-xs font-heading font-medium transition-all duration-300"
                                                    >
                                                        Выпускники
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </AnimatedList>
                    </div>
                </Container>
            </div>
        </section>

        <!-- =================================================================
             5. СОТРУДНИЧЕСТВО (Grey Background Overlapped by FAQ + Infinite Marquee)
             ================================================================= -->
        <section class="relative z-10 pt-44 sm:pt-52 lg:pt-66 pb-8 sm:pb-16 lg:pb-28 bg-[#EFEFEF] overflow-hidden">
            <Container>
                <!-- Section Header: Title 54px Medium Uppercase + Subtitle Montserrat Light 24px -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 sm:gap-6 mb-5 sm:mb-10 lg:mb-16">
                    <h2 class="font-heading font-medium text-xl sm:text-3xl lg:text-[54px] text-slate-900 uppercase tracking-tight leading-none">
                        СОТРУДНИЧЕСТВО
                    </h2>
                    <div class="font-heading font-light text-sm sm:text-lg lg:text-[24px] text-slate-700 leading-snug md:text-right max-w-lg">
                        Какие кружки, секции и дополнительное развитие предлагает колледж?
                    </div>
                </div>

                <!-- Continuous Draggable Logo Carousel Ticker (компактные вертикальные отступы) -->
                <div class="relative py-1 select-none">
                    <div
                        ref="partnerTickerRef"
                        @mouseenter="isHoveringTicker = true"
                        @mouseleave="() => { isHoveringTicker = false; stopTickerDragWithMomentum(); }"
                        @pointerdown="onTickerPointerDown"
                        @pointermove="onTickerPointerMove"
                        @pointerup="onTickerPointerUp"
                        @pointercancel="onTickerPointerUp"
                        @touchstart.passive="onTickerTouchStart"
                        @touchmove.passive="onTickerTouchMove"
                        @touchend.passive="onTickerTouchEnd"
                        @click.capture="onTickerClickCapture"
                        class="flex items-center gap-6 sm:gap-14 lg:gap-20 overflow-x-auto scrollbar-none py-1.5 sm:py-3 select-none -mx-[14px] px-[14px] sm:mx-0 sm:px-0 scroll-pl-[14px] sm:scroll-pl-0"
                        style="-webkit-overflow-scrolling: touch; touch-action: pan-x pan-y; overscroll-behavior-x: contain;"
                        :class="{
                            'cursor-grab': !isTickerMouseDown,
                            'cursor-grabbing': isTickerMouseDown
                        }"
                    >
                        <div
                            v-for="(p, idx) in repeatedPartners"
                            :key="`${p.id}-${idx}`"
                            class="shrink-0 flex items-center justify-center transition-all duration-300 grayscale hover:grayscale-0 opacity-80 hover:opacity-100"
                        >
                            <img
                                v-if="p.logo_url"
                                :src="p.logo_url"
                                :alt="p.name"
                                class="max-h-8 sm:max-h-11 w-auto object-contain select-none pointer-events-none"
                            />
                            <span v-else class="font-heading font-semibold text-sm sm:text-lg text-slate-700 select-none">
                                {{ p.name }}
                            </span>
                        </div>
                    </div>
                </div>
            </Container>
        </section>

        <!-- =================================================================
             6. ПОСЛЕДНИЕ НОВОСТИ (Exact design matching user mockup)
             ================================================================= -->
        <section class="py-8 sm:py-16 lg:py-24 bg-white">
            <Container>
                <!-- Section Header: Title 54px + Subtitle below -->
                <div class="mb-5 sm:mb-10 lg:mb-12 space-y-1.5 sm:space-y-2">
                    <h2 class="font-heading font-black text-xl sm:text-3xl lg:text-[54px] text-slate-900 uppercase tracking-tight leading-none">
                        ПОСЛЕДНИЕ НОВОСТИ
                    </h2>
                    <p class="font-heading font-light text-xs sm:text-base text-slate-600">
                        Какие кружки, секции и дополнительное развитие предлагает колледж?
                    </p>
                </div>

                <!-- 2 Columns: Left 7/12, Right 5/12, Gap 32px (gap-8) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">
                    <!-- Left Column (7/12): Large Featured Article with Date Badge Pill inside photo (Max height 500px) -->
                    <div v-if="latestNews[0]" class="lg:col-span-7 group">
                        <!-- Big Photo with Date Pill Badge (max-height <= 500px) -->
                        <Link :href="`/news/${latestNews[0].slug}`" class="relative block rounded-[20px] sm:rounded-[32px] overflow-hidden bg-slate-100 w-full max-h-[500px]">
                            <LazyImage
                                :src="latestNews[0].image_url"
                                :alt="latestNews[0].title"
                                class="w-full max-h-[500px] h-[200px] sm:h-[420px] lg:h-[480px] object-cover group-hover:scale-105 transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)]"
                            />
                            <!-- Плашка с датой (Shadcn Badge in bottom right corner) -->
                            <Badge
                                variant="outline"
                                class="absolute bottom-3 right-3 sm:bottom-4 sm:right-4 bg-white/95 backdrop-blur-md text-slate-900 border-white/50 shadow-xs text-xs font-heading font-medium"
                            >
                                {{ formatNewsDate(latestNews[0].published_at) }}
                            </Badge>
                        </Link>

                        <!-- Featured Article Title with line-clamp-3 limit -->
                        <h3 class="font-heading font-bold text-base sm:text-2xl lg:text-[26px] text-slate-900 group-hover:text-[#0082C9] transition-colors leading-snug mt-3 sm:mt-4 line-clamp-3">
                            <Link :href="`/news/${latestNews[0].slug}`">{{ latestNews[0].title }}</Link>
                        </h3>
                    </div>

                    <!-- Right Column (5/12): 5 Compact News Items going tightly one after another with max 3 lines -->
                    <div class="lg:col-span-5 flex flex-col divide-y divide-slate-200/80">
                        <div
                            v-for="item in latestNews.slice(1, 6)"
                            :key="item.id"
                            class="flex items-center justify-between gap-3 sm:gap-4 py-2.5 sm:py-3.5 first:pt-0 last:pb-0 group"
                        >
                            <div class="space-y-1 flex-1 pr-2 sm:pr-3 min-w-0">
                                <h4 class="font-heading font-bold text-[13px] sm:text-[15px] lg:text-[16px] text-slate-900 group-hover:text-[#0082C9] transition-colors line-clamp-3 leading-snug">
                                    <Link :href="`/news/${item.slug}`">{{ item.title }}</Link>
                                </h4>
                                <div class="text-[11px] sm:text-xs text-slate-400 font-normal">
                                    {{ formatNewsDate(item.published_at) }}
                                </div>
                            </div>

                            <Link
                                :href="`/news/${item.slug}`"
                                class="w-20 h-14 sm:w-28 sm:h-18 lg:w-32 lg:h-20 rounded-[14px] sm:rounded-[18px] overflow-hidden shrink-0 bg-slate-100"
                            >
                                <LazyImage
                                    :src="item.image_url"
                                    :alt="item.title"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
                                />
                            </Link>
                        </div>
                    </div>
                </div>
            </Container>
        </section>

        <!-- =================================================================
             7. ПОДАЙ ЗАЯВКУ НА ПОСТУПЛЕНИЕ ОНЛАЙН (CTA Banner Strip from Mockup)
             ================================================================= -->
        <section class="bg-[#f4f7f9] py-2 sm:py-4">
            <Container>
                <div class="rounded-2xl py-4 sm:py-8 flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-6">
                    <h3 class="font-heading font-black text-lg sm:text-2xl lg:text-3xl text-slate-900 tracking-tight text-center sm:text-left">
                        Подай заявку на поступление онлайн
                    </h3>

                    <button
                        type="button"
                        @click="openApplyModal()"
                        class="px-6 sm:px-8 py-3 sm:py-3.5 rounded-full bg-[#0082C9] hover:bg-[#0070ad] text-white font-heading font-semibold text-[13px] sm:text-[14px] leading-tight tracking-wide transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] cursor-pointer shrink-0 hover:scale-105 active:scale-95 inline-flex items-center gap-2.5 group"
                    >
                        <span>Отправить заявку</span>
                        <ArrowRight class="w-4 h-4 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:translate-x-1" />
                    </button>
                </div>
            </Container>
        </section>

        <!-- =================================================================
             8. MODAL: Video / Virtual Tour (Shadcn Dialog)
             ================================================================= -->
        <Dialog v-model:open="isVideoModalOpen">
            <DialogContent class="sm:max-w-4xl p-0 overflow-hidden bg-black border-slate-800 text-white rounded-2xl">
                <DialogHeader class="p-4 border-b border-white/10">
                    <DialogTitle class="text-white text-base font-heading font-semibold">
                        {{ videoTourModalTitle }}
                    </DialogTitle>
                </DialogHeader>
                <div class="relative aspect-video bg-black flex items-center justify-center">
                    <iframe
                        class="w-full h-full"
                        src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=0"
                        :title="videoTourModalTitle"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
