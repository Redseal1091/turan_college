<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/shared/Container.vue';
import IntroCard from '@/Components/layout/IntroCard.vue';
import SpotlightCard from '@/Components/bits/SpotlightCard.vue';
import Button from '@/Components/ui/button/Button.vue';
import {
    Award,
    Building2,
    CheckCircle,
    FileText,
    GraduationCap,
    HeartHandshake,
    ShieldCheck,
    Target,
    Users,
    Sparkles,
    BookOpen,
    MapPin,
    ArrowRight,
    ArrowUpRight
} from 'lucide-vue-next';

interface StaffMember {
    id: number;
    full_name: string;
    position: string;
    department: string;
    bio: string;
    photo_url?: string | null;
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
    staff: StaffMember[];
}>();

// Show leadership & key faculty members on About page
const featuredStaff = computed(() => {
    return props.staff.slice(0, 4);
});

const defaultPillars = [
    {
        title: '1. Инновации',
        desc: 'Мы активно внедряем цифровые технологии и практико-ориентированный подход в образовательный процесс.',
        icon: Sparkles,
        color: 'text-[#0082C9]',
        spotlight: 'rgba(0, 130, 201, 0.08)',
    },
    {
        title: '2. Партнёрства',
        desc: 'Колледж сотрудничает с ведущими предприятиями, что обеспечивает нашим студентам прохождение практики и трудоустройство.',
        icon: Building2,
        color: 'text-[#0F4C3A]',
        spotlight: 'rgba(15, 76, 58, 0.08)',
    },
    {
        title: '3. Преподаватели',
        desc: 'Наши педагоги — это опытные специалисты высшей категории, обладающие глубокими знаниями и практическим опытом.',
        icon: Users,
        color: 'text-amber-600',
        spotlight: 'rgba(245, 158, 11, 0.08)',
    },
    {
        title: '4. Качественное образование',
        desc: 'Учебные программы соответствуют современным стандартам МОН РК и учитывают актуальные мировые тренды.',
        icon: Award,
        color: 'text-indigo-600',
        spotlight: 'rgba(99, 102, 241, 0.08)',
    },
    {
        title: '5. Студенческая жизнь',
        desc: 'Мы создаём яркую атмосферу: IT-инкубатор, ENACTUS, дебаты, спортивные секции и творческие площадки.',
        icon: HeartHandshake,
        color: 'text-rose-600',
        spotlight: 'rgba(244, 63, 94, 0.08)',
    },
    {
        title: '6. Инфраструктура в центре',
        desc: 'г. Алматы, ул. Сатпаева 16а. Просторные аудитории, библиотека, столовая и охраняемое общежитие.',
        icon: MapPin,
        color: 'text-teal-600',
        spotlight: 'rgba(20, 184, 166, 0.08)',
    },
];

const pillarsList = computed(() => {
    if (props.page?.content?.pillars && Array.isArray(props.page.content.pillars) && props.page.content.pillars.length > 0) {
        return props.page.content.pillars.map((item: any, idx: number) => {
            const fallback = defaultPillars[idx % defaultPillars.length];
            return {
                title: item.title || fallback.title,
                desc: item.desc || fallback.desc,
                icon: fallback.icon,
                color: fallback.color,
                spotlight: fallback.spotlight,
            };
        });
    }
    return defaultPillars;
});
</script>

