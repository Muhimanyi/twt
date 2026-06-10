<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ChevronLeft, User, CreditCard, Phone, Mail, MapPin,
    Calendar, Globe, ShieldCheck, Car, Anchor, FileText, UserCheck,
    Settings, Info, ExternalLink, Printer, Download, Activity
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const props = defineProps<{
    conducteur: any;
}>();

const formatDate = (date: string) => {
    if (!date) return __('common.na');
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const printDocument = () => {
    window.print();
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Recensement', href: '#' },
            { title: 'Conducteurs', href: '/conducteurs' },
            { title: 'Détails Conducteur', href: '#' }
        ],
    },
});
</script>

<template>

    <Head :title="`${__('conducteurs.details')}: ${conducteur.name}`" />

    <div class="p-8 max-w-7xl mx-auto space-y-8 print:hidden">
        <!-- Navigation & Actions -->
        <div class="flex items-center justify-between">
            <Link href="/conducteurs"
                class="flex items-center gap-2 text-slate-400 hover:text-rdc-blue transition-all group">
                <ChevronLeft class="size-4 group-hover:-translate-x-1 transition-transform" />
                <span class="text-xs font-black uppercase tracking-widest">{{ __('conducteurs.back') }}</span>
            </Link>
            <div class="flex gap-3">
                <Button @click="printDocument" variant="outline"
                    class="rounded-xl gap-2 font-bold h-10 border-slate-200">
                    <Printer class="size-4" /> {{ __('tools.print_sheet') }}
                </Button>
                <Button @click="printDocument"
                    class="rounded-xl gap-2 font-bold h-10 bg-rdc-blue text-white shadow-lg shadow-rdc-blue/20">
                    <Download class="size-4" /> {{ __('tools.export_pdf') }}
                </Button>
            </div>
        </div>

        <!-- Profile Header Card -->
        <div
            class="relative overflow-hidden bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-black/40">
            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-r from-rdc-blue to-rdc-red opacity-10">
            </div>
            <div class="p-10 pt-16 flex flex-col md:flex-row gap-10 items-start">
                <div class="relative">
                    <div
                        class="size-40 rounded-3xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-300 border-4 border-white dark:border-slate-950 shadow-2xl overflow-hidden ring-1 ring-slate-200 dark:ring-slate-800">
                        <img v-if="conducteur.photo_url" :src="conducteur.photo_url"
                            class="w-full h-full object-cover" />
                        <User v-else class="size-20" />
                    </div>
                    <Badge
                        class="absolute -bottom-3 left-1/2 -translate-x-1/2 px-4 py-1.5 bg-white dark:bg-slate-950 text-rdc-blue border-2 border-rdc-blue/20 shadow-lg font-black tracking-widest">
                        {{ conducteur.identification_number }}
                    </Badge>
                </div>

                <div class="flex-1 space-y-6">
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <h1
                                class="text-4xl font-black tracking-tighter text-slate-900 dark:text-slate-100 uppercase">
                                {{ conducteur.name }}</h1>
                            <Badge variant="secondary"
                                class="h-6 px-3 bg-rdc-blue/10 text-rdc-blue border-none font-black uppercase text-[10px] tracking-widest">
                                {{ conducteur.sector }}
                            </Badge>
                        </div>
                        <div class="flex flex-wrap gap-4 text-sm font-medium text-slate-400">
                            <div class="flex items-center gap-2">
                                <MapPin class="size-4" /> {{ conducteur.province?.name }}
                            </div>
                            <div class="flex items-center gap-2">
                                <Globe class="size-4" /> {{ conducteur.nationality }}
                            </div>
                            <div class="flex items-center gap-2">
                                <ShieldCheck class="size-4" /> {{ __('common.status') }}: {{ __('common.active') }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4">
                        <div class="relative overflow-hidden p-4 rounded-xl bg-white dark:bg-slate-800/50 border border-blue-100 dark:border-rdc-blue/20 shadow-sm">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent dark:from-rdc-blue/[0.04]" />
                            <div class="relative">
                                <div class="size-8 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue mb-2">
                                    <CreditCard class="size-4" />
                                </div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('conducteurs.form.license') }}</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white mt-0.5">{{ conducteur.license_number || __('conducteurs.no_license') }}</p>
                            </div>
                        </div>
                        <div class="relative overflow-hidden p-4 rounded-xl bg-white dark:bg-slate-800/50 border border-purple-100 dark:border-purple-500/20 shadow-sm">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-transparent dark:from-purple-500/[0.04]" />
                            <div class="relative">
                                <div class="size-8 rounded-lg bg-purple-500/10 dark:bg-purple-500/20 flex items-center justify-center text-purple-600 dark:text-purple-400 mb-2">
                                    <FileText class="size-4" />
                                </div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('permis.category') }}</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white mt-0.5">{{ conducteur.license_category || __('common.na') }}</p>
                            </div>
                        </div>
                        <div class="relative overflow-hidden p-4 rounded-xl bg-white dark:bg-slate-800/50 border border-emerald-100 dark:border-emerald-500/20 shadow-sm">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-transparent dark:from-emerald-500/[0.04]" />
                            <div class="relative">
                                <div class="size-8 rounded-lg bg-emerald-500/10 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-2">
                                    <UserCheck class="size-4" />
                                </div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('conducteurs.gender') }}</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white mt-0.5">{{ conducteur.gender === 'M' ? __('common.male') : __('common.female') }}</p>
                            </div>
                        </div>
                        <div class="relative overflow-hidden p-4 rounded-xl bg-white dark:bg-slate-800/50 border border-red-100 dark:border-rdc-red/20 shadow-sm">
                            <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-transparent dark:from-rdc-red/[0.04]" />
                            <div class="relative">
                                <div class="size-8 rounded-lg bg-rdc-red/10 dark:bg-rdc-red/20 flex items-center justify-center text-rdc-red mb-2">
                                    <Calendar class="size-4" />
                                </div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('conducteurs.form.id_expiration') }}</p>
                                <p class="text-sm font-black text-rdc-red mt-0.5">{{ formatDate(conducteur.expiration_date) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Personal Info & Identity -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Identity Piece -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 space-y-8">
                    <div
                        class="flex items-center gap-3 text-slate-900 dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-6">
                        <CreditCard class="size-5" />
                        <h2 class="text-lg font-black tracking-tight uppercase">{{ __('conducteurs.form.id_birth') }}</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-4">
                            <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                                <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                                    <Calendar class="size-4" />
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('conducteurs.form.birth') }}</p>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ formatDate(conducteur.birth_date) }} à {{ conducteur.birth_place }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                                <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                                    <Users class="size-4" />
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('conducteurs.form.filiation') }}</p>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('conducteurs.form.parentage') }} {{ conducteur.father_name }} et de {{ conducteur.mother_name }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                                <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                                    <ShieldCheck class="size-4" />
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('conducteurs.form.marital_status') }}</p>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ conducteur.marital_status }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                                <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                                    <FileText class="size-4" />
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('conducteurs.form.id_type') }}</p>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ conducteur.id_piece_type }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                                <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                                    <CreditCard class="size-4" />
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('conducteurs.form.id_number') }}</p>
                                    <p class="text-sm font-black text-rdc-blue">{{ conducteur.id_piece_number }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                                <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                                    <MapPin class="size-4" />
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('conducteurs.form.issued_on') }}</p>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ formatDate(conducteur.id_piece_issued_at) }} à {{ conducteur.id_piece_issued_place }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact & Residence -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 space-y-8">
                    <div
                        class="flex items-center gap-3 text-slate-900 dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-6">
                        <Phone class="size-5" />
                        <h2 class="text-lg font-black tracking-tight uppercase">{{ __('conducteurs.form.contact_residence') }}</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div
                                    class="size-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                    <Phone class="size-4" />
                                </div>
                                <div class="space-y-0.5">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('common.phone') }}</p>
                                    <p class="text-sm font-black text-slate-900 dark:text-slate-100">{{
                                        conducteur.phone }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="size-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                    <Mail class="size-4" />
                                </div>
                                <div class="space-y-0.5">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('common.email') }}</p>
                                    <p class="text-sm font-black text-slate-900 dark:text-slate-100">{{
                                        conducteur.email || __('common.na') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div
                                class="size-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 mt-1">
                                <MapPin class="size-4" />
                            </div>
                            <div class="space-y-0.5">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('conducteurs.form.residence_address') }}</p>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300 leading-relaxed">{{
                                    conducteur.address }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Engine Info & Affiliation -->
            <div class="space-y-8">
                <!-- Linked Engine -->
                <div class="bg-rdc-blue/5 rounded-3xl border border-rdc-blue/10 p-8 space-y-8">
                    <div class="flex items-center justify-between border-b border-rdc-blue/10 pb-6">
                        <div class="flex items-center gap-3 text-rdc-blue">
                            <Settings class="size-5" />
                            <h2 class="text-lg font-black tracking-tight uppercase">{{ __('conducteurs.form.affiliated_engine') }}</h2>
                        </div>
                        <Link v-if="conducteur.engin" :href="`/engins/${conducteur.engin_id}`"
                            class="text-rdc-blue hover:underline">
                            <ExternalLink class="size-4" />
                        </Link>
                    </div>

                    <div v-if="conducteur.engin" class="space-y-6">
                        <div class="flex items-center gap-5">
                            <div
                                class="size-14 rounded-2xl bg-white dark:bg-slate-800 shadow-sm flex items-center justify-center text-rdc-blue border border-rdc-blue/10">
                                <Car v-if="conducteur.engin.category === 'roulant'" class="size-7" />
                                <Anchor v-else class="size-7" />
                            </div>
                            <div>
                                <p
                                    class="text-sm font-black text-slate-900 dark:text-slate-100 uppercase tracking-tight">
                                    {{ conducteur.engin.vehicle_brand }} {{ conducteur.engin.vehicle_type }}</p>
                                <p class="text-[10px] font-bold text-rdc-blue uppercase tracking-[0.2em]">{{
                                    conducteur.engin.identification_number }}</p>
                            </div>
                        </div>

                        <div class="space-y-4 pt-4">
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-bold text-slate-400 uppercase tracking-widest">{{ __('conducteurs.engine.chassis') }}</span>
                                <span class="font-black text-slate-900 dark:text-slate-100">{{
                                    conducteur.engin.chassis_number || 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-bold text-slate-400 uppercase tracking-widest">{{ __('conducteurs.engine.engine_number') }}</span>
                                <span class="font-black text-slate-900 dark:text-slate-100">{{
                                    conducteur.engin.engine_number || 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-bold text-slate-400 uppercase tracking-widest">{{ __('common.color') }}</span>
                                <span class="font-black text-slate-900 dark:text-slate-100">{{
                                    conducteur.engin.vehicle_color }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="py-10 text-center">
                        <Info class="size-8 text-slate-300 mx-auto mb-3" />
                        <p class="text-sm font-bold text-slate-400 italic">{{ __('conducteurs.no_engine') }}</p>
                    </div>
                </div>

                <!-- Affiliation & Service -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 space-y-8">
                    <div
                        class="flex items-center gap-3 text-slate-900 dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-6">
                        <Activity class="size-5" />
                        <h2 class="text-lg font-black tracking-tight uppercase">{{ __('conducteurs.form.service_affiliation') }}</h2>
                    </div>
                    <div class="space-y-6">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('conducteurs.form.agency') }}</p>
                            <p class="text-sm font-black text-slate-900 dark:text-slate-100">{{
                                conducteur.affiliation || __('common.none') }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('conducteurs.form.transport_mode') }}</p>
                            <Badge variant="outline" class="font-black">
                                {{ conducteur.transport_mode || __('common.not_specified') }}
                            </Badge>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('conducteurs.form.profession') }}</p>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300 capitalize">{{
                                conducteur.profession }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End Dashboard View -->

    <!-- PRINTABLE VIEW (Hidden on screen, block on print) -->
    <div id="printable-area"
        class="hidden print:flex bg-white mx-auto w-full flex max-w-4xl print:p-0 print:border-none print:w-[210mm] print:h-[297mm] print:mx-auto">
        <div id="images">
            <img src="/LOGO-TW-03-1.png" class="h-30 rotate-90" />
            <img src="/LOGO-TW-03-1.png" class="h-20 w-20 bg-gray-100 rounded-full object-contain my-2" />
        </div>

        <div class="block">
            <!-- Header -->
            <header class="text-center mb-6">
                <i class="text-center">République Démocratique du Congo</i>
                <h1 class="font-bold text-lg">PROVINCE DU TANGANYIKA</h1>
                <em class="text-center text-sm">Ministère Provincial du Transport et Voies de Communication</em>
                <img src="/armoiry.png" class="h-20 w-20 mx-auto bg-gray-100 rounded-full object-contain my-2"
                    alt="Armoiries">
                <h2 class="text-xs font-semibold">Division Provinciale des Transports et Voies de Communication</h2>

                <div class="flex">
                    <div class="w-3/4">
                        <h1 class="font-black text-lg mt-4 uppercase p-4 border-4 border-black">
                            FORMULAIRE D’IDENTIFICATION DE CONDUCTEUR N°………/MINITRANS/TANG/20……
                        </h1>
                    </div>
                    <div class="w-1/4">
                        <div class="h-32 w-32 border-2 border-black mx-auto overflow-hidden">
                            <img v-if="conducteur.photo_url" :src="conducteur.photo_url"
                                class="w-full h-full object-cover" />
                            <div v-else
                                class="w-full h-full flex justify-center items-center text-xs text-center p-2 font-bold text-slate-300">
                                {{ __('conducteurs.form.photo') }}</div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="p-4 mx-auto space-y-6">
                <!-- Identité -->
                <div class="block">
                    <h1 class="text-sm text-blue-800 font-bold mb-2" style="letter-spacing: -0.5px;">{{ __('conducteurs.form.identity').toUpperCase() }}</h1>
                    <div class="flex gap-4">
                        <div class="w-full space-y-2 text-sm">
                            <div class="flex w-full gap-2">
                                <p class="min-w-[60px]">{{ __('common.name') }}:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    conducteur.name }}
                                </p>
                            </div>
                            <div class="flex w-full gap-2">
                                <div class="flex w-3/4 gap-2">
                                    <p class="min-w-[180px]">{{ __('conducteurs.form.birth') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        conducteur.birth_place }}, le {{ formatDate(conducteur.birth_date) }}</p>
                                </div>
                                <div class="flex w-1/4 gap-2">
                                    <p>{{ __('conducteurs.gender') }}:</p>
                                    <p
                                        class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                        {{ conducteur.gender === 'M' ? __('common.male') : __('common.female') }}</p>
                                </div>
                            </div>
                            <div class="flex w-full gap-2">
                                <p class="min-w-[160px]">{{ __('conducteurs.form.father_name') }}:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    conducteur.father_name }}</p>
                            </div>
                            <div class="flex w-full gap-2">
                                <p class="min-w-[160px]">{{ __('conducteurs.form.mother_name') }}:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    conducteur.mother_name }}</p>
                            </div>
                            <div class="flex w-full gap-4">
                                <div class="flex w-1/2">
                                    <p class="min-w-[80px]">{{ __('conducteurs.form.marital_status') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        conducteur.marital_status }}</p>
                                </div>
                                <div class="flex w-1/2">
                                    <p class="min-w-[100px]">{{ __('conducteurs.form.nationality') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        conducteur.nationality }}</p>
                                </div>
                            </div>
                            <div class="flex w-full gap-4">
                                <div class="flex w-1/2">
                                    <p class="min-w-[80px]">{{ __('common.phone') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        conducteur.phone }}</p>
                                </div>
                                <div class="flex w-1/2">
                                    <p class="min-w-[80px]">{{ __('common.email') }}:</p>
                                    <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                        conducteur.email || 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex w-full gap-2">
                                <p class="min-w-[160px]">{{ __('conducteurs.form.residence_address') }}:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    conducteur.address }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents d'identité -->
                <div class="block">
                    <h1 class="text-sm text-blue-800 font-bold mb-2" style="letter-spacing: -0.5px;">{{ __('conducteurs.print.id_pieces').toUpperCase() }}
                    </h1>
                    <div class="text-sm space-y-2">
                        <div class="flex w-full gap-4">
                            <div class="flex w-1/2">
                                <p class="min-w-[120px]">{{ __('conducteurs.form.id_type') }}:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    conducteur.id_piece_type }}</p>
                            </div>
                            <div class="flex w-1/2">
                                <p class="min-w-[80px]">{{ __('conducteurs.form.id_number') }}:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase text-center">
                                    {{ conducteur.id_piece_number }}</p>
                            </div>
                        </div>
                        <div class="flex w-full gap-4">
                            <div class="flex w-1/2">
                                <p class="min-w-[120px]">{{ __('conducteurs.form.issued_on') }}:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    formatDate(conducteur.id_piece_issued_at) }}</p>
                            </div>
                            <div class="flex w-1/2">
                                <p class="min-w-[80px]">{{ __('common.at') }}:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase text-center">
                                    {{ conducteur.id_piece_issued_place }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations Professionnelles -->
                <div class="block">
                    <h1 class="text-sm text-blue-800 font-bold mb-2" style="letter-spacing: -0.5px;">INFORMATIONS
                        PROFESSIONNELLES</h1>
                    <div class="text-sm space-y-2">
                        <div class="flex w-full gap-4">
                            <div class="flex w-1/3">
                                <p class="min-w-[80px]">Permis N°:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    conducteur.license_number || 'N/A' }}</p>
                            </div>
                            <div class="flex w-1/3">
                                <p class="min-w-[80px]">Catégorie:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                    {{ conducteur.license_category || 'N/A' }}</p>
                            </div>
                            <div class="flex w-1/3">
                                <p class="min-w-[80px]">Expiration:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                    {{ formatDate(conducteur.expiration_date) }}</p>
                            </div>
                        </div>
                        <div class="flex w-full gap-4">
                            <div class="flex w-1/2">
                                <p class="min-w-[150px]">Profession Déclarée:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    conducteur.profession }}</p>
                            </div>
                            <div class="flex w-1/2">
                                <p class="min-w-[100px]">Mode de Transport:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                    {{ conducteur.transport_mode || 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex w-full gap-4">
                            <div class="flex w-1/2">
                                <p class="min-w-[180px]">Agence/Association:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    conducteur.affiliation || 'N/A' }}</p>
                            </div>
                            <div class="flex w-1/2">
                                <p class="min-w-[80px]">Secteur:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                    {{ conducteur.sector }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Engin Affilié -->
                <div class="block" v-if="conducteur.engin">
                    <h1 class="text-sm text-blue-800 font-bold mb-2" style="letter-spacing: -0.5px;">ENGIN AFFILIE
                    </h1>
                    <div class="text-sm space-y-2">
                        <div class="flex w-full gap-4">
                            <div class="flex w-1/2">
                                <p class="min-w-[80px]">Marque:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    conducteur.engin.vehicle_brand }} {{ conducteur.engin.vehicle_type }}</p>
                            </div>
                            <div class="flex w-1/2">
                                <p class="min-w-[80px]">Couleur:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase text-center">
                                    {{ conducteur.engin.vehicle_color }}</p>
                            </div>
                        </div>
                        <div class="flex w-full gap-4">
                            <div class="flex w-1/2">
                                <p class="min-w-[120px]">N° Châssis:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    conducteur.engin.chassis_number || 'N/A' }}</p>
                            </div>
                            <div class="flex w-1/2">
                                <p class="min-w-[120px]">N° Moteur:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase text-center">
                                    {{ conducteur.engin.engine_number || 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex w-full gap-2">
                            <p class="min-w-[180px]">N° Identification Engin:</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                conducteur.engin.identification_number }}</p>
                        </div>
                    </div>
                </div>

                <!-- Administration -->
                <div class="block">
                    <h1 class="text-sm text-blue-800 font-bold mb-2" style="letter-spacing: -0.5px;">RESERVE A
                        L'ADMINISTRATION</h1>
                    <div class="text-sm space-y-2">
                        <div class="flex w-full gap-2">
                            <p class="min-w-[200px]">Numéro d'Identification (ID):</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                conducteur.identification_number }}</p>
                        </div>
                        <div class="flex w-full gap-4">
                            <div class="flex w-1/2">
                                <p class="min-w-[180px]">Date d'enregistrement:</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase text-center">
                                    {{ formatDate(conducteur.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full flex justify-between text-sm mt-8 px-4">
                <div class="text-center">
                    <p class="font-bold">Visa du Conducteur</p>
                    <p class="text-xs text-gray-600">(nom et signature)</p>
                </div>
                <div class="text-center">
                    <p>Fait à {{ conducteur.province?.name || '________________' }}, le {{
                        formatDate(conducteur.created_at) }}</p>
                    <p class="font-bold mt-8">Pour la division provinciale des transports</p>
                    <p class="text-[10px] text-gray-600">(Noms, Signature et sceau du chef ou de son délégué)</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@media print {
    @page {
        size: A4;
        margin: 0;
    }

    body {
        background-color: white !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    body * {
        visibility: hidden;
    }

    #printable-area,
    #printable-area * {
        visibility: visible;
    }

    #printable-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 210mm;
        height: 297mm;
        margin: 0;
        padding: 15mm;
        box-shadow: none;
        box-sizing: border-box;
        overflow: hidden;
    }

    .no-print {
        display: none !important;
    }
}
</style>
