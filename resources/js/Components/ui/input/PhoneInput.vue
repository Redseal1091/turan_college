<script setup lang="ts">
import { ref } from 'vue';
import { formatPhoneNumber, isPhoneComplete } from '@/composables/usePhoneMask';
import { cn } from '@/lib/utils';

interface Props {
    modelValue?: string;
    id?: string;
    name?: string;
    required?: boolean;
    disabled?: boolean;
    placeholder?: string;
    borderless?: boolean;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: '',
    id: undefined,
    name: undefined,
    required: false,
    disabled: false,
    placeholder: '+7 (7XX) XXX-XX-XX',
    borderless: false,
    class: '',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'blur'): void;
}>();

const isTouched = ref(false);

const onInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const formatted = formatPhoneNumber(input.value);
    input.value = formatted;
    emit('update:modelValue', formatted);
};

const onBlur = () => {
    isTouched.value = true;
    emit('blur');
};

const onKeydown = (event: KeyboardEvent) => {
    const input = event.target as HTMLInputElement;
    // If pressing Backspace and character before cursor is a delimiter, move cursor back
    if (event.key === 'Backspace') {
        const selStart = input.selectionStart || 0;
        const val = input.value;
        if (selStart > 0) {
            const charBefore = val[selStart - 1];
            if ([' ', ')', '-', '('].includes(charBefore)) {
                // Remove formatting character and the digit before it
                event.preventDefault();
                let sliceEnd = selStart - 1;
                while (sliceEnd > 0 && [' ', ')', '-', '('].includes(val[sliceEnd - 1])) {
                    sliceEnd--;
                }
                if (sliceEnd > 0) {
                    const newVal = val.slice(0, sliceEnd - 1) + val.slice(selStart);
                    const formatted = formatPhoneNumber(newVal);
                    input.value = formatted;
                    emit('update:modelValue', formatted);
                }
            }
        }
    }
};
</script>

<template>
    <div class="w-full">
        <input
            :id="id"
            :name="name"
            type="tel"
            :value="modelValue"
            :required="required"
            :disabled="disabled"
            :placeholder="placeholder"
            @input="onInput"
            @blur="onBlur"
            @keydown="onKeydown"
            autocomplete="tel"
            :class="cn(
                'flex h-10 w-full rounded-md px-3 py-2 text-sm text-slate-900 transition-all duration-200 outline-none',
                borderless
                    ? 'border-0 bg-slate-100/80 hover:bg-slate-100 focus:bg-white focus:ring-2 focus:ring-[#0082C9]'
                    : 'border border-slate-200 bg-white hover:border-slate-300 focus:border-[#0082C9] focus:ring-2 focus:ring-[#0082C9]/20',
                props.class
            )"
        />
        <p
            v-if="isTouched && modelValue && !isPhoneComplete(modelValue)"
            class="mt-1 text-xs text-rose-500 font-medium"
        >
            Введите полный номер: +7 (7XX) XXX-XX-XX
        </p>
    </div>
</template>
