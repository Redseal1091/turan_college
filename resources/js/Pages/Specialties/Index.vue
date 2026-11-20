<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/shared/Container.vue';
import IntroCard from '@/Components/layout/IntroCard.vue';
import Card from '@/Components/ui/card/Card.vue';
import SpotlightCard from '@/Components/bits/SpotlightCard.vue';
import Button from '@/Components/ui/button/Button.vue';
import { getSpecialtyImage } from '@/lib/specialtyImages';
import {
    Clock,
    ArrowRight,
    BookOpen
} from 'lucide-vue-next';

interface Specialty {
    id: number;
    code: string;
    title: string;
    slug: string;
    qualification: string;
    department: string;
    base_education: string;
    duration: string;
    tuition_fee: number;
    description: string;
    skills: string[];
}

defineProps<{
    specialties: Specialty[];
    departments?: string[];
    filters?: {
        department?: string;
        base?: string;
    };
}>();

const formatDurationLines = (duration?: string) => {
    if (!duration) return [];
    if (duration.includes('|')) {
        return duration.split('|').map((s) => s.trim());
    }
    return [duration];
};
</script>

<template>
    <AppLayout title="Специальности — Колледж Туран">
        <!-- Header / Intro Banner with Main Header -->
        <IntroCard
            title="СПЕЦИАЛЬНОСТИ"
            :breadcrumbs="[
                { label: 'Главная', href: '/' },
                { label: 'Специальности' },
            ]"
        />

        <!-- Specialties Cards Grid (Clean white background, reduced top gap) -->
        <section class="pt-6 sm:pt-8 pb-12 sm:pb-16 bg-white">
            <Container>
                <div v-if="specialties.length === 0" class="text-center py-16">
                    <BookOpen class="w-12 h-12 text-slate-300 mx-auto mb-3" />
                    <h3 class="font-heading font-bold text-xl text-slate-700">Специальности не найдены</h3>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    <SpotlightCard
                        v-for="item in specialties"
                        :key="item.id"
                        class-name="p-5 sm:p-8 rounded-[24px] sm:rounded-[30px] bg-slate-50 border-0 shadow-none transition-all duration-300 group"
                        spotlight-color="rgba(0, 130, 201, 0.12)"
                    >
                        <div class="h-full flex flex-col justify-between">
                            <div>
                                <!-- Top row: Code and Department -->
                                <div class="flex items-center justify-between gap-2 mb-3.5 sm:mb-5">
                                    <span class="font-mono text-xs sm:text-sm font-bold px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl bg-white text-slate-700 border-0 shadow-none">
                                        {{ item.code }}
                                    </span>
                                    <span class="text-xs sm:text-sm font-semibold px-2.5 sm:px-3 py-1 rounded-xl bg-white text-[#0F4C3A] border-0 shadow-none">
                                        {{ item.department }}
                                    </span>
                                </div>

                                <!-- Title and 3D Visual Icon -->
                                <div class="flex items-start justify-between gap-3 sm:gap-4 mb-3 sm:mb-4">
                                    <div class="flex-1">
                                        <h3 class="font-heading font-bold text-lg sm:text-[24px] leading-snug sm:leading-[1.2] text-slate-900 group-hover:text-[#0082C9] transition-colors">
                                            <Link :href="`/specialties/${item.slug}`">{{ item.title }}</Link>
                                        </h3>
                                    </div>
                                    <div class="shrink-0 w-14 h-14 sm:w-22 sm:h-22 -mt-1 group-hover:scale-110 transition-transform duration-300">
                                        <img
                                            :src="getSpecialtyImage(item)"
                                            :alt="item.title"
                                            class="w-full h-full object-contain"
                                        />
                                    </div>
                                </div>

                                <!-- Duration: Clock icon top-aligned, 9 & 11 grades split into 2 lines -->
                                <div class="text-sm text-slate-600 font-medium mb-3 sm:mb-4 flex items-start gap-2.5">
                                    <Clock class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" />
                                    <div class="space-y-0.5 leading-snug">
                                        <div v-for="(line, idx) in formatDurationLines(item.duration)" :key="idx">
                                            {{ line }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Description: compact on mobile -->
                                <p class="text-sm sm:text-base text-slate-600 line-clamp-2 sm:line-clamp-3 mb-4 sm:mb-6 leading-relaxed">
                                    {{ item.description }}
                                </p>

                                <!-- Key Skills -->
                                <div class="flex flex-wrap gap-1.5 sm:gap-2 mb-4 sm:mb-6">
                                    <span
                                        v-for="(skill, idx) in (item.skills || []).slice(0, 3)"
                                        :key="idx"
                                        class="text-xs sm:text-sm px-2.5 sm:px-3 py-1 rounded-lg bg-white text-[#0082C9] font-medium border-0 shadow-none"
                                    >
                                        {{ skill }}
                                    </span>
                                </div>
                            </div>

                            <!-- Card Footer: Tuition and Button (On mobile: button moves to next line, full width) -->
                            <div class="pt-4 sm:pt-6 border-t border-slate-200/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                                <div>
                                    <span class="text-xs sm:text-sm text-slate-400 block mb-0.5">Стоимость:</span>
                                    <span class="font-heading font-bold text-base sm:text-lg text-slate-900">{{ item.tuition_fee.toLocaleString() }} ₸/год</span>
                                </div>
                                <Link :href="`/specialties/${item.slug}`" class="w-full sm:w-auto">
                                    <Button size="default" variant="default" class="w-full sm:w-auto rounded-full px-5 sm:px-6 py-2.5 sm:py-3 font-heading font-semibold text-[14px] leading-[14px] gap-2 group justify-center bg-[#0082C9] hover:bg-[#0070ad] text-white border-0 shadow-none">
                                        <span>Подробнее</span>
                                        <ArrowRight class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" />
                                    </Button>
                                </Link>
                            </div>
                        </div>
                    </SpotlightCard>
                </div>
            </Container>
        </section>
    </AppLayout>
</template>
