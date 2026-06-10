<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Plus, Search, Trash2, Printer, Receipt, DollarSign, Users, Calendar, Filter } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { usePermissions } from '@/composables/usePermissions';
import { useTrans } from '@/composables/useTrans';

const { can } = usePermissions();
const { __ } = useTrans();

const props = defineProps<{
    paiements: Array<any>;
    taxes: Array<any>;
    conducteurs: Array<any>;
    engins: Array<any>;
    debardeurs: Array<any>;
    provinces: Array<any>;
    stats: { total: number; today: number; total_amount: string };
}>();

const isFormModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedPaiement = ref<any>(null);
const searchQuery = ref('');

const form = useForm({
    taxe_id: '' as any,
    payable_type: 'Conducteur',
    payable_id: '' as any,
    payment_method: 'cash',
    province_id: '' as any,
    notes: '',
});

const payableOptions = computed(() => {
    if (form.payable_type === 'Conducteur') return props.conducteurs;
    if (form.payable_type === 'Engin') return props.engins;
    if (form.payable_type === 'Transporteur') return props.debardeurs;
    return [];
});

const selectedTaxe = computed(() => props.taxes.find(t => t.id == form.taxe_id));

const payableLabel = (item: any, type: string) => {
    if (type === 'Conducteur') return `${item.name} (${item.identification_number})`;
    if (type === 'Engin') return `${item.plate_number ?? item.registration_number ?? 'N/A'} — ${item.owner_name}`;
    return `${item.name} (${item.identification_number})`;
};

const filteredPaiements = computed(() => {
    if (!searchQuery.value) return props.paiements;
    const q = searchQuery.value.toLowerCase();
    return props.paiements.filter(p =>
        (p.reference || '').toLowerCase().includes(q) ||
        (p.taxe?.label || '').toLowerCase().includes(q) ||
        (p.payable_label || '').toLowerCase().includes(q)
    );
});

const methodLabel: Record<string, string> = {
    cash: __('paiements.method_cash'),
    bank: __('paiements.method_bank'),
    mobile_money: __('paiements.method_mobile'),
};

const openCreateModal = () => {
    form.reset();
    isFormModalOpen.value = true;
};

const submitCreate = () => {
    form.post('/paiements', {
        preserveScroll: true,
        onSuccess: () => { isFormModalOpen.value = false; },
    });
};

const openDeleteModal = (p: any) => {
    selectedPaiement.value = p;
    isDeleteModalOpen.value = true;
};

const submitDelete = () => {
    router.delete(`/paiements/${selectedPaiement.value.id}`, {
        preserveScroll: true,
        onSuccess: () => { isDeleteModalOpen.value = false; selectedPaiement.value = null; },
    });
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Finances', href: '#' },
            { title: 'Paiements des Taxes', href: '/paiements' },
        ],
    },
});
</script>

