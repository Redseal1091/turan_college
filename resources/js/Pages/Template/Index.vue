<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/shared/Container.vue';
import IntroCard from '@/Components/layout/IntroCard.vue';
import Button from '@/Components/ui/button/Button.vue';
import Badge from '@/Components/ui/badge/Badge.vue';
import { Field, FieldLabel, FieldDescription } from '@/Components/ui/field';
import Input from '@/Components/ui/input/Input.vue';
import Textarea from '@/Components/ui/textarea/Textarea.vue';
import PhoneInput from '@/Components/ui/input/PhoneInput.vue';
import { SpotlightCard } from '@/Components/bits';
import { isPhoneComplete } from '@/composables/usePhoneMask';
import { useApplyModal } from '@/composables/useApplyModal';
import {
    ArrowLeft,
    ArrowRight,
    ArrowUpRight,
    CheckCircle2,
    Calendar,
    Clock,
    FileText,
    BookOpen,
    Users,
    Award,
    Sparkles,
    ShieldCheck,
    Layers,
    Table as TableIcon,
    SlidersHorizontal,
    Send,
} from 'lucide-vue-next';

// Unified Apply Modal Composable
const { openApplyModal } = useApplyModal();

// Carousel state
const sliderRef = ref<HTMLDivElement | null>(null);
const activeSlide = ref(0);

const goToSlide = (index: number) => {
    activeSlide.value = index;
    if (!sliderRef.value) return;
    const slidesElements = sliderRef.value.children;
    if (slidesElements[index]) {
        (slidesElements[index] as HTMLElement).scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'start',
        });
    }
};

const nextSlide = () => {
    const nextIdx = (activeSlide.value + 1) % slides.length;
    goToSlide(nextIdx);
};

const prevSlide = () => {
    const prevIdx = (activeSlide.value - 1 + slides.length) % slides.length;
    goToSlide(prevIdx);
};

const onTrackScroll = () => {
    if (!sliderRef.value) return;
    const scrollLeft = sliderRef.value.scrollLeft;
    const firstCard = sliderRef.value.firstElementChild as HTMLElement | null;
    const slideWidth = firstCard ? firstCard.offsetWidth + 20 : 360;
    const calculatedIdx = Math.round(scrollLeft / slideWidth);
    if (calculatedIdx >= 0 && calculatedIdx < slides.length) {
        activeSlide.value = calculatedIdx;
    }
};

// Slider items
const slides = [
    {
        id: 1,
        tag: 'Инновации',
        title: 'IT-лаборатории и хакатон-зоны',
        desc: 'Современные аудитории с высокопроизводительными рабочими станциями для разработки ПО и дизайна.',
        image: '/images/about_mainpage.png',
    },
    {
        id: 2,
        tag: 'Практика',
        title: 'Стажировки в топ-компаниях',
        desc: 'Студенты проходят оплачиваемые практики в ведущих банках, телеком- и IT-компаниях Казахстана.',
        image: '/images/Intro_back.jpg',
    },
    {
        id: 3,
        tag: 'Кампус',
        title: 'Комфортабельное общежитие',
        desc: 'Собственное общежитие в центре Алматы с Wi-Fi, кухнями, прачечной и круглосуточной охраной.',
        image: '/images/about_mainpage.png',
    },
    {
        id: 4,
        tag: 'Спорт и творчество',
        title: 'Более 15 студенческих клубов',
        desc: 'Дебатные лиги, танцевальные ансамбли, киберспортивные секции и медиа-центр колледжа «Туран».',
        image: '/images/Intro_back.jpg',
    },
];

