import { ref } from 'vue';

export interface ApplyModalOptions {
    specialtyId?: number | string | null;
    specialtyTitle?: string | null;
    notes?: string;
}

const isApplyModalOpen = ref(false);
const activeSpecialtyId = ref<number | string | null>(null);
const activeSpecialtyTitle = ref<string | null>(null);
const activeNotes = ref<string>('');

export function useApplyModal() {
    const openApplyModal = (options?: ApplyModalOptions) => {
        if (options?.specialtyId !== undefined) {
            activeSpecialtyId.value = options.specialtyId;
        } else {
            activeSpecialtyId.value = null;
        }

        if (options?.specialtyTitle !== undefined) {
            activeSpecialtyTitle.value = options.specialtyTitle;
        } else {
            activeSpecialtyTitle.value = null;
        }

        if (options?.notes !== undefined) {
            activeNotes.value = options.notes;
        } else {
            activeNotes.value = '';
        }

        isApplyModalOpen.value = true;
    };

    const closeApplyModal = () => {
        isApplyModalOpen.value = false;
    };

    return {
        isApplyModalOpen,
        activeSpecialtyId,
        activeSpecialtyTitle,
        activeNotes,
        openApplyModal,
        closeApplyModal,
    };
}
