<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Shield, ShieldCheck, ShieldAlert, UserCog, Search, Save,
    Check, X, AlertCircle, MapPin, Globe, LockKeyhole, Crown,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { toast } from 'vue-sonner';
import { index as permissionsRoute, update as updateRoute } from '@/routes/permissions';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { usePermissions } from '@/composables/usePermissions';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();
const { can } = usePermissions();

const props = defineProps<{
    users: Array<{
        id: number;
        name: string;
        email: string;
        role: string[] | string | null;
        province: string | null;
        province_assignments?: Array<{
            province_id: number;
            province_name: string;
            role: string;
        }>;
        permission_keys: string[];
        permissions_by_province: Record<string, string[]>;
    }>;
    permissionsGrouped: Record<string, Array<{
        id: number;
        key: string;
        group: string;
        label: string;
    }>>;
    provinces: Array<{
        id: number;
        name: string;
    }>;
    roleOptions: Array<{
        value: string;
        label: string;
    }>;
}>();

const page = usePage();
const currentUser = computed(() => page.props.auth?.user);
const isSuperAdmin = computed(() => {
    const roles = currentUser.value?.role;
    if (Array.isArray(roles)) return roles.includes('superadmin');
    return false;
});

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Permissions', href: permissionsRoute().url },
        ],
    },
});

const search = ref('');
const savingUserId = ref<number | null>(null);
const selectedProvinceId = ref<'global' | number>('global');

const selectedUser = ref<{
    id: number;
    name: string;
    email: string;
    role: string[] | string | null;
    province: string | null;
    province_assignments?: Array<{
        province_id: number;
        province_name: string;
        role: string;
    }>;
    permission_keys: string[];
    permissions_by_province: Record<string, string[]>;
} | null>(null);

const editingGlobal = ref<Set<string>>(new Set());
const editingByProvince = ref<Record<string, Set<string>>>({});

const allKeys = computed(() => {
    const keys: string[] = [];
    for (const group of Object.values(props.permissionsGrouped)) {
        for (const perm of group) {
            keys.push(perm.key);
        }
    }
    return keys;
});

const filteredUsers = computed(() => {
    if (!search.value) return props.users;
    const q = search.value.toLowerCase();
    return props.users.filter(u =>
        u.name.toLowerCase().includes(q) ||
        u.email.toLowerCase().includes(q)
    );
});

// Current editing set based on selected province
const currentEditingSet = computed(() => {
    if (selectedProvinceId.value === 'global') {
        return editingGlobal.value;
    }
    const pid = String(selectedProvinceId.value);
    return editingByProvince.value[pid] || new Set<string>();
});

function getRoleLabel(value: string) {
    return props.roleOptions.find(r => r.value === value)?.label || value;
}

function getUserRoles(user: any): string[] {
    if (Array.isArray(user.role)) return user.role;
    if (typeof user.role === 'string') {
        try { return JSON.parse(user.role); } catch { return [user.role]; }
    }
    return [];
}

function isTargetSuperAdmin(user: any): boolean {
    const roles = getUserRoles(user);
    return roles.includes('superadmin');
}

function selectUser(user: any) {
    selectedUser.value = user;
    editingGlobal.value = new Set(user.permission_keys);
    editingByProvince.value = {};
    for (const a of (user.province_assignments || [])) {
        const pid = String(a.province_id);
        editingByProvince.value[pid] = new Set(user.permissions_by_province[pid] || []);
    }
    selectedProvinceId.value = 'global';
}

function deselectUser() {
    selectedUser.value = null;
    editingGlobal.value = new Set();
    editingByProvince.value = {};
    selectedProvinceId.value = 'global';
}

function togglePermission(key: string) {
    if (selectedProvinceId.value === 'global') {
        if (editingGlobal.value.has(key)) {
            editingGlobal.value.delete(key);
        } else {
            editingGlobal.value.add(key);
        }
    } else {
        const pid = String(selectedProvinceId.value);
        if (!editingByProvince.value[pid]) {
            editingByProvince.value[pid] = new Set();
        }
        if (editingByProvince.value[pid].has(key)) {
            editingByProvince.value[pid].delete(key);
        } else {
            editingByProvince.value[pid].add(key);
        }
    }
}

function isGroupSelected(groupPerms: any[]): boolean {
    return groupPerms.every(p => currentEditingSet.value.has(p.key));
}

function isGroupPartial(groupPerms: any[]): boolean {
    return groupPerms.some(p => currentEditingSet.value.has(p.key)) && !isGroupSelected(groupPerms);
}

