<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Plus, Search, Car, Anchor, Plane, Activity,
    Trash2, Pencil, Globe, UserCircle, MapPin, Settings, CreditCard, ShieldCheck
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
import DriverForm from '@/components/conducteurs/DriverForm.vue';
import { usePermissions } from '@/composables/usePermissions';
import { useTrans } from '@/composables/useTrans';

const { can } = usePermissions();
const { __ } = useTrans();

const props = defineProps<{
    conducteurs: Array<any>;
    provinces: Array<any>;
    currentSector: string | null;
    currentProvinceId: string | null;
    stats: {
        total: number;
        by_sector: Record<string, number>;
        by_province: Record<number, number>;
    };
}>();

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedConducteur = ref<any>(null);
const searchQuery = ref('');

const sectors = [
    { value: 'routier', label: __('conducteurs.sector.routier'), icon: Car },
    { value: 'lacustre', label: __('conducteurs.sector.lacustre'), icon: Anchor },
    { value: 'aerien', label: __('conducteurs.sector.aerien'), icon: Plane },
    { value: 'ferroviaire', label: __('conducteurs.sector.ferroviaire'), icon: Activity },
];

const form = useForm({
    sector: 'routier',
    province_id: '' as any,
    engin_id: null as any,
    engin_id_number: '', // For search
    name: '',
    gender: 'M',
    birth_place: '',
    birth_date: '',
    father_name: '',
    mother_name: '',
    marital_status: 'Célibataire',
    nationality: 'Congolaise',
    profession: '',
    license_number: '',
    license_category: '',
    license_issued_at: '',
    license_expires_at: '',
    id_piece_type: 'Carte d\'Electeur',
    id_piece_number: '',
    id_piece_issued_place: '',
    id_piece_issued_at: '',
    migratory_document_type: '',
    visa_type: '',
    phone: '',
    email: '',
    address: '',
    affiliation: '',
    transport_mode: 'Diurne',
    expiration_date: '',
    photo: null as any,
    existing_photo_url: null as string | null,
    // Engine pre-fill fields (just for UI, not all saved in conducteurs table if we rely on engin_id)
    engine_type: '',
    engine_brand: '',
    engine_chassis: '',
    engine_number: '',
    engine_year: '',
    engine_color: '',
    owner_name: '',
    owner_phone: '',
    owner_email: '',
    owner_address: '',
    usage: 'Personnel',
});

const groupBy = ref<'none' | 'province' | 'sector'>('none');

const groupedConducteurs = computed(() => {
    const filtered = props.conducteurs.filter(c =>
        c.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        c.identification_number.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        c.license_number?.toLowerCase().includes(searchQuery.value.toLowerCase())
    );

    if (groupBy.value === 'none') return { [__('conducteurs.all')]: filtered };

    const groups: Record<string, any[]> = {};
    filtered.forEach(c => {
        let key = '';
        if (groupBy.value === 'province') {
            key = c.province?.name || __('conducteurs.unknown_province');
        } else if (groupBy.value === 'sector') {
            key = sectors.find(s => s.value === c.sector)?.label || __('conducteurs.unknown_sector');
        }

        if (!groups[key]) groups[key] = [];
        groups[key].push(c);
    });

    return groups;
});

const submitCreate = () => {
    form.post('/conducteurs', {
        onSuccess: () => {
            isCreateModalOpen.value = false;
            form.reset();
        },
    });
};

const openCreateModal = () => {
    form.reset();
    form.existing_photo_url = null;
    if (props.currentSector) form.sector = props.currentSector;
    if (props.currentProvinceId) form.province_id = String(props.currentProvinceId);
    isCreateModalOpen.value = true;
};

