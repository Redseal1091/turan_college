<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/shared/Container.vue';
import IntroCard from '@/Components/layout/IntroCard.vue';
import Button from '@/Components/ui/button/Button.vue';
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
import { ArrowLeft, ArrowRight, X, ChevronLeft, ChevronRight, Maximize2 } from 'lucide-vue-next';

interface NewsItem {
    id: number;
    title: string;
    slug: string;
    category: string;
    published_at: string;
    excerpt: string;
    content: string;
    image_url: string;
    gallery?: string[];
}

interface SpecialtyOption {
    id: number;
    title: string;
}

const props = defineProps<{
    article: NewsItem;
    recentNews: NewsItem[];
    specialties?: SpecialtyOption[];
}>();

// Title condition: If title > 130 characters, decrease font size and weight
const isLongTitle = computed(() => (props.article.title?.length || 0) > 130);

// Photo gallery: accommodates photos of any size without breaking the layout
const defaultSampleGallery = [
    '/images/gallery/01KVXR45GBB8VEAEMSSG2JWYD6.jpg',
    '/images/gallery/01KVXR45GDNZWEC75HWE7CW1Q2.jpg',
    '/images/gallery/01KVXR45GE8JHYNC7FB5DXN6F6.jpg',
    '/images/gallery/01KVXR45GH5AY53RQ3PPZ9QV4B.jpg',
    '/images/dump/photo_1.png',
    '/images/dump/photo_2.png',
    '/images/dump/event_1.png',
    '/images/dump/event_2.png',
];

const galleryPhotos = computed<string[]>(() => {
    if (props.article.gallery && props.article.gallery.length > 0) {
        return props.article.gallery;
    }
    const list: string[] = [];
    if (props.article.image_url) {
        list.push(props.article.image_url);
    }
    defaultSampleGallery.forEach((img) => {
        if (!list.includes(img)) {
            list.push(img);
        }
    });
    return list;
});

// Scroll Lock Management (prevents layout shift when scrollbar disappears)
const lockScroll = () => {
    if (typeof window === 'undefined' || typeof document === 'undefined') return;
    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
    if (scrollbarWidth > 0) {
        document.body.style.paddingRight = `${scrollbarWidth}px`;
    }
    document.body.style.overflow = 'hidden';
};

const unlockScroll = () => {
    if (typeof window === 'undefined' || typeof document === 'undefined') return;
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
};

// Slider Track State & Dragging
const galleryTrackRef = ref<HTMLElement | null>(null);
const canScrollLeft = ref(false);
const canScrollRight = ref(true);
const activeGalleryIndex = ref(0);

let isPointerDown = false;
let startX = 0;
let scrollStart = 0;
let hasDragged = false;
let touchStartX = 0;
let touchStartY = 0;
let hasTouchMoved = false;

const onPointerDown = (e: PointerEvent) => {
    // If touch event, leave to native browser smooth touch scroll
    if (e.pointerType === 'touch') {
        isPointerDown = false;
        return;
    }
    if (!galleryTrackRef.value) return;
    isPointerDown = true;
    hasDragged = false;
    startX = e.pageX;
    scrollStart = galleryTrackRef.value.scrollLeft;
};

const onPointerMove = (e: PointerEvent) => {
    if (!isPointerDown || !galleryTrackRef.value || e.pointerType === 'touch') return;
    const deltaX = e.pageX - startX;
    if (Math.abs(deltaX) > 6) {
        hasDragged = true;
    }
    galleryTrackRef.value.scrollLeft = scrollStart - deltaX;
};

const onPointerUp = () => {
    isPointerDown = false;
};

const onTouchStart = (e: TouchEvent) => {
    if (e.touches && e.touches[0]) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
        hasTouchMoved = false;
    }
};

const onTouchMove = (e: TouchEvent) => {
    if (e.touches && e.touches[0]) {
        const dx = Math.abs(e.touches[0].clientX - touchStartX);
        const dy = Math.abs(e.touches[0].clientY - touchStartY);
        if (dx > 8 || dy > 8) {
            hasTouchMoved = true;
        }
    }
};

const onTouchEnd = () => {
    setTimeout(() => {
        hasTouchMoved = false;
    }, 150);
};