<template>
    <AppLayout :title="page?.seo_title || page?.title || 'О нас — Колледж Туран'">
        <!-- Hero / Intro Banner with Main Header -->
        <IntroCard
            title="О КОЛЛЕДЖЕ «ТУРАН»"
            :breadcrumbs="[
                { label: 'Главная', href: '/' },
                { label: 'О нас' },
            ]"
        />

        <!-- Main About Text & Why Us (from turan-college.edu.kz/ru/2050-o_nas.html) -->
        <section class="py-16 sm:py-24 bg-white">
            <Container>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center mb-16">
                    <div class="lg:col-span-7 space-y-5">
                        <div class="inline-block px-3 py-1 rounded-full bg-emerald-50 text-[#0F4C3A] text-xs font-bold uppercase tracking-wider">
                            Традиции и инновации
                        </div>
                        <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 leading-tight">
                            {{ page?.content?.hero?.title || 'Мы объединяем традиции образования с инновационными подходами к обучению' }}
                        </h2>
                        <p class="text-slate-600 text-base leading-relaxed">
                            {{ page?.content?.hero?.subtitle || 'Создаем условия для раскрытия потенциала каждого студента. Наш колледж готовит специалистов по 8 ключевым востребованным направлениям цифровой экономики, бизнеса, туризма и юриспруденции.' }}
                        </p>

                        <div class="pt-2 flex flex-wrap gap-4 text-xs sm:text-sm text-slate-700 font-semibold">
                            <div class="flex items-center gap-2">
                                <CheckCircle class="w-4 h-4 text-[#0082C9]" />
                                <span>{{ page?.content?.hero?.years || 23 }} года успешной работы</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <CheckCircle class="w-4 h-4 text-[#0082C9]" />
                                <span>Генеральная лицензия МОН РК {{ page?.content?.hero?.license_number || '№ 14003946' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <CheckCircle class="w-4 h-4 text-[#0082C9]" />
                                <span>Траектория Колледж &rarr; Университет Туран</span>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5 rounded-[28px] overflow-hidden border-0 shadow-none">
                        <img
                            :src="page?.featured_image ? (page.featured_image.startsWith('images/') ? '/' + page.featured_image : '/storage/' + page.featured_image) : '/images/dump/about.jpg'"
                            alt="Кампус колледжа Туран"
                            class="w-full h-80 object-cover"
                        />
                    </div>
                </div>

                <!-- 6 Pillars: Why Choose Us -->
                <div class="mb-12">
                    <h3 class="font-heading font-black text-2xl text-slate-900 mb-8 uppercase text-center sm:text-left">
                        Почему выбирают нас:
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <SpotlightCard
                            v-for="(pillar, idx) in pillarsList"
                            :key="idx"
                            class-name="p-6 sm:p-7 rounded-[26px] bg-slate-50 border-0 shadow-none cursor-default"
                            :spotlight-color="pillar.spotlight"
                        >
                            <div :class="['w-10 h-10 rounded-xl bg-white border-0 shadow-none flex items-center justify-center mb-4', pillar.color]">
                                <component :is="pillar.icon" class="w-5 h-5" />
                            </div>
                            <h4 class="font-heading font-bold text-lg text-slate-900 mb-2">{{ pillar.title }}</h4>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                {{ pillar.desc }}
                            </p>
                        </SpotlightCard>
                    </div>
                </div>
            </Container>
        </section>

        <!-- Pedagogical Staff Preview with link to dedicated /about/teachers page -->
        <section id="staff" class="py-16 sm:py-24 bg-white border-t border-slate-200/80 scroll-mt-20">
            <Container>
                <!-- Section Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 sm:mb-14">
                    <div class="space-y-3 max-w-2xl">
                        <div class="inline-block px-4 py-1.5 rounded-full bg-[#0082C9]/10 text-[#0082C9] text-sm font-bold uppercase tracking-wider">
                            Команда профессионалов
                        </div>
                        <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 uppercase tracking-tight">
                            {{ page?.content?.leadership_intro?.title || 'Педагогический состав' }}
                        </h2>
                        <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                            {{ page?.content?.leadership_intro?.desc || 'Руководство, преподаватели-исследователи и эксперты-практики колледжа «Туран»' }}
                        </p>
                    </div>
                </div>

                <!-- Featured Teachers Grid: Matching Reference Style (Light Frosted Glass, Full ФИО, No Subtitle, 12px Blue Badge) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-7">
                    <Link
                        v-for="s in featuredStaff"
                        :key="s.id"
                        href="/about/teachers"
                        class="group relative overflow-hidden rounded-[26px] sm:rounded-[32px] bg-slate-100 border border-slate-200/80 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] hover:-translate-y-1.5 shadow-none hover:shadow-none flex flex-col justify-end min-h-[460px] sm:min-h-[490px] select-none cursor-pointer"
                    >
                        <!-- Full-bleed Background Photo shifted higher so heads are higher in the frame -->
                        <img
                            v-if="s.photo_url"
                            :src="s.photo_url"
                            :alt="s.full_name"
                            class="absolute -top-8 sm:-top-11 inset-x-0 w-full h-[calc(100%+2rem)] sm:h-[calc(100%+2.75rem)] object-cover object-top transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-105"
                            loading="lazy"
                        />
                        <div
                            v-else
                            class="absolute inset-0 w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400 font-heading font-black text-6xl flex items-center justify-center select-none"
                        >
                            {{ s.full_name.charAt(0) }}
                        </div>

                        <!-- Floating Light Frosted Glass Info Panel (Светлое стекло) -->
                        <div class="relative z-10 m-3 sm:m-3.5 p-4 sm:p-5 rounded-[20px] sm:rounded-[24px] bg-white/80 backdrop-blur-xl border border-white/70 text-slate-900 transition-all duration-300 group-hover:bg-white/90 group-hover:border-white shadow-[0_4px_24px_rgba(0,0,0,0.06)]">
                            <!-- Header Row: Full Name (ФИО без сокращений) on Left, Circular Arrow Button on Right -->
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-heading font-bold text-base sm:text-lg text-slate-900 leading-snug group-hover:text-[#0082C9] transition-colors break-words flex-1 min-w-0" :title="s.full_name">
                                    {{ s.full_name }}
                                </h3>

                                <!-- Circular Action Button with ↗ (ArrowUpRight) - Clean Distinct Border, Zero Shadow -->
                                <div
                                    class="shrink-0 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/90 group-hover:bg-[#0082C9] border border-slate-300 group-hover:border-[#0082C9] flex items-center justify-center text-slate-800 group-hover:text-white transition-all duration-300 group-hover:scale-105 shadow-none"
                                    aria-label="Подробнее"
                                >
                                    <ArrowUpRight class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                                </div>
                            </div>

                            <!-- Bio Description -->
                            <p class="text-sm text-slate-600 leading-relaxed font-normal mt-2.5 pt-2.5 border-t border-slate-200/70 line-clamp-3">
                                {{ s.bio }}
                            </p>
                        </div>
                    </Link>
                </div>

                <!-- Bottom Full Directory CTA -->
                <div class="mt-12 text-center">
                    <Link
                        href="/about/teachers"
                        class="inline-flex items-center gap-3 px-9 py-4 rounded-full border-2 border-[#0082C9] text-[#0082C9] hover:bg-[#0082C9] hover:text-white font-heading font-semibold text-base transition-all duration-300 hover:scale-105 active:scale-95 group cursor-pointer"
                    >
                        <span>Перейти на страницу педагогического состава</span>
                        <ArrowRight class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" />
                    </Link>
                </div>
            </Container>
        </section>
    </AppLayout>
</template>