function toggleGroup(groupPerms: any[]) {
    const allSel = isGroupSelected(groupPerms);
    if (selectedProvinceId.value === 'global') {
        for (const p of groupPerms) {
            allSel ? editingGlobal.value.delete(p.key) : editingGlobal.value.add(p.key);
        }
    } else {
        const pid = String(selectedProvinceId.value);
        if (!editingByProvince.value[pid]) editingByProvince.value[pid] = new Set();
        for (const p of groupPerms) {
            allSel ? editingByProvince.value[pid].delete(p.key) : editingByProvince.value[pid].add(p.key);
        }
    }
}

function grantAll() {
    if (selectedProvinceId.value === 'global') {
        editingGlobal.value = new Set(allKeys.value);
    } else {
        const pid = String(selectedProvinceId.value);
        editingByProvince.value[pid] = new Set(allKeys.value);
    }
}

function revokeAll() {
    if (selectedProvinceId.value === 'global') {
        editingGlobal.value = new Set();
    } else {
        const pid = String(selectedProvinceId.value);
        editingByProvince.value[pid] = new Set();
    }
}

async function savePermissions() {
    if (!selectedUser.value) return;
    savingUserId.value = selectedUser.value.id;

    const provincePermissions = (selectedUser.value.province_assignments || [])
        .filter(a => editingByProvince.value[String(a.province_id)])
        .map(a => ({
            province_id: a.province_id,
            keys: Array.from(editingByProvince.value[String(a.province_id)]),
        }));

    router.put(updateRoute(selectedUser.value.id).url, {
        permission_keys: Array.from(editingGlobal.value),
        province_permissions: provincePermissions,
    }, {
        onSuccess: () => {
            const u = props.users.find(x => x.id === selectedUser.value!.id);
            if (u) {
                u.permission_keys = Array.from(editingGlobal.value);
                for (const pp of provincePermissions) {
                    if (u.permissions_by_province) {
                        u.permissions_by_province[String(pp.province_id)] = pp.keys;
                    }
                }
            }
            toast.success(__('common.save'));
            savingUserId.value = null;
        },
        onError: (errs: any) => {
            if (typeof errs === 'string') {
                toast.error(errs);
            } else {
                toast.error(__('common.save'));
            }
            savingUserId.value = null;
        },
    });
}

const selectedAssignment = computed(() => {
    if (!selectedUser.value || selectedProvinceId.value === 'global') return null;
    return selectedUser.value.province_assignments?.find(
        a => a.province_id === selectedProvinceId.value
    );
});

const userCountByProvince = computed(() => {
    const counts: Record<string, number> = {};
    for (const u of props.users) {
        for (const a of (u.province_assignments || [])) {
            const name = a.province_name;
            counts[name] = (counts[name] || 0) + 1;
        }
    }
    const noProvince = props.users.filter(u => !u.province_assignments?.length).length;
    if (noProvince > 0) counts['Sans affectation'] = noProvince;
    return counts;
});
</script>

