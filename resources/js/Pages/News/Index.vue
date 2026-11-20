<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/shared/Container.vue';
import IntroCard from '@/Components/layout/IntroCard.vue';
import { ArrowLeft, ArrowRight } from 'lucide-vue-next';

interface NewsItem {
    id: number;
    title: string;
    slug: string;
    category: string;
    published_at: string;
    excerpt: string;
    image_url: string;
}

interface PaginationProps {
    data: NewsItem[];
    current_page: number;
    last_page: number;
    next_page_url: string | null;
    prev_page_url: string | null;
}

defineProps<{
    news: PaginationProps;
    categories: string[];
    currentCategory?: string;
}>();

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
    <AppLayout title="НОВОСТИ — Колледж Туран">
        <!-- =================================================================
             1. HERO / INTRO BANNER (Main Header on intro background)
             ================================================================= -->
        <IntroCard
            title="НОВОСТИ"
            :breadcrumbs="[
                { label: 'Главная', href: '/' },
                { label: 'Новости' },
            ]"
        />

        <!-- =================================================================
             2. NEWS MASONRY GRID (Reduced Top Padding, Hover Effects, No Blue Text)
             ================================================================= -->
        <section class="pt-2 sm:pt-4 pb-16 sm:pb-24 bg-white">
            <Container>
                <!-- Native Responsive Masonry Grid -->
                <div class="columns-1 md:columns-2 lg:columns-3 gap-8 [column-fill:_balance]">
                    <article
                        v-for="item in news.data"
                        :key="item.id"
                        class="break-inside-avoid mb-8 group cursor-pointer"
                    >
                        <!-- Card Image with Hover Zoom & Floating Date Pill -->
                        <Link
                            :href="`/news/${item.slug}`"
                            class="relative block rounded-[24px] sm:rounded-[28px] overflow-hidden bg-slate-100 mb-3.5"
                        >
                            <img
                                :src="item.image_url"
                                :alt="item.title"
                                class="w-full h-auto object-cover transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-[1.03]"
                            />
                            <!-- Date badge pill like on homepage -->
                            <div class="absolute bottom-3.5 right-3.5 bg-white/95 text-slate-900 text-xs font-heading font-medium px-3.5 py-1.5 rounded-full shadow-sm backdrop-blur-md">
                                {{ formatNewsDate(item.published_at) }}
                            </div>
                        </Link>

                        <!-- Title: No blue text on hover, stays dark / crisp black -->
                        <h2 class="font-heading font-bold text-base sm:text-[17px] text-slate-900 group-hover:text-black transition-colors line-clamp-3 leading-snug">
                            <Link :href="`/news/${item.slug}`">
                                {{ item.title }}
                            </Link>
                        </h2>

                        <!-- Excerpt snippet -->
                        <p v-if="item.excerpt" class="text-xs sm:text-sm text-slate-600 line-clamp-2 mt-2 leading-relaxed">
                            {{ item.excerpt }}
                        </p>
                    </article>
                </div>

                <!-- Pagination pushed to right side (justify-end) with near-black styling -->
                <div v-if="news.last_page > 1" class="flex items-center justify-end gap-3 mt-12 sm:mt-16">
                    <Link v-if="news.prev_page_url" :href="news.prev_page_url">
                        <button
                            type="button"
                            class="h-11 px-6 rounded-full bg-slate-900 hover:bg-black text-white font-heading font-semibold text-[13px] leading-[13px] tracking-wide inline-flex items-center gap-2 cursor-pointer transition-all duration-300 active:scale-95 group shadow-none"
                        >
                            <ArrowLeft class="w-4 h-4 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:-translate-x-1" />
                            <span>Предыдущая</span>
                        </button>
                    </Link>

                    <span class="text-sm font-heading font-bold text-slate-900 px-3">
                        {{ news.current_page }} / {{ news.last_page }}
                    </span>

                    <Link v-if="news.next_page_url" :href="news.next_page_url">
                        <button
                            type="button"
                            class="h-11 px-6 rounded-full bg-slate-900 hover:bg-black text-white font-heading font-semibold text-[13px] leading-[13px] tracking-wide inline-flex items-center gap-2 cursor-pointer transition-all duration-300 active:scale-95 group shadow-none"
                        >
                            <span>Следующая</span>
                            <ArrowRight class="w-4 h-4 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:translate-x-1" />
                        </button>
                    </Link>
                </div>
            </Container>
        </section>
    </AppLayout>
</template>
