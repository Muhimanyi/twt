<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Plus, Search, Trash2, Pencil, Globe, Settings,
    DollarSign, Users, BarChart3, Filter, Eye, Zap,
    ArrowUp, ArrowDown, ArrowUpDown, Activity, Building2,
    Calendar, MapPin, Tag
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
import { usePermissions } from '@/composables/usePermissions';
import { useTrans } from '@/composables/useTrans';

const { can } = usePermissions();
const { __ } = useTrans();

const props = defineProps<{
    taxes: Array<any>;
    provinces: Array<any>;
    currentProvinceId: string | null;
    stats: {
        total: number;
        active: number;
        by_target: Record<string, number>;
    };
}>();

const isFormModalOpen = ref(false);
const formMode = ref<'create' | 'edit'>('create');
const isDeleteModalOpen = ref(false);
const isViewModalOpen = ref(false);
const selectedTaxe = ref<any>(null);
const searchQuery = ref('');

const filters = ref({
    target: '',
    status: '',
});

const sortKey = ref('created_at');
const sortOrder = ref<'asc' | 'desc'>('desc');

const sortBy = (key: string) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortOrder.value = 'asc';
    }
};

const form = useForm({
    label: '',
    beneficiary: '',
    target: 'Conducteur' as string,
    amount: '' as any,
    currency: 'USD' as string,
    frequency: 'Annuelle' as string,
    sector: '' as string,
    province_id: '' as any,
    description: '' as string,
    is_active: true,
});

const targetOptions = [
    { value: 'Conducteur', label: __('taxes.target.conducteur') },
    { value: 'Engin', label: __('taxes.target.engin') },
    { value: 'Transporteur', label: __('taxes.target.transporteur') },
    { value: 'Passager', label: __('taxes.target.passager') },
    { value: 'Chargeur', label: __('taxes.target.chargeur') },
];

const frequencyOptions = [
    { value: 'Mensuelle', label: __('taxes.frequency.monthly') },
    { value: 'Trimestrielle', label: __('taxes.frequency.quarterly') },
    { value: 'Semestrielle', label: __('taxes.frequency.semester') },
    { value: 'Annuelle', label: __('taxes.frequency.yearly') },
    { value: 'Unique', label: __('taxes.frequency.one_time') },
];

const sectorOptions = [
    { value: 'routier', label: __('sector.routier') },
    { value: 'lacustre', label: __('sector.lacustre') },
    { value: 'ferroviaire', label: __('sector.ferroviaire') },
    { value: 'aerien', label: __('sector.aerien') },
];

const currencyOptions = [
    { value: 'USD', label: 'USD ($)' },
    { value: 'EUR', label: 'EUR (€)' },
    { value: 'CDF', label: 'CDF (₣)' },
    { value: 'ZAR', label: 'ZAR (R)' },
];

const processedTaxes = computed(() => {
    let results = [...props.taxes];

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase().trim();
        results = results.filter(t =>
            (t.label || '').toLowerCase().includes(query) ||
            (t.beneficiary || '').toLowerCase().includes(query) ||
            (t.target || '').toLowerCase().includes(query) ||
            (t.description || '').toLowerCase().includes(query) ||
            (t.sector || '').toLowerCase().includes(query) ||
            (t.province?.name || '').toLowerCase().includes(query)
        );
    }

    if (filters.value.target) {
        results = results.filter(t => t.target === filters.value.target);
    }

    if (filters.value.status !== '') {
        const isActive = filters.value.status === 'active';
        results = results.filter(t => t.is_active === isActive);
    }

    results.sort((a, b) => {
        let valA = a[sortKey.value];
        let valB = b[sortKey.value];

        if (sortKey.value === 'province') {
            valA = a.province?.name || 'Nationale';
            valB = b.province?.name || 'Nationale';
        }

        if (sortKey.value === 'amount') {
            valA = Number(valA || 0);
            valB = Number(valB || 0);
        } else if (typeof valA === 'string') {
            valA = valA.toLowerCase();
            valB = valB.toLowerCase();
        }

        if (valA < valB) return sortOrder.value === 'asc' ? -1 : 1;
        if (valA > valB) return sortOrder.value === 'asc' ? 1 : -1;
        return 0;
    });

    return results;
});

