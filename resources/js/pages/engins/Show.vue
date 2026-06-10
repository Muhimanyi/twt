<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Printer, Download, ArrowLeft, User, Car, Anchor, FileText, Calendar, MapPin, Hash, Phone, Mail, Activity } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const props = defineProps<{
    engin: any;
}>();

const printDocument = () => {
    window.print();
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Recensement', href: '#' },
            { title: 'Engins', href: '/engins' },
            { title: 'Détails Engin', href: '#' }
        ],
    },
});
</script>

<template>

    <Head :title="__('engins.details_page') + ' - ' + (engin.identification_number || '')" />

    <!-- DASHBOARD VIEW (Screen only) -->
    <div class="print:hidden p-4">
        <!-- Header Actions -->
        <div class="flex items-center justify-between">
            <Button variant="ghost" as-child class="gap-2 text-slate-500 hover:text-slate-900">
                <Link href="/engins">
                    <ArrowLeft class="w-4 h-4" /> {{ __('engins.back') }}
                </Link>
            </Button>
            <div class="flex gap-3">
                <Button @click="printDocument" variant="outline" class="gap-2 bg-white text-slate-700 shadow-sm">
                    <Download class="w-4 h-4" /> {{ __('engins.export_pdf') }}
                </Button>
                <Button @click="printDocument" class="gap-2 bg-rdc-blue text-white hover:bg-rdc-blue/90 shadow-md">
                    <Printer class="w-4 h-4" /> {{ __('engins.print_card') }}
                </Button>
            </div>
        </div>

        <!-- Header Profile -->
        <div class="bg-white rounded-2xl p-3 shadow-sm border border-slate-200 flex items-center gap-6">
            <div
                class="h-20 w-20 rounded-2xl bg-slate-50 flex items-center justify-center text-rdc-blue border border-slate-100">
                <Car v-if="engin.category === 'roulant'" class="w-10 h-10" />
                <Anchor v-else class="w-10 h-10" />
            </div>
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h1 class="text-2xl font-black text-slate-900 uppercase">
                        {{ engin.category === 'roulant' ? engin.plate_number || engin.vehicle_brand :
                            engin.vessel_name || engin.registration_number }}
                    </h1>
                    <span
                        class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
                        {{ engin.category }} {{ engin.sub_category ? ' - ' + engin.sub_category : '' }}
                    </span>
                </div>
                <p class="text-slate-500 text-sm font-medium flex items-center gap-2">
                    <Hash class="w-4 h-4" /> ID: {{ engin.identification_number }}
                </p>
            </div>
        </div>

        <!-- Grid Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Propriétaire -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                    <div class="p-2 bg-blue-100 text-blue-700 rounded-lg">
                        <User class="w-5 h-5" />
                    </div>
                    <h2 class="font-bold text-slate-800">{{ __('engins.owner') }}</h2>
                </div>
                <div class="p-5 space-y-4 text-sm">
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">{{ __('engins.form.full_name') }}</span>
                        <span class="font-bold text-slate-900 uppercase">{{ engin.owner_name }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">{{ __('engins.contact') }}</span>
                        <span class="font-bold text-slate-900">{{ engin.owner_phone }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">{{ __('common.email') }}</span>
                        <span class="font-bold text-slate-900">{{ engin.owner_email || '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">{{ __('common.address') }}</span>
                        <span class="font-bold text-slate-900">{{ engin.owner_address }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-500 font-medium">{{ __('engins.nationality') }}</span>
                        <span class="font-bold text-slate-900 uppercase">{{ engin.owner_nationality }}</span>
                    </div>
                </div>
            </div>

            <!-- Détails Engin -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 text-indigo-700 rounded-lg">
                        <Activity class="w-5 h-5" />
                    </div>
                    <h2 class="font-bold text-slate-800">{{ __('engins.technical_specs') }}</h2>
                </div>

                <div class="p-5 space-y-4 text-sm" v-if="engin.category === 'roulant'">
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">{{ __('engins.brand_model') }}</span>
                        <span class="font-bold text-slate-900 uppercase">{{ engin.vehicle_brand }} {{
                            engin.vehicle_type }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">{{ __('engins.chassis_number') }}</span>
                        <span class="font-bold text-slate-900 uppercase">{{ engin.chassis_number || '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">{{ __('engins.engine_number') }}</span>
                        <span class="font-bold text-slate-900 uppercase">{{ engin.engine_number || '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">{{ __('engins.manufacture_year_short') }}</span>
                        <span class="font-bold text-slate-900">{{ engin.manufacture_year || '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-500 font-medium">{{ __('engins.power') }}</span>
                        <span class="font-bold text-slate-900">{{ engin.horsepower ? engin.horsepower + ' CV' : '-'
                            }}</span>
                    </div>
                </div>

                <div class="p-5 space-y-4 text-sm" v-if="engin.category === 'flottant'">
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">{{ __('engins.type_material') }}</span>
                        <span class="font-bold text-slate-900 uppercase">{{ engin.construction_type || '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">{{ __('engins.dimensions') }}</span>
                        <span class="font-bold text-slate-900">{{ engin.length || 0 }}m x {{ engin.width || 0 }}m x
                            {{ engin.height || 0 }}m</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">{{ __('engins.capacity') }}</span>
                        <span class="font-bold text-slate-900">{{ engin.capacity || '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">{{ __('engins.propulsion') }}</span>
                        <span class="font-bold text-slate-900">{{ engin.propulsion_type || '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-500 font-medium">{{ __('engins.power') }}</span>
                        <span class="font-bold text-slate-900">{{ engin.horsepower ? engin.horsepower + ' CV' : '-'
                            }}</span>
                    </div>
                </div>
            </div>

            <!-- Administration -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden md:col-span-2">
                <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 text-emerald-700 rounded-lg">
                        <FileText class="w-5 h-5" />
                    </div>
                    <h2 class="font-bold text-slate-800">{{ __('engins.admin_data') }}</h2>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                    <div class="flex items-start gap-4 p-4 rounded-xl border border-slate-100 bg-slate-50/50">
                        <div class="p-2 bg-white rounded-lg shadow-sm">
                            <Hash class="w-5 h-5 text-slate-500" />
                        </div>
                        <div>
                            <p class="text-slate-500 font-medium text-xs mb-1">{{ __('engins.id_number') }}</p>
                            <p class="font-bold text-slate-900">{{ engin.identification_number }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-xl border border-slate-100 bg-slate-50/50">
                        <div class="p-2 bg-white rounded-lg shadow-sm">
                            <MapPin class="w-5 h-5 text-slate-500" />
                        </div>
                        <div>
                            <p class="text-slate-500 font-medium text-xs mb-1">{{ __('engins.identification_place') }}</p>
                            <p class="font-bold text-slate-900 uppercase">{{ engin.identification_place }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-xl border border-slate-100 bg-slate-50/50">
                        <div class="p-2 bg-white rounded-lg shadow-sm">
                            <Calendar class="w-5 h-5 text-slate-500" />
                        </div>
                        <div>
                            <p class="text-slate-500 font-medium text-xs mb-1">{{ __('engins.identification_date') }}</p>
                            <p class="font-bold text-slate-900">
                                {{ engin.identification_date ? new
                                    Date(engin.identification_date).toLocaleDateString('fr-FR', {
                                        year: 'numeric',
                                        month: 'long', day: 'numeric'
                                    }) : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PRINTABLE VIEW (Hidden on screen, block on print) -->
    <div id="printable-area"
        class="hidden print:block print:absolute print:inset-0 print:m-0 print:p-0 print:w-full print:h-[100vh] bg-white print:z-[9999]">
        <!-- Header -->
        <img src="/maritime.png" v-if="engin.category == 'flottant'"
            class="absolute inset-0 w-[210mm] h-[297mm] object-cover -z-10" alt="">
        <img src="/routiere.png" v-if="engin.category == 'roulant'"
            class="absolute inset-0 w-[210mm] h-[297mm] object-cover -z-10" alt="">
        <div class="block absolute top-60 w-full">
            <div class="font-bold text-3xl text-center" style="font-family: 'Bernard MT Condensed', sans-serif;">
                <p v-if="engin.category === 'roulant'">
                    {{ __('engins.print.roulant_title', { sub_category: engin.sub_category === 'sans_moteur' ? __('engins.print.without') :
                        __('engins.print.with') }) }}
                </p>
                <p v-else-if="engin.category === 'flottant'">
                    {{ __('engins.print.flottant_title', { construction_type: engin.construction_type === 'bois' ? __('engins.print.wood') :
                        __('engins.print.steel') }) }}
                </p>
                <p>*******************************************</p>
            </div>
            <div class="p-20">
                <div class="border-4 border-dashed border-black p-4 mx-auto space-y-6">
                    <!-- Propriétaire Info -->
                    <div class="block">
                        <h1 class="text-sm text-blue-800 font-bold mb-2" style="letter-spacing: -0.5px;">{{ __('engins.print.owner_info').toUpperCase() }}</h1>
                        <div class="text-sm space-y-2">
                            <div class="flex w-full gap-2">
                                <div class="flex w-4/5">
                                    <p class="min-w-[60px]">{{ __('common.name') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        engin.owner_name }}</p>
                                </div>
                                <div class="flex w-1/5">
                                    <p>{{ __('engins.form.gender') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                        {{ engin.owner_gender }}</p>
                                </div>
                            </div>

                            <div class="flex w-full gap-2">
                                <div class="flex w-full">
                                    <p class="min-w-[180px]">{{ __('engins.print.birth_place_date') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        engin.owner_birth_place }}</p>
                                </div>
                            </div>

                            <div class="flex w-full gap-2">
                                <p class="min-w-[160px]">{{ __('engins.form.father_name') }}:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    engin.owner_father_name }}</p>
                            </div>

                            <div class="flex w-full gap-2">
                                <p class="min-w-[160px]">{{ __('engins.form.mother_name') }}:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    engin.owner_mother_name }}</p>
                            </div>

                            <div class="flex w-full gap-4">
                                <div class="flex w-1/2">
                                    <p class="min-w-[80px]">{{ __('engins.form.marital_status') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        engin.owner_marital_status }}</p>
                                </div>
                                <div class="flex w-1/2">
                                    <p class="min-w-[100px]">{{ __('engins.form.nationality') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                        {{ engin.owner_nationality }}</p>
                                </div>
                            </div>

                            <div class="flex w-full gap-4">
                                <div class="flex w-1/2">
                                    <p class="min-w-[80px]">{{ __('engins.contact') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        engin.owner_phone }}</p>
                                </div>
                                <div class="flex w-1/2">
                                    <p class="min-w-[80px]">{{ __('common.email') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full text-center">{{
                                        engin.owner_email || __('common.na') }}</p>
                                </div>
                            </div>

                            <div class="flex w-full gap-2">
                                <p class="min-w-[160px]">{{ __('engins.address') }}:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    engin.owner_address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Engin Info -->
                    <div class="block">
                        <h1 class="text-sm text-blue-800 font-bold mb-2" style="letter-spacing: -0.5px;">
                            {{ __('engins.print.engine_info').toUpperCase() }}
                        </h1>

                        <!-- Roulant -->
                        <div class="text-sm space-y-2" v-if="engin.category === 'roulant'">
                            <div class="flex w-full gap-4">
                                <div class="flex w-2/3">
                                    <p class="min-w-[160px]">{{ __('engins.print.vehicle_genre') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        engin.vehicle_genre }}</p>
                                </div>
                                <div class="flex w-1/3">
                                    <p class="min-w-[60px]">{{ __('engins.form.brand') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                        {{ engin.vehicle_brand }}</p>
                                </div>
                            </div>

                            <div class="flex w-full gap-4">
                                <div class="flex w-1/2">
                                    <p class="min-w-[80px]">{{ __('engins.form.type_model') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        engin.vehicle_type }}</p>
                                </div>
                                <div class="flex w-1/2">
                                    <p class="min-w-[80px]">{{ __('engins.form.color') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                        {{ engin.vehicle_color }}</p>
                                </div>
                            </div>

                            <div class="flex w-full gap-4 items-center">
                                <p class="min-w-[60px]">{{ __('engins.form.usage') }}:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">
                                    {{ Array.isArray(engin.usage) ? engin.usage.join(' - ') : (engin.usage || __('common.na')) }}
                                </p>
                            </div>

                            <div class="flex w-full gap-2">
                                <p class="min-w-[200px]">{{ __('engins.print.plate_number') }}:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    engin.plate_number }}</p>
                            </div>

                            <div class="flex w-full gap-4" v-if="engin.sub_category === 'avec moteur'">
                                <div class="flex w-1/2">
                                    <p class="min-w-[120px]">{{ __('engins.form.engine_number') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        engin.engine_number }}</p>
                                </div>
                                <div class="flex w-1/2">
                                    <p class="min-w-[120px]">{{ __('engins.form.engine_brand') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                        {{ engin.engine_brand }}</p>
                                </div>
                            </div>

                            <div class="flex w-full gap-4">
                                <div class="flex w-1/2">
                                    <p class="min-w-[120px]">{{ __('engins.form.chassis_number') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full uppercase text-center">
                                        {{ engin.chassis_number }}</p>
                                </div>
                                <div class="flex w-1/2">
                                    <p class="min-w-[160px]">{{ __('engins.print.manufacture_year') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full uppercase text-center">
                                        {{ engin.manufacture_year }}</p>
                                </div>
                            </div>

                            <div class="flex w-full gap-4" v-if="engin.sub_category === 'avec moteur'">
                                <div class="flex w-1/2">
                                    <p class="min-w-[100px]">{{ __('engins.print.horsepower') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full uppercase text-center">
                                        {{ engin.horsepower }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Flottant -->
                        <div class="text-sm space-y-2" v-if="engin.category === 'flottant'">
                            <div class="flex w-full gap-4">
                                <div class="flex w-1/2">
                                    <p class="min-w-[160px]">{{ __('engins.print.vessel_name') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        engin.vessel_name }}</p>
                                </div>
                                <div class="flex w-1/2">
                                    <p class="min-w-[160px]">{{ __('engins.print.construction_type') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                        {{ engin.construction_type }}</p>
                                </div>
                            </div>

                            <div class="flex w-full gap-4">
                                <div class="flex w-1/2">
                                    <p class="min-w-[160px]">{{ __('engins.print.registration_place') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        engin.registration_place }}</p>
                                </div>
                                <div class="flex w-1/2">
                                    <p class="min-w-[160px]">{{ __('engins.print.registration_date') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                        {{ engin.registration_date ? new
                                            Date(engin.registration_date).toLocaleDateString()
                                            : __('common.na') }}</p>
                                </div>
                            </div>

                            <div class="flex w-full gap-2">
                                <p class="min-w-[200px]">{{ __('engins.print.registration_number') }}:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    engin.registration_number }}</p>
                            </div>

                            <div class="flex w-full gap-4">
                                <div class="flex w-1/3">
                                    <p class="min-w-[80px]">{{ __('engins.form.length') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full uppercase text-center">
                                        {{ engin.length }}</p>
                                </div>
                                <div class="flex w-1/3">
                                    <p class="min-w-[80px]">{{ __('engins.form.width') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                        {{ engin.width }}</p>
                                </div>
                                <div class="flex w-1/3">
                                    <p class="min-w-[80px]">{{ __('engins.form.height') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                        {{ engin.height }}</p>
                                </div>
                            </div>

                            <div class="flex w-full gap-4">
                                <div class="flex w-1/2">
                                    <p class="min-w-[80px]">{{ __('engins.form.capacity') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        engin.capacity }}</p>
                                </div>
                                <div class="flex w-1/2">
                                    <p class="min-w-[140px]">{{ __('engins.form.navigation_line') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                        {{ engin.navigation_line }}</p>
                                </div>
                            </div>

                            <div class="flex w-full gap-4" v-if="engin.sub_category === 'avec moteur'">
                                <div class="flex w-1/2">
                                    <p class="min-w-[140px]">{{ __('engins.form.propulsion_type') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        engin.propulsion_type }}</p>
                                </div>
                                <div class="flex w-1/2">
                                    <p class="min-w-[120px]">{{ __('engins.print.horsepower') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                        {{ engin.horsepower }}</p>
                                </div>
                            </div>

                            <div class="flex w-full gap-4">
                                <div class="flex w-1/2">
                                    <p class="min-w-[140px]">{{ __('engins.print.crew_count') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full uppercase text-center">
                                        {{ engin.crew_count }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Info -->
                    <div class="block">
                        <h1 class="text-sm text-blue-800 font-bold mb-2" style="letter-spacing: -0.5px;">
                            {{ __('engins.print.admin_section').toUpperCase() }}
                        </h1>
                        <div class="text-sm space-y-2">
                            <div class="flex w-full gap-2">
                                <p class="min-w-[260px]">{{ __('engins.print.identification_number') }}:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    engin.identification_number }}</p>
                            </div>
                            <div class="flex w-full gap-4">
                                <div class="flex w-3/5">
                                    <p class="min-w-[200px]">{{ __('engins.print.identification_place_date') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        engin.identification_place }}</p>
                                </div>
                                <div class="flex w-2/5 gap-2">
                                    <p>{{ __('engins.print.on_date') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                        {{ engin.identification_date ? new
                                            Date(engin.identification_date).toLocaleDateString() : __('common.na') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-red-600 text-xs mt-4">
                    <h1 class="font-bold mb-1" style="letter-spacing: -0.5px;">{{ __('engins.print.important_notice').toUpperCase() }}</h1>
                    <p>{{ __('engins.print.transfer_notice') }}</p>
                </div>

                <div class="w-full flex justify-between text-sm mt-8 px-4">
                    <div class="text-center">
                        <p class="font-bold">{{ __('engins.form.owner_signature') }}</p>
                        <p class="text-xs text-gray-600">{{ __('engins.form.name_signature') }}</p>
                    </div>
                    <div class="text-center">
                        <p>{{ __('engins.print.done_at') }} {{ engin.identification_place || '_________________' }}, {{ __('engins.print.on_date_short') }} {{
                            engin.identification_date ?
                                new Date(engin.identification_date).toLocaleDateString() : '_________________' }}</p>
                        <p class="font-bold mt-8">{{ __('engins.print.for_division') }}</p>
                        <p class="text-[10px] text-gray-600">{{ __('engins.print.delegue_signature') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@import url('https://fonts.cdnfonts.com/css/bernard-mt-condensed');

@media print {
    @page {
        size: A4 portrait;
        margin: 0 !important;
        padding: 0 !important;
    }

    html,
    body {
        width: 210mm !important;
        height: 297mm !important;
        margin: 0 !important;
        padding: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        background-color: white !important;
        overflow: hidden !important;
    }

    body * {
        visibility: hidden;
    }

    #printable-area,
    #printable-area * {
        visibility: visible;
    }

    #printable-area {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }

    .no-print {
        display: none !important;
    }
}
</style>
