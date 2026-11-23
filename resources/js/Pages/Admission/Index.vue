<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/shared/Container.vue';
import IntroCard from '@/Components/layout/IntroCard.vue';
import Button from '@/Components/ui/button/Button.vue';
import { Field, FieldLabel } from '@/Components/ui/field';
import Input from '@/Components/ui/input/Input.vue';
import Textarea from '@/Components/ui/textarea/Textarea.vue';
import PhoneInput from '@/Components/ui/input/PhoneInput.vue';
import { isPhoneComplete } from '@/composables/usePhoneMask';
import {
    CheckCircle,
    FileText,
    Calendar,
    ArrowRight,
    Home,
} from 'lucide-vue-next';

interface Specialty {
    id: number;
    title: string;
    code: string;
    base_education: string;
}

interface FaqItem {
    id: number;
    question: string;
    answer: string;
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
    specialties: Specialty[];
    faqs?: FaqItem[];
}>();

const defaultSteps = [
    {
        step_num: 1,
        title: 'Выбор специальности',
        desc: 'Выберите направление из актуальных программ обучения на базе 9 или 11 классов.',
        badgeColor: 'bg-[#0082C9] text-white',
    },
    {
        step_num: 2,
        title: 'Подача заявления',
        desc: 'Заполните онлайн-заявку на сайте или посетите приемную комиссию лично (ул. Сатпаева 16а).',
        badgeColor: 'bg-[#FDA685] text-slate-900',
    },
    {
        step_num: 3,
        title: 'Пакет документов',
        desc: 'Предоставьте подлинник аттестата, медсправку 075-У, 4 фото 3х4 и удостоверение личности.',
        badgeColor: 'bg-[#0F4C3A] text-white',
    },
    {
        step_num: 4,
        title: 'Зачисление',
        desc: 'Конкурс грантов или заключение договора и торжественное получение студенческого билета!',
        badgeColor: 'bg-emerald-600 text-white',
    },
];

const stepsList = computed(() => {
    if (props.page?.content?.steps && Array.isArray(props.page.content.steps) && props.page.content.steps.length > 0) {
        return props.page.content.steps.map((s: any, idx: number) => {
            const fallback = defaultSteps[idx % defaultSteps.length];
            return {
                step_num: s.step_num || idx + 1,
                title: s.title || fallback.title,
                desc: s.desc || fallback.desc,
                badgeColor: fallback.badgeColor,
            };
        });
    }
    return defaultSteps;
});

const defaultDocuments = [
    'Документ (подлинник и копия) об окончании 9 или 11 класса (аттестат с приложением).',
    'Медицинская справка по форме № 075-У со снимком флюорографии (для инвалидов I и II группы — заключение МСЭ).',
    '4 фотографии размером 3х4 см.',
    'Копия свидетельства о рождении или удостоверения личности абитуриента с ИИН.',
    'Копия удостоверения личности одного из родителей (для несовершеннолетних).',
];

const requiredDocumentsList = computed(() => {
    if (props.page?.content?.required_documents && Array.isArray(props.page.content.required_documents) && props.page.content.required_documents.length > 0) {
        return props.page.content.required_documents.map((d: any) => typeof d === 'string' ? d : (d.item || d.name || ''));
    }
    return defaultDocuments;
});

const defaultDormFeatures = [
    'Круглосуточное видеонаблюдение и пропускная система безопасности',
    'Оборудованные кухни на каждом этаже, прачечная и сушильные комнаты',
    'Высокоскоростной Wi-Fi на всей территории кампуса',
    'Шаговая доступность (5 минут) до учебных корпусов колледжа',
];

const dormitoryFeatures = computed(() => {
    if (props.page?.content?.dormitory?.features && Array.isArray(props.page.content.dormitory.features) && props.page.content.dormitory.features.length > 0) {
        return props.page.content.dormitory.features.map((f: any) => typeof f === 'string' ? f : (f.feature || f.name || ''));
    }
    return defaultDormFeatures;
});

const form = useForm({
    applicant_name: '',
    phone: '',
    email: '',
    specialty_id: props.specialties[0]?.id || '',
    base_education: '9_grade',
    notes: '',
    documents: [] as File[],
    photo: null as File | null,
});

const onDocumentsChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        form.documents = Array.from(target.files);
    }
};

const onPhotoChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.photo = target.files[0];
    }
};

const submit = () => {
    form.post('/admission/apply', {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset('applicant_name', 'phone', 'email', 'notes', 'documents', 'photo');
        },
    });
};
</script>

<template>
    <AppLayout :title="page?.seo_title || page?.title || 'Абитуриенту — Колледж Туран'">
        <!-- Hero / Intro Banner with Main Header -->
        <IntroCard
            title="АБИТУРИЕНТУ"
            :bg-image="page?.featured_image ? (page.featured_image.startsWith('images/') ? '/' + page.featured_image : '/storage/' + page.featured_image) : undefined"
            :breadcrumbs="[
                { label: 'Главная', href: '/' },
                { label: 'Абитуриенту' },
            ]"
        />

        <!-- Admission Deadlines Banner: No borders, no shadows, no blue backgrounds -->
        <section class="py-6 sm:py-8 bg-white">
            <Container>
                <div class="p-5 sm:p-7 rounded-[26px] bg-slate-50 border-0 shadow-none flex flex-col lg:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-3.5 sm:gap-4 text-left w-full lg:w-auto">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white text-[#0082C9] border-0 shadow-none flex items-center justify-center shrink-0">
                            <Calendar class="w-6 h-6 sm:w-7 sm:h-7" />
                        </div>
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-white text-[#0F4C3A] text-sm font-bold uppercase tracking-wider mb-1 border-0 shadow-none">
                                {{ page?.content?.deadlines?.note || '2026–2027 учебный год' }}
                            </div>
                            <h3 class="font-heading font-black text-lg sm:text-xl lg:text-2xl text-slate-900 uppercase tracking-tight leading-tight">
                                Сроки приёма документов
                            </h3>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 w-full lg:w-auto">
                        <div class="p-4 rounded-2xl bg-white border-0 shadow-none flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-emerald-500 shrink-0"></div>
                            <div>
                                <span class="text-sm text-slate-500 block leading-tight">Дневное отделение</span>
                                <span class="font-heading font-bold text-sm sm:text-base text-[#0F4C3A]">{{ page?.content?.deadlines?.daytime_dates || 'с 25 июня по 27 августа' }}</span>
                            </div>
                        </div>
                        <div class="p-4 rounded-2xl bg-white border-0 shadow-none flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-[#0082C9] shrink-0"></div>
                            <div>
                                <span class="text-sm text-slate-500 block leading-tight">Заочное отделение</span>
                                <span class="font-heading font-bold text-sm sm:text-base text-[#0082C9]">{{ page?.content?.deadlines?.extramural_dates || 'с 25 июня по 20 сентября' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </Container>
        </section>

        <!-- 4 Steps of Admission: No borders, no shadows, slightly grayish blocks -->
        <section class="py-8 sm:py-14 bg-white">
            <Container>
                <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
                    <h2 class="font-heading font-black text-2xl sm:text-4xl text-slate-900 uppercase tracking-tight">
                        Как поступить в колледж
                    </h2>
                    <p class="text-slate-600 text-sm sm:text-base mt-2">
                        Простые и понятные шаги для успешного зачисления
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    <div
                        v-for="step in stepsList"
                        :key="step.step_num"
                        class="p-6 sm:p-7 rounded-[24px] bg-slate-50 border-0 shadow-none text-center"
                    >
                        <div :class="['w-11 h-11 rounded-2xl font-heading font-black text-lg flex items-center justify-center mx-auto mb-4 border-0 shadow-none', step.badgeColor]">
                            {{ step.step_num }}
                        </div>
                        <h4 class="font-heading font-bold text-base sm:text-lg text-slate-900 mb-2">{{ step.title }}</h4>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            {{ step.desc }}
                        </p>
                    </div>
                </div>
            </Container>
        </section>

        <!-- Required Documents from Official Site: No borders, no shadows -->
        <section class="py-8 sm:py-14 bg-white">
            <Container>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">
                    <!-- Documents Checklist -->
                    <div class="lg:col-span-6 p-6 sm:p-8 rounded-[26px] bg-slate-50 border-0 shadow-none">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-11 h-11 rounded-2xl bg-white text-[#0082C9] border-0 shadow-none flex items-center justify-center shrink-0">
                                <FileText class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="font-heading font-black text-lg sm:text-xl text-slate-900 uppercase">
                                    Необходимые документы
                                </h3>
                                <p class="text-sm text-slate-500">Официальный перечень колледжа «Туран»</p>
                            </div>
                        </div>

                        <ul class="space-y-3 text-sm text-slate-700">
                            <li
                                v-for="(doc, idx) in requiredDocumentsList"
                                :key="idx"
                                class="flex items-start gap-3"
                            >
                                <CheckCircle class="w-5 h-5 text-[#0082C9] shrink-0 mt-0.5" />
                                <span>{{ doc }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Dormitory & Benefits -->
                    <div class="lg:col-span-6 p-6 sm:p-8 rounded-[26px] bg-slate-50 border-0 shadow-none">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-11 h-11 rounded-2xl bg-white text-[#0F4C3A] border-0 shadow-none flex items-center justify-center shrink-0">
                                <Home class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="font-heading font-black text-lg sm:text-xl text-slate-900 uppercase">
                                    {{ page?.content?.dormitory?.title || 'Студенческое общежитие и гранты' }}
                                </h3>
                                <p class="text-sm text-slate-500">{{ page?.content?.dormitory?.badge || 'Условия проживания и государственная поддержка' }}</p>
                            </div>
                        </div>

                        <p class="text-sm text-slate-600 leading-relaxed mb-4">
                            {{ page?.content?.dormitory?.desc || 'Колледж располагает собственным комфортабельным общежитием в шаговой доступности. Круглосуточная охрана, Wi-Fi, кухни и комнаты самоподготовки.' }}
                        </p>

                        <div v-if="page?.content?.tuition_grants" class="p-3.5 mb-4 rounded-2xl bg-white border-0 shadow-none text-sm text-slate-700 leading-relaxed">
                            {{ page.content.tuition_grants }}
                        </div>

                        <ul class="space-y-2 text-sm text-slate-700">
                            <li
                                v-for="(feat, idx) in dormitoryFeatures"
                                :key="idx"
                                class="flex items-start gap-2.5 p-2 rounded-xl bg-white"
                            >
                                <CheckCircle class="w-4 h-4 text-[#0F4C3A] shrink-0 mt-0.5" />
                                <span>{{ feat }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </Container>
        </section>

        <!-- Online Application Form Section (#apply): Full-width stretched form, compact, blue buttons with white text, no borders, no shadows -->
        <section id="apply" class="py-8 sm:py-14 bg-white">
            <Container class="max-w-4xl">
                <div class="p-6 sm:p-9 rounded-[28px] bg-slate-50 border-0 shadow-none text-slate-900">
                    <div class="text-center mb-6">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                            Электронная приёмная комиссия
                        </div>
                        <h2 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 uppercase tracking-tight">
                            Подать заявление онлайн
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-xl mx-auto">
                            Заполните форму, прикрепите документы и ваше заявление поступит напрямую сотрудникам приёмной комиссии
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-3.5 text-sm">
                        <!-- Row 1: Name and Phone -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <Field>
                                <FieldLabel for="adm_applicant_name">ФИО абитуриента</FieldLabel>
                                <Input
                                    id="adm_applicant_name"
                                    v-model="form.applicant_name"
                                    type="text"
                                    borderless
                                    required
                                    placeholder="Сериков Алишер Канатович"
                                />
                            </Field>

                            <Field>
                                <FieldLabel for="adm_phone">Номер телефона</FieldLabel>
                                <PhoneInput
                                    id="adm_phone"
                                    v-model="form.phone"
                                    borderless
                                    required
                                />
                            </Field>
                        </div>

                        <!-- Row 2: Email and Specialty -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <Field>
                                <FieldLabel for="adm_email">E-mail (необязательно)</FieldLabel>
                                <Input
                                    id="adm_email"
                                    v-model="form.email"
                                    type="email"
                                    borderless
                                    placeholder="applicant@example.com"
                                />
                            </Field>

                            <Field>
                                <FieldLabel for="adm_specialty">Интересующая специальность</FieldLabel>
                                <select
                                    id="adm_specialty"
                                    v-model="form.specialty_id"
                                    class="flex h-10 w-full rounded-md border-0 bg-slate-200/60 hover:bg-slate-200/80 px-3 py-2 text-sm text-slate-900 focus-visible:bg-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0082C9] transition-all"
                                >
                                    <option v-for="s in specialties" :key="s.id" :value="s.id">
                                        {{ s.code }} — {{ s.title }}
                                    </option>
                                </select>
                            </Field>
                        </div>

                        <!-- Row 3: Base Education Tabs (Borderless, grayish background) -->
                        <div class="grid grid-cols-2 gap-3 pt-0.5">
                            <label
                                class="flex items-center justify-center gap-2 p-2.5 rounded-xl border-0 text-xs sm:text-sm font-medium cursor-pointer transition-all shadow-none"
                                :class="form.base_education === '9_grade' ? 'bg-white text-[#0082C9] font-bold ring-2 ring-[#0082C9]' : 'bg-slate-200/60 text-slate-700 hover:bg-slate-200'"
                            >
                                <input type="radio" value="9_grade" v-model="form.base_education" class="sr-only" />
                                <span>База 9 классов</span>
                            </label>
                            <label
                                class="flex items-center justify-center gap-2 p-2.5 rounded-xl border-0 text-xs sm:text-sm font-medium cursor-pointer transition-all shadow-none"
                                :class="form.base_education === '11_grade' ? 'bg-white text-[#0082C9] font-bold ring-2 ring-[#0082C9]' : 'bg-slate-200/60 text-slate-700 hover:bg-slate-200'"
                            >
                                <input type="radio" value="11_grade" v-model="form.base_education" class="sr-only" />
                                <span>База 11 классов</span>
                            </label>
                        </div>

                        <!-- Row 4: File Uploads with Blue Buttons and White Text -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- Documents Upload -->
                            <Field>
                                <FieldLabel for="adm_documents">
                                    <span>Прикрепить документы</span>
                                    <span class="text-slate-400 font-normal ml-1.5">(аттестат, удостоверение)</span>
                                </FieldLabel>
                                <input
                                    id="adm_documents"
                                    type="file"
                                    multiple
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    @change="onDocumentsChange"
                                    class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0082C9] file:text-white hover:file:bg-[#0070ad] file:cursor-pointer cursor-pointer border-0 bg-slate-200/60 rounded-xl p-1.5 transition-colors"
                                />
                            </Field>

                            <!-- Photo Upload -->
                            <Field>
                                <FieldLabel for="adm_photo">
                                    <span>Фотография 3х4</span>
                                    <span class="text-slate-400 font-normal ml-1.5">(для личного дела)</span>
                                </FieldLabel>
                                <input
                                    id="adm_photo"
                                    type="file"
                                    accept="image/*"
                                    @change="onPhotoChange"
                                    class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0082C9] file:text-white hover:file:bg-[#0070ad] file:cursor-pointer cursor-pointer border-0 bg-slate-200/60 rounded-xl p-1.5 transition-colors"
                                />
                            </Field>
                        </div>

                        <!-- Row 5: Question / Comment -->
                        <Field>
                            <FieldLabel for="adm_notes">Вопрос или комментарий</FieldLabel>
                            <Textarea
                                id="adm_notes"
                                v-model="form.notes"
                                borderless
                                rows="2"
                                placeholder="Задайте вопрос приемной комиссии..."
                            />
                        </Field>

                        <!-- Submit Button: No border, no shadow, compact -->
                        <Button
                            type="submit"
                            :disabled="form.processing || (form.phone && !isPhoneComplete(form.phone))"
                            class="w-full justify-center bg-[#0082C9] hover:bg-[#0070ad] text-white h-11 rounded-full font-heading font-semibold text-sm group gap-2.5 transition-colors cursor-pointer border-0 shadow-none mt-2"
                        >
                            <span>Отправить заявление в приёмную комиссию</span>
                            <ArrowRight class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" />
                        </Button>

                        <div v-if="$page.props.flash?.success" class="p-3.5 bg-emerald-100 text-emerald-900 text-xs sm:text-sm rounded-xl border-0 shadow-none font-medium">
                            {{ $page.props.flash.success }}
                        </div>
                    </form>
                </div>
            </Container>
        </section>
    </AppLayout>
</template>