const filterByProvince = (id: any) => {
    router.visit('/taxes', {
        data: {
            province_id: id === 'all' ? null : id
        },
        preserveState: true,
        preserveScroll: true
    });
};

const resetFilters = () => {
    searchQuery.value = '';
    filters.value = { target: '', status: '' };
};

const openCreateModal = () => {
    form.reset();
    formMode.value = 'create';
    isFormModalOpen.value = true;
};

const openEditModal = (taxe: any) => {
    selectedTaxe.value = taxe;
    form.clearErrors();
    form.label = taxe.label;
    form.beneficiary = taxe.beneficiary;
    form.target = taxe.target;
    form.amount = taxe.amount;
    form.currency = taxe.currency;
    form.frequency = taxe.frequency;
    form.sector = taxe.sector || '';
    form.province_id = taxe.province_id ? String(taxe.province_id) : '';
    form.description = taxe.description || '';
    form.is_active = taxe.is_active;
    formMode.value = 'edit';
    isFormModalOpen.value = true;
};

const openDeleteModal = (taxe: any) => {
    selectedTaxe.value = taxe;
    isDeleteModalOpen.value = true;
};

const openViewModal = (taxe: any) => {
    selectedTaxe.value = taxe;
    isViewModalOpen.value = true;
};

const submitCreate = () => {
    form.post('/taxes', {
        preserveScroll: true,
        onSuccess: () => {
            isFormModalOpen.value = false;
            form.reset();
            selectedTaxe.value = null;
        },
        onError: () => {
            console.error('Erreur lors de la création');
        },
    });
};

const submitEdit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(`/taxes/${selectedTaxe.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            isFormModalOpen.value = false;
            form.reset();
            selectedTaxe.value = null;
        },
        onError: () => {
            console.error('Erreur lors de la modification');
        },
    });
};

const submitDelete = () => {
    router.delete(`/taxes/${selectedTaxe.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            isDeleteModalOpen.value = false;
            selectedTaxe.value = null;
        },
        onError: () => {
            console.error('Erreur lors de la suppression');
        },
    });
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Gestion', href: '#' },
            { title: 'Taxes et Impôts', href: '/taxes' },
        ],
    },
});
</script>