// Cards Grid items
const cardItems = [
    {
        id: 1,
        code: '06130100',
        title: 'Программное обеспечение',
        dept: 'IT и инженерия',
        duration: '2 года 10 месяцев',
        tuition: '480 000 ₸/год',
        desc: 'Подготовка frontend-, backend- и mobile-разработчиков, системных администраторов и инженеров баз данных.',
    },
    {
        id: 2,
        code: '04140100',
        title: 'Маркетинг и PR',
        dept: 'Экономика и бизнес',
        duration: '1 год 10 месяцев',
        tuition: '450 000 ₸/год',
        desc: 'Стратегический маркетинг, управление брендом, таргетированная реклама и продвижение в социальных сетях.',
    },
    {
        id: 3,
        code: '02110300',
        title: 'Графический дизайн',
        dept: 'Дизайн и искусство',
        duration: '2 года 10 месяцев',
        tuition: '490 000 ₸/год',
        desc: 'Брендинг, UI/UX веб-дизайн, векторная и 3D-графика, подготовка портфолио для креативных агентств.',
    },
];

// Table data
const tableData = [
    {
        code: '06130100',
        title: 'Разработка программного обеспечения',
        qualification: 'Техник-программист',
        form: 'Очная',
        duration: '2 г. 10 мес.',
        fee: '480 000 ₸',
        status: 'Гранты и платно',
        statusColor: 'emerald',
    },
    {
        code: '02110300',
        title: 'Графический и мультимедийный дизайн',
        qualification: 'Дизайнер',
        form: 'Очная',
        duration: '2 г. 10 мес.',
        fee: '490 000 ₸',
        status: 'Идёт набор',
        statusColor: 'blue',
    },
    {
        code: '04140100',
        title: 'Маркетинг (по отраслям)',
        qualification: 'Маркетолог',
        form: 'Очная / Заочная',
        duration: '1 г. 10 мес.',
        fee: '450 000 ₸',
        status: 'Гранты и платно',
        statusColor: 'emerald',
    },
    {
        code: '10150100',
        title: 'Туризм и сервис',
        qualification: 'Менеджер по туризму',
        form: 'Очная',
        duration: '2 г. 10 мес.',
        fee: '430 000 ₸',
        status: 'Осталось 5 мест',
        statusColor: 'amber',
    },
    {
        code: '04210100',
        title: 'Правоведение',
        qualification: 'Юрист',
        form: 'Очная / Заочная',
        duration: '1 г. 10 мес.',
        fee: '460 000 ₸',
        status: 'Идёт набор',
        statusColor: 'blue',
    },
];

// Form state
const form = useForm({
    applicant_name: '',
    phone: '',
    email: '',
    department: 'it',
    study_format: 'full_time',
    hostel_needed: false,
    documents: [] as File[],
    notes: '',
});

const isFormSubmitted = ref(false);

const onDocumentsChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        form.documents = Array.from(target.files);
    }
};

const submitDemoForm = () => {
    isFormSubmitted.value = true;
    setTimeout(() => {
        isFormSubmitted.value = false;
        form.reset();
    }, 3500);
};
</script>

