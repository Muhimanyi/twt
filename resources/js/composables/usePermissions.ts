import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function usePermissions() {
    const page = usePage();
    const auth = computed(() => page.props.auth as any);

    const userPermissions = computed(() => {
        return auth.value?.permissions?.keys || [];
    });

    function can(key: string): boolean {
        return userPermissions.value.includes(key);
    }

    function canAny(keys: string[]): boolean {
        return keys.some(k => userPermissions.value.includes(k));
    }

    function canAll(keys: string[]): boolean {
        return keys.every(k => userPermissions.value.includes(k));
    }

    return { can, canAny, canAll, userPermissions };
}