<template>
    <Head :title="__('permissions.title')" />

    <div class="space-y-8 p-1">
        <div class="flex items-end justify-between bg-white/95 text-slate-900 dark:bg-slate-900/95 dark:text-slate-100 backdrop-blur-sm p-6 rounded-3xl shadow-lg shadow-slate-200/40 dark:shadow-black/20 border border-slate-200/70 dark:border-slate-800/80">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-600 text-xs font-bold uppercase tracking-widest">
                    <ShieldCheck class="size-3" /> {{ __('permissions.title') }}
                </div>
                <h1 class="font-heading text-3xl font-black tracking-tight">{{ __('permissions.title') }}</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm max-w-md leading-relaxed">
                    {{ __('permissions.subtitle') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            <!-- Left: User list grouped by province -->
            <div class="xl:col-span-4 bg-white dark:bg-slate-950 rounded-3xl border border-slate-200/70 dark:border-slate-800/80 shadow-xl overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800">
                    <div class="relative">
                        <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                        <Input v-model="search" :placeholder="__('common.search')"
                            class="h-11 pl-10 rounded-xl border-slate-100 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-900/80" />
                    </div>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-[700px] overflow-y-auto">
                    <div v-for="user in filteredUsers" :key="user.id"
                        @click="selectUser(user)"
                        :class="[
                            'p-3.5 cursor-pointer transition-all duration-200 hover:bg-slate-50 dark:hover:bg-slate-900/60',
                            selectedUser?.id === user.id ? 'bg-rdc-blue/5 dark:bg-rdc-blue/10 border-l-2 border-rdc-blue' : 'border-l-2 border-transparent'
                        ]">
                        <div class="flex items-center gap-3">
                            <div class="size-9 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0"
                                :class="{ 'bg-rdc-blue/10 text-rdc-blue': selectedUser?.id === user.id }">
                                <UserCog class="size-4" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-900 dark:text-slate-100 truncate flex items-center gap-1.5">
                                    {{ user.name }}
                                    <Crown v-if="isTargetSuperAdmin(user)" class="size-3.5 text-amber-500 shrink-0" />
                                </p>
                                <p class="text-[10px] text-slate-400 truncate">{{ user.email }}</p>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <Badge v-for="a in user.province_assignments" :key="a.province_id"
                                        variant="secondary"
                                        class="h-3.5 px-1 text-[7px] font-bold rounded bg-rdc-blue/5 text-rdc-blue border border-rdc-blue/10">
                                        {{ a.province_name }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="!filteredUsers.length" class="p-8 text-center text-sm text-slate-400 italic">
                        {{ __('common.no_results') }}
                    </div>
                </div>
            </div>

            <!-- Right: Permission Editor -->
            <div class="xl:col-span-8 bg-white dark:bg-slate-950 rounded-3xl border border-slate-200/70 dark:border-slate-800/80 shadow-xl overflow-hidden">
                <template v-if="selectedUser">
                    <!-- User Header -->
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-lg font-black text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                    <UserCog class="size-5 text-rdc-blue" />
                                    {{ selectedUser.name }}
                                    <Crown v-if="isTargetSuperAdmin(selectedUser)" class="size-4 text-amber-500" />
                                </h2>
                                <p class="text-xs text-slate-400 mt-0.5">{{ selectedUser.email }}</p>
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    <Badge v-for="role in getUserRoles(selectedUser)" :key="role"
                                        variant="secondary"
                                        class="h-5 px-2 text-[9px] font-bold rounded-lg bg-purple-50 text-purple-600 border-none"
                                        :class="{ 'bg-amber-50 text-amber-600': role === 'superadmin' }">
                                        {{ getRoleLabel(role) }}
                                    </Badge>
                                </div>
                            </div>
                            <!-- Superadmin protection warning -->
                            <div v-if="isTargetSuperAdmin(selectedUser) && !isSuperAdmin"
                                class="flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold">
                                <ShieldAlert class="size-4" />
                                {{ __('permissions.superadmin_protected') }}
                            </div>
                        </div>
                    </div>

                    <!-- Province Selector + Actions -->
                    <div class="px-6 pt-5 pb-2 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-3">
                                <MapPin class="size-4 text-slate-400" />
                                <Select v-model="selectedProvinceId">
                                    <SelectTrigger class="h-10 w-56 rounded-xl border-slate-100 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-900/80">
                                        <SelectValue :placeholder="__('permissions.select_province')" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="global">
                                            <div class="flex items-center gap-2">
                                                <Globe class="size-3.5" />
                                                {{ __('permissions.global') }}
                                            </div>
                                        </SelectItem>
                                        <SelectItem v-for="a in selectedUser.province_assignments" :key="a.province_id"
                                            :value="a.province_id">
                                            <div class="flex items-center gap-2">
                                                <MapPin class="size-3.5" />
                                                {{ a.province_name }}
                                                <span class="text-[10px] text-slate-400 ml-1">({{ getRoleLabel(a.role) }})</span>
                                            </div>
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div class="flex items-center gap-2 ml-auto">
                                <span class="text-[10px] text-slate-400 font-medium">
                                    {{ selectedProvinceId === 'global' ? editingGlobal.size : (editingByProvince[String(selectedProvinceId)]?.size || 0) }} / {{ allKeys.length }}
                                </span>
                                <Button variant="outline" size="sm" @click="grantAll" v-if="can('users.manage-permissions')"
                                    class="h-8 rounded-lg text-[10px] font-bold px-3 bg-emerald-50 text-emerald-600 border-emerald-200 hover:bg-emerald-100">
                                    <Check class="size-3 mr-1" /> {{ __('permissions.grant_all') }}
                                </Button>
                                <Button variant="outline" size="sm" @click="revokeAll" v-if="can('users.manage-permissions')"
                                    class="h-8 rounded-lg text-[10px] font-bold px-3 bg-slate-50 text-slate-400 border-slate-200 hover:bg-slate-100">
                                    <X class="size-3 mr-1" /> {{ __('permissions.revoke_all') }}
                                </Button>
                            </div>
                        </div>

                        <!-- Province context info -->
                        <div v-if="selectedAssignment" class="flex items-center gap-2 mt-3 text-[11px] text-slate-400">
                            <Shield class="size-3" />
                            {{ __('users.role') }} : <span class="font-bold text-slate-600 dark:text-slate-300">{{ getRoleLabel(selectedAssignment.role) }}</span>
                            <span class="text-slate-300 mx-1">·</span>
                            Les permissions du rôle s'appliquent par défaut. Vous pouvez ajouter des permissions spécifiques ci-dessous.
                        </div>
                    </div>

                    <!-- Permissions Grid -->
                    <div class="p-6 max-h-[600px] overflow-y-auto">
                        <div v-for="(perms, group) in permissionsGrouped" :key="group" class="mb-5 last:mb-0">
                            <div class="flex items-center gap-3 mb-2.5">
                                <button @click="toggleGroup(perms)"
                                    class="flex items-center gap-2 text-[11px] font-black uppercase tracking-[0.12em] text-slate-400 hover:text-rdc-blue transition-colors">
                                    <div class="size-4.5 rounded flex items-center justify-center"
                                        :class="isGroupSelected(perms)
                                            ? 'bg-rdc-blue/10 text-rdc-blue'
                                            : isGroupPartial(perms)
                                                ? 'bg-amber-50 text-amber-500'
                                                : 'bg-slate-50 dark:bg-slate-800 text-slate-300'">
                                        <Check v-if="isGroupSelected(perms)" class="size-3" />
                                        <AlertCircle v-else-if="isGroupPartial(perms)" class="size-3" />
                                        <X v-else class="size-3" />
                                    </div>
                                    {{ group }}
                                </button>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5">
                                <div v-for="perm in perms" :key="perm.id"
                                    @click="isTargetSuperAdmin(selectedUser) && !isSuperAdmin ? null : togglePermission(perm.key)"
                                    :class="[
                                        'flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl cursor-pointer transition-all border text-[11px] font-medium',
                                        currentEditingSet.has(perm.key)
                                            ? 'bg-rdc-blue/5 border-rdc-blue/20 text-rdc-blue dark:bg-rdc-blue/10 dark:border-rdc-blue/30'
                                            : 'bg-slate-50/50 border-transparent text-slate-500 hover:bg-slate-100 dark:bg-slate-900/50 dark:hover:bg-slate-800/60',
                                        isTargetSuperAdmin(selectedUser) && !isSuperAdmin ? 'opacity-50 cursor-not-allowed' : ''
                                    ]">
                                    <div class="size-4.5 rounded flex items-center justify-center shrink-0 transition-all"
                                        :class="currentEditingSet.has(perm.key)
                                            ? 'bg-rdc-blue text-white shadow-sm'
                                            : 'bg-slate-200 dark:bg-slate-700'">
                                        <Check v-if="currentEditingSet.has(perm.key)" class="size-3" />
                                    </div>
                                    {{ perm.label }}
                                </div>
                            </div>
                        </div>

                        <!-- Superadmin read-only notice -->
                        <div v-if="isTargetSuperAdmin(selectedUser) && !isSuperAdmin"
                            class="mt-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 flex items-center gap-3 text-amber-700 text-sm">
                            <ShieldAlert class="size-5 shrink-0" />
                            <span class="font-semibold">{{ __('permissions.superadmin_protected') }}.</span>
                            <span class="text-amber-600">{{ __('permissions.superadmin_protected') }}.</span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="p-5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/30">
                        <div class="flex items-center gap-3 text-[10px] text-slate-400">
                            <span>Global: <strong class="text-slate-600 dark:text-slate-300">{{ editingGlobal.size }}</strong></span>
                            <span v-for="a in selectedUser.province_assignments" :key="a.province_id" class="flex items-center gap-1">
                                <MapPin class="size-3" />
                                {{ a.province_name }}:
                                <strong class="text-slate-600 dark:text-slate-300">{{ editingByProvince[String(a.province_id)]?.size || 0 }}</strong>
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <Button variant="ghost" @click="deselectUser"
                                class="rounded-xl font-bold text-slate-400">
                                {{ __('common.cancel') }}
                            </Button>
                            <Button @click="savePermissions" v-if="can('users.manage-permissions')"
                                :disabled="savingUserId !== null || (isTargetSuperAdmin(selectedUser) && !isSuperAdmin)"
                                class="h-11 px-6 rounded-xl bg-rdc-blue text-white hover:bg-rdc-blue/90 shadow-lg shadow-rdc-blue/20 font-bold gap-2"
                                :class="{ 'opacity-50': isTargetSuperAdmin(selectedUser) && !isSuperAdmin }">
                                <Save class="size-4" />
                                {{ savingUserId === selectedUser.id ? __('common.loading') : __('permissions.save') }}
                            </Button>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <template v-else>
                    <div class="flex flex-col items-center justify-center py-24 px-8 text-center">
                        <div class="size-16 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center mb-5">
                            <LockKeyhole class="size-8 text-slate-300 dark:text-slate-600" />
                        </div>
                        <h3 class="text-lg font-black text-slate-400 dark:text-slate-500 mb-2">
                            {{ __('permissions.select_user') }}
                        </h3>
                        <p class="text-sm text-slate-400 dark:text-slate-500 max-w-sm">
                            {{ __('permissions.subtitle') }}
                        </p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
