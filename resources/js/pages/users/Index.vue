<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Pencil, Trash2, Plus, User as UserIcon, Mail, Phone, Shield, ShieldCheck, UserCog, MapPin, Globe, Crown,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { toast } from 'vue-sonner';
import { index as usersRoute, store, update, destroy as destroyUser } from '@/routes/users';
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
        phone: string | null;
        photo: string | null;
        role: string[] | string | null;
        email_verified_at: string | null;
        province_id: number | null;
        province?: { id: number; name: string } | null;
        province_assignments?: Array<{
            id: number;
            province_id: number;
            province: { id: number; name: string };
            role: string;
            is_primary: boolean;
        }>;
    }>;
    provinces: Array<{
        id: number;
        name: string;
    }>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Utilisateurs', href: usersRoute().url },
        ],
    },
});

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedUser = ref<any>(null);
const expandedUsers = ref<Set<number>>(new Set());

const roleOptions = [
    { value: 'superadmin', label: __('users.roles.superadmin') },
    { value: 'admin', label: __('users.roles.admin') },
    { value: 'admin-region', label: __('users.roles.admin_region') },
    { value: 'manager', label: __('users.roles.manager') },
    { value: 'secretaire', label: __('users.roles.secretaire') },
    { value: 'user', label: __('users.roles.user') },
];

const form = ref<{
    name: string;
    email: string;
    phone: string;
    photo: File | null;
    photoPreview: string | null;
    role: string[];
    province_id: number | null;
    province_assignments: Array<{ province_id: number | null; role: string; is_primary: boolean }>;
}>({
    name: '',
    email: '',
    phone: '',
    photo: null,
    photoPreview: null,
    role: ['admin'],
    province_id: null,
    province_assignments: [{ province_id: null, role: 'user', is_primary: true }],
});

const photoInput = ref<HTMLInputElement | null>(null);

function onPhotoChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.value.photo = target.files[0];
        const reader = new FileReader();
        reader.onload = (e) => {
            form.value.photoPreview = e.target?.result as string;
        };
        reader.readAsDataURL(target.files[0]);
    }
}

function removePhoto() {
    form.value.photo = null;
    form.value.photoPreview = null;
    if (photoInput.value) {
        photoInput.value.value = '';
    }
}

const errors = ref<Record<string, string>>({});
const processing = ref(false);

const usersByProvince = computed(() => {
    const noProvinceLabel = __('permissions.without_assignment');
    const grouped: Record<string, typeof props.users> = {
        [noProvinceLabel]: [],
    };

    for (const province of props.provinces) {
        grouped[province.name] = [];
    }

    for (const user of props.users) {
        const assignments = user.province_assignments;
        if (assignments && assignments.length > 0) {
            for (const a of assignments) {
                const pName = a.province?.name || noProvinceLabel;
                if (!grouped[pName]) grouped[pName] = [];
                if (!grouped[pName].find(u => u.id === user.id)) {
                    grouped[pName].push(user);
                }
            }
        } else {
            grouped[noProvinceLabel].push(user);
        }
    }

    return Object.entries(grouped).filter(([_, users]) => users.length > 0);
});

function addAssignment() {
    form.value.province_assignments.push({ province_id: null, role: 'user', is_primary: false });
}

function removeAssignment(index: number) {
    if (form.value.province_assignments.length > 1) {
        form.value.province_assignments.splice(index, 1);
    }
}

function resetForm() {
    form.value = {
        name: '',
        email: '',
        phone: '',
        photo: null,
        photoPreview: null,
        role: ['admin'],
        province_id: null,
        province_assignments: [{ province_id: null, role: 'user', is_primary: true }],
    };
    errors.value = {};
    processing.value = false;
    if (photoInput.value) {
        photoInput.value.value = '';
    }
}

function openCreateModal() {
    resetForm();
    isCreateModalOpen.value = true;
}

function openEditModal(user: any) {
    selectedUser.value = user;

    let roles = user.role;
    if (typeof roles === 'string') {
        try { roles = JSON.parse(roles); } catch { roles = [roles]; }
    }
    if (!Array.isArray(roles)) roles = roles ? [roles] : [];

    const assignments = user.province_assignments?.length
        ? user.province_assignments.map((a: any) => ({
            province_id: a.province_id,
            role: a.role,
            is_primary: a.is_primary,
        }))
        : [{ province_id: user.province_id, role: roles[0] || 'user', is_primary: true }];

    form.value = {
        name: user.name,
        email: user.email,
        phone: user.phone || '',
        photo: null,
        photoPreview: user.photo ? '/storage/' + user.photo : null,
        role: roles,
        province_id: user.province_id,
        province_assignments: assignments,
    };
    errors.value = {};
    isEditModalOpen.value = true;
    if (photoInput.value) {
        photoInput.value.value = '';
    }
}

