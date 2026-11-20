import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useUiStrings() {
    const page = usePage();

    const locale = computed<string>(() => (page.props.locale as string) || 'ru');

    const ui = computed<Record<string, string>>(() => {
        const raw = page.props.ui;
        return raw && typeof raw === 'object' ? (raw as Record<string, string>) : {};
    });

    function t(key: string, fallback?: string): string {
        const val = ui.value[key];
        if (typeof val === 'string' && val.length > 0) {
            return val;
        }
        return fallback ?? key;
    }

    return {
        locale,
        ui,
        t,
    };
}
