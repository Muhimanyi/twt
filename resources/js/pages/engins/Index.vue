<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import {
    Plus, Search, Car, Bike, Truck,
    Trash2, Pencil, Globe, FileText,
    MapPin, Users, Info, Settings,
    Anchor, Ship, UserCircle, Map, Filter
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import {
    Dialog, DialogContent, DialogDescription,
    DialogFooter, DialogHeader, DialogTitle
} from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import OwnerForm from '@/components/engins/OwnerForm.vue';
import TerrestrialForm from '@/components/engins/TerrestrialForm.vue';
import { usePermissions } from '@/composables/usePermissions';
import { useTrans } from '@/composables/useTrans';

const { can } = usePermissions();
const { __ } = useTrans();
import MaritimeForm from '@/components/engins/MaritimeForm.vue';

const props = defineProps<{
    engins: Array<any>;
    provinces: Array<any>;
    currentCategory: string | null;
    currentProvinceId: string | null;
    stats: {
        total: number;
        by_category: Record<string, number>;
        by_province: Record<number, number>;
    };
}>();

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedEngin = ref<any>(null);
const searchQuery = ref('');

const filters = ref({
    category: '' as string,
    construction_type: '' as string,
    sub_category: '' as string,
});

watch(() => props.currentCategory, (newCategory) => {
    if (!searchQuery.value) {
        filters.value.category = newCategory || '';
    }
}, { immediate: true });

const handleCategoryFilter = (value: string) => {
    if (value) {
        router.visit(`/engins/category/${value}`, { preserveState: true });
    } else {
        router.visit('/engins', { preserveState: true });
    }
};

const resetFilters = () => {
    searchQuery.value = '';
    filters.value = { category: '', construction_type: '', sub_category: '' };
};

const categories = [
    { value: 'roulant', label: __('engins.category.roulant'), icon: Car },
    { value: 'flottant', label: __('engins.category.flottant'), icon: Anchor },
];

const subCategories = [
    { value: 'avec moteur', label: __('engins.filter.with_engine') },
    { value: 'sans moteur', label: __('engins.filter.without_engine') },
];

const form = useForm({
    category: 'roulant',
    sub_category: 'avec moteur',
    construction_type: '',
    province_id: '' as any,
    // Owner Info
    owner_name: '',
    owner_gender: 'M',
    owner_birth_place: '',
    owner_father_name: '',
    owner_mother_name: '',
    owner_marital_status: '',
    owner_nationality: 'Congolaise',
    owner_phone: '',
    owner_email: '',
    owner_address: '',
    owner_photo: null as any,
    // Vehicle Info (Common/Terrestrial)
    vehicle_genre: '',
    vehicle_brand: '',
    vehicle_type: '',
    vehicle_color: '',
    plate_number: '',
    engine_number: '',
    engine_brand: '',
    chassis_number: '',
    manufacture_year: null as any,
    horsepower: null as any,
    // Maritime Specific
    vessel_name: '',
    registration_number: '',
    registration_place: '',
    registration_date: '',
    capacity: '',
    navigation_line: '',
    height: null as any,
    length: null as any,
    width: null as any,
    propulsion_type: '',
    usage: [] as string[],
    crew_count: null as any,
    driver_count: null as any,
    // Admin
    identification_place: '',
    identification_date: new Date().toISOString().split('T')[0],
});

const filteredEngins = computed(() => {
    let results = [...props.engins];

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase().trim();
        results = results.filter(e =>
            (e.owner_name || '').toLowerCase().includes(query) ||
            (e.identification_number || '').toLowerCase().includes(query) ||
            (e.plate_number || '').toLowerCase().includes(query) ||
            (e.vessel_name || '').toLowerCase().includes(query) ||
            (e.registration_number || '').toLowerCase().includes(query) ||
            (e.vehicle_brand || '').toLowerCase().includes(query) ||
            (e.vehicle_genre || '').toLowerCase().includes(query) ||
            (e.vehicle_type || '').toLowerCase().includes(query) ||
            (e.construction_type || '').toLowerCase().includes(query)
        );
    }

    if (filters.value.category) {
        results = results.filter(e => e.category === filters.value.category);
    }

    if (filters.value.sub_category) {
        results = results.filter(e => e.sub_category === filters.value.sub_category);
    }

    if (filters.value.construction_type) {
        const material = filters.value.construction_type.toLowerCase();
        results = results.filter(e => {
            const type = (e.construction_type || '').toLowerCase();
            return type.includes(material);
        });
    }

    return results;
});

const groupedEngins = computed(() => {
    const isFlottant = props.currentCategory === 'flottant';
    if (!isFlottant) {
        return { all: filteredEngins.value };
    }

    const groups: Record<string, { avec_moteur: any[]; sans_moteur: any[] }> = {
        acier: { avec_moteur: [], sans_moteur: [] },
        bois: { avec_moteur: [], sans_moteur: [] },
        autre: { avec_moteur: [], sans_moteur: [] }
    };

    filteredEngins.value.forEach(engin => {
        const type = (engin.construction_type || '').toLowerCase();
        const isAvecMoteur = engin.sub_category === 'avec moteur';
        let groupKey = 'autre';

        if (type.includes('acier') || type.includes('metal') || type.includes('fer')) {
            groupKey = 'acier';
        } else if (type.includes('bois') || type.includes('wood')) {
            groupKey = 'bois';
        }

        if (isAvecMoteur) {
            groups[groupKey].avec_moteur.push(engin);
        } else {
            groups[groupKey].sans_moteur.push(engin);
        }
    });

    return groups;
});