const onSlideClick = (index: number) => {
    if (hasDragged || hasTouchMoved) return;
    openLightbox(index);
};

const onGalleryScroll = () => {
    if (!galleryTrackRef.value) return;
    const { scrollLeft, scrollWidth, clientWidth } = galleryTrackRef.value;
    canScrollLeft.value = scrollLeft > 10;
    canScrollRight.value = scrollLeft < scrollWidth - clientWidth - 10;

    const firstItem = galleryTrackRef.value.firstElementChild as HTMLElement;
    const itemWidth = firstItem ? firstItem.offsetWidth : 320;
    const index = Math.round(scrollLeft / (itemWidth + 16));
    activeGalleryIndex.value = Math.min(Math.max(0, index), galleryPhotos.value.length - 1);
};

const scrollGallery = (direction: 'prev' | 'next') => {
    if (!galleryTrackRef.value) return;
    const firstItem = galleryTrackRef.value.firstElementChild as HTMLElement;
    const itemWidth = firstItem ? firstItem.offsetWidth : 320;
    const scrollAmount = itemWidth + 16;
    galleryTrackRef.value.scrollBy({
        left: direction === 'next' ? scrollAmount : -scrollAmount,
        behavior: 'smooth',
    });
};

// Fullscreen Photo Lightbox Modal
const isLightboxOpen = ref(false);
const currentLightboxIndex = ref(0);

const openLightbox = (index: number) => {
    currentLightboxIndex.value = Math.max(0, Math.min(index, galleryPhotos.value.length - 1));
    isLightboxOpen.value = true;
    lockScroll();
};

const closeLightbox = () => {
    isLightboxOpen.value = false;
    unlockScroll();
};

const nextLightbox = () => {
    if (galleryPhotos.value.length === 0) return;
    currentLightboxIndex.value = (currentLightboxIndex.value + 1) % galleryPhotos.value.length;
};

const prevLightbox = () => {
    if (galleryPhotos.value.length === 0) return;
    currentLightboxIndex.value = (currentLightboxIndex.value - 1 + galleryPhotos.value.length) % galleryPhotos.value.length;
};

const handleGlobalKeyDown = (e: KeyboardEvent) => {
    if (!isLightboxOpen.value) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') nextLightbox();
    if (e.key === 'ArrowLeft') prevLightbox();
};

