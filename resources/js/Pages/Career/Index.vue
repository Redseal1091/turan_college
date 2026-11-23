<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/shared/Container.vue';
import IntroCard from '@/Components/layout/IntroCard.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import Card from '@/Components/ui/card/Card.vue';
import Button from '@/Components/ui/button/Button.vue';
import {
    Briefcase,
    Building2,
    CheckCircle,
    TrendingUp,
    ExternalLink,
    GraduationCap,
    ArrowRight,
    FileCheck2,
    Users,
    Sparkles
} from 'lucide-vue-next';

interface PartnerItem {
    id: number;
    name: string;
    industry: string;
}

interface VacancyItem {
    title: string;
    company: string;
    type: string;
    salary: string;
    requirements: string;
}

interface PageData {
    id: number;
    slug: string;
    title: string;
    featured_image?: string | null;
    seo_title?: string | null;
    seo_description?: string | null;
    content?: Record<string, any> | null;
    is_active?: boolean;
}

const props = defineProps<{
    page?: PageData | null;
    partners: PartnerItem[];
    vacancies: VacancyItem[];
}>();

const defaultTips = [
    {
        num: '01',
        title: 'Четкая цель и специализация',
        desc: 'Укажите точную специальность и желаемую должность. Например: «Junior frontend-разработчик» или «Помощник бухгалтера».'
    },
    {
        num: '02',
        title: 'Учебные и реальные проекты',
        desc: 'Опишите практические кейсы, выполненные курсовые проекты, технологии и стек, которыми вы овладели во время учебы в колледже «Туран».'
    },
    {
        num: '03',
        title: 'Производственная практика',
        desc: 'Укажите организацию, где проходили практику, свои функциональные обязанности и достигнутые результаты.'
    },
    {
        num: '04',
        title: 'Soft Skills и активность',
        desc: 'Грамотность, пунктуальность, умение работать в команде, участие в студенческих клубах и олимпиадах WorldSkills.'
    }
];

const resumeTipsList = computed(() => {
    if (props.page?.content?.resume_guide && Array.isArray(props.page.content.resume_guide) && props.page.content.resume_guide.length > 0) {
        return props.page.content.resume_guide.map((tip: any, idx: number) => ({
            num: tip.step_num || `0${idx + 1}`,
            title: tip.title || defaultTips[idx % defaultTips.length].title,
            desc: tip.desc || defaultTips[idx % defaultTips.length].desc,
        }));
    }
    return defaultTips;
});
</script>