const openEditModal = (conducteur: any) => {
    selectedConducteur.value = conducteur;
    form.clearErrors();
    form.sector = conducteur.sector;
    form.province_id = String(conducteur.province_id);
    form.engin_id = conducteur.engin_id;
    form.engin_id_number = conducteur.engin?.identification_number || '';
    form.name = conducteur.name;
    form.gender = conducteur.gender;
    form.birth_place = conducteur.birth_place;
    form.birth_date = conducteur.birth_date?.split('T')[0];
    form.father_name = conducteur.father_name;
    form.mother_name = conducteur.mother_name;
    form.marital_status = conducteur.marital_status;
    form.nationality = conducteur.nationality;
    form.profession = conducteur.profession;
    form.license_number = conducteur.license_number;
    form.license_category = conducteur.license_category;
    form.license_issued_at = conducteur.license_issued_at?.split('T')[0];
    form.license_expires_at = conducteur.license_expires_at?.split('T')[0];
    form.id_piece_type = conducteur.id_piece_type;
    form.id_piece_number = conducteur.id_piece_number;
    form.id_piece_issued_place = conducteur.id_piece_issued_place;
    form.id_piece_issued_at = conducteur.id_piece_issued_at?.split('T')[0];
    form.migratory_document_type = conducteur.migratory_document_type;
    form.visa_type = conducteur.visa_type;
    form.phone = conducteur.phone;
    form.email = conducteur.email;
    form.address = conducteur.address;
    form.affiliation = conducteur.affiliation;
    form.transport_mode = conducteur.transport_mode;
    form.expiration_date = conducteur.expiration_date?.split('T')[0];
    form.photo = null;
    form.existing_photo_url = conducteur.photo_url || null;

    // Pre-fill engine info from relation
    if (conducteur.engin) {
        form.engine_type = conducteur.engin.vehicle_genre;
        form.engine_brand = conducteur.engin.vehicle_brand;
        form.engine_chassis = conducteur.engin.chassis_number;
        form.engine_number = conducteur.engin.engine_number;
        form.engine_year = conducteur.engin.manufacture_year;
        form.engine_color = conducteur.engin.vehicle_color;
        form.owner_name = conducteur.engin.owner_name;
        form.owner_phone = conducteur.engin.owner_phone;
        form.owner_email = conducteur.engin.owner_email;
        form.owner_address = conducteur.engin.owner_address;
    }

    isEditModalOpen.value = true;
};

const submitEdit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(`/conducteurs/${selectedConducteur.value.id}`, {
        forceFormData: true,
        onSuccess: () => {
            isEditModalOpen.value = false;
            form.reset();
        },
    });
};

const openDeleteModal = (conducteur: any) => {
    selectedConducteur.value = conducteur;
    isDeleteModalOpen.value = true;
};

const submitDelete = () => {
    router.delete(`/conducteurs/${selectedConducteur.value.id}`, {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
};

const filterByProvince = (id: any) => {
    router.visit('/conducteurs', {
        data: {
            province_id: id === 'all' ? null : id,
            sector: props.currentSector
        },
        preserveState: true
    });
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Recensement', href: '#' },
            { title: 'Conducteurs', href: '/conducteurs' },
        ],
    },
});
</script>

