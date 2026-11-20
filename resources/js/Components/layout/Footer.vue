<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Container from '@/Components/shared/Container.vue';
import { useUiStrings } from '@/composables/useUiStrings';

const { t } = useUiStrings();
</script>

<template>
    <footer
        class="relative text-white bg-cover bg-center font-sans overflow-hidden"
        style="background-image: url('/images/footer.png');"
    >
        <!-- Dark ambient overlay to ensure high contrast and readability -->
        <div class="pt-14 sm:pt-16 pb-12">
            <Container>
                <!-- 1. Top Section: Logo & Address/Email -->
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 pb-10">
                    <!-- Brand Left: White Box with Blue Logo + Text -->
                    <Link href="/" class="flex items-center gap-3.5 group">
                        <div class="w-12 h-12 lg:w-[64px] lg:h-[64px] bg-white flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-200 aspect-square">
                            <img
                                src="/images/logo_college_turan.svg"
                                alt="Turan Logo"
                                class="w-full h-full object-contain"
                            />
                        </div>
                        <div>
                            <div class="font-gotham font-black text-xl sm:text-2xl tracking-wider text-white uppercase leading-none">
                                TURAN COLLEGE
                            </div>
                            <div class="font-['PT_Sans',sans-serif] text-[12px] text-slate-300 italic mt-1 font-normal tracking-tight">
                                {{ t('brand_slogan', 'Для тех, кто верит в себя и стремится к успеху!') }}
                            </div>
                        </div>
                    </Link>

                    <!-- Right Address & Email -->
                    <div class="text-xs sm:text-sm text-slate-200 space-y-1.5 md:text-right">
                        <div>Республика Казахстан, г. Алматы, ул. Сатпаева 16а</div>
                        <div>
                            <a
                                href="mailto:turan_college@mail.ru"
                                class="text-[#FDA685] hover:text-[#ffbca3] underline font-medium transition-colors"
                            >
                                turan_college@mail.ru
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 2. Main Navigation Columns (Matching Mockup Layout) -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 sm:gap-10 py-6 text-xs sm:text-[13px]">
                    <!-- Col 1: О нас -->
                    <div>
                        <h4 class="font-heading font-bold text-white mb-3 text-sm sm:text-base">
                            О нас:
                        </h4>
                        <ul class="space-y-2 text-slate-200">
                            <li><Link href="/about" class="hover:text-white hover:underline transition-colors">Про колледж</Link></li>
                            <li><Link href="/about#staff" class="hover:text-white hover:underline transition-colors">Педагогический состав</Link></li>
                            <li><Link href="/about#mission" class="hover:text-white hover:underline transition-colors">Миссия, стратегия и видение</Link></li>
                            <li><Link href="/specialties" class="hover:text-white hover:underline transition-colors">Образовательная деятельность</Link></li>
                            <li><Link href="/about#international" class="hover:text-white hover:underline transition-colors">Международное сотрудничество</Link></li>
                            <li><Link href="/about#docs" class="hover:text-white hover:underline transition-colors">Самооценка</Link></li>
                        </ul>
                    </div>

                    <!-- Col 2: Карьера -->
                    <div>
                        <h4 class="font-heading font-bold text-white mb-3 text-sm sm:text-base">
                            Карьера:
                        </h4>
                        <ul class="space-y-2 text-slate-200">
                            <li><Link href="/career" class="hover:text-white hover:underline transition-colors">О выпускниках</Link></li>
                            <li><Link href="/career#resume" class="hover:text-white hover:underline transition-colors">Как составлять резюме</Link></li>
                            <li><Link href="/career#rules" class="hover:text-white hover:underline transition-colors">Правила успешного трудоустройства</Link></li>
                            <li><Link href="/career#practice" class="hover:text-white hover:underline transition-colors">Практика и социальное партнерство</Link></li>
                        </ul>
                    </div>

                    <!-- Col 3: Абитуриенту & Студенту -->
                    <div>
                        <h4 class="font-heading font-bold text-white mb-3 text-sm sm:text-base">
                            Абитуриенту:
                        </h4>
                        <ul class="space-y-2 text-slate-200">
                            <li><Link href="/admission#rules" class="hover:text-white hover:underline transition-colors">Правила приема</Link></li>
                            <li><Link href="/admission#fees" class="hover:text-white hover:underline transition-colors">Положение об оплате</Link></li>
                            <li><Link href="/admission#docs" class="hover:text-white hover:underline transition-colors">Необходимые документы</Link></li>
                        </ul>

                        <h4 class="font-heading font-bold text-white mt-6 mb-3 text-sm sm:text-base">
                            Студенту:
                        </h4>
                        <ul class="space-y-2 text-slate-200">
                            <li><Link href="/student#med" class="hover:text-white hover:underline transition-colors">Медицинский пункт</Link></li>
                            <li><Link href="/student#food" class="hover:text-white hover:underline transition-colors">Пункт питания</Link></li>
                            <li><Link href="/student#rules" class="hover:text-white hover:underline transition-colors">Правила внутреннего распорядка</Link></li>
                        </ul>
                    </div>

                    <!-- Col 4: Главные ссылки и телефоны -->
                    <div class="space-y-6">
                        <ul class="space-y-2.5">
                            <li>
                                <Link href="/specialties" class="font-heading font-bold text-base sm:text-lg text-white hover:text-[#FDA685] transition-colors">
                                    Специальности
                                </Link>
                            </li>
                            <li>
                                <Link href="/news" class="font-heading font-bold text-base sm:text-lg text-white hover:text-[#FDA685] transition-colors">
                                    Новости
                                </Link>
                            </li>
                            <li>
                                <Link href="/contacts" class="font-heading font-bold text-base sm:text-lg text-white hover:text-[#FDA685] transition-colors">
                                    Контакты
                                </Link>
                            </li>
                        </ul>

                        <div class="space-y-1.5 text-sm sm:text-base font-semibold">
                            <div>
                                <a href="tel:+77272604035" class="text-[#FDA685] hover:text-[#ffbca3] transition-colors">
                                    +7 (727) 260-40-35
                                </a>
                            </div>
                            <div>
                                <a href="tel:+77272604030" class="text-[#FDA685] hover:text-[#ffbca3] transition-colors">
                                    +7 (727) 260-40-30
                                </a>
                            </div>
                            <div>
                                <a href="tel:+77785555365" class="text-[#FDA685] hover:text-[#ffbca3] transition-colors">
                                    +7 (778) 555 53 65
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </Container>
        </div>

        <!-- 3. Bottom Bar: Copyright & Privacy Policy -->
        <div class="bg-black/50 backdrop-blur-md py-4 text-xs text-slate-400 border-t border-white/5">
            <Container class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div>
                    © 2025 turan-college.edu.kz
                </div>
                <div class="flex items-center gap-6">
                    <Link href="/about#privacy" class="hover:text-white transition-colors">
                        Политика конфиденциальности
                    </Link>
                    <a href="/admin" target="_blank" class="hover:text-emerald-400 text-slate-400 transition-colors">
                        Админка
                    </a>
                </div>
            </Container>
        </div>
    </footer>
</template>