const submitCreate = () => {
    form.post('/engins', {
        onSuccess: () => {
            isCreateModalOpen.value = false;
            form.reset();
        },
    });
};

const openCreateModal = () => {
    form.reset();
    isCreateModalOpen.value = true;
};

const openEditModal = (engin: any) => {
    selectedEngin.value = engin;
    form.clearErrors();
    form.category = engin.category;
    form.sub_category = engin.sub_category;
    form.construction_type = engin.construction_type || '';
    form.province_id = String(engin.province_id);
    form.owner_name = engin.owner_name;
    form.owner_gender = engin.owner_gender;
    form.owner_birth_place = engin.owner_birth_place;
    form.owner_father_name = engin.owner_father_name;
    form.owner_mother_name = engin.owner_mother_name;
    form.owner_marital_status = engin.owner_marital_status;
    form.owner_nationality = engin.owner_nationality;
    form.owner_phone = engin.owner_phone;
    form.owner_email = engin.owner_email;
    form.owner_address = engin.owner_address;
    form.owner_photo = null;
    form.vehicle_genre = engin.vehicle_genre;
    form.vehicle_brand = engin.vehicle_brand;
    form.vehicle_type = engin.vehicle_type;
    form.vehicle_color = engin.vehicle_color;
    form.plate_number = engin.plate_number;
    form.engine_number = engin.engine_number;
    form.engine_brand = engin.engine_brand || '';
    form.chassis_number = engin.chassis_number;
    form.manufacture_year = engin.manufacture_year;
    form.horsepower = engin.horsepower;
    // Maritime fields
    form.vessel_name = engin.vessel_name || '';
    form.registration_number = engin.registration_number || '';
    form.registration_place = engin.registration_place || '';
    form.registration_date = engin.registration_date ? engin.registration_date.split('T')[0] : '';
    form.capacity = engin.capacity || '';
    form.navigation_line = engin.navigation_line || '';
    form.height = engin.height;
    form.length = engin.length;
    form.width = engin.width;
    form.propulsion_type = engin.propulsion_type || '';
    form.usage = engin.usage || [];
    form.crew_count = engin.crew_count;
    form.driver_count = engin.driver_count;

    form.identification_place = engin.identification_place;
    form.identification_date = engin.identification_date.split('T')[0];

    isEditModalOpen.value = true;
};

const submitEdit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(`/engins/${selectedEngin.value.id}`, {
        forceFormData: true,
        onSuccess: () => {
            isEditModalOpen.value = false;
            form.reset();
        },
    });
};

const openDeleteModal = (engin: any) => {
    selectedEngin.value = engin;
    isDeleteModalOpen.value = true;
};

const submitDelete = () => {
    router.delete(`/engins/${selectedEngin.value.id}`, {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
};

const filterByProvince = (id: any) => {
    router.visit('/engins', {
        data: {
            province_id: id,
            category: props.currentCategory
        },
        preserveState: true
    });
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Recensement', href: '#' },
            { title: 'Engins', href: '/engins' },
        ],
    },
});
</script>

