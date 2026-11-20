<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Container from '@/Components/shared/Container.vue';
import MainHeader from '@/Components/layout/MainHeader.vue';

export interface BreadcrumbItem {
    label: string;
    href?: string;
}

const props = withDefaults(
    defineProps<{
        bgImage?: string;
        bgBlur?: boolean;
        variant?: 'home' | 'inner' | 'clean';
        noBackground?: boolean;
        title?: string;
        titleClass?: string;
        fullWidth?: boolean;
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        bgImage: '/images/Intro_back.jpg',
        bgBlur: false,
        variant: 'inner',
        noBackground: false,
        titleClass: '',
        fullWidth: false,
    }
);

const isCleanMode = computed(() => props.noBackground || props.variant === 'clean');
</script>

<template>
    <div class="w-full">
        <!-- 1. CLEAN MODE: Clean white header & title without photo background -->
        <section v-if="isCleanMode" class="w-full bg-white mb-6 sm:mb-10">
            <!-- Main Header with dark text on white page -->
            <MainHeader :dark-text="true" class="relative z-20 pb-4 border-b border-slate-100" />

            <!-- Title & Breadcrumbs in standard Container -->
            <div class="pt-6 sm:pt-20 pb-2">
                <Container class="w-full">
                    <div v-if="title || (breadcrumbs && breadcrumbs.length)" :class="fullWidth ? 'w-full' : 'max-w-5xl'">
                        <h1
                            v-if="title"
                            :class="[
                                'tracking-tight uppercase text-slate-900 mb-3 sm:mb-4',
                                titleClass || (title.length > 130
                                    ? 'font-heading font-medium text-lg sm:text-2xl md:text-3xl leading-[1.25]'
                                    : 'font-heading font-black text-2xl sm:text-4xl md:text-5xl lg:text-[52px] leading-[1.12]')
                            ]"
                        >
                            {{ title }}
                        </h1>

                        <!-- Breadcrumbs: PT Sans, 16px, dark text palette -->
                        <nav
                            v-if="breadcrumbs && breadcrumbs.length"
                            class="flex flex-wrap items-center gap-1.5 sm:gap-2 font-['PT_Sans',sans-serif] text-[15px] sm:text-[16px] leading-[16px]"
                            aria-label="Хлебные крошки"
                        >
                            <template v-for="(crumb, idx) in breadcrumbs" :key="idx">
                                <Link
                                    v-if="crumb.href && idx < breadcrumbs.length - 1"
                                    :href="crumb.href"
                                    class="text-slate-500 hover:text-slate-900 transition-colors"
                                >
                                    {{ crumb.label }}
                                </Link>
                                <span
                                    v-else-if="idx < breadcrumbs.length - 1"
                                    class="text-slate-400"
                                >
                                    {{ crumb.label }}
                                </span>
                                <span
                                    v-else
                                    class="text-slate-900 font-semibold"
                                    aria-current="page"
                                >
                                    {{ crumb.label }}
                                </span>

                                <span v-if="idx < breadcrumbs.length - 1" class="text-slate-300 select-none">/</span>
                            </template>
                        </nav>
                    </div>

                    <slot />
                </Container>
            </div>
        </section>

        <!-- 2. STANDARD MODE: Rounded Card with Photo Background (Home, etc.) -->
        <section v-else class="w-full px-0 sm:px-[10px] max-w-[1820px] mx-auto mb-8 sm:mb-12 lg:mb-[60px]">
            <!-- Intro card: flush to screen edges and no border-radius on mobile, rounded on desktop -->
            <div
                class="relative overflow-hidden rounded-none sm:rounded-[36px] bg-[#0c1219] text-white bg-cover bg-center w-full max-w-[1800px] mx-auto flex flex-col justify-between"
                :class="variant === 'home' ? 'min-h-[440px] sm:min-h-[540px] lg:min-h-[650px]' : 'min-h-[250px] sm:min-h-[320px] md:min-h-[380px]'"
                :style="!bgBlur && bgImage ? { backgroundImage: `url('${bgImage}')` } : undefined"
            >
                <!-- Blurred background image layer when bgBlur is enabled -->
                <template v-if="bgBlur && bgImage">
                    <div
                        class="absolute inset-0 bg-cover bg-center scale-110 blur-2xl brightness-[0.45] pointer-events-none transform-gpu"
                        :style="{ backgroundImage: `url('${bgImage}')` }"
                    />
                    <div class="absolute inset-0 bg-black/35 pointer-events-none" />
                </template>

                <!-- 1. Main Header inside the standard site Container -->
                <MainHeader class="relative z-20" />

                <!-- 2. Intro Content inside standard site Container with comfortable top breathing room on mobile -->
                <div class="relative z-10 flex-1 flex flex-col justify-center pt-6 pb-5 sm:py-8 lg:py-10">
                    <Container class="w-full">
                        <!-- Title and Breadcrumbs for Inner Pages -->
                        <div v-if="title || (breadcrumbs && breadcrumbs.length)" :class="fullWidth ? 'w-full' : 'max-w-4xl'">
                            <!-- Заголовок с отступом 20пкс вниз до хлебных крошек -->
                            <h1
                                v-if="title"
                                :class="[
                                    'tracking-tight uppercase text-white mb-3 sm:mb-5 pt-1.5 sm:pt-0',
                                    titleClass || (title.length > 130
                                        ? 'font-heading font-medium text-base sm:text-xl md:text-2xl lg:text-[28px] leading-[1.25]'
                                        : 'font-heading font-semibold sm:font-extrabold text-xl sm:text-3xl md:text-4xl lg:text-[54px] leading-[1.15]')
                                ]"
                            >
                                {{ title }}
                            </h1>

                            <!-- После заголовка страницы идут хлебные крошки, шрифт ПТсанс, высота 16пкс -->
                            <!-- Последний активный пункт бредкрамбов яркий белый, прошлые ссылки опасити 50% -->
                            <nav
                                v-if="breadcrumbs && breadcrumbs.length"
                                class="flex flex-wrap items-center gap-1.5 sm:gap-2 font-['PT_Sans',sans-serif] text-[13px] sm:text-[16px] leading-[16px]"
                                aria-label="Хлебные крошки"
                            >
                                <template v-for="(crumb, idx) in breadcrumbs" :key="idx">
                                    <Link
                                        v-if="crumb.href && idx < breadcrumbs.length - 1"
                                        :href="crumb.href"
                                        class="text-white/50 hover:text-white transition-opacity"
                                    >
                                        {{ crumb.label }}
                                    </Link>
                                    <span
                                        v-else-if="idx < breadcrumbs.length - 1"
                                        class="text-white/50"
                                    >
                                        {{ crumb.label }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-white font-medium"
                                        aria-current="page"
                                    >
                                        {{ crumb.label }}
                                    </span>

                                    <!-- Separator '/' with 50% opacity -->
                                    <span v-if="idx < breadcrumbs.length - 1" class="text-white/50 select-none">/</span>
                                </template>
                            </nav>
                        </div>

                        <slot />
                    </Container>
                </div>
            </div>
        </section>
    </div>
</template>
