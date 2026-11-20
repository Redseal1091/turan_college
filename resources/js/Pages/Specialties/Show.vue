<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/shared/Container.vue';
import IntroCard from '@/Components/layout/IntroCard.vue';
import Button from '@/Components/ui/button/Button.vue';
import { getSpecialtyImage } from '@/lib/specialtyImages';
import { useApplyModal } from '@/composables/useApplyModal';
import {
    GraduationCap,
    Clock,
    CheckCircle2,
    Briefcase,
    ArrowRight,
    BookOpen,
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
    study_form: string;
    tuition_fee: number;
    grants_count: number;
    description: string;
    skills: string[];
    career_prospects: string[];
}

const props = defineProps<{
    specialty: Specialty;
    related: Specialty[];
}>();

const { openApplyModal } = useApplyModal();
</script>

<template>
    <AppLayout :title="`${specialty.title} — Колледж Туран`">
        <!-- Breadcrumb & Intro Banner -->
        <IntroCard
            :title="specialty.title"
            :breadcrumbs="[
                { label: 'Главная', href: '/' },
                { label: 'Специальности', href: '/specialties' },
                { label: specialty.title },
            ]"
        />

        <!-- Main Details Content: Clean white background, blocks have slightly grayish bg (bg-slate-50), NO borders, NO shadows -->
        <section class="pt-6 sm:pt-8 pb-12 sm:pb-16 bg-white">
            <Container>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">
                    <!-- Left: Description and Competencies -->
                    <div class="lg:col-span-8 space-y-5 sm:space-y-6">
                        <!-- Overview Card (Slightly grayish background, no borders, no shadows) -->
                        <div class="p-6 sm:p-8 rounded-[26px] sm:rounded-[30px] bg-slate-50 border-0 shadow-none text-slate-900">
                            <div class="flex items-start justify-between gap-4 mb-3 sm:mb-4">
                                <h3 class="font-heading font-bold text-xl sm:text-2xl text-slate-900 leading-snug">
                                    Описание образовательной программы
                                </h3>
                                <div class="shrink-0 w-14 h-14 sm:w-20 sm:h-20 -mt-1">
                                    <img
                                        :src="getSpecialtyImage(specialty)"
                                        :alt="specialty.title"
                                        class="w-full h-full object-contain"
                                    />
                                </div>
                            </div>
                            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                                {{ specialty.description }}
                            </p>

                            <!-- Key Specs Grid: Pure white inner pills against grayish card -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mt-6 pt-5 border-t border-slate-200/50">
                                <div class="p-4 rounded-2xl bg-white border-0 shadow-none">
                                    <span class="text-xs text-slate-400 block mb-1">Форма обучения:</span>
                                    <span class="font-heading font-bold text-sm text-slate-800">{{ specialty.study_form }}</span>
                                </div>
                                <div class="p-4 rounded-2xl bg-white border-0 shadow-none">
                                    <span class="text-xs text-slate-400 block mb-1">Срок обучения:</span>
                                    <span class="font-heading font-bold text-sm text-slate-800">{{ specialty.duration }}</span>
                                </div>
                                <div class="p-4 rounded-2xl bg-white border-0 shadow-none">
                                    <span class="text-xs text-slate-400 block mb-1">Стоимость обучения:</span>
                                    <span class="font-heading font-bold text-sm text-[#0082C9]">{{ specialty.tuition_fee.toLocaleString() }} ₸ / год</span>
                                </div>
                            </div>
                        </div>

                        <!-- Skills Acquired -->
                        <div class="p-6 sm:p-8 rounded-[26px] sm:rounded-[30px] bg-slate-50 border-0 shadow-none text-slate-900">
                            <div class="flex items-center gap-2 mb-4 sm:mb-6">
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-white text-[#0082C9] border-0 shadow-none flex items-center justify-center">
                                    <BookOpen class="w-4 h-4 sm:w-5 sm:h-5" />
                                </div>
                                <h3 class="font-heading font-bold text-xl sm:text-2xl text-slate-900 leading-snug">
                                    Чему научится студент
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
                                <div
                                    v-for="(skill, idx) in specialty.skills"
                                    :key="idx"
                                    class="flex items-start gap-2.5 p-3.5 sm:p-4 rounded-2xl bg-white border-0 shadow-none text-xs sm:text-sm font-medium text-slate-800"
                                >
                                    <CheckCircle2 class="w-4 h-4 text-[#0082C9] shrink-0 mt-0.5" />
                                    <span>{{ skill }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Career Prospects -->
                        <div class="p-6 sm:p-8 rounded-[26px] sm:rounded-[30px] bg-slate-50 border-0 shadow-none text-slate-900">
                            <div class="flex items-center gap-2 mb-4 sm:mb-6">
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-white text-[#FDA685] border-0 shadow-none flex items-center justify-center">
                                    <Briefcase class="w-4 h-4 sm:w-5 sm:h-5" />
                                </div>
                                <h3 class="font-heading font-bold text-xl sm:text-2xl text-slate-900 leading-snug">
                                    Кем сможет работать выпускник
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
                                <div
                                    v-for="(job, idx) in specialty.career_prospects"
                                    :key="idx"
                                    class="flex items-center gap-2.5 p-3.5 sm:p-4 rounded-2xl bg-white border-0 shadow-none text-xs sm:text-sm font-medium text-slate-800"
                                >
                                    <div class="w-2 h-2 rounded-full bg-[#FDA685] shrink-0"></div>
                                    <span>{{ job }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Sidebar: CTA Application Card (Slightly grayish bg, no borders, no shadows) -->
                    <div class="lg:col-span-4 sticky top-24 space-y-5 sm:space-y-6">
                        <div class="p-6 sm:p-8 rounded-[26px] sm:rounded-[28px] bg-slate-50 border-0 shadow-none text-slate-900">
                            <h3 class="font-heading font-bold text-lg sm:text-xl text-slate-900 mb-2 leading-snug">
                                Хотите учиться по этой специальности?
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 mb-5 leading-relaxed">
                                Подайте заявление прямо сейчас, приложите документы и получите оперативную консультацию приёмной комиссии.
                            </p>

                            <Button
                                @click="openApplyModal({ specialtyId: specialty.id, specialtyTitle: specialty.title })"
                                class="w-full justify-center bg-[#0082C9] hover:bg-[#0070ad] text-white rounded-full font-heading font-semibold text-[14px] leading-[14px] py-3.5 sm:py-4 gap-2.5 group cursor-pointer border-0 shadow-none transition-colors"
                            >
                                <GraduationCap class="w-4 h-4" />
                                <span>Подать заявление онлайн</span>
                                <ArrowRight class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" />
                            </Button>

                            <div class="mt-5 pt-4 border-t border-slate-200/50 text-center">
                                <span class="text-xs text-slate-500 block mb-1">Или позвоните в приёмную комиссию:</span>
                                <a
                                    href="tel:+77272604000"
                                    class="font-heading font-black text-lg sm:text-2xl text-[#0082C9] hover:underline block tracking-tight"
                                >
                                    +7 (727) 260-40-00
                                </a>
                            </div>
                        </div>

                        <!-- Related Specialties -->
                        <div v-if="related.length > 0" class="p-5 sm:p-7 rounded-[26px] sm:rounded-[28px] bg-slate-50 border-0 shadow-none text-slate-900">
                            <h4 class="font-heading font-bold text-xs sm:text-sm text-slate-900 mb-3 uppercase tracking-wider">
                                Другие специальности отделения
                            </h4>
                            <div class="space-y-2 sm:space-y-2.5">
                                <Link
                                    v-for="rel in related"
                                    :key="rel.id"
                                    :href="`/specialties/${rel.slug}`"
                                    class="block p-3 sm:p-3.5 rounded-2xl bg-white hover:bg-slate-100 transition-colors border-0 shadow-none"
                                >
                                    <div class="font-heading font-semibold text-xs sm:text-sm text-slate-900 mb-0.5">
                                        {{ rel.title }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ rel.code }}</div>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </Container>
        </section>
    </AppLayout>
</template>