<template>

        <Head :title="__('engins.page_title')" />

        <div class="space-y-8 p-6">
            <!-- Header Section -->
            <div
                class="flex items-end justify-between bg-white/95 text-slate-900 dark:bg-slate-900/95 dark:text-slate-100 backdrop-blur-sm p-6 rounded-xl shadow-lg shadow-slate-200/40 dark:shadow-black/20 border border-slate-200/70 dark:border-slate-800/80">
                <div class="space-y-1">
                    <div class="flex items-center gap-3 text-rdc-blue mb-1">
                        <Settings class="size-5" />
                        <span class="text-[10px] font-black uppercase tracking-[0.3em]">{{ __('common.transport_module') }}</span>
                    </div>
                    <h1 class="text-4xl font-black tracking-tighter ">{{ __('engins.identification') }} <span class="text-rdc-red">{{ __('engins.of_engines') }}</span></h1>
                    <p class="text-sm text-slate-400 font-medium tracking-tight">{{ __('engins.page_subtitle') }}</p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="relative w-64">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                        <Input v-model="searchQuery" :placeholder="__('common.search')"
                            class="pl-10 h-12 rounded-xl bg-slate-50 border-none dark:bg-slate-900" />
                    </div>
                    <Button v-if="can('engins.create')" @click="openCreateModal"
                        class="h-12 px-6 rounded-xl bg-rdc-blue text-white shadow-lg shadow-rdc-blue/20 hover:bg-rdc-blue/90 transition-all font-bold gap-3">
                        <Plus class="size-5" />
                        {{ __('engins.create') }}
                    </Button>
                </div>
            </div>

            <!-- Filters -->
            <div
                class="flex flex-wrap items-center gap-4 p-4 bg-white dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center gap-2 text-slate-400">
                    <Filter class="size-4" />
                    <span class="text-xs font-black uppercase tracking-wider">{{ __('engins.filters_label') }}:</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <select v-model="filters.category"
                        class="h-9 w-40 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-xs font-medium text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-rdc-blue">
                        <option value="">{{ __('engins.filter.all_types') }}</option>
                        <option value="roulant">{{ __('engins.category.roulant') }}</option>
                        <option value="flottant">{{ __('engins.category.flottant') }}</option>
                    </select>
                    <select v-model="filters.construction_type"
                        class="h-9 w-44 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-xs font-medium text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-rdc-blue">
                        <option value="">{{ __('engins.filter.all_materials') }}</option>
                        <option value="acier">{{ __('engins.filter.steel_metal') }}</option>
                        <option value="bois">{{ __('engins.filter.wood') }}</option>
                    </select>
                    <select v-model="filters.sub_category"
                        class="h-9 w-44 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-xs font-medium text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-rdc-blue">
                        <option value="">{{ __('engins.filter.all_motorizations') }}</option>
                        <option value="avec moteur">{{ __('engins.filter.with_engine') }}</option>
                        <option value="sans moteur">{{ __('engins.filter.without_engine') }}</option>
                    </select>
                    <Button variant="ghost" size="sm" @click="resetFilters"
                        class="h-9 px-3 text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                        {{ __('common.reset') }}
                    </Button>
                </div>
                <div class="ml-auto text-xs font-medium text-slate-400">
                    {{ filteredEngins.length }} {{ __('engins.result') }}{{ filteredEngins.length !== 1 ? 's' : '' }}
                </div>
            </div>

            <!-- Category Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div @click="router.visit('/engins')"
                    class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:bg-slate-950 dark:border-slate-800 cursor-pointer hover:border-rdc-blue transition-all"
                    :class="{ 'ring-2 ring-rdc-blue/50 bg-rdc-blue/5': !currentCategory }">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="size-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 dark:bg-slate-900 dark:text-slate-400">
                            <Users class="size-5" />
                        </div>
                        <Badge variant="outline" class="text-[10px] font-black uppercase tracking-widest">{{ __('common.total') }}</Badge>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('engins.all') }}</p>
                    <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ stats.total }}</p>
                </div>

                <div v-for="cat in categories" :key="cat.value" @click="router.visit(`/engins/category/${cat.value}`)"
                    class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:bg-slate-950 dark:border-slate-800 cursor-pointer hover:border-rdc-blue transition-all"
                    :class="{ 'ring-2 ring-rdc-blue/50 bg-rdc-blue/5': currentCategory === cat.value }">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="size-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 dark:bg-slate-900 dark:text-slate-400">
                            <component :is="cat.icon" class="size-5" />
                        </div>
                        <Badge variant="outline" class="text-[10px] font-black uppercase tracking-widest">{{ __('common.type') }}</Badge>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ cat.label }}</p>
                    <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ stats.by_category[cat.value] ||
                        0
                        }}</p>
                </div>
            </div>

            <!-- Province Filters -->
            <div class="flex flex-wrap gap-2 pb-2 overflow-x-auto no-scrollbar">
                <Button variant="ghost" @click="filterByProvince(null)"
                    class="rounded-full px-6 h-10 text-xs font-black uppercase tracking-tighter  border"
                    :class="!currentProvinceId ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-400 border-slate-100'">
                    {{ __('engins.all_provinces') }}
                </Button>
                <Button v-for="prov in provinces" :key="prov.id" variant="ghost" @click="filterByProvince(prov.id)"
                    class="rounded-full px-6 h-10 text-xs font-black uppercase tracking-tighter  border gap-3 transition-all"
                    :class="currentProvinceId == prov.id ? 'bg-rdc-blue text-white border-rdc-blue shadow-lg shadow-rdc-blue/20' : 'bg-white text-slate-400 border-slate-100 hover:border-rdc-blue/30'">
                    {{ prov.name }}
                    <Badge v-if="stats.by_province[prov.id]" variant="secondary"
                        class="h-5 min-w-5 px-1.5 flex items-center justify-center rounded-full text-[10px] font-black bg-white/20 text-white border-none">
                        {{ stats.by_province[prov.id] }}
                    </Badge>
                </Button>
            </div>

            <!-- List View -->
            <div
                class="bg-white shadow-xl shadow-slate-200/50 overflow-hidden rounded-xl border border-slate-200/70 dark:bg-slate-950 dark:border-slate-800/80">
                <div class="overflow-x-auto" v-if="currentCategory === 'flottant'">
                    <template v-for="(engineGroups, groupKey) in groupedEngins" :key="groupKey">
                        <div v-if="engineGroups.avec_moteur.length > 0 || engineGroups.sans_moteur.length > 0"
                            class="relative border-b border-slate-100 dark:border-slate-800 last:border-b-0">
                            <div
                                class="sticky top-0 z-10 bg-slate-100/90 dark:bg-slate-900/90 backdrop-blur-sm px-6 py-3 flex items-center gap-3 border-b border-slate-200/50 dark:border-slate-700/50">
                                <Ship class="size-5 text-rdc-blue" />
                                <span class="text-xs font-black uppercase tracking-[0.2em] text-rdc-blue">
                                    {{ groupKey === 'acier' ? __('engins.steel_crafts') : groupKey === 'bois' ?
                                    __('engins.wood_crafts') : __('engins.other_crafts') }}
                                </span>
                                <Badge variant="secondary" class="text-[10px] font-black">{{
                                    engineGroups.avec_moteur.length + engineGroups.sans_moteur.length }}</Badge>
                            </div>

                            <!-- Avec Moteur -->
                            <div v-if="engineGroups.avec_moteur.length > 0"
                                class="bg-emerald-50/50 dark:bg-emerald-950/20">
                                <div
                                    class="px-6 py-2 flex items-center gap-2 border-b border-slate-100 dark:border-slate-800">
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">{{ __('engins.filter.with_engine') }}</span>
                                    <Badge variant="outline"
                                        class="text-[9px] h-5 bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800">
                                        {{ engineGroups.avec_moteur.length }}</Badge>
                                </div>
                                <table class="w-full border-collapse">
                                    <thead>
                                        <tr
                                            class="bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800">
                                            <th
                                                class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                                {{ __('engins.table.id') }}</th>
                                            <th
                                                class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                                {{ __('engins.table.owner') }}</th>
                                            <th
                                                class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                                {{ __('engins.table.details') }}</th>
                                            <th
                                                class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                                {{ __('engins.table.province') }}</th>
                                            <th
                                                class="px-6 py-3 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                                {{ __('common.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        <tr v-for="engin in engineGroups.avec_moteur" :key="engin.id"
                                            class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/60 transition-all">
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-xs font-black text-rdc-blue font-mono tracking-tighter">{{
                                                            engin.identification_number }}</span>
                                                    <span class="text-[9px] text-slate-400 font-bold uppercase mt-0.5">
                                                        {{ engin.registration_number || __('engins.without_registration') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="size-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 dark:bg-slate-800 overflow-hidden">
                                                        <img v-if="engin.owner_photo_url" :src="engin.owner_photo_url"
                                                            class="w-full h-full object-cover" />
                                                        <UserCircle v-else class="size-5" />
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="text-sm font-bold text-slate-900 dark:text-slate-100">{{
                                                                engin.owner_name }}</span>
                                                        <span class="text-[10px] text-slate-400 font-medium">{{
                                                            engin.owner_phone
                                                            }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col gap-1">
                                                    <div class="flex items-center gap-2">
                                                        <span
                                                            class="text-xs font-black text-slate-700 dark:text-slate-300">
                                                            {{ engin.vessel_name || engin.vehicle_genre }}
                                                        </span>
                                                        <Badge variant="outline"
                                                            class="text-[8px] h-4 uppercase font-bold">{{
                                                                engin.sub_category }}</Badge>
                                                    </div>
                                                    <span class="text-[10px] text-slate-400">
                                                        {{ engin.construction_type || __('engins.undefined_type') }} • {{
                                                        engin.capacity || __('common.na') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="size-8 rounded-lg bg-rdc-blue/5 flex items-center justify-center text-rdc-blue">
                                                        <MapPin class="size-4" />
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="text-[10px] font-black uppercase text-slate-900 dark:text-slate-100 ">{{
                                                                engin.province?.name || __('common.na') }}</span>
                                                        <span
                                                            class="text-[9px] text-slate-400 font-bold tracking-tight uppercase">{{
                                                                engin.identification_place }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end gap-2">
                                                    <Button size="icon" variant="ghost" as-child
                                                        class="size-8 rounded-lg hover:bg-slate-100">
                                                        <Link :href="`/engins/${engin.id}`">
                                                            <Globe class="size-4" />
                                                        </Link>
                                                    </Button>
                                                    <Button v-if="can('engins.edit')" size="icon" variant="ghost" @click="openEditModal(engin)"
                                                        class="size-8 rounded-lg hover:bg-rdc-blue/10 hover:text-rdc-blue">
                                                        <Pencil class="size-4" />
                                                    </Button>
                                                    <Button v-if="can('engins.delete')" size="icon" variant="ghost" @click="openDeleteModal(engin)"
                                                        class="size-8 rounded-lg hover:bg-rdc-red/10 hover:text-rdc-red">
                                                        <Trash2 class="size-4" />
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Sans Moteur -->
                            <div v-if="engineGroups.sans_moteur.length > 0" class="bg-amber-50/50 dark:bg-amber-950/20">
                                <div
                                    class="px-6 py-2 flex items-center gap-2 border-b border-slate-100 dark:border-slate-800">
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">{{ __('engins.filter.without_engine') }}</span>
                                    <Badge variant="outline"
                                        class="text-[9px] h-5 bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300 border-amber-200 dark:border-amber-800">
                                        {{ engineGroups.sans_moteur.length }}</Badge>
                                </div>
                                <table class="w-full border-collapse">
                                    <thead>
                                        <tr
                                            class="bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800">
                                            <th
                                                class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                                {{ __('engins.table.id') }}</th>
                                            <th
                                                class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                                {{ __('engins.table.owner') }}</th>
                                            <th
                                                class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                                {{ __('engins.table.details') }}</th>
                                            <th
                                                class="px-6 py-3 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                                {{ __('engins.table.province') }}</th>
                                            <th
                                                class="px-6 py-3 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                                {{ __('common.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        <tr v-for="engin in engineGroups.sans_moteur" :key="engin.id"
                                            class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/60 transition-all">
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-xs font-black text-rdc-blue font-mono tracking-tighter">{{
                                                            engin.identification_number }}</span>
                                                    <span class="text-[9px] text-slate-400 font-bold uppercase mt-0.5">
                                                        {{ engin.registration_number || __('engins.without_registration') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="size-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 dark:bg-slate-800 overflow-hidden">
                                                        <img v-if="engin.owner_photo_url" :src="engin.owner_photo_url"
                                                            class="w-full h-full object-cover" />
                                                        <UserCircle v-else class="size-5" />
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="text-sm font-bold text-slate-900 dark:text-slate-100">{{
                                                                engin.owner_name }}</span>
                                                        <span class="text-[10px] text-slate-400 font-medium">{{
                                                            engin.owner_phone
                                                            }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col gap-1">
                                                    <div class="flex items-center gap-2">
                                                        <span
                                                            class="text-xs font-black text-slate-700 dark:text-slate-300">
                                                            {{ engin.vessel_name || engin.vehicle_genre }}
                                                        </span>
                                                        <Badge variant="outline"
                                                            class="text-[8px] h-4 uppercase font-bold">{{
                                                                engin.sub_category }}</Badge>
                                                    </div>
                                                    <span class="text-[10px] text-slate-400">
                                                        {{ engin.construction_type || __('engins.undefined_type') }} • {{
                                                        engin.capacity || __('common.na') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="size-8 rounded-lg bg-rdc-blue/5 flex items-center justify-center text-rdc-blue">
                                                        <MapPin class="size-4" />
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="text-[10px] font-black uppercase text-slate-900 dark:text-slate-100 ">{{
                                                                engin.province?.name || __('common.na') }}</span>
                                                        <span
                                                            class="text-[9px] text-slate-400 font-bold tracking-tight uppercase">{{
                                                                engin.identification_place }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end gap-2">
                                                    <Button size="icon" variant="ghost" as-child
                                                        class="size-8 rounded-lg hover:bg-slate-100">
                                                        <Link :href="`/engins/${engin.id}`">
                                                            <Globe class="size-4" />
                                                        </Link>
                                                    </Button>
                                                    <Button v-if="can('engins.edit')" size="icon" variant="ghost" @click="openEditModal(engin)"
                                                        class="size-8 rounded-lg hover:bg-rdc-blue/10 hover:text-rdc-blue">
                                                        <Pencil class="size-4" />
                                                    </Button>
                                                    <Button v-if="can('engins.delete')" size="icon" variant="ghost" @click="openDeleteModal(engin)"
                                                        class="size-8 rounded-lg hover:bg-rdc-red/10 hover:text-rdc-red">
                                                        <Trash2 class="size-4" />
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="overflow-x-auto" v-else>
                    <table class="w-full border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-50/80 dark:bg-slate-900/90 border-b border-slate-100 dark:border-slate-800">
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                    {{ __('engins.table.id') }}</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                    {{ __('engins.table.owner') }}</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                    {{ __('engins.table.details') }}</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                    {{ __('engins.table.province') }}</th>
                                <th
                                    class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                    {{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="engin in filteredEngins" :key="engin.id"
                                class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/60 transition-all">
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-rdc-blue font-mono tracking-tighter">{{
                                            engin.identification_number }}</span>
                                        <span class="text-[9px] text-slate-400 font-bold uppercase mt-0.5">
                                            {{ engin.category === 'roulant' ? (engin.plate_number || __('engins.without_plate')) :
                                                (engin.registration_number || __('engins.without_registration')) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="size-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 dark:bg-slate-800 overflow-hidden">
                                            <img v-if="engin.owner_photo_url" :src="engin.owner_photo_url"
                                                class="w-full h-full object-cover" />
                                            <UserCircle v-else class="size-5" />
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{
                                                engin.owner_name }}</span>
                                            <span class="text-[10px] text-slate-400 font-medium">{{ engin.owner_phone
                                                }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-black text-slate-700 dark:text-slate-300">
                                                {{ engin.category === 'roulant' ? `${engin.vehicle_brand}
                                                ${engin.vehicle_type}` : (engin.vessel_name || engin.vehicle_genre) }}
                                            </span>
                                            <Badge variant="outline" class="text-[8px] h-4 uppercase font-bold">{{
                                                engin.sub_category }}</Badge>
                                        </div>
                                        <span class="text-[10px] text-slate-400">
                                            {{ engin.category === 'roulant' ? engin.vehicle_genre : `${engin.capacity ||
                                                __('common.na')} • ${engin.navigation_line || __('engins.without_line')}` }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="size-8 rounded-lg bg-rdc-blue/5 flex items-center justify-center text-rdc-blue">
                                            <MapPin class="size-4" />
                                        </div>
                                        <div class="flex flex-col">
                                            <span
                                                class="text-[10px] font-black uppercase text-slate-900 dark:text-slate-100 ">{{
                                                    engin.province?.name || __('common.na') }}</span>
                                            <span
                                                class="text-[9px] text-slate-400 font-bold tracking-tight uppercase">{{
                                                    engin.identification_place }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button size="icon" variant="ghost" as-child
                                            class="size-8 rounded-lg hover:bg-slate-100">
                                            <Link :href="`/engins/${engin.id}`">
                                                <Globe class="size-4" />
                                            </Link>
                                        </Button>
                                        <Button size="icon" variant="ghost" @click="openEditModal(engin)"
                                            class="size-8 rounded-lg hover:bg-rdc-blue/10 hover:text-rdc-blue">
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button size="icon" variant="ghost" @click="openDeleteModal(engin)"
                                            class="size-8 rounded-lg hover:bg-rdc-red/10 hover:text-rdc-red">
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Engin Modal -->
        <Dialog v-model:open="isCreateModalOpen">
            <DialogContent class="max-w-4xl p-0 rounded-lg overflow-hidden border-none shadow-2xl max-h-[90vh]">
                <div class="bg-white dark:bg-slate-50 h-full overflow-y-auto custom-scrollbar">
                    <div class="p-6 border-b-2 border-blue-900">
                        <h2 class="text-lg font-bold uppercase text-center text-blue-900">{{ __('engins.create.header_republic') }}</h2>
                        <p class="text-sm text-center font-normal">{{ __('engins.create.header_ministry') }}</p>
                        <h3 class="text-base font-bold text-center text-blue-900 mt-4 uppercase">{{ __('engins.create.header_identification_form') }}</h3>
                    </div>

                    <form @submit.prevent="submitCreate" class="p-6 space-y-6">
                        <!-- Catégorie et Province -->
                        <div class="grid grid-cols-3 gap-4">
                            <div class="form-group">
                                <label class="text-xs font-bold">{{ __('common.province') }}</label>
                                <select v-model="form.province_id" class="form-input">
                                    <option value="">{{ __('common.select') }}...</option>
                                    <option v-for="prov in provinces" :key="prov.id" :value="String(prov.id)">{{
                                        prov.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="text-xs font-bold">{{ __('engins.form.category') }}</label>
                                <select v-model="form.category" class="form-input">
                                    <option v-for="cat in categories" :key="cat.value" :value="cat.value">{{ cat.label
                                        }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="text-xs font-bold">{{ __('engins.form.propulsion') }}</label>
                                <select v-model="form.sub_category" class="form-input">
                                    <option v-for="sub in subCategories" :key="sub.value" :value="sub.value">{{
                                        sub.label }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Section Armateur -->
                        <fieldset class="form-fieldset">
                            <legend class="form-legend">{{ __('engins.form.owner_section') }}</legend>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('engins.form.full_name') }}</label>
                                    <input v-model="form.owner_name" type="text" class="form-input" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('engins.form.gender') }}</label>
                                    <select v-model="form.owner_gender" class="form-input">
                                        <option value="M">{{ __('engins.form.masculine') }}</option>
                                        <option value="F">{{ __('engins.form.feminine') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('engins.form.birth_place') }}</label>
                                    <input v-model="form.owner_birth_place" type="text" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('engins.form.father_name') }}</label>
                                    <input v-model="form.owner_father_name" type="text" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('engins.form.mother_name') }}</label>
                                    <input v-model="form.owner_mother_name" type="text" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('engins.form.marital_status') }}</label>
                                    <input v-model="form.owner_marital_status" type="text" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('engins.form.nationality') }}</label>
                                    <input v-model="form.owner_nationality" type="text" class="form-input"
                                        value="Congolaise">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('engins.form.phone') }}</label>
                                    <input v-model="form.owner_phone" type="text" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('common.email') }}</label>
                                    <input v-model="form.owner_email" type="email" class="form-input">
                                </div>
                                <div class="form-group col-span-2">
                                    <label class="form-label">{{ __('common.address') }}</label>
                                    <input v-model="form.owner_address" type="text" class="form-input">
                                </div>
                            </div>
                        </fieldset>

                        <!-- Section Engin -->
                        <fieldset class="form-fieldset">
                            <legend class="form-legend">{{ __('engins.form.engine_section') }}</legend>

                            <template v-if="form.category === 'flottant'">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.vessel_type') }}</label>
                                        <input v-model="form.vehicle_genre" type="text" class="form-input"
                                            :placeholder="__('engins.form.vessel_type_placeholder')">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.manufacturing_model') }}</label>
                                        <input v-model="form.vehicle_brand" type="text" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.denomination') }}</label>
                                        <input v-model="form.vessel_name" type="text" class="form-input"
                                            :placeholder="__('engins.form.denomination_placeholder')">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.manufacturing_date') }}</label>
                                        <input v-model="form.manufacture_year" type="text" class="form-input"
                                            :placeholder="__('engins.form.year_placeholder')">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.registration_number') }}</label>
                                        <input v-model="form.registration_number" type="text" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.registration_place') }}</label>
                                        <input v-model="form.registration_place" type="text" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.registration_date') }}</label>
                                        <input v-model="form.registration_date" type="date" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.navigation_line') }}</label>
                                        <input v-model="form.navigation_line" type="text" class="form-input"
                                            :placeholder="__('engins.form.navigation_line_placeholder')">
                                    </div>
                                </div>

                                <!-- Dimensions -->
                                <div class="grid grid-cols-3 gap-4 mt-4">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.length') }}</label>
                                        <input v-model="form.length" type="number" step="0.01" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.width') }}</label>
                                        <input v-model="form.width" type="number" step="0.01" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.height') }}</label>
                                        <input v-model="form.height" type="number" step="0.01" class="form-input">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.capacity') }}</label>
                                        <input v-model="form.capacity" type="text" class="form-input"
                                            :placeholder="__('engins.form.capacity_placeholder')">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.boat_color') }}</label>
                                        <input v-model="form.vehicle_color" type="text" class="form-input">
                                    </div>
                                </div>

                                <!-- Type Construction & Motorisation -->
                                <div class="grid grid-cols-2 gap-4 mt-4 p-4 bg-slate-100 rounded">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.construction_type') }}</label>
                                        <select v-model="form.construction_type" class="form-input">
                                            <option value="">{{ __('common.select') }}...</option>
                                            <option value="acier">{{ __('engins.form.steel_construction') }}</option>
                                            <option value="bois">{{ __('engins.form.wood_construction') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group" v-if="form.sub_category === 'avec moteur'">
                                        <label class="form-label">{{ __('engins.form.engine_brand') }}</label>
                                        <input v-model="form.engine_brand" type="text" class="form-input">
                                    </div>
                                    <div class="form-group" v-if="form.sub_category === 'avec moteur'">
                                        <label class="form-label">{{ __('engins.form.horsepower') }}</label>
                                        <input v-model="form.horsepower" type="number" class="form-input">
                                    </div>
                                    <div class="form-group" v-if="form.sub_category === 'sans moteur'">
                                        <label class="form-label">{{ __('engins.form.propulsion_type') }}</label>
                                        <input v-model="form.propulsion_type" type="text" class="form-input"
                                            :placeholder="__('engins.form.propulsion_placeholder')">
                                    </div>
                                </div>

                                <!-- Usage -->
                                <div class="form-group mt-4">
                                    <label class="form-label font-bold">{{ __('engins.form.usage') }} :</label>
                                    <div class="flex flex-wrap gap-4 mt-2">
                                        <label class="flex items-center gap-2"><input type="checkbox" value="personnel"
                                                v-model="form.usage"> {{ __('engins.form.passenger_transport') }}</label>
                                        <label class="flex items-center gap-2"><input type="checkbox"
                                                value="marchandise" v-model="form.usage"> {{ __('engins.form.freight') }}</label>
                                        <label class="flex items-center gap-2"><input type="checkbox" value="peche"
                                                v-model="form.usage"> {{ __('engins.form.fishing') }}</label>
                                        <label class="flex items-center gap-2"><input type="checkbox" value="privee"
                                                v-model="form.usage"> {{ __('engins.form.private') }}</label>
                                    </div>
                                </div>
                            </template>

                            <template v-else>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.genre') }}</label>
                                        <input v-model="form.vehicle_genre" type="text" class="form-input"
                                            :placeholder="__('engins.form.genre_placeholder')">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.brand') }}</label>
                                        <input v-model="form.vehicle_brand" type="text" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.type_model') }}</label>
                                        <input v-model="form.vehicle_type" type="text" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.color') }}</label>
                                        <input v-model="form.vehicle_color" type="text" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.plate') }}</label>
                                        <input v-model="form.plate_number" type="text" class="form-input">
                                    </div>
                                    <div class="form-group" v-if="form.sub_category === 'avec moteur'">
                                        <label class="form-label">{{ __('engins.form.manufacture_year') }}</label>
                                        <input v-model="form.manufacture_year" type="number" class="form-input">
                                    </div>
                                    <div class="form-group" v-if="form.sub_category === 'avec moteur'">
                                        <label class="form-label">{{ __('engins.form.cv') }}</label>
                                        <input v-model="form.horsepower" type="number" class="form-input">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mt-4" v-if="form.sub_category === 'avec moteur'">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.engine_number') }}</label>
                                        <input v-model="form.engine_number" type="text" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">{{ __('engins.form.chassis_number') }}</label>
                                        <input v-model="form.chassis_number" type="text" class="form-input">
                                    </div>
                                </div>
                            </template>
                        </fieldset>

                        <!-- Section Équipage (Flottant) -->
                        <fieldset v-if="form.category === 'flottant'" class="form-fieldset">
                            <legend class="form-legend">{{ __('engins.form.crew_section') }}</legend>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('engins.form.crew_count') }}</label>
                                    <input v-model="form.crew_count" type="number" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('engins.form.driver_count') }}</label>
                                    <input v-model="form.driver_count" type="number" class="form-input">
                                </div>
                            </div>
                        </fieldset>

                        <!-- Validation Administrative -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('engins.form.identification_place') }}</label>
                                <input v-model="form.identification_place" type="text" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('engins.form.identification_date') }}</label>
                                <input v-model="form.identification_date" type="date" class="form-input">
                            </div>
                        </div>

                        <!-- Signature -->
                        <div class="grid grid-cols-2 gap-8 mt-6">
                            <div class="border border-dashed border-slate-400 p-4 rounded">
                                <p class="text-xs font-bold underline">{{ __('engins.form.owner_signature') }}</p>
                                <p class="text-[10px] text-slate-500 mt-8">{{ __('engins.form.name_signature') }}</p>
                            </div>
                            <div class="border border-dashed border-slate-400 p-4 rounded">
                                <p class="text-xs font-bold underline">{{ __('engins.form.for_commissioner') }}</p>
                                <p class="text-[10px] text-slate-500 mt-8">{{ __('engins.form.name_signature_seal') }}</p>
                            </div>
                        </div>

                        <div class="text-center mt-6">
                            <Button type="submit" :disabled="form.processing"
                                class="bg-blue-900 hover:bg-blue-800 text-white px-8 py-2 rounded font-bold">
{{ __('engins.form.save_form') }}
                            </Button>
                        </div>
                    </form>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Edit Engin Modal -->
        <Dialog v-model:open="isEditModalOpen">
            <DialogContent class="max-w-5xl p-0 rounded-xl overflow-hidden border-none shadow-2xl">
                <div class="bg-white dark:bg-slate-950 h-[85vh] overflow-y-auto custom-scrollbar">
                    <DialogHeader class="p-8 pb-0">
                        <DialogTitle class="text-2xl font-black ">{{ __('engins.edit.title') }} <span
                                class="text-rdc-blue">{{ __('engins.edit.subtitle') }}</span>
                        </DialogTitle>
                        <DialogDescription>{{ __('engins.edit.description') }} {{
                            selectedEngin?.identification_number
                            }}</DialogDescription>
                    </DialogHeader>

                    <form @submit.prevent="submitEdit" class="p-8 pt-6 space-y-8">
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 dark:bg-slate-900/50 p-6 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <div class="space-y-1.5 col-span-2">
                                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">{{ __('engins.edit.service_province') }}</Label>
                                <Select v-model="form.province_id">
                                    <SelectTrigger class="rounded-xl h-11 bg-white dark:bg-slate-950">
                                        <SelectValue :placeholder="__('common.select')" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="prov in provinces" :key="prov.id" :value="String(prov.id)">
                                            {{ prov.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <OwnerForm :form="form" />

                        <!-- Technical Section (Conditional) -->
                        <TerrestrialForm v-if="form.category === 'roulant'" :form="form" />
                        <MaritimeForm v-else :form="form" />

                        <DialogFooter class="pt-8 border-t border-slate-100 dark:border-slate-800">
                            <Button type="button" variant="ghost" @click="isEditModalOpen = false"
                                class="rounded-xl">{{ __('common.cancel') }}</Button>
                            <Button type="submit" :disabled="form.processing"
                                class="h-12 px-10 rounded-xl bg-slate-900 text-white shadow-lg font-black ">
                                {{ __('engins.edit.save_changes') }}
                            </Button>
                        </DialogFooter>
                    </form>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Modal -->
        <Dialog v-model:open="isDeleteModalOpen">
            <DialogContent class="max-w-md p-8 rounded-xl border-none shadow-2xl text-center">
                <DialogHeader class="mb-6 flex flex-col items-center">
                    <div class="size-16 rounded-full bg-rdc-red/10 flex items-center justify-center text-rdc-red mb-4">
                        <Trash2 class="size-8" />
                    </div>
                    <DialogTitle class="text-xl font-black text-slate-900 tracking-tight">{{ __('engins.delete.title') }}
                    </DialogTitle>
                    <DialogDescription class="pt-2">
                        {{ __('engins.delete.confirm_message') }} <span
                            class="font-black text-slate-900">{{
                                selectedEngin?.identification_number }}</span> ?
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="flex flex-col sm:flex-row gap-3">
                    <Button variant="ghost" @click="isDeleteModalOpen = false"
                        class="flex-1 rounded-xl h-12">{{ __('common.keep') }}</Button>
                    <Button @click="submitDelete"
                        class="flex-1 h-12 rounded-xl bg-rdc-red text-white font-black ">{{ __('engins.delete.confirm') }}</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #1e293b;
}

/* Form Styles from test.html */
.form-fieldset {
    border: 1px solid #cccccc;
    border-radius: 6px;
    padding: 16px;
    margin-bottom: 20px;
    background-color: #f8fafc;
}

.form-legend {
    font-weight: bold;
    color: #1e3a8a;
    text-transform: uppercase;
    padding: 0 10px;
    font-size: 12px;
    letter-spacing: 0.5px;
}

.form-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 8px;
}

.form-group.col-span-2 {
    grid-column: span 2;
}

.form-label {
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 4px;
    color: #333333;
}

.form-input {
    padding: 8px 12px;
    border: 1px solid #cccccc;
    border-radius: 4px;
    font-size: 13px;
    background-color: #ffffff;
    outline: none;
    transition: border-color 0.2s;
    width: 100%;
}

.form-input:focus {
    border-color: #1e3a8a;
    box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.1);
}

.grid-cols-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.grid-cols-3 {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 16px;
}
</style>