function openDeleteModal(user: any) {
    selectedUser.value = user;
    isDeleteModalOpen.value = true;
}

function submitCreate() {
    processing.value = true;
    const payload: Record<string, any> = {
        name: form.value.name,
        email: form.value.email,
        phone: form.value.phone,
        role: form.value.role,
        province_id: form.value.province_id,
        province_assignments: form.value.province_assignments,
    };
    if (form.value.photo) {
        payload.photo = form.value.photo;
    }
    router.post(store().url, payload, {
        headers: { 'X-CSRF-TOKEN': (window as any).csrfToken },
        onSuccess: () => {
            isCreateModalOpen.value = false;
            resetForm();
            toast.success(__('common.save'));
        },
        onError: (errs) => {
            errors.value = errs as Record<string, string>;
            processing.value = false;
        },
    });
}

function submitEdit() {
    processing.value = true;
    const payload: Record<string, any> = {
        name: form.value.name,
        email: form.value.email,
        phone: form.value.phone,
        role: form.value.role,
        province_id: form.value.province_id,
        province_assignments: form.value.province_assignments,
    };
    if (form.value.photo) {
        payload.photo = form.value.photo;
    }
    router.put(update(selectedUser.value.id).url, payload, {
        headers: { 'X-CSRF-TOKEN': (window as any).csrfToken },
        onSuccess: () => {
            isEditModalOpen.value = false;
            resetForm();
            toast.success(__('common.save'));
        },
        onError: (errs) => {
            errors.value = errs as Record<string, string>;
            processing.value = false;
        },
    });
}

function submitDelete() {
    processing.value = true;
    router.delete(destroyUser(selectedUser.value.id).url, {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
            toast.success(__('common.delete'));
        },
        onError: () => {
            processing.value = false;
        },
    });
}

function getRoleLabel(value: string) {
    return roleOptions.find(opt => opt.value === value)?.label || value;
}

function toggleRole(role: string) {
    const idx = form.value.role.indexOf(role);
    if (idx > -1) {
        form.value.role.splice(idx, 1);
    } else {
        form.value.role.push(role);
    }
}

function getUserRoles(user: any): string[] {
    if (Array.isArray(user.role)) return user.role;
    if (typeof user.role === 'string') {
        try { return JSON.parse(user.role); } catch { return [user.role]; }
    }
    return [];
}

function toggleUserExpanded(id: number) {
    if (expandedUsers.value.has(id)) {
        expandedUsers.value.delete(id);
    } else {
        expandedUsers.value.add(id);
    }
}
</script>

