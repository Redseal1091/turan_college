<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
} from '@/Components/ui/dialog';
import { Field, FieldLabel } from '@/Components/ui/field';
import Input from '@/Components/ui/input/Input.vue';
import PhoneInput from '@/Components/ui/input/PhoneInput.vue';
import Textarea from '@/Components/ui/textarea/Textarea.vue';
import Button from '@/Components/ui/button/Button.vue';
import { isPhoneComplete } from '@/composables/usePhoneMask';
import { useApplyModal } from '@/composables/useApplyModal';
import {
    FileText,
    Upload,
    X,
    CheckCircle2,
    ArrowRight,
    AlertCircle,
    Paperclip
} from 'lucide-vue-next';

interface SpecialtyOption {
    id: number | string;
    title: string;
    code?: string;
}

const props = withDefaults(
    defineProps<{
        open?: boolean;
        specialties?: SpecialtyOption[];
        defaultSpecialtyId?: number | string | null;
        defaultSpecialtyTitle?: string | null;
    }>(),
    {
        open: undefined,
        specialties: undefined,
        defaultSpecialtyId: null,
        defaultSpecialtyTitle: null,
    }
);

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const page = usePage();
const {
    isApplyModalOpen: globalOpen,
    activeSpecialtyId,
    activeSpecialtyTitle,
    activeNotes,
    closeApplyModal,
} = useApplyModal();

// Control open state: local prop if defined, else global composable
const isOpen = computed({
    get() {
        if (props.open !== undefined) {
            return props.open;
        }
        return globalOpen.value;
    },
    set(val: boolean) {
        if (props.open !== undefined) {
            emit('update:open', val);
        } else {
            if (!val) closeApplyModal();
            else globalOpen.value = true;
        }
    },
});

// Specialties list: from props or from shared page props
const availableSpecialties = computed<SpecialtyOption[]>(() => {
    if (props.specialties && props.specialties.length > 0) {
        return props.specialties;
    }
    const shared = (page.props as any).all_specialties || (page.props as any).specialties;
    if (Array.isArray(shared) && shared.length > 0) {
        return shared;
    }
    return [];
});

// Form state
const form = useForm({
    applicant_name: '',
    phone: '',
    email: '',
    specialty_id: '' as string | number,
    base_education: '9_grade' as '9_grade' | '11_grade',
    notes: '',
    documents: [] as File[],
});

// Attached files (up to 10 files)
const attachedFiles = ref<File[]>([]);
const fileInputRef = ref<HTMLInputElement | null>(null);
const fileWarning = ref<string | null>(null);
const isSuccess = ref(false);

const MAX_FILES = 10;

// Sync specialty from props or global state when opened
watch(
    isOpen,
    (val) => {
        if (val) {
            isSuccess.value = false;
            fileWarning.value = null;

            const targetSpecId = props.defaultSpecialtyId ?? activeSpecialtyId.value;
            if (targetSpecId) {
                form.specialty_id = targetSpecId;
            } else if (availableSpecialties.value.length > 0 && !form.specialty_id) {
                form.specialty_id = availableSpecialties.value[0].id;
            }

            const targetNotes = activeNotes.value;
            if (targetNotes && !form.notes) {
                form.notes = targetNotes;
            }
        }
    },
    { immediate: true }
);

// Format file size helper
const formatFileSize = (bytes: number): string => {
    if (bytes < 1024) return bytes + ' Б';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(0) + ' КБ';
    return (bytes / (1024 * 1024)).toFixed(1) + ' МБ';
};

// Open file selector
const triggerFileInput = () => {
    if (fileInputRef.value) {
        fileInputRef.value.click();
    }
};

// Handle file selection (allowing multiple selections up to 10)
const onFilesChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (!target.files || target.files.length === 0) return;

    fileWarning.value = null;
    const newFiles = Array.from(target.files);
    const currentCount = attachedFiles.value.length;
    const slotsAvailable = MAX_FILES - currentCount;

    if (slotsAvailable <= 0) {
        fileWarning.value = `Вы уже прикрепили максимальное количество файлов (${MAX_FILES}).`;
        target.value = '';
        return;
    }

    if (newFiles.length > slotsAvailable) {
        fileWarning.value = `Можно прикрепить до ${MAX_FILES} файлов. Добавлено первых ${slotsAvailable} из ${newFiles.length}.`;
        const allowed = newFiles.slice(0, slotsAvailable);
        attachedFiles.value.push(...allowed);
    } else {
        attachedFiles.value.push(...newFiles);
    }

    form.documents = [...attachedFiles.value];
    target.value = '';
};

