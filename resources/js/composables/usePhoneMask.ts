export function formatPhoneNumber(value: string): string {
    if (!value) return '';
    // Strip all non-digit characters
    let digits = value.replace(/\D/g, '');

    // Normalize leading 8 to 7
    if (digits.startsWith('8')) {
        digits = '7' + digits.slice(1);
    } else if (!digits.startsWith('7') && digits.length > 0) {
        digits = '7' + digits;
    }

    // Limit to 11 digits
    digits = digits.slice(0, 11);

    if (digits.length === 0) return '';
    if (digits.length === 1) return '+7';

    let result = '+7';
    if (digits.length > 1) {
        result += ' (' + digits.slice(1, 4);
    }
    if (digits.length >= 4) {
        result += ') ';
    }
    if (digits.length > 4) {
        result += digits.slice(4, 7);
    }
    if (digits.length >= 7) {
        result += '-';
    }
    if (digits.length > 7) {
        result += digits.slice(7, 9);
    }
    if (digits.length >= 9) {
        result += '-';
    }
    if (digits.length > 9) {
        result += digits.slice(9, 11);
    }

    return result;
}

export function isPhoneComplete(value: string): boolean {
    if (!value) return false;
    const digits = value.replace(/\D/g, '');
    return digits.length === 11;
}

export function handlePhoneInput(event: Event, callback: (val: string) => void) {
    const input = event.target as HTMLInputElement;
    const formatted = formatPhoneNumber(input.value);
    input.value = formatted;
    callback(formatted);
}