<template>
    <Head :title="__('paiements.page_title')" />

    <div class="space-y-8 p-6">
        <!-- Header -->
        <div class="flex items-end justify-between bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm p-6 rounded-xl shadow-lg border border-slate-200/70 dark:border-slate-800/80">
            <div class="space-y-1">
                <div class="flex items-center gap-3 text-rdc-blue mb-1">
                    <Receipt class="size-5" />
                    <span class="text-[10px] font-black uppercase tracking-[0.3em]">{{ __('common.finance_module') }}</span>
                </div>
                <h1 class="text-4xl font-black tracking-tighter text-slate-900 dark:text-slate-100">
                    {{ __('paiements.h1_prefix') }} <span class="text-rdc-red">{{ __('taxes.title_short') }}</span>
                </h1>
                <p class="text-sm text-slate-400 font-medium">{{ __('paiements.subtitle') }}</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative w-64">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                    <Input v-model="searchQuery" :placeholder="__('paiements.search_placeholder')" class="pl-10 h-12 rounded-xl bg-slate-50 border-none dark:bg-slate-900" />
                </div>
                <Button v-if="can('paiements.create')" @click="openCreateModal" class="h-12 px-6 rounded-xl bg-rdc-blue text-white font-bold gap-3 hover:bg-rdc-blue/90">
                    <Plus class="size-5" /> {{ __('paiements.create') }}
                </Button>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-6">
            <div class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:bg-slate-950 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <div class="size-10 rounded-xl bg-blue-100 flex items-center justify-center text-rdc-blue dark:bg-slate-900">
                        <Receipt class="size-5" />
                    </div>
                    <Badge variant="outline" class="text-[10px] font-black uppercase tracking-widest">{{ __('common.total') }}</Badge>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('paiements.total_label') }}</p>
                <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ stats.total }}</p>
            </div>
            <div class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:bg-slate-950 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <div class="size-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 dark:bg-slate-900">
                        <Calendar class="size-5" />
                    </div>
                    <Badge variant="outline" class="text-[10px] font-black uppercase tracking-widest">{{ __('paiements.today_label_short') }}</Badge>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('paiements.today_label') }}</p>
                <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ stats.today }}</p>
            </div>
            <div class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:bg-slate-950 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <div class="size-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 dark:bg-slate-900">
                        <DollarSign class="size-5" />
                    </div>
                    <Badge variant="outline" class="text-[10px] font-black uppercase tracking-widest">{{ __('taxes.amount') }}</Badge>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('paiements.total_encaissement') }}</p>
                <p class="text-2xl font-black text-rdc-blue">{{ Number(stats.total_amount).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) }}</p>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-xl shadow-slate-200/50 overflow-hidden rounded-xl border border-slate-200/70 dark:bg-slate-950 dark:border-slate-800/80">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-slate-900/90 border-b border-slate-100 dark:border-slate-800">
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('paiements.reference') }}</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('paiements.taxe_column') }}</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('paiements.payeur') }}</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('paiements.amount') }}</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('paiements.method') }}</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('paiements.date') }}</th>
                            <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-if="filteredPaiements.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-400 font-medium">{{ __('paiements.no_results') }}</td>
                        </tr>
                        <tr v-for="p in filteredPaiements" :key="p.id" class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/60 transition-all">
                            <td class="px-6 py-4">
                                <span class="text-xs font-black font-mono text-rdc-blue">{{ p.reference }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ p.taxe?.label }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ p.payable_label }}</span>
                                    <Badge variant="outline" class="text-[8px] h-4 w-fit mt-0.5">{{ p.payable_type_label }}</Badge>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-black text-rdc-blue">{{ Number(p.amount).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) }} {{ p.currency }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <Badge variant="secondary" class="text-[10px] font-bold">{{ methodLabel[p.payment_method] ?? p.payment_method }}</Badge>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ new Date(p.paid_at).toLocaleDateString('fr-FR') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a :href="`/paiements/${p.id}/print`" target="_blank">
                                        <Button size="icon" variant="ghost" class="size-8 rounded-lg hover:bg-rdc-blue/10 hover:text-rdc-blue">
                                            <Printer class="size-4" />
                                        </Button>
                                    </a>
                                    <Button v-if="can('paiements.delete')" size="icon" variant="ghost" @click="openDeleteModal(p)" class="size-8 rounded-lg hover:bg-rdc-red/10 hover:text-rdc-red">
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

    <!-- Create Modal -->
    <Dialog v-model:open="isFormModalOpen">
        <DialogContent class="max-w-xl rounded-xl border-none shadow-2xl">
            <DialogHeader>
                <DialogTitle class="text-2xl font-black">{{ __('paiements.create_title') }}</DialogTitle>
                <DialogDescription>{{ __('paiements.create_description') }}</DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submitCreate" class="space-y-5">
                <div class="space-y-1.5">
                    <Label class="text-xs font-bold">{{ __('paiements.taxe_field') }}</Label>
                    <select v-model="form.taxe_id" class="h-10 w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-rdc-blue">
                        <option value="">{{ __('paiements.taxe_placeholder') }}</option>
                        <option v-for="t in taxes" :key="t.id" :value="t.id">{{ t.label }} ({{ t.currency }} {{ Number(t.amount).toLocaleString('fr-FR') }})</option>
                    </select>
                    <p v-if="form.errors.taxe_id" class="text-xs text-rdc-red">{{ form.errors.taxe_id }}</p>
                </div>

                <div v-if="selectedTaxe" class="rounded-lg bg-rdc-blue/5 border border-rdc-blue/20 p-3 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">{{ __('paiements.amount_display_label') }}</span>
                    <span class="text-lg font-black text-rdc-blue">{{ Number(selectedTaxe.amount).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) }} {{ selectedTaxe.currency }}</span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <Label class="text-xs font-bold">{{ __('paiements.payable_type_field') }}</Label>
                        <select v-model="form.payable_type" @change="form.payable_id = ''" class="h-10 w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-rdc-blue">
                            <option value="Conducteur">{{ __('paiements.payable_type_conducteur') }}</option>
                            <option value="Engin">{{ __('paiements.payable_type_engin') }}</option>
                            <option value="Transporteur">{{ __('paiements.payable_type_transporteur') }}</option>
                        </select>
                        <p v-if="form.errors.payable_type" class="text-xs text-rdc-red">{{ form.errors.payable_type }}</p>
                    </div>
                    <div class="space-y-1.5">
                        <Label class="text-xs font-bold">{{ __('paiements.payeur_field') }}</Label>
                        <select v-model="form.payable_id" class="h-10 w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-rdc-blue">
                            <option value="">{{ __('paiements.payeur_placeholder') }}</option>
                            <option v-for="item in payableOptions" :key="item.id" :value="item.id">
                                {{ payableLabel(item, form.payable_type) }}
                            </option>
                        </select>
                        <p v-if="form.errors.payable_id" class="text-xs text-rdc-red">{{ form.errors.payable_id }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <Label class="text-xs font-bold">{{ __('paiements.method_field') }}</Label>
                        <select v-model="form.payment_method" class="h-10 w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-rdc-blue">
                            <option value="cash">{{ __('paiements.method_cash_option') }}</option>
                            <option value="bank">{{ __('paiements.method_bank_option') }}</option>
                            <option value="mobile_money">{{ __('paiements.method_mobile') }}</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <Label class="text-xs font-bold">{{ __('common.province') }}</Label>
                        <select v-model="form.province_id" class="h-10 w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-rdc-blue">
                            <option value="">{{ __('common.national') }}</option>
                            <option v-for="prov in provinces" :key="prov.id" :value="prov.id">{{ prov.name }}</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <Label class="text-xs font-bold">{{ __('paiements.notes_field') }}</Label>
                    <textarea v-model="form.notes" :placeholder="__('paiements.notes_placeholder')" rows="2"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-rdc-blue"></textarea>
                </div>

                <DialogFooter class="gap-3">
                    <Button type="button" variant="ghost" @click="isFormModalOpen = false" class="rounded-lg">{{ __('common.cancel') }}</Button>
                    <Button type="submit" :disabled="form.processing" class="h-10 px-6 rounded-lg bg-rdc-blue text-white font-bold gap-2">
                        <Printer class="size-4" />
                        {{ __('taxes.paiements.print') }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <!-- Delete Modal -->
    <Dialog v-model:open="isDeleteModalOpen">
        <DialogContent class="max-w-md p-8 rounded-xl border-none shadow-2xl text-center">
            <DialogHeader class="mb-6 flex flex-col items-center">
                <div class="size-16 rounded-full bg-rdc-red/10 flex items-center justify-center text-rdc-red mb-4">
                    <Trash2 class="size-8" />
                </div>
                <DialogTitle class="text-xl font-black">{{ __('paiements.delete_title') }}</DialogTitle>
                <DialogDescription class="pt-2">
                    {{ __('paiements.delete_confirm', { reference: selectedPaiement?.reference || '' }) }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="flex gap-3">
                <Button variant="ghost" @click="isDeleteModalOpen = false" class="flex-1 rounded-lg h-10">{{ __('common.keep') }}</Button>
                <Button @click="submitDelete" class="flex-1 h-10 rounded-lg bg-rdc-red text-white font-black">{{ __('common.confirm') }}</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
