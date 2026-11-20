<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/shared/Container.vue';
import IntroCard from '@/Components/layout/IntroCard.vue';
import Button from '@/Components/ui/button/Button.vue';
import { Field, FieldLabel } from '@/Components/ui/field';
import Input from '@/Components/ui/input/Input.vue';
import PhoneInput from '@/Components/ui/input/PhoneInput.vue';
import Textarea from '@/Components/ui/textarea/Textarea.vue';
import { isPhoneComplete } from '@/composables/usePhoneMask';
import {
    MapPin,
    Phone,
    Mail,
    Clock,
    Send,
    MessageSquare,
    Building2,
    CheckCircle2,
    ArrowRight
} from 'lucide-vue-next';

interface PhoneItem {
    label: string;
    number: string;
}

interface ContactsData {
    address: string;
    phones: PhoneItem[];
    email: string;
    working_hours: string;
}

defineProps<{
    contacts: ContactsData;
}>();

const form = useForm({
    name: '',
    phone: '',
    message: '',
});

const submit = () => {
    form.post('/contacts/send', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AppLayout title="Контакты — Колледж Туран">
        <!-- Hero / Intro Banner with Main Header -->
        <IntroCard
            title="КОНТАКТЫ"
            :breadcrumbs="[
                { label: 'Главная', href: '/' },
                { label: 'Контакты' },
            ]"
        />

        <!-- Main Contacts & Feedback Form -->
        <section class="py-16 sm:py-24 bg-white">
            <Container>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-10">
                    <!-- Left: Contact Details -->
                    <div class="lg:col-span-5 space-y-6">
                        <div class="p-6 sm:p-9 rounded-[28px] bg-slate-50 border-0 shadow-none text-slate-900">
                            <h3 class="font-heading font-black text-xl sm:text-2xl text-slate-900 mb-6 uppercase tracking-tight">
                                Контактная информация
                            </h3>

                            <div class="space-y-6 text-sm">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-white text-[#0082C9] border-0 shadow-none flex items-center justify-center shrink-0">
                                        <MapPin class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 block mb-0.5">Адрес колледжа:</span>
                                        <span class="font-semibold text-slate-900">{{ contacts.address }}</span>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-white text-[#0F4C3A] border-0 shadow-none flex items-center justify-center shrink-0">
                                        <Phone class="w-5 h-5" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <span class="text-xs text-slate-400 block mb-0.5">Телефоны:</span>
                                        <div v-for="(p, idx) in contacts.phones" :key="idx">
                                            <span class="text-slate-500 text-xs">{{ p.label }}: </span>
                                            <a :href="`tel:${p.number}`" class="font-bold text-slate-900 hover:text-[#0082C9] transition-colors">
                                                {{ p.number }}
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-white text-[#0082C9] border-0 shadow-none flex items-center justify-center shrink-0">
                                        <Mail class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 block mb-0.5">Электронная почта:</span>
                                        <a :href="`mailto:${contacts.email}`" class="font-bold text-slate-900 hover:text-[#0082C9] transition-colors">
                                            {{ contacts.email }}
                                        </a>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-white text-amber-600 border-0 shadow-none flex items-center justify-center shrink-0">
                                        <Clock class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 block mb-0.5">Режим работы:</span>
                                        <span class="font-semibold text-slate-900">{{ contacts.working_hours }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Feedback Form (Submits to Controller) -->
                    <div class="lg:col-span-7">
                        <div class="p-6 sm:p-9 rounded-[28px] bg-slate-50 border-0 shadow-none text-slate-900">
                            <h3 class="font-heading font-black text-xl sm:text-2xl text-slate-900 mb-2 uppercase tracking-tight">
                                Написать нам
                            </h3>
                            <p class="text-slate-500 text-sm mb-6">
                                Оставьте сообщение, и приемная комиссия ответит на ваш запрос в течение рабочего дня.
                            </p>

                            <form @submit.prevent="submit" class="space-y-4">
                                <Field>
                                    <FieldLabel for="contact_name">Ваше имя</FieldLabel>
                                    <Input
                                        id="contact_name"
                                        v-model="form.name"
                                        type="text"
                                        borderless
                                        required
                                        placeholder="Айдос Сериков"
                                    />
                                </Field>

                                <Field>
                                    <FieldLabel for="contact_phone">Номер телефона</FieldLabel>
                                    <PhoneInput
                                        id="contact_phone"
                                        v-model="form.phone"
                                        borderless
                                        required
                                    />
                                </Field>

                                <Field>
                                    <FieldLabel for="contact_message">Текст сообщения или вопрос</FieldLabel>
                                    <Textarea
                                        id="contact_message"
                                        v-model="form.message"
                                        borderless
                                        required
                                        rows="4"
                                        placeholder="Интересуют условия поступления на грант после 9 класса..."
                                    />
                                </Field>

                                <Button
                                    type="submit"
                                    :disabled="form.processing || (form.phone && !isPhoneComplete(form.phone))"
                                    class="w-full justify-center bg-[#0082C9] hover:bg-[#0070ad] text-white h-11 rounded-full font-heading font-semibold text-sm group gap-2.5 cursor-pointer transition-colors border-0 shadow-none mt-2"
                                >
                                    <span>Отправить сообщение</span>
                                    <Send class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" />
                                </Button>

                                <div v-if="$page.props.flash?.success" class="p-4 bg-emerald-100 text-emerald-900 text-xs sm:text-sm rounded-2xl border-0 shadow-none flex items-center gap-2">
                                    <CheckCircle2 class="w-5 h-5 text-emerald-700 shrink-0" />
                                    <span>{{ $page.props.flash.success }}</span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </Container>
        </section>
    </AppLayout>
</template>