<template>
    <AppLayout :title="page?.seo_title || page?.title || 'Карьера и практика — Колледж Туран'">
        <!-- Banner with Main Header -->
        <IntroCard
            title="КАРЬЕРА"
            :bg-image="page?.featured_image ? (page.featured_image.startsWith('images/') ? '/' + page.featured_image : '/storage/' + page.featured_image) : undefined"
            :breadcrumbs="[
                { label: 'Главная', href: '/' },
                { label: 'Карьера' },
            ]"
        />

        <!-- Stats / Pillars -->
        <section class="py-16 bg-white">
            <Container>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 mb-16">
                    <div class="p-6 sm:p-8 rounded-[28px] bg-slate-50 border-0 shadow-none border-t-4 border-t-[#0082C9]">
                        <div class="font-heading font-extrabold text-3xl text-[#0082C9] mb-2">
                            {{ page?.content?.metrics?.employment_rate || '95%' }}
                        </div>
                        <h4 class="font-heading font-bold text-lg text-slate-900 mb-2">Уровень занятости</h4>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Официальное трудоустройство в партнерских компаниях или продолжение обучения в Университете «Туран» по ускоренной программе.
                        </p>
                    </div>

                    <div class="p-6 sm:p-8 rounded-[28px] bg-slate-50 border-0 shadow-none border-t-4 border-t-[#FDA685]">
                        <div class="font-heading font-extrabold text-3xl text-[#FDA685] mb-2">
                            {{ page?.content?.metrics?.partners_count || '150+' }}
                        </div>
                        <h4 class="font-heading font-bold text-lg text-slate-900 mb-2">Компаний-партнеров</h4>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Прямые долгосрочные меморандумы с Kaspi Bank, Halyk Bank, Kcell, IT-холдингами, туристическими фирмами и сетями отелей.
                        </p>
                    </div>

                    <div class="p-6 sm:p-8 rounded-[28px] bg-slate-50 border-0 shadow-none border-t-4 border-t-[#0082C9]">
                        <div class="font-heading font-extrabold text-3xl text-[#0082C9] mb-2">Со 2 курса</div>
                        <h4 class="font-heading font-bold text-lg text-slate-900 mb-2">Оплачиваемая практика</h4>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            {{ page?.content?.metrics?.dual_education_text || 'Возможность совмещения учебы по индивидуальному графику дуального обучения на реальных рабочих местах.' }}
                        </p>
                    </div>
                </div>

                <!-- Practice Info banner if present -->
                <div v-if="page?.content?.practice_info" class="p-6 sm:p-8 rounded-[28px] bg-slate-50 border-0 shadow-none mb-16 text-slate-700 text-sm sm:text-base leading-relaxed">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white text-[#0082C9] border-0 shadow-none flex items-center justify-center shrink-0">
                            <GraduationCap class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-lg text-slate-900 mb-2">Производственная практика и дуальное обучение</h3>
                            <p>{{ page.content.practice_info }}</p>
                        </div>
                    </div>
                </div>

                <!-- Vacancies List -->
                <div>
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <Badge variant="secondary" class="mb-1 bg-slate-100 text-slate-700 border-0">Актуальные предложения</Badge>
                            <h3 class="font-heading font-extrabold text-2xl sm:text-3xl text-slate-900">
                                Вакансии и стажировки для студентов
                            </h3>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(vac, idx) in vacancies"
                            :key="idx"
                            class="p-6 sm:p-7 rounded-[26px] bg-slate-50 border-0 shadow-none flex flex-col md:flex-row md:items-center justify-between gap-6 transition-transform duration-200 hover:-translate-y-0.5 text-slate-900"
                        >
                            <div class="space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-heading font-bold text-lg text-slate-900">{{ vac.title }}</h4>
                                    <span class="inline-block px-2.5 py-0.5 rounded-lg bg-white text-[#0082C9] text-sm font-semibold border-0 shadow-none">{{ vac.type }}</span>
                                </div>
                                <div class="text-sm font-semibold text-[#0082C9]">{{ vac.company }}</div>
                                <p class="text-sm text-slate-600 max-w-2xl leading-relaxed">
                                    Требования: {{ vac.requirements }}
                                </p>
                            </div>

                            <div class="flex flex-col sm:flex-row md:flex-col items-start md:items-end justify-between gap-3 shrink-0">
                                <span class="font-heading font-bold text-base sm:text-lg text-slate-900">{{ vac.salary }}</span>
                                <Button size="default" class="rounded-full px-6 py-3 font-heading font-semibold text-[14px] leading-[14px] gap-2 group bg-[#0082C9] hover:bg-[#0070ad] text-white border-0 shadow-none cursor-pointer">
                                    <span>Откликнуться через Центр карьеры</span>
                                    <ArrowRight class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </Container>
        </section>

        <!-- Official Advice: How to build resume -->
        <section class="py-16 bg-white border-t border-slate-100">
            <Container>
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <Badge variant="secondary" class="mb-2 bg-slate-100 text-slate-700 border-0">Методические рекомендации</Badge>
                    <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-slate-900">
                        Как составить идеальное резюме
                    </h2>
                    <p class="text-slate-600 mt-2 text-sm sm:text-base">
                        Рекомендации Центра карьеры колледжа «Туран» для студентов и выпускников.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        v-for="tip in resumeTipsList"
                        :key="tip.num"
                        class="p-6 rounded-[24px] bg-slate-50 border-0 shadow-none hover:-translate-y-0.5 transition-transform duration-200"
                    >
                        <div class="font-mono text-2xl font-extrabold text-[#0082C9] mb-3">
                            {{ tip.num }}
                        </div>
                        <h4 class="font-heading font-bold text-base text-slate-900 mb-2">
                            {{ tip.title }}
                        </h4>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            {{ tip.desc }}
                        </p>
                    </div>
                </div>
            </Container>
        </section>

        <!-- Partners Grid -->
        <section class="py-16 bg-white border-t border-slate-100">
            <Container>
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <Badge variant="secondary" class="mb-2 bg-slate-100 text-slate-700 border-0">Работодатели</Badge>
                    <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-slate-900">
                        Где проходят практику наши студенты
                    </h2>
                    <p class="text-slate-600 mt-2 text-sm sm:text-base">
                        Ведущие организации и холдинги Республики Казахстан.
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div
                        v-for="p in partners"
                        :key="p.id"
                        class="p-5 rounded-[22px] bg-slate-50 border-0 shadow-none text-center flex flex-col items-center justify-center hover:-translate-y-0.5 transition-transform duration-200"
                    >
                        <div class="font-heading font-bold text-base text-slate-900">{{ p.name }}</div>
                        <div class="text-sm text-slate-500 mt-1">{{ p.industry }}</div>
                    </div>
                </div>
            </Container>
        </section>
    </AppLayout>
</template>