<template>
    <Head :title="__('users.title')" />

    <div class="space-y-8 p-1">
        <div class="flex items-end justify-between bg-white/95 text-slate-900 dark:bg-slate-900/95 dark:text-slate-100 backdrop-blur-sm p-6 rounded-3xl shadow-lg shadow-slate-200/40 dark:shadow-black/20 border border-slate-200/70 dark:border-slate-800/80">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rdc-blue/10 text-rdc-blue text-xs font-bold uppercase tracking-widest">
                    <Shield class="size-3" />
                    {{ __('users.title') }}
                </div>
                <h1 class="font-heading text-3xl font-black tracking-tight">{{ __('users.title') }}</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm max-w-md leading-relaxed">
                    {{ __('permissions.subtitle') }}
                </p>
            </div>
            <Button v-if="can('users.create')" @click="openCreateModal"
                class="h-12 px-6 rounded-xl bg-rdc-blue text-white shadow-lg shadow-rdc-blue/20 hover:bg-rdc-blue/90 transition-all font-bold gap-3">
                <Plus class="size-5" />
                {{ __('users.create') }}
            </Button>
        </div>

        <!-- Users grouped by province -->
        <div v-for="[provinceName, provinceUsers] in usersByProvince" :key="provinceName" class="space-y-3">
            <div class="flex items-center gap-3 px-1">
                <MapPin class="size-4 text-rdc-blue" />
                <h2 class="text-sm font-black uppercase tracking-[0.15em] text-slate-400 dark:text-slate-500">
                    {{ provinceName }}
                </h2>
                <span class="text-[10px] font-bold text-slate-300 dark:text-slate-600 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full">
                    {{ provinceUsers.length }}
                </span>
            </div>

            <div class="bg-white dark:bg-slate-950 rounded-3xl border border-slate-200/70 dark:border-slate-800/80 shadow-xl shadow-slate-200/50 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-900/90">
                                <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('users.name') }}</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('common.contact') }}</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('users.role') }}</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('common.status') }}</th>
                                <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="user in provinceUsers" :key="user.id"
                                class="group transition-all duration-300 hover:bg-slate-50/50 dark:hover:bg-slate-800/60">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="size-10 rounded-xl overflow-hidden bg-slate-100 flex items-center justify-center text-slate-400 shrink-0 dark:bg-slate-800">
                                            <img v-if="user.photo" :src="'/storage/' + user.photo" class="size-full object-cover" />
                                            <UserIcon v-else class="size-5" />
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                                {{ user.name }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-medium">
                                                ID: #{{ user.id }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                            <Mail class="size-3.5 opacity-60 shrink-0" />
                                            {{ user.email }}
                                        </div>
                                        <div v-if="user.phone" class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                                            <Phone class="size-3.5 opacity-60 shrink-0" />
                                            {{ user.phone }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-1.5">
                                        <template v-if="Array.isArray(user.role)">
                                            <Badge v-for="role in user.role" :key="role" variant="secondary"
                                                class="h-5 px-2 text-[9px] font-bold rounded-lg bg-slate-100 text-slate-600 border-none dark:bg-slate-800 dark:text-slate-300">
                                                {{ getRoleLabel(role) }}
                                            </Badge>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div v-if="user.email_verified_at"
                                        class="flex items-center gap-1.5 text-[10px] font-bold text-green-500">
                                        <ShieldCheck class="size-3" /> {{ __('common.yes') }}
                                    </div>
                                    <div v-else class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400">
                                        <Shield class="size-3 opacity-50" /> {{ __('common.no') }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        <Button size="icon" variant="ghost" @click="toggleUserExpanded(user.id)"
                                            class="size-9 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
                                            <MapPin class="size-4" />
                                        </Button>
                                        <Button v-if="can('users.edit')" size="icon" variant="ghost" @click="openEditModal(user)"
                                            class="size-9 rounded-xl hover:bg-rdc-yellow/10 hover:text-rdc-yellow">
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button v-if="can('users.delete')" size="icon" variant="ghost" @click="openDeleteModal(user)"
                                            class="size-9 rounded-xl hover:bg-rdc-red/10 hover:text-rdc-red">
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Province assignments detail row -->
                            <tr v-for="user in provinceUsers.filter(u => expandedUsers.has(u.id))" :key="'detail-' + user.id">
                                <td colspan="5" class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/30">
                                    <div class="flex items-center gap-4 text-xs">
                                        <Globe class="size-3.5 text-rdc-blue shrink-0" />
                                        <span class="font-bold text-slate-500 dark:text-slate-400">Affectations :</span>
                                        <template v-if="user.province_assignments?.length">
                                            <Badge v-for="a in user.province_assignments" :key="a.id"
                                                variant="secondary"
                                                class="h-5 px-2.5 text-[9px] font-bold rounded-lg bg-rdc-blue/5 text-rdc-blue border border-rdc-blue/10">
                                                {{ a.province?.name || 'N/A' }}
                                                <span class="ml-1 opacity-60">({{ getRoleLabel(a.role) }})</span>
                                            </Badge>
                                        </template>
                                        <span v-else class="text-slate-400 italic">{{ __('permissions.without_assignment') }}</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="!usersByProvince.length" class="text-center py-12 text-sm text-slate-400 italic">
            {{ __('common.no_results') }}
        </div>
    </div>

    <!-- Create Modal -->
    <Dialog v-model:open="isCreateModalOpen">
        <DialogContent class="sm:max-w-[600px] p-0 rounded-3xl overflow-hidden border-none shadow-2xl">
            <div class="p-8 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 max-h-[85vh] overflow-y-auto">
                <DialogHeader class="mb-8">
                    <div class="size-12 rounded-xl bg-rdc-blue/10 flex items-center justify-center text-rdc-blue mb-4">
                        <Plus class="size-6" />
                    </div>
                    <DialogTitle class="text-2xl font-black">{{ __('users.create') }}</DialogTitle>
                    <DialogDescription class="text-slate-500 dark:text-slate-400 leading-relaxed">
                        {{ __('users.password') }} : <code class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded font-bold text-rdc-blue">123456Place</code>
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitCreate" class="space-y-6">
                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">{{ __('users.name') }}</Label>
                        <Input v-model="form.name" placeholder="Ex: Jean Dupont" required
                            class="h-12 rounded-xl border-slate-100 bg-slate-50/70 dark:bg-slate-900/80" />
                        <div v-if="errors.name" class="text-xs text-red-500 mt-1 font-bold">{{ errors.name }}</div>
                    </div>

                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">{{ __('users.email') }}</Label>
                        <Input v-model="form.email" type="email" placeholder="email@exemple.com" required
                            class="h-12 rounded-xl border-slate-100 bg-slate-50/70 dark:bg-slate-900/80" />
                        <div v-if="errors.email" class="text-xs text-red-500 mt-1 font-bold">{{ errors.email }}</div>
                    </div>

                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">{{ __('users.phone') }}</Label>
                        <Input v-model="form.phone" type="tel" placeholder="+243 XXX XXX XXX"
                            class="h-12 rounded-xl border-slate-100 bg-slate-50/70 dark:bg-slate-900/80" />
                        <div v-if="errors.phone" class="text-xs text-red-500 mt-1 font-bold">{{ errors.phone }}</div>
                    </div>

                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">{{ __('users.photo') }}</Label>
                        <div class="flex items-center gap-4">
                            <Input ref="photoInput" type="file" accept="image/jpeg,image/png,image/webp" @change="onPhotoChange"
                                class="h-12 rounded-xl border-slate-100 bg-slate-50/70 dark:bg-slate-900/80 file:h-full file:border-0 file:bg-transparent file:text-sm file:font-bold file:text-rdc-blue" />
                            <div v-if="form.photoPreview" class="relative size-14 shrink-0 rounded-xl overflow-hidden border border-slate-200">
                                <img :src="form.photoPreview" class="size-full object-cover" />
                                <button type="button" @click="removePhoto"
                                    class="absolute -top-1.5 -right-1.5 size-5 rounded-full bg-red-500 text-white text-[10px] flex items-center justify-center font-bold shadow">×</button>
                            </div>
                        </div>
                        <div v-if="errors.photo" class="text-xs text-red-500 mt-1 font-bold">{{ errors.photo }}</div>
                    </div>

                    <!-- Global Roles -->
                    <div class="p-5 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30 space-y-4">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-2">
                            <Shield class="size-3 text-rdc-blue" /> {{ __('users.role') }}
                        </p>
                        <div class="grid grid-cols-2 gap-y-3 gap-x-4">
                            <div v-for="(role, idx) in roleOptions" :key="role.value"
                                class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-white dark:hover:bg-slate-800/50 transition-colors cursor-pointer"
                                @click="toggleRole(role.value)">
                                <input type="checkbox" :checked="form.role.includes(role.value)"
                                    class="size-5 rounded border-slate-300 text-rdc-blue focus:ring-rdc-blue cursor-pointer pointer-events-none" />
                                <label class="text-sm font-bold text-slate-600 dark:text-slate-300 cursor-pointer select-none">{{ role.label }}</label>
                            </div>
                        </div>
                    </div>

                    <!-- Province Assignments -->
                    <div class="bg-slate-50 p-6 rounded-3xl space-y-4 dark:bg-slate-900/90">
                        <div class="flex items-center justify-between">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-2">
                                <MapPin class="size-3 text-rdc-blue" /> {{ __('users.province') }}
                            </p>
                            <Button type="button" variant="outline" size="sm" @click="addAssignment"
                                class="rounded-xl text-[10px] font-bold h-8 px-3">
                                + {{ __('common.create') }}
                            </Button>
                        </div>

                        <div v-for="(assignment, idx) in form.province_assignments" :key="idx"
                            class="flex items-start gap-3 p-4 bg-white dark:bg-slate-800/50 rounded-2xl border border-slate-200/70 dark:border-slate-700/50">
                            <div class="flex-1 space-y-2">
                                <Select v-model="assignment.province_id">
                                    <SelectTrigger class="h-10 rounded-xl border-slate-100 bg-white dark:bg-slate-800">
                                        <SelectValue :placeholder="__('users.province')" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="p in provinces" :key="p.id" :value="p.id">
                                            {{ p.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="flex-1 space-y-2">
                                <Select v-model="assignment.role">
                                    <SelectTrigger class="h-10 rounded-xl border-slate-100 bg-white dark:bg-slate-800">
                                        <SelectValue :placeholder="__('users.role')" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="r in roleOptions" :key="r.value" :value="r.value">
                                            {{ r.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <Button type="button" variant="ghost" size="icon" @click="removeAssignment(idx)"
                                class="size-10 rounded-xl hover:bg-rdc-red/10 hover:text-rdc-red shrink-0 mt-0.5">
                                <Trash2 class="size-4" />
                            </Button>
                        </div>
                    </div>

                    <DialogFooter class="mt-8 gap-3">
                        <Button type="button" variant="ghost" @click="isCreateModalOpen = false"
                            class="rounded-xl font-bold text-slate-400">{{ __('common.cancel') }}</Button>
                        <Button type="submit" :disabled="processing"
                            class="h-12 px-8 rounded-xl bg-rdc-blue text-white hover:bg-rdc-blue/90 shadow-lg shadow-rdc-blue/20 font-bold">
                            {{ __('common.save') }}
                        </Button>
                    </DialogFooter>
                </form>
            </div>
        </DialogContent>
    </Dialog>

    <!-- Edit Modal -->
    <Dialog v-model:open="isEditModalOpen">
        <DialogContent class="sm:max-w-[600px] p-0 rounded-3xl overflow-hidden border-none shadow-2xl">
            <div class="p-8 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 max-h-[85vh] overflow-y-auto">
                <DialogHeader class="mb-8">
                    <div class="size-12 rounded-xl bg-rdc-yellow/10 flex items-center justify-center text-rdc-yellow mb-4">
                        <UserCog class="size-6" />
                    </div>
                    <DialogTitle class="text-2xl font-black">{{ __('users.edit') }} : {{ selectedUser?.name }}</DialogTitle>
                </DialogHeader>

                <form @submit.prevent="submitEdit" class="space-y-6">
                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">{{ __('users.name') }}</Label>
                        <Input v-model="form.name" required class="h-12 rounded-xl border-slate-100 bg-slate-50/70" />
                        <div v-if="errors.name" class="text-xs text-red-500 mt-1 font-bold">{{ errors.name }}</div>
                    </div>

                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">{{ __('users.email') }}</Label>
                        <Input v-model="form.email" type="email" required class="h-12 rounded-xl border-slate-100 bg-slate-50/70" />
                        <div v-if="errors.email" class="text-xs text-red-500 mt-1 font-bold">{{ errors.email }}</div>
                    </div>

                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">{{ __('users.phone') }}</Label>
                        <Input v-model="form.phone" type="tel" placeholder="+243 XXX XXX XXX"
                            class="h-12 rounded-xl border-slate-100 bg-slate-50/70" />
                        <div v-if="errors.phone" class="text-xs text-red-500 mt-1 font-bold">{{ errors.phone }}</div>
                    </div>

                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">{{ __('users.photo') }}</Label>
                        <div class="flex items-center gap-4">
                            <Input ref="photoInput" type="file" accept="image/jpeg,image/png,image/webp" @change="onPhotoChange"
                                class="h-12 rounded-xl border-slate-100 bg-slate-50/70 file:h-full file:border-0 file:bg-transparent file:text-sm file:font-bold file:text-rdc-blue" />
                            <div v-if="form.photoPreview" class="relative size-14 shrink-0 rounded-xl overflow-hidden border border-slate-200">
                                <img :src="form.photoPreview" class="size-full object-cover" />
                                <button type="button" @click="removePhoto"
                                    class="absolute -top-1.5 -right-1.5 size-5 rounded-full bg-red-500 text-white text-[10px] flex items-center justify-center font-bold shadow">×</button>
                            </div>
                        </div>
                        <div v-if="errors.photo" class="text-xs text-red-500 mt-1 font-bold">{{ errors.photo }}</div>
                    </div>

                    <div class="bg-slate-50 p-6 rounded-3xl space-y-4 dark:bg-slate-900/90">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-2">
                            <Shield class="size-3 text-rdc-yellow" /> {{ __('users.role') }}
                        </p>
                        <div class="grid grid-cols-2 gap-y-3 gap-x-4">
                            <div v-for="role in roleOptions" :key="role.value" class="flex items-center space-x-3">
                                <input type="checkbox" :checked="form.role.includes(role.value)"
                                    @change="toggleRole(role.value)"
                                    class="size-5 rounded border-slate-300 text-rdc-yellow focus:ring-rdc-yellow cursor-pointer" />
                                <label class="text-sm font-bold text-slate-600 dark:text-slate-300 cursor-pointer">{{ role.label }}</label>
                            </div>
                        </div>
                    </div>

                    <!-- Province Assignments -->
                    <div class="bg-slate-50 p-6 rounded-3xl space-y-4 dark:bg-slate-900/90">
                        <div class="flex items-center justify-between">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-2">
                                <MapPin class="size-3 text-rdc-yellow" /> {{ __('users.province') }}
                            </p>
                            <Button type="button" variant="outline" size="sm" @click="addAssignment"
                                    class="rounded-xl text-[10px] font-bold h-8 px-3">
                                    + {{ __('common.create') }}
                                </Button>
                        </div>

                        <div v-for="(assignment, idx) in form.province_assignments" :key="idx"
                            class="flex items-start gap-3 p-4 bg-white dark:bg-slate-800/50 rounded-2xl border border-slate-200/70 dark:border-slate-700/50">
                            <div class="flex-1 space-y-2">
                                <Select v-model="assignment.province_id">
                                    <SelectTrigger class="h-10 rounded-xl border-slate-100 bg-white dark:bg-slate-800">
                                        <SelectValue :placeholder="__('users.province')" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="p in provinces" :key="p.id" :value="p.id">
                                            {{ p.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="flex-1 space-y-2">
                                <Select v-model="assignment.role">
                                    <SelectTrigger class="h-10 rounded-xl border-slate-100 bg-white dark:bg-slate-800">
                                        <SelectValue :placeholder="__('users.role')" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="r in roleOptions" :key="r.value" :value="r.value">
                                            {{ r.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <Button type="button" variant="ghost" size="icon" @click="removeAssignment(idx)"
                                class="size-10 rounded-xl hover:bg-rdc-red/10 hover:text-rdc-red shrink-0 mt-0.5">
                                <Trash2 class="size-4" />
                            </Button>
                        </div>
                    </div>

                    <DialogFooter class="mt-8 gap-3">
                        <Button type="button" variant="ghost" @click="isEditModalOpen = false"
                            class="rounded-xl font-bold text-slate-700">Annuler</Button>
                        <Button type="submit" :disabled="processing"
                            class="h-12 px-8 rounded-xl bg-slate-900 text-white hover:bg-slate-800 shadow-lg font-bold">
                            {{ __('common.save') }}
                        </Button>
                    </DialogFooter>
                </form>
            </div>
        </DialogContent>
    </Dialog>

    <!-- Delete Modal -->
    <Dialog v-model:open="isDeleteModalOpen">
        <DialogContent class="sm:max-w-[420px] p-0 rounded-3xl overflow-hidden border-none shadow-2xl">
            <div class="p-8 bg-white dark:bg-slate-950">
                <DialogHeader class="mb-6">
                    <div class="size-12 rounded-xl bg-rdc-red/10 flex items-center justify-center text-rdc-red mb-4">
                        <Trash2 class="size-6" />
                    </div>
                    <DialogTitle class="text-xl font-black">{{ __('users.delete') }}</DialogTitle>
                    <DialogDescription class="text-slate-500 dark:text-slate-400 pt-2 leading-relaxed">
                        {{ __('common.delete') }} <span class="text-slate-900 font-black dark:text-slate-100">"{{ selectedUser?.name }}"</span> ?
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="mt-8 gap-3">
                    <Button type="button" variant="ghost" @click="isDeleteModalOpen = false"
                        class="flex-1 rounded-xl font-bold text-slate-400">Annuler</Button>
                    <Button type="button" :disabled="processing" @click="submitDelete"
                        class="flex-1 h-12 rounded-xl bg-rdc-red text-white hover:bg-rdc-red/90 shadow-lg shadow-rdc-red/20 font-bold">
                        {{ __('common.delete') }}
                    </Button>
                </DialogFooter>
            </div>
        </DialogContent>
    </Dialog>
</template>