<template>

    <Head :title="__('taxes.title')" />

    <div class="space-y-8 p-6">
        <!-- Header Section -->
        <div
            class="flex items-end justify-between bg-white/95 text-slate-900 dark:bg-slate-900/95 dark:text-slate-100 backdrop-blur-sm p-6 rounded-xl shadow-lg shadow-slate-200/40 dark:shadow-black/20 border border-slate-200/70 dark:border-slate-800/80">
            <div class="space-y-1">
                <div class="flex items-center gap-3 text-rdc-blue mb-1">
                    <DollarSign class="size-5" />
                    <span class="text-[10px] font-black uppercase tracking-[0.3em]">{{ __('common.finance_module') }}</span>
                </div>
                <h1 class="text-4xl font-black tracking-tighter">{{ __('taxes.title') }}</h1>
                <p class="text-sm text-slate-400 font-medium tracking-tight">{{ __('taxes.subtitle') }}</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 mr-4">
                    <Label class="text-[10px] font-black uppercase text-slate-400 whitespace-nowrap">{{ __('common.province') }}:</Label>
                    <Select :model-value="String(currentProvinceId || 'all')" @update:model-value="filterByProvince">
                        <SelectTrigger class="w-40 h-10 rounded-xl bg-slate-50 border-none">
                            <SelectValue :placeholder="__('common.all_provinces')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ __('common.all_national') }}</SelectItem>
                            <SelectItem v-for="prov in provinces" :key="prov.id" :value="String(prov.id)">{{ prov.name
                                }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="relative w-64">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                    <Input v-model="searchQuery" :placeholder="__('taxes.search_placeholder')"
                        class="pl-10 h-12 rounded-xl bg-slate-50 border-none dark:bg-slate-900" />
                </div>
                <Button v-if="can('taxes.create')" @click="openCreateModal"
                    class="h-12 px-6 rounded-xl bg-rdc-blue text-white shadow-lg shadow-rdc-blue/20 hover:bg-rdc-blue/90 transition-all font-bold gap-3">
                    <Plus class="size-5" />
                    {{ __('taxes.create') }}
                </Button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:bg-slate-950 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="size-10 rounded-xl bg-blue-100 flex items-center justify-center text-rdc-blue dark:bg-slate-900 dark:text-slate-400">
                        <BarChart3 class="size-5" />
                    </div>
                    <Badge variant="outline" class="text-[10px] font-black uppercase tracking-widest">{{ __('common.total') }}</Badge>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('taxes.total_label') }}</p>
                <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ stats.total }}</p>
            </div>

            <div
                class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:bg-slate-950 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="size-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 dark:bg-slate-900 dark:text-slate-400">
                        <Zap class="size-5" />
                    </div>
                    <Badge variant="outline" class="text-[10px] font-black uppercase tracking-widest">{{ __('common.active') }}</Badge>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('taxes.active_label') }}</p>
                <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ stats.active }}</p>
            </div>

            <div
                class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:bg-slate-950 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="size-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 dark:bg-slate-900 dark:text-slate-400">
                        <Users class="size-5" />
                    </div>
                    <Badge variant="outline" class="text-[10px] font-black uppercase tracking-widest">{{ __('taxes.targets') }}</Badge>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('taxes.target_types') }}</p>
                <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ Object.keys(stats.by_target).length
                }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div
            class="flex flex-wrap items-center gap-4 p-4 bg-white dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center gap-2 text-slate-400">
                <Filter class="size-4" />
                <span class="text-xs font-black uppercase tracking-wider">{{ __('tools.filter') }}:</span>
            </div>
            <div class="flex flex-wrap gap-2">
                <select v-model="filters.target"
                    class="h-9 w-44 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-xs font-medium text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-rdc-blue">
                    <option value="">{{ __('taxes.all_targets') }}</option>
                    <option v-for="opt in targetOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </select>
                <select v-model="filters.status"
                    class="h-9 w-40 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-xs font-medium text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-rdc-blue">
                    <option value="">{{ __('common.all_statuses') }}</option>
                    <option value="active">{{ __('common.active') }}</option>
                    <option value="inactive">{{ __('common.inactive') }}</option>
                </select>
                <Button variant="ghost" size="sm" @click="resetFilters"
                    class="h-9 px-3 text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                    {{ __('common.reset') }}
                </Button>
            </div>
            <div class="ml-auto text-xs font-medium text-slate-400">
                {{ processedTaxes.length }} {{ processedTaxes.length > 1 ? __('common.results') : __('common.result') }}
            </div>
        </div>

        <!-- Taxes Table -->
        <div
            class="bg-white shadow-xl shadow-slate-200/50 overflow-hidden rounded-xl border border-slate-200/70 dark:bg-slate-950 dark:border-slate-800/80">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-slate-900/90 border-b border-slate-100 dark:border-slate-800 select-none">
                            <th @click="sortBy('label')"
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 cursor-pointer hover:text-rdc-blue transition-colors group">
                                <div class="flex items-center gap-1">
                                    {{ __('taxes.label') }}
                                    <ArrowUp v-if="sortKey === 'label' && sortOrder === 'asc'" class="size-3 text-rdc-blue" />
                                    <ArrowDown v-else-if="sortKey === 'label' && sortOrder === 'desc'" class="size-3 text-rdc-blue" />
                                    <ArrowUpDown v-else class="size-3 opacity-0 group-hover:opacity-100 transition-opacity" />
                                </div>
                            </th>
                            <th @click="sortBy('target')"
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 cursor-pointer hover:text-rdc-blue transition-colors group">
                                <div class="flex items-center gap-1">
                                    {{ __('taxes.target') }}
                                    <ArrowUp v-if="sortKey === 'target' && sortOrder === 'asc'" class="size-3 text-rdc-blue" />
                                    <ArrowDown v-else-if="sortKey === 'target' && sortOrder === 'desc'" class="size-3 text-rdc-blue" />
                                    <ArrowUpDown v-else class="size-3 opacity-0 group-hover:opacity-100 transition-opacity" />
                                </div>
                            </th>
                            <th @click="sortBy('beneficiary')"
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 cursor-pointer hover:text-rdc-blue transition-colors group">
                                <div class="flex items-center gap-1">
                                    {{ __('taxes.beneficiary') }}
                                    <ArrowUp v-if="sortKey === 'beneficiary' && sortOrder === 'asc'" class="size-3 text-rdc-blue" />
                                    <ArrowDown v-else-if="sortKey === 'beneficiary' && sortOrder === 'desc'" class="size-3 text-rdc-blue" />
                                    <ArrowUpDown v-else class="size-3 opacity-0 group-hover:opacity-100 transition-opacity" />
                                </div>
                            </th>
                            <th @click="sortBy('amount')"
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 cursor-pointer hover:text-rdc-blue transition-colors group">
                                <div class="flex items-center gap-1">
                                    {{ __('taxes.amount') }}
                                    <ArrowUp v-if="sortKey === 'amount' && sortOrder === 'asc'" class="size-3 text-rdc-blue" />
                                    <ArrowDown v-else-if="sortKey === 'amount' && sortOrder === 'desc'" class="size-3 text-rdc-blue" />
                                    <ArrowUpDown v-else class="size-3 opacity-0 group-hover:opacity-100 transition-opacity" />
                                </div>
                            </th>
                            <th @click="sortBy('frequency')"
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 cursor-pointer hover:text-rdc-blue transition-colors group">
                                <div class="flex items-center gap-1">
                                    {{ __('taxes.frequency') }}
                                    <ArrowUp v-if="sortKey === 'frequency' && sortOrder === 'asc'" class="size-3 text-rdc-blue" />
                                    <ArrowDown v-else-if="sortKey === 'frequency' && sortOrder === 'desc'" class="size-3 text-rdc-blue" />
                                    <ArrowUpDown v-else class="size-3 opacity-0 group-hover:opacity-100 transition-opacity" />
                                </div>
                            </th>
                            <th @click="sortBy('is_active')"
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 cursor-pointer hover:text-rdc-blue transition-colors group">
                                <div class="flex items-center gap-1">
                                    {{ __('common.status') }}
                                    <ArrowUp v-if="sortKey === 'is_active' && sortOrder === 'asc'" class="size-3 text-rdc-blue" />
                                    <ArrowDown v-else-if="sortKey === 'is_active' && sortOrder === 'desc'" class="size-3 text-rdc-blue" />
                                    <ArrowUpDown v-else class="size-3 opacity-0 group-hover:opacity-100 transition-opacity" />
                                </div>
                            </th>
                            <th
                                class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                {{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-if="processedTaxes.length === 0"
                            class="hover:bg-slate-50/50 dark:hover:bg-slate-800/60 transition-all">
                            <td colspan="7" class="px-6 py-8 text-center">
                                <p class="text-sm text-slate-400 font-medium">{{ __('taxes.no_results') }}</p>
                            </td>
                        </tr>
                        <tr v-for="taxe in processedTaxes" :key="taxe.id"
                            class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/60 transition-all">
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-900 dark:text-slate-100">{{
                                        taxe.label }}</span>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <Badge variant="outline" class="text-[8px] h-4 w-fit uppercase font-bold">{{
                                            taxe.sector || __('taxes.all_sectors') }}</Badge>
                                        <Badge :variant="taxe.province_id ? 'secondary' : 'default'"
                                            class="text-[8px] h-4 w-fit uppercase font-bold">
                                            {{ taxe.province?.name || __('common.national') }}
                                        </Badge>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <Badge variant="outline" class="text-[10px] font-bold">{{ taxe.target }}</Badge>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{
                                        taxe.beneficiary }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-black text-rdc-blue">{{
                                        Number(taxe.amount).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                                    <Badge variant="secondary" class="text-[9px] font-bold">{{ taxe.currency
                                    }}</Badge>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-400">{{
                                    taxe.frequency }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <Badge :variant="taxe.is_active ? 'default' : 'outline'"
                                    :class="taxe.is_active ? 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900 dark:text-emerald-300 dark:border-emerald-800' : 'text-slate-600 dark:text-slate-400'">
                                    {{ taxe.is_active ? __('common.active') : __('common.inactive') }}
                                </Badge>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <Button size="icon" variant="ghost" @click="openViewModal(taxe)"
                                        class="size-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                                        <Eye class="size-4" />
                                    </Button>
                                    <Button v-if="can('taxes.edit')" size="icon" variant="ghost" @click="openEditModal(taxe)"
                                        class="size-8 rounded-lg hover:bg-rdc-blue/10 hover:text-rdc-blue">
                                        <Pencil class="size-4" />
                                    </Button>
                                    <Button v-if="can('taxes.delete')" size="icon" variant="ghost" @click="openDeleteModal(taxe)"
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

    <!-- View Detail Modal -->
    <Dialog v-model:open="isViewModalOpen">
        <DialogContent class="max-w-2xl rounded-xl border-none shadow-2xl">
            <DialogHeader>
                <DialogTitle class="text-2xl font-black">{{ __('taxes.details_title') }}</DialogTitle>
            </DialogHeader>
            <div v-if="selectedTaxe" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                        <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                            <Tag class="size-4" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('taxes.label') }}</p>
                            <p class="text-base font-black text-slate-900 dark:text-white mt-0.5">{{ selectedTaxe.label }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                        <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                            <Users class="size-4" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('taxes.target') }}</p>
                            <p class="text-base font-bold text-slate-700 dark:text-slate-300 mt-0.5">{{ selectedTaxe.target }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                        <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                            <Building2 class="size-4" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('taxes.beneficiary') }}</p>
                            <p class="text-base font-bold text-slate-700 dark:text-slate-300 mt-0.5">{{ selectedTaxe.beneficiary }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                        <div class="size-9 rounded-lg bg-emerald-500/10 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5">
                            <DollarSign class="size-4" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('taxes.amount') }}</p>
                            <p class="text-base font-black text-rdc-blue mt-0.5">{{ Number(selectedTaxe.amount).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }} {{ selectedTaxe.currency }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                        <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                            <Calendar class="size-4" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('taxes.frequency') }}</p>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mt-0.5">{{ selectedTaxe.frequency }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                        <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                            <Activity class="size-4" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('taxes.sector') }}</p>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mt-0.5 uppercase">{{ selectedTaxe.sector || __('taxes.all_sectors') }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                        <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                            <MapPin class="size-4" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('taxes.scope_province') }}</p>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mt-0.5">
                                {{ selectedTaxe.province?.name || __('taxes.national_taxe') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div v-if="selectedTaxe.description">
                    <Label class="text-xs font-bold text-slate-400 uppercase">{{ __('common.description') }}</Label>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">{{ selectedTaxe.description }}</p>
                </div>

                <div>
                    <Label class="text-xs font-bold text-slate-400 uppercase">{{ __('common.status') }}</Label>
                    <Badge :variant="selectedTaxe.is_active ? 'default' : 'outline'"
                        :class="selectedTaxe.is_active ? 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900 dark:text-emerald-300 dark:border-emerald-800' : 'text-slate-600 dark:text-slate-400'"
                        class="mt-1">
                        {{ selectedTaxe.is_active ? __('common.active') : __('common.inactive') }}
                    </Badge>
                </div>
            </div>

            <DialogFooter>
                <Button variant="ghost" @click="isViewModalOpen = false" class="rounded-xl">{{ __('common.close') }}</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <!-- Create/Edit Modal -->
    <Dialog v-model:open="isFormModalOpen">
        <DialogContent class="max-w-2xl rounded-xl border-none shadow-2xl">
            <DialogHeader>
                <DialogTitle class="text-2xl font-black">
                    {{ formMode === 'edit' ? __('taxes.edit') : __('taxes.create') }}
                </DialogTitle>
                <DialogDescription>
                    {{ formMode === 'edit' ? __('taxes.edit_description', { label: selectedTaxe?.label || '' }) : __('taxes.create_description') }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="formMode === 'edit' ? submitEdit() : submitCreate()" class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <Label class="text-xs font-bold">{{ __('taxes.label_field') }}</Label>
                        <Input v-model="form.label" :placeholder="__('taxes.label_placeholder')"
                            class="rounded-lg h-10" />
                        <p v-if="form.errors.label" class="text-xs text-rdc-red">{{ form.errors.label }}</p>
                    </div>
                    <div class="space-y-1.5">
                        <Label class="text-xs font-bold">{{ __('taxes.target_field') }}</Label>
                        <Select v-model="form.target">
                            <SelectTrigger class="rounded-lg h-10">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="opt in targetOptions" :key="opt.value" :value="opt.value">
                                    {{ opt.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.target" class="text-xs text-rdc-red">{{ form.errors.target }}</p>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <Label class="text-xs font-bold">{{ __('taxes.beneficiary_field') }}</Label>
                    <Input v-model="form.beneficiary" :placeholder="__('taxes.beneficiary_placeholder')"
                        class="rounded-lg h-10" />
                    <p v-if="form.errors.beneficiary" class="text-xs text-rdc-red">{{ form.errors.beneficiary }}</p>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <Label class="text-xs font-bold">{{ __('taxes.amount_field') }}</Label>
                        <Input v-model="form.amount" type="number" step="0.01" placeholder="0.00"
                            class="rounded-lg h-10" />
                        <p v-if="form.errors.amount" class="text-xs text-rdc-red">{{ form.errors.amount }}</p>
                    </div>
                    <div class="space-y-1.5">
                        <Label class="text-xs font-bold">{{ __('taxes.currency_field') }}</Label>
                        <Select v-model="form.currency">
                            <SelectTrigger class="rounded-lg h-10">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="opt in currencyOptions" :key="opt.value" :value="opt.value">
                                    {{ opt.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.currency" class="text-xs text-rdc-red">{{ form.errors.currency }}</p>
                    </div>
                    <div class="space-y-1.5">
                        <Label class="text-xs font-bold">{{ __('taxes.frequency_field') }}</Label>
                        <Select v-model="form.frequency">
                            <SelectTrigger class="rounded-lg h-10">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="opt in frequencyOptions" :key="opt.value" :value="opt.value">
                                    {{ opt.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.frequency" class="text-xs text-rdc-red">{{ form.errors.frequency }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <Label class="text-xs font-bold">{{ __('taxes.sector_field') }}</Label>
                        <select v-model="form.sector" class="h-10 w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-rdc-blue">
                            <option value="">{{ __('taxes.all_sectors_option') }}</option>
                            <option v-for="opt in sectorOptions" :key="opt.value" :value="opt.value">{{
                                opt.label
                            }}</option>
                        </select>
                        <p v-if="form.errors.sector" class="text-xs text-rdc-red">{{ form.errors.sector }}</p>
                    </div>
                    <div class="space-y-1.5">
                        <Label class="text-xs font-bold">{{ __('taxes.province_field') }}</Label>
                        <select v-model="form.province_id" class="h-10 w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-rdc-blue">
                            <option value="">{{ __('taxes.national_option') }}</option>
                            <option v-for="province in provinces" :key="province.id" :value="province.id">{{
                                province.name
                            }}</option>
                        </select>
                        <p v-if="form.errors.province_id" class="text-xs text-rdc-red">{{ form.errors.province_id }}</p>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <Label class="text-xs font-bold">{{ __('common.description_optional') }}</Label>
                    <textarea v-model="form.description" :placeholder="__('taxes.notes_placeholder')"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-rdc-blue"
                        rows="3"></textarea>
                </div>

                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input v-model="form.is_active" type="checkbox"
                            class="w-4 h-4 rounded border-slate-300 text-rdc-blue" />
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('taxes.active_checkbox') }}</span>
                    </label>
                </div>

                <DialogFooter class="gap-3">
                    <Button type="button" variant="ghost" @click="isFormModalOpen = false"
                        class="rounded-lg">{{ __('common.cancel') }}</Button>
                    <Button type="submit" :disabled="form.processing"
                        class="h-10 px-6 rounded-lg bg-rdc-blue text-white font-bold">
                        {{ formMode === 'edit' ? __('common.update') : __('taxes.create_submit') }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <!-- Delete Confirmation Modal -->
    <Dialog v-model:open="isDeleteModalOpen">
        <DialogContent class="max-w-md p-8 rounded-xl border-none shadow-2xl text-center">
            <DialogHeader class="mb-6 flex flex-col items-center">
                <div class="size-16 rounded-full bg-rdc-red/10 flex items-center justify-center text-rdc-red mb-4">
                    <Trash2 class="size-8" />
                </div>
                <DialogTitle class="text-xl font-black text-slate-900 tracking-tight">{{ __('taxes.delete_title') }}
                </DialogTitle>
                <DialogDescription class="pt-2">
                    {{ __('taxes.delete_confirm', { label: selectedTaxe?.label || '' }) }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="flex flex-col sm:flex-row gap-3">
                <Button variant="ghost" @click="isDeleteModalOpen = false"
                    class="flex-1 rounded-lg h-10">{{ __('common.keep') }}</Button>
                <Button @click="submitDelete" class="flex-1 h-10 rounded-lg bg-rdc-red text-white font-black">{{ __('taxes.delete_confirm_button') }}</Button>
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
</style>
