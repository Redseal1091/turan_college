<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/shared/Container.vue';
import IntroCard from '@/Components/layout/IntroCard.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import Card from '@/Components/ui/card/Card.vue';
import Button from '@/Components/ui/button/Button.vue';
import {
    Calendar,
    Clock,
    FileText,
    Users,
    Download,
    BookOpen,
    HelpCircle,
    CheckCircle,
    HeartPulse,
    Utensils,
    Library,
    ArrowRight
} from 'lucide-vue-next';

interface CallItem {
    pair: string;
    time: string;
}

interface ClubItem {
    name: string;
    desc: string;
}

defineProps<{
    scheduleNotice: string;
    callSchedule: CallItem[];
    clubs: ClubItem[];
}>();
</script>

<template>
    <AppLayout title="Студенту — Колледж Туран">
        <!-- Banner with Main Header -->
        <IntroCard
            title="СТУДЕНТУ"
            :breadcrumbs="[
                { label: 'Главная', href: '/' },
                { label: 'Студенту' },
            ]"
        />

        <!-- Schedules and Bells -->
        <section class="py-16 bg-white">
            <Container>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-10 items-start">
                    <!-- Left: Call Schedule -->
                    <div class="lg:col-span-5">
                        <div class="p-6 sm:p-8 rounded-[28px] bg-slate-50 border-0 shadow-none text-slate-900">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-white text-[#0082C9] border-0 shadow-none flex items-center justify-center">
                                    <Clock class="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 class="font-heading font-bold text-xl text-slate-900">Расписание звонков</h3>
                                    <p class="text-xs text-slate-500">Учебный год 2026/2027</p>
                                </div>
                            </div>

                            <div class="space-y-2.5">
                                <div
                                    v-for="(item, idx) in callSchedule"
                                    :key="idx"
                                    :class="[
                                        'p-3.5 rounded-2xl flex items-center justify-between text-sm border-0 shadow-none',
                                        item.pair.includes('Обед')
                                            ? 'bg-[#FFF5F1] text-slate-900 font-semibold'
                                            : 'bg-white text-slate-700'
                                    ]"
                                >
                                    <span>{{ item.pair }}</span>
                                    <span class="font-mono font-bold text-xs">{{ item.time }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Schedule Downloads and Academic Calendar -->
                    <div class="lg:col-span-7 space-y-6">
                        <div class="p-6 sm:p-8 rounded-[28px] bg-slate-50 border-0 shadow-none text-slate-900">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-white text-[#FDA685] border-0 shadow-none flex items-center justify-center">
                                    <Calendar class="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 class="font-heading font-bold text-xl text-slate-900">Расписание занятий по курсам</h3>
                                    <p class="text-xs text-slate-500">Обновлено на весенний семестр</p>
                                </div>
                            </div>

                            <p class="text-sm text-slate-600 mb-6">
                                Выберите ваш курс и отделение для скачивания актуального графика занятий и расписания экзаменационных сессий.
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="p-4 rounded-2xl bg-white border-0 shadow-none text-center transition-colors">
                                    <div class="font-heading font-bold text-slate-800 mb-1">1 курс</div>
                                    <span class="text-xs text-slate-500 block mb-3">Все специальности</span>
                                    <Button size="sm" variant="outline" class="w-full text-xs gap-1 rounded-xl bg-slate-50 hover:bg-[#0082C9] hover:text-white border-0 shadow-none">
                                        <Download class="w-3.5 h-3.5" />
                                        Скачать PDF
                                    </Button>
                                </div>

                                <div class="p-4 rounded-2xl bg-white border-0 shadow-none text-center transition-colors">
                                    <div class="font-heading font-bold text-slate-800 mb-1">2 курс</div>
                                    <span class="text-xs text-slate-500 block mb-3">Все специальности</span>
                                    <Button size="sm" variant="outline" class="w-full text-xs gap-1 rounded-xl bg-slate-50 hover:bg-[#0082C9] hover:text-white border-0 shadow-none">
                                        <Download class="w-3.5 h-3.5" />
                                        Скачать PDF
                                    </Button>
                                </div>

                                <div class="p-4 rounded-2xl bg-white border-0 shadow-none text-center transition-colors">
                                    <div class="font-heading font-bold text-slate-800 mb-1">3 курс</div>
                                    <span class="text-xs text-slate-500 block mb-3">Все специальности</span>
                                    <Button size="sm" variant="outline" class="w-full text-xs gap-1 rounded-xl bg-slate-50 hover:bg-[#0082C9] hover:text-white border-0 shadow-none">
                                        <Download class="w-3.5 h-3.5" />
                                        Скачать PDF
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <!-- Certificates and Requests -->
                        <div class="p-6 sm:p-8 rounded-[28px] bg-slate-50 border-0 shadow-none text-slate-900">
                            <h3 class="font-heading font-bold text-xl text-slate-900 mb-2">
                                Заказ справок онлайн (ЦОС)
                            </h3>
                            <p class="text-sm text-slate-600 mb-5">
                                Центр обслуживания студентов выдает электронные справки с места учебы, транскрипты и справки для военкомата в течение 24 часов.
                            </p>
                            <div class="flex flex-wrap items-center gap-3">
                                <Button variant="default" size="default" class="rounded-full px-6 py-2.5 font-heading font-semibold text-[14px] leading-[14px] gap-2 group bg-[#0082C9] hover:bg-[#0070ad] text-white border-0 shadow-none">
                                    <FileText class="w-4 h-4" />
                                    <span>Справка с места учебы</span>
                                    <ArrowRight class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" />
                                </Button>
                                <Button size="default" class="rounded-full px-6 py-2.5 font-heading font-semibold text-[14px] leading-[14px] gap-2 group bg-white text-slate-800 hover:bg-slate-200 border-0 shadow-none">
                                    <FileText class="w-4 h-4" />
                                    <span>Заказать транскрипт</span>
                                    <ArrowRight class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </Container>
        </section>

        <!-- College Infrastructure: Medical, Food, Library (Official info) -->
        <section class="py-16 bg-white border-t border-slate-100">
            <Container>
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <Badge variant="secondary" class="mb-2 bg-slate-100 text-slate-700 border-0">Инфраструктура кампуса</Badge>
                    <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-slate-900">
                        Службы поддержки студентов
                    </h2>
                    <p class="text-slate-600 mt-2 text-sm sm:text-base">
                        Забота о здоровье, питании и учебных ресурсах каждого учащегося колледжа «Туран».
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 sm:p-7 rounded-[28px] bg-slate-50 border-0 shadow-none flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-white text-rose-600 border-0 shadow-none flex items-center justify-center mb-5">
                                <HeartPulse class="w-6 h-6" />
                            </div>
                            <h3 class="font-heading font-bold text-xl text-slate-900 mb-3">Медицинский пункт</h3>
                            <p class="text-sm text-slate-600 leading-relaxed mb-4">
                                Оказание первичной медико-санитарной и доврачебной помощи. Плановые медицинские осмотры, вакцинация и контроль санитарно-эпидемиологического режима.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-200/50 text-xs text-slate-500 font-medium">
                            Режим работы: Пн – Пт, 08:30 – 17:00 (ул. Сатпаева 16а)
                        </div>
                    </div>

                    <div class="p-6 sm:p-7 rounded-[28px] bg-slate-50 border-0 shadow-none flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-white text-amber-600 border-0 shadow-none flex items-center justify-center mb-5">
                                <Utensils class="w-6 h-6" />
                            </div>
                            <h3 class="font-heading font-bold text-xl text-slate-900 mb-3">Пункт питания (Столовая)</h3>
                            <p class="text-sm text-slate-600 leading-relaxed mb-4">
                                Собственная светлая столовая и буфет на территории колледжа. Сбалансированное горячее питание, свежая выпечка и напитки. Строгий контроль качества блюд.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-200/50 text-xs text-slate-500 font-medium">
                            Режим работы: Пн – Сб, 08:00 – 16:30
                        </div>
                    </div>

                    <div class="p-6 sm:p-7 rounded-[28px] bg-slate-50 border-0 shadow-none flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-white text-[#0082C9] border-0 shadow-none flex items-center justify-center mb-5">
                                <Library class="w-6 h-6" />
                            </div>
                            <h3 class="font-heading font-bold text-xl text-slate-900 mb-3">Библиотечный комплекс</h3>
                            <p class="text-sm text-slate-600 leading-relaxed mb-4">
                                Богатый фонд учебной, научной и периодической литературы. Читальный зал с доступом к электронным базам данных, методическим пособиям и Wi-Fi.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-200/50 text-xs text-slate-500 font-medium">
                            Доступ к электронным ресурсам 24/7
                        </div>
                    </div>
                </div>
            </Container>
        </section>

        <!-- Student Clubs & Life -->
        <section class="py-16 bg-white border-t border-slate-100">
            <Container>
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <Badge variant="secondary" class="mb-2 bg-slate-100 text-slate-700 border-0">Внеучебная деятельность</Badge>
                    <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-slate-900">
                        Студенческие клубы и сообщества
                    </h2>
                    <p class="text-slate-600 mt-2 text-sm sm:text-base">
                        Развивай софт-скиллы, заводи друзей и побеждай на городских и республиканских соревнованиях.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        v-for="(club, idx) in clubs"
                        :key="idx"
                        class="p-6 rounded-[24px] bg-slate-50 border-0 shadow-none hover:-translate-y-1 transition-transform duration-200"
                    >
                        <h4 class="font-heading font-bold text-lg text-slate-900 mb-2">
                            {{ club.name }}
                        </h4>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            {{ club.desc }}
                        </p>
                    </div>
                </div>
            </Container>
        </section>
    </AppLayout>
</template>