<template>
    <AppLayout title="Шаблон обычной страницы — Колледж Туран">
        <!-- 1. Header / Intro Banner (Clean child header mode: white bg, page title, breadcrumbs) -->
        <IntroCard
            variant="clean"
            title="ШАБЛОН СТРАНИЦЫ"
            :breadcrumbs="[
                { label: 'Главная', href: '/' },
                { label: 'Шаблон страницы' },
            ]"
        />

        <!-- Main Page Body: Strictly white page background, blocks have slightly grayish bg (bg-slate-50), no borders, no shadows -->
        <div class="bg-white py-8 sm:py-14 space-y-16 sm:space-y-24">
            <!-- =============================================================
                 SECTION 1: ТИПОГРАФИКА И ЗАГОЛОВКИ (All Headings & Hierarchy)
                 ============================================================= -->
            <section>
                <Container class="max-w-6xl">
                    <div class="mb-8">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                            Раздел 01 • Стили текста
                        </div>
                        <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 uppercase tracking-tight">
                            Иерархия заголовков и текст
                        </h2>
                        <p class="text-slate-500 text-sm sm:text-base mt-1.5">
                            Стандартные типографические стили для оформления контентных страниц сайта
                        </p>
                    </div>

                    <!-- Typography Demo Block: Slightly grayish background (bg-slate-50), border-0, shadow-none -->
                    <div class="p-6 sm:p-10 rounded-[28px] bg-slate-50 border-0 shadow-none text-slate-900 space-y-8">
                        <!-- H1 Example -->
                        <div class="space-y-1">
                            <span class="text-xs font-bold font-mono text-slate-400 uppercase">H1 • 42–52px Black Uppercase</span>
                            <h1 class="font-heading font-black text-3xl sm:text-4xl md:text-5xl uppercase tracking-tight text-slate-900 leading-[1.12]">
                                Заголовок первого уровня (H1)
                            </h1>
                        </div>

                        <!-- H2 Example -->
                        <div class="space-y-1">
                            <span class="text-xs font-bold font-mono text-slate-400 uppercase">H2 • 28–36px Black Uppercase</span>
                            <h2 class="font-heading font-black text-2xl sm:text-3xl md:text-4xl uppercase tracking-tight text-slate-900 leading-[1.18]">
                                Заголовок второго уровня (H2)
                            </h2>
                        </div>

                        <!-- H3 Example -->
                        <div class="space-y-1">
                            <span class="text-xs font-bold font-mono text-slate-400 uppercase">H3 • 22–26px Bold</span>
                            <h3 class="font-heading font-bold text-xl sm:text-2xl text-slate-900 leading-snug">
                                Заголовок третьего уровня (H3) для подразделов
                            </h3>
                        </div>

                        <!-- H4 Example -->
                        <div class="space-y-1">
                            <span class="text-xs font-bold font-mono text-slate-400 uppercase">H4 • 18–20px Bold</span>
                            <h4 class="font-heading font-bold text-lg sm:text-xl text-slate-900 leading-snug">
                                Заголовок четвёртого уровня (H4) в карточках
                            </h4>
                        </div>

                        <!-- H5 & H6 Example -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <span class="text-xs font-bold font-mono text-slate-400 uppercase">H5 • 16px Semibold</span>
                                <h5 class="font-heading font-semibold text-base text-slate-800">
                                    Заголовок пятого уровня (H5)
                                </h5>
                            </div>
                            <div class="space-y-1">
                                <span class="text-xs font-bold font-mono text-slate-400 uppercase">H6 / Subtitle • 14px Medium Uppercase</span>
                                <h6 class="font-heading font-medium text-sm text-slate-600 uppercase tracking-wider">
                                    Заголовок шестого уровня (H6)
                                </h6>
                            </div>
                        </div>

                        <!-- Paragraph Styles -->
                        <div class="space-y-4 pt-4 border-t border-slate-200/50">
                            <div>
                                <span class="text-xs font-bold font-mono text-slate-400 uppercase block mb-1">Вводный абзац (Lead) • 18px Regular</span>
                                <p class="text-base sm:text-lg text-slate-700 leading-relaxed">
                                    Колледж Международного Университета «Туран» — это современный образовательный хаб, где фундаментальная теория объединяется с практическими кейсами ведущих компаний Казахстана.
                                </p>
                            </div>

                            <div>
                                <span class="text-xs font-bold font-mono text-slate-400 uppercase block mb-1">Основной текст (Body) • 16px Regular</span>
                                <p class="text-base text-slate-600 leading-relaxed">
                                    Образовательный процесс строится на принципах академической честности, передовых цифровых технологий и дуального обучения. Студенты имеют доступ к лицензированному софту, лабораториям виртуальной реальности, спортивным комплексам и библиотечным ресурсам университета.
                                </p>
                            </div>

                            <div>
                                <span class="text-xs font-bold font-mono text-slate-400 uppercase block mb-1">Пояснительный текст (Caption) • 14px Regular</span>
                                <p class="text-sm text-slate-500 leading-relaxed">
                                    Примечание: минимальный допустимый размер шрифта на страницах составляет 14px (text-sm). Размер text-xs используется исключительно для компактных служебных бейджей.
                                </p>
                            </div>
                        </div>

                        <!-- Accent Callout Block inside Card: White on Grayish Card -->
                        <div class="p-5 sm:p-6 rounded-2xl bg-white border-0 shadow-none flex items-start gap-3.5">
                            <Sparkles class="w-5 h-5 text-[#0082C9] shrink-0 mt-0.5" />
                            <div>
                                <h5 class="font-heading font-bold text-base text-slate-900 mb-1">
                                    Важное примечание для оформления
                                </h5>
                                <p class="text-sm text-slate-600 leading-relaxed">
                                    Все блоки размещаются на слегка сероватом фоне (<code class="font-mono text-xs bg-slate-100 px-1.5 py-0.5 rounded text-slate-800">bg-slate-50</code>), что исключает сливание белого контента с белой подложкой страницы. Тени и рамки отсутствуют.
                                </p>
                            </div>
                        </div>
                    </div>
                </Container>
            </section>

            <!-- =============================================================
                 SECTION 2: СЕТКА КАРТОЧЕК (Cards Grid)
                 ============================================================= -->
            <section>
                <Container class="max-w-6xl">
                    <div class="mb-8">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                            Раздел 02 • Компоненты
                        </div>
                        <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 uppercase tracking-tight">
                            Сетка интерактивных карточек
                        </h2>
                        <p class="text-slate-500 text-sm sm:text-base mt-1.5">
                            Адаптивная трехколоночная сетка со Spotlight-анимацией курсора и плавным подъёмом
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                        <SpotlightCard
                            v-for="item in cardItems"
                            :key="item.id"
                            class-name="p-6 sm:p-8 rounded-[26px] sm:rounded-[30px] bg-slate-50 border-0 shadow-none transition-all duration-300 group flex flex-col justify-between"
                            spotlight-color="rgba(0, 130, 201, 0.12)"
                        >
                            <div>
                                <!-- Top row: Code and Department Pill (Pure white background) -->
                                <div class="flex items-center justify-between gap-2 mb-4">
                                    <span class="font-mono text-xs sm:text-sm font-bold px-2.5 py-1 rounded-xl bg-white text-slate-700 border-0 shadow-none">
                                        {{ item.code }}
                                    </span>
                                    <span class="text-xs sm:text-sm font-semibold px-2.5 py-1 rounded-xl bg-white text-[#0F4C3A] border-0 shadow-none">
                                        {{ item.dept }}
                                    </span>
                                </div>

                                <!-- Card Title -->
                                <h3 class="font-heading font-bold text-xl sm:text-2xl text-slate-900 group-hover:text-[#0082C9] transition-colors leading-snug mb-3">
                                    {{ item.title }}
                                </h3>

                                <!-- Duration -->
                                <div class="text-sm text-slate-600 font-medium mb-3 flex items-center gap-2">
                                    <Clock class="w-4 h-4 text-slate-400 shrink-0" />
                                    <span>{{ item.duration }}</span>
                                </div>

                                <!-- Description -->
                                <p class="text-sm sm:text-base text-slate-600 leading-relaxed mb-6">
                                    {{ item.desc }}
                                </p>
                            </div>

                            <!-- Card Footer: Tuition and Action Button -->
                            <div class="pt-5 border-t border-slate-200/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <span class="text-xs text-slate-400 block">Стоимость:</span>
                                    <span class="font-heading font-bold text-base sm:text-lg text-slate-900">{{ item.tuition }}</span>
                                </div>

                                <Button
                                    size="default"
                                    class="w-full sm:w-auto rounded-full px-5 py-2.5 font-heading font-semibold text-sm gap-2 justify-center bg-[#0082C9] hover:bg-[#0070ad] text-white border-0 shadow-none cursor-pointer"
                                >
                                    <span>Подробнее</span>
                                    <ArrowRight class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" />
                                </Button>
                            </div>
                        </SpotlightCard>
                    </div>
                </Container>
            </section>

            <!-- =============================================================
                 SECTION 3: СЛАЙДЕР / КАРУСЕЛЬ (Slider / Carousel)
                 ============================================================= -->
            <section>
                <Container class="max-w-6xl">
                    <!-- Carousel Header with Title, Counter and Control Buttons -->
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6 sm:mb-8">
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                                Раздел 03 • Слайдер
                            </div>
                            <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 uppercase tracking-tight">
                                Карусель материалов и событий
                            </h2>
                            <p class="text-slate-500 text-sm sm:text-base mt-1.5">
                                Горизонтальный скролл с поддержкой жестов на мобильных и стрелок на десктопе
                            </p>
                        </div>

                        <!-- Slide Counter and Navigation Buttons -->
                        <div class="flex items-center gap-4 self-end sm:self-auto shrink-0">
                            <!-- Numeric Slide Counter (01 / 04) -->
                            <span class="font-mono text-xs sm:text-sm font-bold text-slate-400 select-none">
                                0{{ activeSlide + 1 }} / 0{{ slides.length }}
                            </span>

                            <div class="flex items-center gap-2">
                                <button
                                    @click="prevSlide"
                                    type="button"
                                    aria-label="Предыдущий слайд"
                                    class="w-11 h-11 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-800 flex items-center justify-center transition-colors cursor-pointer border-0 shadow-none active:scale-95"
                                >
                                    <ArrowLeft class="w-5 h-5" />
                                </button>
                                <button
                                    @click="nextSlide"
                                    type="button"
                                    aria-label="Следующий слайд"
                                    class="w-11 h-11 rounded-full bg-[#0082C9] hover:bg-[#0070ad] text-white flex items-center justify-center transition-colors cursor-pointer border-0 shadow-none active:scale-95"
                                >
                                    <ArrowRight class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Scrollable Carousel Track: No visible scrollbar anywhere -->
                    <div
                        ref="sliderRef"
                        @scroll.passive="onTrackScroll"
                        class="carousel-track flex gap-5 sm:gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory py-2 pb-3 scrollbar-none no-scrollbar [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden -mx-4 px-4 sm:mx-0 sm:px-0"
                    >
                        <div
                            v-for="slide in slides"
                            :key="slide.id"
                            class="shrink-0 w-[290px] sm:w-[350px] md:w-[380px] snap-start rounded-[26px] bg-slate-50 p-6 sm:p-7 border-0 shadow-none flex flex-col justify-between select-none"
                        >
                            <div>
                                <div class="inline-block px-3 py-1 rounded-xl bg-white text-[#0082C9] text-xs font-bold uppercase tracking-wider mb-4 border-0 shadow-none">
                                    {{ slide.tag }}
                                </div>
                                <h4 class="font-heading font-bold text-xl sm:text-2xl text-slate-900 leading-snug mb-3">
                                    {{ slide.title }}
                                </h4>
                                <p class="text-slate-600 text-sm sm:text-base leading-relaxed mb-6">
                                    {{ slide.desc }}
                                </p>
                            </div>

                            <div class="pt-4 border-t border-slate-200/50 flex items-center justify-between">
                                <span class="text-xs sm:text-sm font-semibold text-[#0082C9]">Узнать больше</span>
                                <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-[#0082C9] shadow-none">
                                    <ArrowUpRight class="w-4 h-4" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carousel Pagination Dots (Indicators) -->
                    <div class="flex items-center justify-center gap-2 mt-5">
                        <button
                            v-for="(slide, idx) in slides"
                            :key="slide.id"
                            type="button"
                            @click="goToSlide(idx)"
                            class="transition-all duration-300 rounded-full cursor-pointer border-0 p-0"
                            :class="activeSlide === idx ? 'w-8 h-2 bg-[#0082C9]' : 'w-2 h-2 bg-slate-300 hover:bg-slate-400'"
                            :aria-label="`Слайд ${idx + 1}`"
                        />
                    </div>
                </Container>
            </section>

            <!-- =============================================================
                 SECTION 4: ТАБЛИЦА ДАННЫХ (Compact Data Table)
                 ============================================================= -->
            <section>
                <Container class="max-w-6xl">
                    <div class="mb-5 sm:mb-6">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                            Раздел 04 • Таблица
                        </div>
                        <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 uppercase tracking-tight">
                            Таблица образовательных программ
                        </h2>
                        <p class="text-slate-500 text-sm sm:text-base mt-1.5">
                            Компактная адаптивная таблица без лишних отступов и с чистой типографикой
                        </p>
                    </div>

                    <!-- Table Outer Card: Compact padding, slightly grayish bg, no borders, no shadows -->
                    <div class="overflow-hidden rounded-[20px] sm:rounded-[24px] bg-slate-50 border-0 shadow-none p-1 sm:p-2.5">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse min-w-[640px]">
                                <thead>
                                    <tr class="border-b border-slate-200/60 text-xs font-heading font-bold uppercase tracking-wider text-slate-500">
                                        <th class="py-2.5 pl-3 pr-3">Код</th>
                                        <th class="py-2.5 px-3">Специальность</th>
                                        <th class="py-2.5 px-3">Квалификация</th>
                                        <th class="py-2.5 px-3">Срок</th>
                                        <th class="py-2.5 px-3">Стоимость</th>
                                        <th class="py-2.5 px-3">Статус</th>
                                        <th class="py-2.5 pr-3 pl-3 text-right">Действие</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200/50 text-xs sm:text-sm text-slate-700">
                                    <tr
                                        v-for="row in tableData"
                                        :key="row.code"
                                        class="hover:bg-white/90 transition-colors group"
                                    >
                                        <td class="py-2.5 pl-3 pr-3 font-mono font-bold text-xs text-slate-500">
                                            {{ row.code }}
                                        </td>
                                        <td class="py-2.5 px-3 font-heading font-bold text-slate-900 group-hover:text-[#0082C9] transition-colors">
                                            {{ row.title }}
                                        </td>
                                        <td class="py-2.5 px-3 text-slate-600 text-xs sm:text-sm">
                                            {{ row.qualification }}
                                        </td>
                                        <td class="py-2.5 px-3 text-slate-600 whitespace-nowrap text-xs sm:text-sm">
                                            {{ row.duration }}
                                        </td>
                                        <td class="py-2.5 px-3 font-heading font-bold text-slate-900 whitespace-nowrap text-xs sm:text-sm">
                                            {{ row.fee }}
                                        </td>
                                        <td class="py-2.5 px-3 whitespace-nowrap">
                                            <span
                                                class="inline-block px-2 py-0.5 rounded-lg text-xs font-semibold"
                                                :class="{
                                                    'bg-emerald-100 text-[#0F4C3A]': row.statusColor === 'emerald',
                                                    'bg-blue-100 text-[#0082C9]': row.statusColor === 'blue',
                                                    'bg-amber-100 text-amber-900': row.statusColor === 'amber',
                                                }"
                                            >
                                                {{ row.status }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 pr-3 pl-3 text-right whitespace-nowrap">
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white hover:bg-[#0082C9] hover:text-white text-[#0082C9] font-heading font-semibold text-xs transition-colors cursor-pointer border-0 shadow-none"
                                            >
                                                <span>Подробнее</span>
                                                <ArrowRight class="w-3 h-3" />
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </Container>
            </section>

            <!-- =============================================================
                 SECTION 5: ФОРМА ЗАЯВКИ (Compact Form)
                 ============================================================= -->
            <section id="form">
                <Container class="max-w-4xl">
                    <div class="mb-8 text-center">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                            Раздел 05 • Форма
                        </div>
                        <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 uppercase tracking-tight">
                            Форма подачи заявки
                        </h2>
                        <p class="text-slate-500 text-sm sm:text-base mt-1.5 max-w-lg mx-auto">
                            Компактная форма с маской телефона, синими кнопками загрузки файлов и валидацией
                        </p>
                        <div class="mt-4 flex justify-center">
                            <Button
                                type="button"
                                @click="openApplyModal()"
                                class="rounded-full px-6 py-2.5 bg-[#0082C9] hover:bg-[#0070ad] text-white font-heading font-semibold text-xs sm:text-sm gap-2 border-0 shadow-none cursor-pointer"
                            >
                                <span>Открыть модальное окно (до 10 файлов)</span>
                                <ArrowRight class="w-4 h-4" />
                            </Button>
                        </div>
                    </div>

                    <!-- Form Card: Slightly grayish bg (bg-slate-50), border-0, shadow-none -->
                    <div class="p-6 sm:p-9 rounded-[28px] bg-slate-50 border-0 shadow-none text-slate-900">
                        <form @submit.prevent="submitDemoForm" class="space-y-4 text-sm">
                            <!-- Row 1: Name and Phone -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <Field>
                                    <FieldLabel for="tpl_applicant_name">ФИО заявителя</FieldLabel>
                                    <Input
                                        id="tpl_applicant_name"
                                        v-model="form.applicant_name"
                                        type="text"
                                        borderless
                                        required
                                        placeholder="Сериков Алишер Канатович"
                                    />
                                </Field>

                                <Field>
                                    <FieldLabel for="tpl_phone">Номер телефона (маска +7)</FieldLabel>
                                    <PhoneInput
                                        id="tpl_phone"
                                        v-model="form.phone"
                                        borderless
                                        required
                                    />
                                </Field>
                            </div>

                            <!-- Row 2: Email and Department -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <Field>
                                    <FieldLabel for="tpl_email">Электронная почта (E-mail)</FieldLabel>
                                    <Input
                                        id="tpl_email"
                                        v-model="form.email"
                                        type="email"
                                        borderless
                                        placeholder="applicant@turan-college.kz"
                                    />
                                </Field>

                                <Field>
                                    <FieldLabel for="tpl_department">Направление обучения</FieldLabel>
                                    <select
                                        id="tpl_department"
                                        v-model="form.department"
                                        class="flex h-10 w-full rounded-md border-0 bg-slate-200/60 hover:bg-slate-200/80 px-3 py-2 text-sm text-slate-900 focus-visible:bg-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0082C9] transition-all"
                                    >
                                        <option value="it">Информационные технологии и программирование</option>
                                        <option value="design">Графический и мультимедийный дизайн</option>
                                        <option value="marketing">Маркетинг, учет и финансы</option>
                                        <option value="tourism">Туризм и гостиничный сервис</option>
                                        <option value="law">Правоведение и юриспруденция</option>
                                    </select>
                                </Field>
                            </div>

                            <!-- Row 3: Format Tabs (Borderless, grayish selection) -->
                            <div class="space-y-1.5">
                                <FieldLabel>Форма обучения</FieldLabel>
                                <div class="grid grid-cols-2 gap-3">
                                    <label
                                        class="flex items-center justify-center gap-2 p-2.5 rounded-xl border-0 text-xs sm:text-sm font-medium cursor-pointer transition-all shadow-none"
                                        :class="form.study_format === 'full_time' ? 'bg-white text-[#0082C9] font-bold ring-2 ring-[#0082C9]' : 'bg-slate-200/60 text-slate-700 hover:bg-slate-200'"
                                    >
                                        <input type="radio" value="full_time" v-model="form.study_format" class="sr-only" />
                                        <span>Очное дневное отделение</span>
                                    </label>
                                    <label
                                        class="flex items-center justify-center gap-2 p-2.5 rounded-xl border-0 text-xs sm:text-sm font-medium cursor-pointer transition-all shadow-none"
                                        :class="form.study_format === 'part_time' ? 'bg-white text-[#0082C9] font-bold ring-2 ring-[#0082C9]' : 'bg-slate-200/60 text-slate-700 hover:bg-slate-200'"
                                    >
                                        <input type="radio" value="part_time" v-model="form.study_format" class="sr-only" />
                                        <span>Дистанционное / Заочное</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Row 4: File Uploads with Blue Buttons and White Text -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <Field>
                                    <FieldLabel for="tpl_documents">
                                        <span>Прикрепить документы</span>
                                        <span class="text-slate-400 font-normal ml-1.5">(PDF, DOC, JPG)</span>
                                    </FieldLabel>
                                    <input
                                        id="tpl_documents"
                                        type="file"
                                        multiple
                                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                        @change="onDocumentsChange"
                                        class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0082C9] file:text-white hover:file:bg-[#0070ad] file:cursor-pointer cursor-pointer border-0 bg-slate-200/60 rounded-xl p-1.5 transition-colors"
                                    />
                                </Field>

                                <Field>
                                    <FieldLabel for="tpl_photo">
                                        <span>Фотография заявителя</span>
                                        <span class="text-slate-400 font-normal ml-1.5">(3х4 см)</span>
                                    </FieldLabel>
                                    <input
                                        id="tpl_photo"
                                        type="file"
                                        accept="image/*"
                                        class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0082C9] file:text-white hover:file:bg-[#0070ad] file:cursor-pointer cursor-pointer border-0 bg-slate-200/60 rounded-xl p-1.5 transition-colors"
                                    />
                                </Field>
                            </div>

                            <!-- Row 5: Notes / Question -->
                            <Field>
                                <FieldLabel for="tpl_notes">Комментарий или вопрос</FieldLabel>
                                <Textarea
                                    id="tpl_notes"
                                    v-model="form.notes"
                                    borderless
                                    rows="3"
                                    placeholder="Укажите интересующие детали или вопросы приемной комиссии..."
                                />
                            </Field>

                            <!-- Checkbox Options -->
                            <label class="flex items-center gap-2.5 cursor-pointer select-none text-xs sm:text-sm text-slate-600">
                                <input
                                    type="checkbox"
                                    v-model="form.hostel_needed"
                                    class="w-4 h-4 rounded text-[#0082C9] focus:ring-[#0082C9] border-slate-300"
                                />
                                <span>Требуется место в студенческом общежитии колледжа</span>
                            </label>

                            <!-- Submit Button: Blue, no border, no shadow -->
                            <Button
                                type="submit"
                                :disabled="form.processing || (form.phone && !isPhoneComplete(form.phone))"
                                class="w-full justify-center bg-[#0082C9] hover:bg-[#0070ad] text-white h-11 rounded-full font-heading font-semibold text-sm group gap-2.5 transition-colors cursor-pointer border-0 shadow-none mt-2"
                            >
                                <span>Отправить данные заявки</span>
                                <Send class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" />
                            </Button>

                            <!-- Success feedback message -->
                            <div
                                v-if="isFormSubmitted"
                                class="p-4 rounded-xl bg-emerald-100 text-emerald-900 text-xs sm:text-sm font-medium border-0 shadow-none flex items-center gap-2"
                            >
                                <CheckCircle2 class="w-4 h-4 text-emerald-700 shrink-0" />
                                <span>Форма успешно проверена и отправлена!</span>
                            </div>
                        </form>
                    </div>
                </Container>
            </section>
        </div>
    </AppLayout>
</template>

<style scoped>
.carousel-track,
.no-scrollbar {
    -ms-overflow-style: none !important;
    scrollbar-width: none !important;
}
.carousel-track::-webkit-scrollbar,
.no-scrollbar::-webkit-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
    background: transparent !important;
}
</style>
