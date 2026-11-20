import { computed, ref } from 'vue';

export type AccessibilityFontSize = 'normal' | 'large' | 'xlarge';
export type AccessibilityTheme = 'normal' | 'contrast' | 'dark' | 'yellow-blue';
export type AccessibilitySpacing = 'normal' | 'increased';
export type AccessibilityImages = 'on' | 'off';

export interface AccessibilitySettings {
    enabled: boolean;
    fontSize: AccessibilityFontSize;
    theme: AccessibilityTheme;
    spacing: AccessibilitySpacing;
    images: AccessibilityImages;
}

const STORAGE_KEY = 'turan_college_a11y_settings';

const defaultSettings: AccessibilitySettings = {
    enabled: false,
    fontSize: 'normal',
    theme: 'normal',
    spacing: 'normal',
    images: 'on',
};

const settings = ref<AccessibilitySettings>({ ...defaultSettings });
const isInitialized = ref(false);

const allClasses = [
    'a11y-enabled',
    'a11y-font-normal',
    'a11y-font-large',
    'a11y-font-xlarge',
    'a11y-theme-normal',
    'a11y-theme-contrast',
    'a11y-theme-dark',
    'a11y-theme-yellow-blue',
    'a11y-spacing-normal',
    'a11y-spacing-increased',
    'a11y-images-on',
    'a11y-images-off',
];

const applySettingsToDom = (value: AccessibilitySettings) => {
    if (typeof document === 'undefined') return;

    const root = document.documentElement;
    root.classList.remove(...allClasses);

    if (value.enabled) {
        root.classList.add('a11y-enabled');
        root.classList.add(`a11y-font-${value.fontSize}`);
        root.classList.add(`a11y-theme-${value.theme}`);
        root.classList.add(`a11y-spacing-${value.spacing}`);
        root.classList.add(`a11y-images-${value.images}`);
    }
};

const saveSettings = (value: AccessibilitySettings) => {
    if (typeof window === 'undefined') return;
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(value));
    } catch {
        // ignore storage errors
    }
};

const loadSettings = (): AccessibilitySettings => {
    if (typeof window === 'undefined') return { ...defaultSettings };

    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return { ...defaultSettings };

        const parsed = JSON.parse(raw) as Partial<AccessibilitySettings>;
        return {
            enabled: parsed.enabled ?? defaultSettings.enabled,
            fontSize: parsed.fontSize ?? defaultSettings.fontSize,
            theme: parsed.theme ?? defaultSettings.theme,
            spacing: parsed.spacing ?? defaultSettings.spacing,
            images: parsed.images ?? defaultSettings.images,
        };
    } catch {
        return { ...defaultSettings };
    }
};

const patchSettings = (patch: Partial<AccessibilitySettings>) => {
    settings.value = {
        ...settings.value,
        ...patch,
    };
    applySettingsToDom(settings.value);
    saveSettings(settings.value);
};

export const initializeAccessibility = () => {
    if (isInitialized.value) return;
    settings.value = loadSettings();
    applySettingsToDom(settings.value);
    isInitialized.value = true;
};

export const useAccessibility = () => {
    const isEnabled = computed(() => settings.value.enabled);

    const toggleEnabled = () => {
        patchSettings({ enabled: !settings.value.enabled });
    };

    const setEnabled = (enabled: boolean) => patchSettings({ enabled });

    const setFontSize = (fontSize: AccessibilityFontSize) =>
        patchSettings({ fontSize, enabled: true });

    const setTheme = (theme: AccessibilityTheme) =>
        patchSettings({ theme, enabled: true });

    const setSpacing = (spacing: AccessibilitySpacing) =>
        patchSettings({ spacing, enabled: true });

    const setImages = (images: AccessibilityImages) =>
        patchSettings({ images, enabled: true });

    const reset = () => {
        settings.value = { ...defaultSettings, enabled: true };
        applySettingsToDom(settings.value);
        saveSettings(settings.value);
    };

    return {
        settings,
        isEnabled,
        toggleEnabled,
        setEnabled,
        setFontSize,
        setTheme,
        setSpacing,
        setImages,
        reset,
    };
};