// Remove single file
const removeFile = (index: number) => {
    attachedFiles.value.splice(index, 1);
    form.documents = [...attachedFiles.value];
    fileWarning.value = null;
};

// Submit handler
const submitForm = () => {
    if (form.phone && !isPhoneComplete(form.phone)) return;
    if (!form.applicant_name.trim()) return;

    form.documents = [...attachedFiles.value];

    form.post('/admission/apply', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            isSuccess.value = true;
            attachedFiles.value = [];
            setTimeout(() => {
                isSuccess.value = false;
                form.reset();
                isOpen.value = false;
            }, 3000);
        },
        onError: () => {
            isSuccess.value = false;
        },
    });
};
</script>

<template>
    <Dialog :open="isOpen" @update:open="isOpen = $event">
        <DialogContent class="max-w-xl p-5 sm:p-7 rounded-[28px] bg-white border-0 shadow-2xl text-slate-900">
            <!-- Header with Title & Subtitle (Padded right so close button never overlaps) -->
            <DialogHeader class="pr-8 text-left">
                <DialogTitle class="font-heading font-black text-xl sm:text-2xl text-slate-900 uppercase tracking-tight">
                    Подать заявление онлайн
                </DialogTitle>
                <DialogDescription class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                    Заполните форму, приложите документы (до 10 файлов) и сотрудники приёмной комиссии свяжутся с вами в кратчайшие сроки.
                </DialogDescription>
            </DialogHeader>

            <!-- Success Alert -->
            <div
                v-if="isSuccess"
                class="p-4 rounded-2xl bg-emerald-100 text-emerald-900 text-xs sm:text-sm font-medium border-0 shadow-none flex items-center gap-3 my-2"
            >
                <CheckCircle2 class="w-5 h-5 text-emerald-700 shrink-0" />
                <span>Заявление успешно отправлено! Специалист приёмной комиссии свяжется с вами в течение дня.</span>
            </div>

            <!-- Application Form -->
            <form v-else @submit.prevent="submitForm" class="space-y-4 pt-1 text-sm">
                <!-- Row 1: Name and Phone -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <Field>
                        <FieldLabel for="modal_applicant_name">ФИО абитуриента</FieldLabel>
                        <Input
                            id="modal_applicant_name"
                            v-model="form.applicant_name"
                            type="text"
                            borderless
                            required
                            placeholder="Сериков Алишер Канатович"
                        />
                    </Field>

                    <Field>
                        <FieldLabel for="modal_phone">Номер телефона (маска +7)</FieldLabel>
                        <PhoneInput
                            id="modal_phone"
                            v-model="form.phone"
                            borderless
                            required
                        />
                    </Field>
                </div>

                <!-- Row 2: Specialty Selector & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <Field>
                        <FieldLabel for="modal_specialty">Специальность</FieldLabel>
                        <select
                            id="modal_specialty"
                            v-model="form.specialty_id"
                            required
                            class="flex h-10 w-full rounded-md border-0 bg-slate-200/60 hover:bg-slate-200/80 px-3 py-2 text-xs sm:text-sm text-slate-900 focus-visible:bg-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0082C9] transition-all cursor-pointer"
                        >
                            <option value="" disabled>Выберите направление</option>
                            <option
                                v-for="spec in availableSpecialties"
                                :key="spec.id"
                                :value="spec.id"
                            >
                                {{ spec.title }} {{ spec.code ? `(${spec.code})` : '' }}
                            </option>
                        </select>
                    </Field>

                    <Field>
                        <FieldLabel for="modal_email">
                            <span>E-mail</span>
                            <span class="text-slate-400 font-normal ml-1">(необязательно)</span>
                        </FieldLabel>
                        <Input
                            id="modal_email"
                            v-model="form.email"
                            type="email"
                            borderless
                            placeholder="applicant@turan.kz"
                        />
                    </Field>
                </div>

                <!-- Row 3: Base Education Selection -->
                <div class="space-y-1.5">
                    <FieldLabel>База образования</FieldLabel>
                    <div class="grid grid-cols-2 gap-3">
                        <label
                            class="flex items-center justify-center gap-2 p-2.5 rounded-xl border-0 text-xs sm:text-sm font-medium cursor-pointer transition-all shadow-none select-none"
                            :class="form.base_education === '9_grade' ? 'bg-[#0082C9] text-white font-bold' : 'bg-slate-200/60 text-slate-700 hover:bg-slate-200'"
                        >
                            <input type="radio" value="9_grade" v-model="form.base_education" class="sr-only" />
                            <span>После 9 класса</span>
                        </label>
                        <label
                            class="flex items-center justify-center gap-2 p-2.5 rounded-xl border-0 text-xs sm:text-sm font-medium cursor-pointer transition-all shadow-none select-none"
                            :class="form.base_education === '11_grade' ? 'bg-[#0082C9] text-white font-bold' : 'bg-slate-200/60 text-slate-700 hover:bg-slate-200'"
                        >
                            <input type="radio" value="11_grade" v-model="form.base_education" class="sr-only" />
                            <span>После 11 класса</span>
                        </label>
                    </div>
                </div>

                <!-- Row 4: Up to 10 Document Attachments -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <FieldLabel class="mb-0">
                            <span>Документы для поступления</span>
                            <span class="text-slate-400 font-normal ml-1.5">(до 10 файлов)</span>
                        </FieldLabel>

                        <span
                            class="text-xs font-mono font-bold px-2 py-0.5 rounded-md"
                            :class="attachedFiles.length >= MAX_FILES ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-600'"
                        >
                            {{ attachedFiles.length }} / {{ MAX_FILES }} файлов
                        </span>
                    </div>

                    <!-- Hidden native file input -->
                    <input
                        ref="fileInputRef"
                        type="file"
                        multiple
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        @change="onFilesChange"
                        class="hidden"
                    />

                    <!-- File attachment trigger bar -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 p-3 rounded-2xl bg-slate-50 border-0 shadow-none">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <Paperclip class="w-4 h-4 text-[#0082C9] shrink-0" />
                            <span>Аттестат, удостоверение, фото 3х4 (PDF, DOC, JPG, PNG до 10 МБ)</span>
                        </div>

                        <button
                            type="button"
                            @click="triggerFileInput"
                            :disabled="attachedFiles.length >= MAX_FILES"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-[#0082C9] hover:bg-[#0070ad] disabled:bg-slate-300 disabled:cursor-not-allowed text-white text-xs font-heading font-semibold transition-colors cursor-pointer border-0 shadow-none shrink-0"
                        >
                            <Upload class="w-3.5 h-3.5" />
                            <span>{{ attachedFiles.length > 0 ? '+ Добавить ещё' : 'Выбрать файлы' }}</span>
                        </button>
                    </div>

                    <!-- File Limit Warning -->
                    <div
                        v-if="fileWarning"
                        class="p-2.5 rounded-xl bg-amber-50 text-amber-800 text-xs font-medium flex items-center gap-2 border-0 shadow-none"
                    >
                        <AlertCircle class="w-4 h-4 text-amber-600 shrink-0" />
                        <span>{{ fileWarning }}</span>
                    </div>

                    <!-- Attached Files List -->
                    <div
                        v-if="attachedFiles.length > 0"
                        class="space-y-1.5 max-h-40 overflow-y-auto pr-1"
                    >
                        <div
                            v-for="(file, idx) in attachedFiles"
                            :key="`${file.name}-${idx}`"
                            class="flex items-center justify-between gap-3 p-2.5 rounded-xl bg-slate-100/90 text-xs text-slate-800 transition-colors"
                        >
                            <div class="flex items-center gap-2 min-w-0">
                                <FileText class="w-4 h-4 text-[#0082C9] shrink-0" />
                                <span class="font-medium truncate max-w-[240px] sm:max-w-xs">{{ file.name }}</span>
                                <span class="text-[11px] text-slate-400 font-mono shrink-0">({{ formatFileSize(file.size) }})</span>
                            </div>

                            <button
                                type="button"
                                @click="removeFile(idx)"
                                class="w-6 h-6 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-white flex items-center justify-center transition-colors cursor-pointer border-0 shrink-0"
                                :aria-label="`Удалить файл ${file.name}`"
                            >
                                <X class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Row 5: Notes / Comment -->
                <Field>
                    <FieldLabel for="modal_notes">
                        <span>Вопрос или комментарий</span>
                        <span class="text-slate-400 font-normal ml-1">(необязательно)</span>
                    </FieldLabel>
                    <Textarea
                        id="modal_notes"
                        v-model="form.notes"
                        borderless
                        rows="2"
                        placeholder="Укажите интересующие вопросы приемной комиссии..."
                    />
                </Field>

                <!-- Footer: Submit button -->
                <DialogFooter class="pt-2">
                    <Button
                        type="submit"
                        :disabled="form.processing || (form.phone && !isPhoneComplete(form.phone)) || !form.applicant_name"
                        class="w-full justify-center bg-[#0082C9] hover:bg-[#0070ad] text-white h-11 rounded-full font-heading font-semibold text-sm gap-2 border-0 shadow-none cursor-pointer"
                    >
                        <span>Отправить заявление</span>
                        <ArrowRight class="w-4 h-4" />
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