onMounted(() => {
    window.addEventListener('keydown', handleGlobalKeyDown);
    onGalleryScroll();
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleGlobalKeyDown);
    unlockScroll();
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
</script>

<template>
    <AppLayout :title="`${article.title} — Колледж Туран`">
        <!-- =================================================================
             1. HERO / INTRO BANNER (Full Width Title with 20px Bottom Margin)
             ================================================================= -->
        <IntroCard
            :bg-image="article.image_url || '/images/Intro_back.jpg'"
            :bg-blur="true"
            :title="article.title"
            :full-width="true"
            :title-class="isLongTitle
                ? 'font-heading font-medium text-base sm:text-xl md:text-2xl lg:text-[28px] leading-[1.25]'
                : 'font-heading font-semibold sm:font-black text-xl sm:text-3xl md:text-[38px] lg:text-[42px] leading-[1.2]'"
            :breadcrumbs="[
                { label: 'Главная', href: '/' },
                { label: 'Новости', href: '/news' },
                { label: article.title },
            ]"
        />

        <!-- =================================================================
             2. ARTICLE CONTENT (White Background, No Card Border/Padding)
             ================================================================= -->
        <section class="pt-2 sm:pt-4 pb-16 sm:pb-24 bg-white">
            <Container>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-start">
                    <!-- Main Article Body: No inner card, no border, no padding -->
                    <article class="lg:col-span-8 space-y-6 sm:space-y-7">
                        <!-- Featured Image (Max height 500px, rounded) with click-to-zoom popup -->
                        <div
                            v-if="article.image_url"
                            @click="openLightbox(0)"
                            class="rounded-[20px] sm:rounded-[36px] overflow-hidden max-h-[500px] w-full bg-slate-100 shadow-none cursor-pointer group relative"
                        >
                            <img
                                :src="article.image_url"
                                :alt="article.title"
                                class="w-full max-h-[500px] h-[260px] sm:h-[400px] lg:h-[480px] object-cover transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-105"
                            />
                            <!-- Zoom indicator on hover -->
                            <div class="absolute bottom-3 right-3 sm:bottom-4 sm:right-4 bg-black/60 backdrop-blur-md text-white text-[11px] sm:text-xs px-3 sm:px-3.5 py-1.5 rounded-full flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                <Maximize2 class="w-3.5 h-3.5" />
                                <span>Открыть фото</span>
                            </div>
                        </div>

                        <!-- Date Badge -->
                        <div class="text-xs sm:text-sm md:text-base text-slate-500 font-medium">
                            Опубликовано: {{ formatNewsDate(article.published_at) }}
                        </div>

                        <!-- Article Excerpt and Rich Text Content -->
                        <div class="space-y-4 sm:space-y-5 text-slate-800 leading-relaxed font-sans">
                            <p v-if="article.excerpt" class="text-lg sm:text-xl md:text-2xl font-heading font-bold text-slate-900 leading-snug">
                                {{ article.excerpt }}
                            </p>
                            <div class="text-sm sm:text-base md:text-lg text-slate-700 leading-relaxed space-y-4" v-html="article.content"></div>
                        </div>

                        <!-- Photo Gallery Slider: Comfortable compact spacing on mobile, generous on desktop -->
                        <div v-if="galleryPhotos && galleryPhotos.length" class="pt-6 sm:pt-16 mt-6 sm:mt-14 border-t border-slate-200/80 space-y-4 sm:space-y-5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h3 class="font-heading font-bold text-lg sm:text-xl md:text-2xl text-slate-900 leading-tight">
                                        Фотографии события
                                    </h3>
                                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">
                                        Нажмите на любое фото для просмотра в полном размере
                                    </p>
                                </div>

                                <!-- Slider Controls: Compact pagination -->
                                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                                    <span class="text-xs font-semibold text-slate-400 font-heading tabular-nums mr-1">
                                        {{ activeGalleryIndex + 1 }} / {{ galleryPhotos.length }}
                                    </span>
                                    <button
                                        type="button"
                                        @click="scrollGallery('prev')"
                                        :disabled="!canScrollLeft"
                                        class="w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-slate-200 flex items-center justify-center text-slate-700 hover:bg-slate-900 hover:text-white hover:border-slate-900 disabled:opacity-25 transition-all duration-300 cursor-pointer disabled:cursor-not-allowed"
                                        aria-label="Предыдущее фото"
                                    >
                                        <ArrowLeft class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                                    </button>
                                    <button
                                        type="button"
                                        @click="scrollGallery('next')"
                                        :disabled="!canScrollRight"
                                        class="w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-slate-200 flex items-center justify-center text-slate-700 hover:bg-slate-900 hover:text-white hover:border-slate-900 disabled:opacity-25 transition-all duration-300 cursor-pointer disabled:cursor-not-allowed"
                                        aria-label="Следующее фото"
                                    >
                                        <ArrowRight class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                                    </button>
                                </div>
                            </div>

                            <!-- Slider Track: Bleed out to screen edge on mobile (-mx-[14px] px-[14px]) -->
                            <div
                                ref="galleryTrackRef"
                                @scroll="onGalleryScroll"
                                @pointerdown="onPointerDown"
                                @pointermove="onPointerMove"
                                @pointerup="onPointerUp"
                                @pointercancel="onPointerUp"
                                @touchstart.passive="onTouchStart"
                                @touchmove.passive="onTouchMove"
                                @touchend.passive="onTouchEnd"
                                class="-mx-[14px] px-[14px] sm:mx-0 sm:px-0 flex items-center gap-3 sm:gap-4 overflow-x-auto scrollbar-none py-1 select-none cursor-grab active:cursor-grabbing snap-x snap-mandatory scroll-pl-[14px] sm:scroll-pl-0"
                                style="-webkit-overflow-scrolling: touch; touch-action: pan-x pan-y; overscroll-behavior-x: contain;"
                            >
                                <div
                                    v-for="(photo, idx) in galleryPhotos"
                                    :key="idx"
                                    @click="onSlideClick(idx)"
                                    class="snap-start shrink-0 w-[220px] sm:w-[280px] md:w-[320px] h-[155px] sm:h-[195px] md:h-[225px] rounded-xl sm:rounded-3xl overflow-hidden relative group bg-[#f5f5f7] border border-slate-200/80 shadow-none hover:shadow-lg transition-all duration-300 cursor-pointer"
                                >
                                    <!-- Blurred background to cushion any unusual photo dimension -->
                                    <div
                                        class="absolute inset-0 bg-cover bg-center blur-md opacity-25 scale-110 pointer-events-none"
                                        :style="{ backgroundImage: `url('${photo}')` }"
                                    />

                                    <!-- Crisp Foreground Photo: object-cover preserves layout harmony -->
                                    <img
                                        :src="photo"
                                        :alt="`${article.title} - Фото ${idx + 1}`"
                                        class="relative z-10 w-full h-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-105 pointer-events-none"
                                        loading="lazy"
                                    />

                                    <!-- Soft Hover Overlay with Zoom Pill -->
                                    <div class="absolute inset-0 z-20 bg-black/25 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center pointer-events-none">
                                        <div class="px-3.5 py-1.5 rounded-full bg-white/95 backdrop-blur-md text-slate-900 font-heading font-semibold text-xs flex items-center gap-1.5 shadow-md transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                            <Maximize2 class="w-3.5 h-3.5 text-slate-700" />
                                            <span>Увеличить</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Actions: Comfortable compact spacing on mobile, generous on desktop -->
                        <div class="pt-6 sm:pt-20 mt-6 sm:mt-16 border-t border-slate-200/80 flex items-center justify-start">
                            <Link href="/news">
                                <Button
                                    variant="outline"
                                    size="default"
                                    class="gap-2 rounded-full font-heading font-semibold text-[13px] sm:text-[14px] leading-[14px] px-6 sm:px-8 py-3 sm:py-3.5 border-slate-300 text-slate-800 hover:bg-slate-100 group transition-all duration-300"
                                >
                                    <ArrowLeft class="w-4 h-4 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:-translate-x-1" />
                                    <span>Все новости</span>
                                </Button>
                            </Link>
                        </div>
                    </article>

                    <!-- Sidebar: Floating / Sticky on desktop without bottom CTA button -->
                    <aside class="lg:col-span-4 lg:pl-4 self-start lg:sticky lg:top-8 z-10 transition-all duration-300">
                        <div class="bg-slate-50 rounded-[24px] sm:rounded-[28px] p-5 sm:p-7 border-0 shadow-none space-y-5">
                            <div class="flex items-center justify-between pb-3.5 border-b border-slate-200/60">
                                <h4 class="font-heading font-bold text-lg sm:text-xl text-slate-900">
                                    Другие события
                                </h4>
                                <Link
                                    href="/news"
                                    class="text-xs font-semibold text-slate-400 hover:text-slate-900 transition-colors uppercase tracking-wider"
                                >
                                    Все новости
                                </Link>
                            </div>
                            <div class="space-y-4">
                                <div
                                    v-for="item in recentNews"
                                    :key="item.id"
                                    class="pb-4 border-b border-slate-200/60 last:border-0 last:pb-0 group"
                                >
                                    <span class="text-xs text-slate-400 font-medium block mb-1">
                                        {{ formatNewsDate(item.published_at) }}
                                    </span>
                                    <Link
                                        :href="`/news/${item.slug}`"
                                        class="font-heading font-semibold text-[14px] sm:text-[16px] text-slate-800 group-hover:text-black transition-colors line-clamp-2 leading-snug"
                                    >
                                        {{ item.title }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </Container>
        </section>

        <!-- =================================================================
             3. MODAL: Fullscreen Image Lightbox (Попап фотки при нажатии)
             ================================================================= -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="isLightboxOpen"
                    class="fixed inset-0 z-[100] bg-black/95 backdrop-blur-xl flex flex-col justify-between p-4 sm:p-6 select-none"
                    @click.self="closeLightbox"
                >
                    <!-- Lightbox Top Bar -->
                    <div class="flex items-center justify-between text-white py-2 px-2 z-10">
                        <div class="flex items-center gap-3">
                            <span class="text-xs sm:text-sm uppercase tracking-wider font-semibold text-slate-400 font-heading">
                                Фото {{ currentLightboxIndex + 1 }} из {{ galleryPhotos.length }}
                            </span>
                            <span class="hidden sm:inline text-slate-600">|</span>
                            <span class="hidden sm:inline text-xs text-slate-300 font-medium truncate max-w-md">
                                {{ article.title }}
                            </span>
                        </div>

                        <button
                            type="button"
                            @click="closeLightbox"
                            class="px-6 sm:px-8 py-2.5 sm:py-3 rounded-full bg-white/15 hover:bg-white/25 border border-white/25 hover:border-white/45 text-white font-heading font-semibold text-xs sm:text-sm tracking-wider uppercase flex items-center justify-center gap-2.5 transition-all duration-300 cursor-pointer active:scale-95 shadow-lg backdrop-blur-md"
                            aria-label="Закрыть просмотр"
                        >
                            <span>Закрыть</span>
                            <X class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
                        </button>
                    </div>

                    <!-- Lightbox Main Stage -->
                    <div class="flex-1 relative flex items-center justify-center p-2 sm:p-6 min-h-0" @click.self="closeLightbox">
                        <!-- Previous Arrow -->
                        <button
                            v-if="galleryPhotos.length > 1"
                            type="button"
                            @click.stop="prevLightbox"
                            class="absolute left-2 sm:left-6 z-20 w-11 h-11 sm:w-14 sm:h-14 rounded-full bg-black/60 hover:bg-white text-white hover:text-black border border-white/20 flex items-center justify-center backdrop-blur-md transition-all duration-200 cursor-pointer shadow-xl active:scale-95"
                            aria-label="Предыдущее фото"
                        >
                            <ChevronLeft class="w-6 h-6 sm:w-7 sm:h-7" />
                        </button>

                        <!-- Display Photo (Preserves exact dimensions and full clarity without stretching) -->
                        <div class="relative max-h-full max-w-full flex items-center justify-center" @click.stop>
                            <img
                                :key="currentLightboxIndex"
                                :src="galleryPhotos[currentLightboxIndex]"
                                :alt="`${article.title} - Полноразмерное фото ${currentLightboxIndex + 1}`"
                                class="max-h-[72vh] sm:max-h-[78vh] max-w-[92vw] sm:max-w-[85vw] object-contain rounded-xl sm:rounded-2xl shadow-2xl transition-all duration-300"
                            />
                        </div>

                        <!-- Next Arrow -->
                        <button
                            v-if="galleryPhotos.length > 1"
                            type="button"
                            @click.stop="nextLightbox"
                            class="absolute right-2 sm:right-6 z-20 w-11 h-11 sm:w-14 sm:h-14 rounded-full bg-black/60 hover:bg-white text-white hover:text-black border border-white/20 flex items-center justify-center backdrop-blur-md transition-all duration-200 cursor-pointer shadow-xl active:scale-95"
                            aria-label="Следующее фото"
                        >
                            <ChevronRight class="w-6 h-6 sm:w-7 sm:h-7" />
                        </button>
                    </div>

                    <!-- Lightbox Bottom Thumbnails Strip -->
                    <div
                        v-if="galleryPhotos.length > 1"
                        class="w-full flex items-center justify-center gap-2 sm:gap-3 overflow-x-auto scrollbar-none py-2 px-4 z-10"
                    >
                        <button
                            v-for="(thumb, tIdx) in galleryPhotos"
                            :key="tIdx"
                            type="button"
                            @click.stop="currentLightboxIndex = tIdx"
                            class="relative h-12 w-16 sm:h-14 sm:w-20 rounded-lg overflow-hidden shrink-0 border-2 transition-all duration-200 cursor-pointer bg-slate-800"
                            :class="currentLightboxIndex === tIdx ? 'border-white scale-105 opacity-100 shadow-md ring-2 ring-white/50' : 'border-transparent opacity-50 hover:opacity-80'"
                        >
                            <img :src="thumb" class="w-full h-full object-cover" />
                        </button>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AppLayout>
</template>
