<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/shared/Container.vue';
import Button from '@/Components/ui/button/Button.vue';
import { ArrowLeft, Home, Compass, BookOpen, PhoneCall } from 'lucide-vue-next';

const goBack = () => {
    if (typeof window !== 'undefined' && window.history.length > 1) {
        window.history.back();
    } else {
        router.visit('/');
    }
};
</script>

<template>
    <AppLayout title="Ошибка 404 — Страница не найдена — Колледж Туран">
        <section class="min-h-[75vh] flex items-center justify-center py-12 sm:py-20 bg-white">
            <Container class="max-w-2xl text-center">
                <!-- Card Container: Slightly grayish background (bg-slate-50), no borders, no shadows -->
                <div class="p-8 sm:p-14 rounded-[32px] bg-slate-50 border-0 shadow-none text-slate-900 flex flex-col items-center">
                    <!-- Turan College Brand Logo -->
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-white flex items-center justify-center p-3 mb-6 border-0 shadow-none shrink-0 aspect-square">
                        <img
                            src="/images/logo_college_turan.svg"
                            alt="Логотип Колледж Туран"
                            class="w-full h-full object-contain"
                        />
                    </div>

                    <!-- Huge 404 Number -->
                    <div class="font-heading font-black text-6xl sm:text-8xl tracking-tight text-[#0082C9] leading-none mb-3">
                        404
                    </div>

                    <!-- Heading -->
                    <h1 class="font-heading font-black text-2xl sm:text-3xl uppercase tracking-tight text-slate-900 mb-3">
                        Ошибка 404 • Страница не найдена
                    </h1>

                    <!-- Description -->
                    <p class="text-slate-600 text-sm sm:text-base max-w-md mx-auto leading-relaxed mb-8">
                        К сожалению, запрашиваемая страница не существует, была перемещена или удалена. Проверьте правильность адреса.
                    </p>

                    <!-- Action Buttons: "Вернуться назад" and "На главную" -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full sm:w-auto">
                        <Button
                            @click="goBack"
                            type="button"
                            class="w-full sm:w-auto rounded-full px-7 py-3.5 bg-[#0082C9] hover:bg-[#0070ad] text-white font-heading font-semibold text-sm sm:text-base gap-2.5 justify-center border-0 shadow-none cursor-pointer transition-colors"
                        >
                            <ArrowLeft class="w-4 h-4" />
                            <span>Вернуться назад</span>
                        </Button>

                        <Link href="/" class="w-full sm:w-auto">
                            <Button
                                variant="outline"
                                type="button"
                                class="w-full sm:w-auto rounded-full px-7 py-3.5 bg-white hover:bg-slate-100 text-slate-800 font-heading font-semibold text-sm sm:text-base gap-2 justify-center border-0 shadow-none cursor-pointer transition-colors"
                            >
                                <Home class="w-4 h-4" />
                                <span>На главную страницу</span>
                            </Button>
                        </Link>
                    </div>

                    <!-- Quick Navigation Links (Pure white pills) -->
                    <div class="pt-8 mt-8 border-t border-slate-200/50 w-full">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                            Популярные разделы колледжа:
                        </div>
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            <Link
                                href="/specialties"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs sm:text-sm font-medium transition-colors border-0 shadow-none"
                            >
                                <BookOpen class="w-3.5 h-3.5 text-[#0082C9]" />
                                <span>Специальности</span>
                            </Link>
                            <Link
                                href="/admission"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs sm:text-sm font-medium transition-colors border-0 shadow-none"
                            >
                                <Compass class="w-3.5 h-3.5 text-[#0082C9]" />
                                <span>Абитуриенту</span>
                            </Link>
                            <Link
                                href="/contacts"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs sm:text-sm font-medium transition-colors border-0 shadow-none"
                            >
                                <PhoneCall class="w-3.5 h-3.5 text-[#0082C9]" />
                                <span>Контакты</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </Container>
        </section>
    </AppLayout>
</template>