<template>

    <Head :title="__('conducteurs.page_title')" />

    <div class="space-y-8 p-6">
        <!-- Header Section -->
        <div
            class="items-end justify-between bg-white/95 text-slate-900 dark:bg-slate-900/95 dark:text-slate-100 backdrop-blur-sm p-6 rounded-xl shadow-lg shadow-slate-200/40 dark:shadow-black/20 border border-slate-200/70 dark:border-slate-800/80">
            <div class="space-y-1">
                <div class="flex items-center gap-3 text-rdc-blue mb-1">
                    <CreditCard class="size-5" />
                    <span class="text-[10px] font-black uppercase tracking-[0.3em]">{{ __('common.transport_module') }}</span>
                </div>
                <h1 class="text-4xl font-black tracking-tighter w-full">
                    {{ __('conducteurs.page_title') }}
                </h1>
                <p class="text-sm text-slate-400 font-medium tracking-tight">{{ __('conducteurs.page_subtitle') }}</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 mr-4">
                    <Label class="text-[10px] font-black uppercase text-slate-400 whitespace-nowrap">{{ __('common.province') }}:</Label>
                    <Select :model-value="String(currentProvinceId || 'all')" @update:model-value="filterByProvince">
                        <SelectTrigger class="w-40 h-10 rounded-xl bg-slate-50 border-none">
                            <SelectValue :placeholder="__('conducteurs.filter.all_provinces')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ __('conducteurs.filter.all_provinces') }}</SelectItem>
                            <SelectItem v-for="prov in provinces" :key="prov.id" :value="String(prov.id)">{{ prov.name
                                }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="flex items-center gap-2 mr-4">
                    <Label class="text-[10px] font-black uppercase text-slate-400 whitespace-nowrap">{{ __('common.group_by') }}:</Label>
                    <Select v-model="groupBy">
                        <SelectTrigger class="w-32 h-10 rounded-xl bg-slate-50 border-none">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="none">{{ __('common.none') }}</SelectItem>
                            <SelectItem value="province">{{ __('common.province') }}</SelectItem>
                            <SelectItem value="sector">{{ __('common.sector') }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="relative w-64">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                    <Input v-model="searchQuery" :placeholder="__('conducteurs.search_placeholder')"
                        class="pl-10 h-12 rounded-xl bg-slate-50 border-none dark:bg-slate-900" />
                </div>
                <Button v-if="can('conducteurs.create')" @click="openCreateModal"
                    class="h-12 px-6 rounded-xl bg-rdc-blue text-white shadow-lg shadow-rdc-blue/20 hover:bg-rdc-blue/90 transition-all font-bold gap-3">
                    <Plus class="size-5" />
                    {{ __('conducteurs.create') }}
                </Button>
            </div>
        </div>

        <!-- Sector Stats -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div @click="router.visit('/conducteurs')"
                class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:bg-slate-950 dark:border-slate-800 cursor-pointer hover:border-rdc-blue transition-all"
                :class="{ 'ring-2 ring-rdc-blue/50 bg-rdc-blue/5': !currentSector }">
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">{{ __('common.total') }}</p>
                <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ stats.total }}</p>
            </div>
            <div v-for="sec in sectors" :key="sec.value" @click="router.visit(`/conducteurs/sector/${sec.value}`)"
                class="p-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:bg-slate-950 dark:border-slate-800 cursor-pointer hover:border-rdc-blue transition-all"
                :class="{ 'ring-2 ring-rdc-blue/50 bg-rdc-blue/5': currentSector === sec.value }">
                <div class="flex items-center gap-2 mb-1">
                    <component :is="sec.icon" class="size-3 text-slate-400" />
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">{{ sec.label }}</p>
                </div>
                <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ stats.by_sector[sec.value] || 0
                }}</p>
            </div>
        </div>

        <!-- List View -->
        <div
            class="bg-white shadow-xl shadow-slate-200/50 overflow-hidden rounded-xl border border-slate-200/70 dark:bg-slate-950 dark:border-slate-800/80">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-slate-900/90 border-b border-slate-100 dark:border-slate-800">
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                {{ __('conducteurs.table.id') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                {{ __('conducteurs.table.identity') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                {{ __('conducteurs.table.license_id') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                {{ __('conducteurs.table.engine') }}</th>
                            <th
                                class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                {{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody v-for="(group, name) in groupedConducteurs" :key="name"
                        class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-if="groupBy !== 'none'" class="bg-slate-50/50 dark:bg-slate-900/50">
                            <td colspan="5" class="px-6 py-2">
                                <div class="flex items-center gap-2">
                                    <div class="size-1.5 rounded-full bg-rdc-red"></div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{
                                        name }}</span>
                                    <span class="text-[10px] font-bold text-slate-300 ml-auto">{{ group.length }} {{ __('conducteurs.table.drivers_count') }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="cond in group" :key="cond.id"
                            class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/60 transition-all">
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-rdc-blue font-mono">{{
                                        cond.identification_number }}</span>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <Badge variant="outline" class="text-[8px] h-4 w-fit uppercase font-bold">{{
                                            cond.sector }}</Badge>
                                        <Badge variant="secondary"
                                            class="text-[8px] h-4 w-fit uppercase font-bold bg-slate-100 dark:bg-slate-800">
                                            {{ cond.province?.name }}</Badge>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="size-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 dark:bg-slate-800 overflow-hidden">
                                        <img v-if="cond.photo_url" :src="cond.photo_url"
                                            class="w-full h-full object-cover" />
                                        <UserCircle v-else class="size-5" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{
                                            cond.name }}</span>
                                        <span class="text-[10px] text-slate-400 font-medium">{{ cond.phone }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ __('conducteurs.table.license_label') }}: {{
                                        cond.license_number || 'N/A' }}</span>
                                    <span
                                        class="text-[10px] text-slate-400 uppercase font-black tracking-tight mt-0.5">{{
                                            cond.id_piece_type }}: {{ cond.id_piece_number }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div v-if="cond.engin" class="flex items-center gap-3">
                                    <div
                                        class="size-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400">
                                        <Car v-if="cond.engin.category === 'roulant'" class="size-4" />
                                        <Anchor v-else class="size-4" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[10px] font-black uppercase text-slate-900 dark:text-slate-100">{{
                                                cond.engin.vehicle_brand }} {{ cond.engin.vehicle_type }}</span>
                                        <span class="text-[9px] text-rdc-blue font-bold font-mono">{{
                                            cond.engin.identification_number }}</span>
                                    </div>
                                </div>
                                <span v-else class="text-[10px] text-slate-400 font-bold italic">{{ __('conducteurs.no_engine') }}</span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <Button size="icon" variant="ghost" as-child
                                        class="size-8 rounded-lg hover:bg-slate-100">
                                        <Link :href="`/conducteurs/${cond.id}`">
                                            <Globe class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button v-if="can('conducteurs.edit')" size="icon" variant="ghost" @click="openEditModal(cond)"
                                        class="size-8 rounded-lg hover:bg-rdc-blue/10 hover:text-rdc-blue">
                                        <Pencil class="size-4" />
                                    </Button>
                                    <Button v-if="can('conducteurs.delete')" size="icon" variant="ghost" @click="openDeleteModal(cond)"
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

    <!-- Create Modal -->
    <Dialog v-model:open="isCreateModalOpen">
        <DialogContent class="max-w-7xl p-0 rounded-xl overflow-hidden border-none shadow-2xl">
            <div class="bg-white dark:bg-slate-950 h-[85vh] overflow-y-auto custom-scrollbar">
                <DialogHeader class="p-8 pb-0">
                    <DialogTitle class="text-2xl font-black tracking-tighter">{{ __('conducteurs.create_title') }}</DialogTitle>
                    <DialogDescription>{{ __('conducteurs.create_description') }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitCreate" class="p-8 pt-6 space-y-8">
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 dark:bg-slate-900/50 p-6 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <div class="space-y-1.5">
                            <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">{{ __('conducteurs.form.transport_sector') }}</Label>
                            <Select v-model="form.sector">
                                <SelectTrigger class="rounded-xl h-11 bg-white">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="sec in sectors" :key="sec.value" :value="sec.value">{{
                                        sec.label }}</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="space-y-1.5">
                            <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">{{ __('conducteurs.form.service_province') }}</Label>
                            <Select v-model="form.province_id">
                                <SelectTrigger class="rounded-xl h-11 bg-white">
                                    <SelectValue :placeholder="__('common.select')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="prov in provinces" :key="prov.id" :value="String(prov.id)">{{
                                        prov.name }}</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>

                    <DriverForm :form="form" />

                    <!-- Admin Section -->
                    <div class="space-y-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2 text-slate-400">
                            <ShieldCheck class="size-4" />
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em]">{{ __('conducteurs.form.admin_validation') }}
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">{{ __('conducteurs.form.id_expiration') }}</Label>
                                <Input v-model="form.expiration_date" type="date" class="rounded-xl h-11" />
                            </div>
                        </div>
                    </div>

                    <DialogFooter class="pt-8 border-t border-slate-100 dark:border-slate-800">
                        <Button type="button" variant="ghost" @click="isCreateModalOpen = false"
                            class="rounded-xl">{{ __('common.cancel') }}</Button>
                        <Button type="submit" :disabled="form.processing"
                            class="h-12 px-10 rounded-xl bg-rdc-blue text-white shadow-xl shadow-rdc-blue/20 font-black">{{ __('conducteurs.form.validate') }}</Button>
                    </DialogFooter>
                </form>
            </div>
        </DialogContent>
    </Dialog>

    <!-- Edit Modal -->
    <Dialog v-model:open="isEditModalOpen">
        <DialogContent class="max-w-7xl p-0 rounded-xl overflow-hidden border-none shadow-2xl">
            <div class="bg-white dark:bg-slate-950 h-[85vh] overflow-y-auto custom-scrollbar">
                <DialogHeader class="p-8 pb-0">
                    <DialogTitle class="text-2xl font-black tracking-tighter">{{ __('conducteurs.edit') }}</DialogTitle>
                    <DialogDescription>{{ __('conducteurs.edit_description') }} {{
                        selectedConducteur?.identification_number }}</DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitEdit" class="p-8 pt-6 space-y-8">
                    <DriverForm :form="form" />

                    <DialogFooter class="pt-8 border-t border-slate-100 dark:border-slate-800">
                        <Button type="button" variant="ghost" @click="isEditModalOpen = false"
                            class="rounded-xl">{{ __('common.cancel') }}</Button>
                        <Button type="submit" :disabled="form.processing"
                            class="h-12 px-10 rounded-xl bg-slate-900 text-white shadow-lg font-black">{{ __('conducteurs.save_changes') }}</Button>
                    </DialogFooter>
                </form>
            </div>
        </DialogContent>
    </Dialog>

    <!-- Delete Confirmation -->
    <Dialog v-model:open="isDeleteModalOpen">
        <DialogContent class="max-w-md p-8 rounded-xl border-none shadow-2xl text-center">
            <DialogHeader class="mb-6 flex flex-col items-center">
                <div class="size-16 rounded-full bg-rdc-red/10 flex items-center justify-center text-rdc-red mb-4">
                    <Trash2 class="size-8" />
                </div>
                <DialogTitle class="text-xl font-black text-slate-900 tracking-tight">{{ __('conducteurs.delete') }}
                </DialogTitle>
                <DialogDescription class="pt-2">{{ __('conducteurs.delete_confirm') }} {{
                    selectedConducteur?.name }} ?</DialogDescription>
            </DialogHeader>
            <DialogFooter class="flex flex-col sm:flex-row gap-3">
                <Button variant="ghost" @click="isDeleteModalOpen = false"
                    class="flex-1 rounded-xl h-12">{{ __('common.keep') }}</Button>
                <Button @click="submitDelete"
                    class="flex-1 h-12 rounded-xl bg-rdc-red text-white font-black">{{ __('common.delete') }}</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
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
</style>
