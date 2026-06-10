import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useTrans() {
    const page = usePage();
    const translations = computed<Record<string, string>>(() =>
        (page.props as any).translations || {}
    );

    function __(key: string, replace?: Record<string, string>): string {
        let text = translations.value[key];
        if (text === undefined) {
            text = key;
        }
        if (replace) {
            for (const [k, v] of Object.entries(replace)) {
                text = text.replace(`:${k}`, v);
            }
        }
        return text;
    }

    return { __, translations };
}
