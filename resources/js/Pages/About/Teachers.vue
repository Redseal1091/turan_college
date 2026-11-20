<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/shared/Container.vue';
import IntroCard from '@/Components/layout/IntroCard.vue';
import LazyImage from '@/Components/ui/image/LazyImage.vue';
import { Search, ArrowUpRight, X } from 'lucide-vue-next';

interface Teacher {
    id: number;
    full_name: string;
    position: string;
    department: string;
    bio: string;
    photo_url?: string | null;
}

const props = defineProps<{
    teachers: Teacher[];
}>();

const breadcrumbs = [
    { label: 'Главная', href: '/' },
    { label: 'Колледж', href: '/about' },
    { label: 'Педагогический состав' },
];

const selectedDepartment = ref<string>('Все');
const searchQuery = ref<string>('');

const departments = computed(() => {
    const set = new Set<string>();
    props.teachers.forEach((t) => {
        if (t.department) set.add(t.department);
    });
    return ['Все', ...Array.from(set)];
});

const filteredTeachers = computed(() => {
    return props.teachers.filter((t) => {
        const matchesDept = selectedDepartment.value === 'Все' || t.department === selectedDepartment.value;
        const matchesSearch = !searchQuery.value.trim() || 
            t.full_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            t.position.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            t.bio.toLowerCase().includes(searchQuery.value.toLowerCase());
        return matchesDept && matchesSearch;
    });
});

// Teacher Detail Modal & Scroll Locking (prevents layout shift)
const selectedTeacher = ref<Teacher | null>(null);

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

const openTeacherModal = (teacher: Teacher) => {
    selectedTeacher.value = teacher;
    lockScroll();
};

const closeTeacherModal = () => {
    selectedTeacher.value = null;
    unlockScroll();
};

const handleKeyDown = (e: KeyboardEvent) => {
    if (e.key === 'Escape' && selectedTeacher.value) {
        closeTeacherModal();
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
    unlockScroll();
});
</script>

<template>
    <AppLayout title="Педагогический состав — Колледж Туран">
        <!-- 1. Clean Header Mode: No photo background under title -->
        <IntroCard
            :no-background="true"
            title="ПЕДАГОГИЧЕСКИЙ СОСТАВ"
            :breadcrumbs="breadcrumbs"
        />

        <!-- 2. Teachers Page Content: Strictly White Background (bg-white) -->
        <section class="pb-20 mt-4 md:mt-20 sm:pb-28 bg-white">
            <Container>
                <!-- Intro Bar: Subtitle & Real-time Search -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-slate-100">
                    <div class="space-y-2 max-w-2xl">
                        <p class="font-sans text-base sm:text-lg text-slate-700 leading-relaxed font-normal">
                            Преподаватели высшей категории, педагоги-исследователи, магистры и эксперты-практики, передающие студентам современные знания и реальный отраслевой опыт.
                        </p>
                        
                    </div>

                    <!-- Search Input (font 16px, no text-xs, border-0, bg-slate-50) -->
                    <div class="w-full md:w-80 lg:w-96 relative shrink-0">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Поиск по имени или предмету..."
                            class="w-full pl-11 pr-10 py-3 rounded-full bg-slate-50 border-0 text-base text-slate-900 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-[#0082C9] transition-all shadow-none"
                        />
                        <Search class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" />
                        <button
                            v-if="searchQuery"
                            type="button"
                            @click="searchQuery = ''"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400 hover:text-slate-700 p-1 cursor-pointer"
                            aria-label="Очистить поиск"
                        >
                            ✕
                        </button>
                    </div>
                </div>

                <!-- Department Filter Tabs: Grayish pills bg-slate-50, border-0, shadow-none -->
                <div class="py-6 sm:py-8 flex flex-wrap items-center gap-2.5 sm:gap-3">
                    <button
                        v-for="dept in departments"
                        :key="dept"
                        type="button"
                        @click="selectedDepartment = dept"
                        class="px-5 py-2.5 rounded-full text-sm sm:text-base font-heading font-semibold transition-all duration-300 cursor-pointer select-none border-0 shadow-none"
                        :class="selectedDepartment === dept
                            ? 'bg-[#0082C9] text-white'
                            : 'bg-slate-50 text-slate-700 hover:bg-slate-100'"
                    >
                        {{ dept }}
                        <span
                            class="ml-1.5 opacity-80"
                            :class="selectedDepartment === dept ? 'text-white' : 'text-slate-400'"
                        >
                            ({{ dept === 'Все' ? teachers.length : teachers.filter(t => t.department === dept).length }})
                        </span>
                    </button>
                </div>

                <!-- Active Filter Stats -->
                <div v-if="filteredTeachers.length > 0" class="text-slate-500 mb-6 text-right opacity-20">
                    Отображено: {{ filteredTeachers.length }} из {{ teachers.length }}
                </div>

                <!-- Teachers Grid: Matching Reference Style (Light Frosted Glass, Full ФИО, No Subtitle, 12px Blue Badge) -->
                <div
                    v-if="filteredTeachers.length > 0"
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-7"
                >
                    <div
                        v-for="t in filteredTeachers"
                        :key="t.id"
                        @click="openTeacherModal(t)"
                        class="group relative overflow-hidden rounded-[26px] sm:rounded-[32px] bg-slate-100 border border-slate-200/80 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] hover:-translate-y-1.5 shadow-none hover:shadow-none cursor-pointer flex flex-col justify-end min-h-[460px] sm:min-h-[490px] select-none"
                    >
                        <!-- Full-bleed background photo shifted higher so heads are higher in the frame -->
                        <LazyImage
                            v-if="t.photo_url"
                            :src="t.photo_url"
                            :alt="t.full_name"
                            wrapper-class="absolute -top-8 sm:-top-11 inset-x-0 w-full h-[calc(100%+2rem)] sm:h-[calc(100%+2.75rem)]"
                            class="w-full h-full object-cover object-top transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-105"
                            loading="lazy"
                        />
                        <div
                            v-else
                            class="absolute inset-0 w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400 font-heading font-black text-6xl flex items-center justify-center select-none"
                        >
                            {{ t.full_name.charAt(0) }}
                        </div>

                        <!-- Floating Light Frosted Glass Info Panel (Светлое стекло) -->
                        <div class="relative z-10 m-3 sm:m-1.5 p-4 sm:p-5 rounded-[20px] sm:rounded-[24px] bg-white/60 backdrop-blur-sm border border-white/20 text-slate-900 transition-all duration-300 group-hover:bg-white/90 group-hover:border-white shadow-[0_4px_24px_rgba(0,0,0,0.06)]">
                            <!-- Header Row: Full Name (ФИО без сокращений) on Left, Circular Arrow Button on Right -->
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-heading font-semibold text-base sm:text-[18px] text-slate-900 leading-snug group-hover:text-[#0082C9] transition-colors break-words flex-1 min-w-0" :title="t.full_name">
                                    {{ t.full_name }}
                                </h3>

                                <!-- Circular Action Button with ↗ (ArrowUpRight) - Clean Distinct Border, Zero Shadow -->
                                <div
                                    class="shrink-0 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/30 group-hover:bg-[#0082C9] border border-white flex items-center justify-center text-slate-800 group-hover:text-white transition-all duration-300 group-hover:scale-105 shadow-none"
                                    aria-label="Подробнее"
                                >
                                    <ArrowUpRight class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-300 " />
                                </div>
                            </div>

                            <!-- Bio Description -->
                            <p class="text-xs font-heading text-slate-600 leading-relaxed font-normal pt-2.5 line-clamp-3">
                                {{ t.bio }} 
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-20 bg-slate-50 rounded-[28px] border-0 p-8 max-w-lg mx-auto shadow-none">
                    <p class="text-slate-600 text-base mb-4 font-normal">По вашему запросу ничего не найдено</p>
                    <button
                        type="button"
                        @click="() => { selectedDepartment = 'Все'; searchQuery = ''; }"
                        class="px-6 py-2.5 rounded-full bg-[#0082C9] text-white text-sm font-heading font-semibold hover:bg-[#0070ad] transition-colors cursor-pointer border-0 shadow-none"
                    >
                        Сбросить фильтры
                    </button>
                </div>
            </Container>
        </section>

        <!-- Teacher Full Profile Modal (1.5x Larger, Half Border-Radius, Close on Backdrop Click, Cross-only Button) -->
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
                    v-if="selectedTeacher"
                    class="fixed inset-0 z-[100] bg-[#02243d]/35 backdrop-blur-lg flex items-center justify-center p-4 sm:p-6 cursor-pointer"
                    @click="closeTeacherModal"
                >
                    <div
                        class="relative w-full max-w-5xl bg-white rounded-[14px] sm:rounded-[18px] overflow-hidden shadow-2xl border border-slate-100 flex flex-col md:flex-row max-h-[90vh] cursor-default"
                        @click.stop
                    >
                        <!-- Modal Photo -->
                        <div class="w-full md:w-5/12 aspect-[4/3] md:aspect-auto relative bg-slate-100 shrink-0">
                            <LazyImage
                                v-if="selectedTeacher.photo_url"
                                :src="selectedTeacher.photo_url"
                                :alt="selectedTeacher.full_name"
                                wrapper-class="w-full h-full"
                                class="w-full h-full object-cover object-top"
                            />
                            <div
                                v-else
                                class="w-full h-full min-h-[260px] bg-[#0082C9]/10 text-[#0082C9] font-heading font-black text-6xl flex items-center justify-center select-none"
                            >
                                {{ selectedTeacher.full_name.charAt(0) }}
                            </div>
                        </div>

                        <!-- Modal Body (Vertically Centered Text) -->
                        <div class="relative p-6 sm:p-12 flex flex-col justify-center flex-1 overflow-y-auto">
                            <!-- Close Button (Pinned to top right, cross only, cleanly in corner) -->
                            <button
                                type="button"
                                @click="closeTeacherModal"
                                class="absolute top-3.5 right-3.5 sm:top-5 sm:right-5 shrink-0 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center cursor-pointer transition-colors z-30"
                                aria-label="Закрыть"
                            >
                                <X class="w-5 h-5 text-slate-700" />
                            </button>

                            <!-- Vertically Centered Content Block (Clean right padding so heading never collides with close button) -->
                            <div class="space-y-6 my-auto pr-10 sm:pr-12">
                                <div>
                                    <h3 class="font-heading font-bold text-2xl sm:text-3xl text-slate-900 leading-tight">
                                        {{ selectedTeacher.full_name }}
                                    </h3>
                                    <div class="text-base font-semibold text-[#4B8EB5] mt-2">
                                        {{ selectedTeacher.position }}
                                    </div>
                                </div>

                                <!-- Bio text directly without heading -->
                                <div class="pt-4 border-t border-slate-100">
                                    <p class="font-sans text-base sm:text-lg text-slate-700 leading-relaxed whitespace-pre-line font-normal">
                                        {{ selectedTeacher.bio }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AppLayout>
</template>

