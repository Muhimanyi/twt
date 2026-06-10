<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ChevronLeft, Printer, Share2, MapPin, Phone,
    IdCard, Briefcase, Building2, UserCircle,
    Calendar, CreditCard, ShieldCheck, Globe,
    Hash, User, Info, FileText, Download
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { index as debardeursRoute } from '@/routes/debardeurs';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const props = defineProps<{
    debardeur: any;
}>();

function formatDate(dateString: string) {
    if (!dateString) return __('common.none');
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
}

const printDocument = () => {
    window.print();
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Recensement', href: '#' },
            { title: 'Débardeurs', href: debardeursRoute().url },
            { title: 'Détails Débardeur', href: '#' }
        ],
    },
});
</script>

<template>

    <Head :title="__('debardeurs.profile') + ' - ' + debardeur.last_name" />

    <div class="max-w-5xl mx-auto space-y-8 p-1 pb-12 print:hidden">
        <!-- Action Bar -->
        <div class="flex items-center justify-between">
            <Button variant="ghost" as-child class="rounded-xl gap-2 text-slate-500 hover:text-slate-900">
                <Link :href="debardeursRoute().url">
                    <ChevronLeft class="size-4" />
                    {{ __('debardeurs.back') }}
                </Link>
            </Button>
            <div class="flex items-center gap-3">
                <Button @click="printDocument" variant="outline" class="rounded-xl gap-2">
                    <Printer class="size-4" /> {{ __('debardeurs.print_card') }}
                </Button>
                <Button @click="printDocument"
                    class="rounded-xl bg-rdc-blue text-white gap-2 shadow-lg shadow-rdc-blue/20">
                    <Download class="size-4" /> {{ __('debardeurs.export_pdf') }}
                </Button>
            </div>
        </div>

        <!-- Main Profile Header -->
        <div
            class="relative bg-white dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-r from-rdc-blue to-rdc-red opacity-10"></div>

            <div class="relative p-8 md:p-12 flex flex-col md:flex-row gap-10 items-start">
                <!-- Photo/Avatar Container -->
                <div class="relative group">
                    <div
                        class="size-48 rounded-xl bg-slate-100 dark:bg-slate-900 overflow-hidden border-4 border-white dark:border-slate-800 shadow-2xl transition-transform duration-500 group-hover:scale-[1.02]">
                        <img v-if="debardeur.photo_url" :src="debardeur.photo_url" class="w-full h-full object-cover" />
                        <div v-else class="w-full h-full flex items-center justify-center text-slate-300">
                            <UserCircle class="size-24" />
                        </div>
                    </div>
                    <div
                        class="absolute -bottom-3 -right-3 size-12 rounded-2xl bg-rdc-blue text-white flex items-center justify-center shadow-lg border-4 border-white dark:border-slate-950">
                        <ShieldCheck class="size-6" />
                    </div>
                </div>

                <!-- Info Container -->
                <div class="flex-1 space-y-6">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-4xl font-black tracking-tight text-slate-900 dark:text-slate-100">
                                {{ debardeur.last_name }} <span class="text-rdc-blue">{{ debardeur.first_name }}</span>
                            </h1>
                            <Badge class="bg-rdc-blue/10 text-rdc-blue border-none px-3 py-1 font-bold rounded-lg">{{
                                debardeur.sector }}</Badge>
                        </div>
                        <p class="text-lg text-slate-500 font-medium flex items-center gap-2">
                            <Hash class="size-5 text-slate-300" />
                            ID: <span class="font-mono font-bold text-slate-700 dark:text-slate-300">{{
                                debardeur.permanent_number }}</span>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4">
                        <div class="relative overflow-hidden p-4 rounded-xl bg-white dark:bg-slate-800/50 border border-blue-100 dark:border-rdc-blue/20 shadow-sm">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent dark:from-rdc-blue/[0.04]" />
                            <div class="relative">
                                <div class="size-8 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue mb-2">
                                    <MapPin class="size-4" />
                                </div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('debardeurs.current_assignment') }}</p>
                                <p class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ debardeur.province.name }}</p>
                                <p class="text-xs text-slate-500">{{ debardeur.assignment_place }}</p>
                            </div>
                        </div>
                        <div class="relative overflow-hidden p-4 rounded-xl bg-white dark:bg-slate-800/50 border border-amber-100 dark:border-amber-500/20 shadow-sm">
                            <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-transparent dark:from-amber-500/[0.04]" />
                            <div class="relative">
                                <div class="size-8 rounded-lg bg-amber-500/10 dark:bg-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400 mb-2">
                                    <Hash class="size-4" />
                                </div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('debardeurs.vest_number_label') }}</p>
                                <p class="font-black text-2xl text-rdc-blue font-mono mt-0.5">{{ debardeur.vest_number || __('common.none') }}</p>
                            </div>
                        </div>
                        <div class="relative overflow-hidden p-4 rounded-xl bg-white dark:bg-slate-800/50 border border-emerald-100 dark:border-emerald-500/20 shadow-sm">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-transparent dark:from-emerald-500/[0.04]" />
                            <div class="relative">
                                <div class="size-8 rounded-lg bg-emerald-500/10 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-2">
                                    <ShieldCheck class="size-4" />
                                </div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('debardeurs.system_status') }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <div class="size-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ __('debardeurs.active_record') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Left Column: Personal -->
            <div class="md:col-span-2 space-y-8">
                <div
                    class="bg-white dark:bg-slate-950 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-8">
                        <User class="size-5 text-rdc-blue" />
                        <h2 class="text-xl font-black">{{ __('debardeurs.personal_info') }}</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                            <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                                <User class="size-4" />
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('debardeurs.form.name_postname') }}</p>
                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ debardeur.last_name }} {{ debardeur.middle_name || '' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                            <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                                <User class="size-4" />
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('debardeurs.form.first_name') }}</p>
                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ debardeur.first_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                            <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                                <Calendar class="size-4" />
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('debardeurs.form.birth_place_date') }}</p>
                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ debardeur.birth_place }}, {{ formatDate(debardeur.birth_date) }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                            <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                                <Users class="size-4" />
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('debardeurs.form.gender_marital') }}</p>
                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ debardeur.gender === 'M' ? __('debardeurs.gender.male') : __('debardeurs.gender.female') }} — {{ debardeur.marital_status }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                            <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                                <Users class="size-4" />
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('debardeurs.form.filiation_father') }}</p>
                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ debardeur.father_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                            <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                                <Users class="size-4" />
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('debardeurs.form.filiation_mother') }}</p>
                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ debardeur.mother_name }}</p>
                            </div>
                        </div>
                    </div>

                    <Separator class="my-8 opacity-50" />

                    <div class="grid grid-cols-2 gap-y-8 gap-x-12">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('debardeurs.form.contact_phone') }}</label>
                            <p class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                                <Phone class="size-4 text-rdc-blue" /> {{ debardeur.phone }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('debardeurs.form.residence_label') }}</label>
                            <p class="font-bold text-slate-800 dark:text-slate-200">{{ debardeur.residence_address }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-slate-950 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-8">
                        <CreditCard class="size-5 text-rdc-yellow" />
                        <h2 class="text-xl font-black">{{ __('debardeurs.section.documents_identification') }}</h2>
                    </div>

                    <div class="grid grid-cols-2 gap-8">
                        <div
                            class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">{{ __('debardeurs.form.national_identity') }}</p>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-slate-500 font-medium">{{ __('common.type') }}:</span>
                                    <span class="text-xs font-bold">{{ debardeur.id_type }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-slate-500 font-medium">{{ __('debardeurs.form.number_label') }}</span>
                                    <span class="text-xs font-black font-mono">{{ debardeur.id_number }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-slate-500 font-medium">{{ __('debardeurs.form.expiration_label') }}</span>
                                    <span class="text-xs font-bold text-rdc-red">{{
                                        formatDate(debardeur.id_expiration_date) }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-if="debardeur.migratory_doc_type"
                            class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">{{ __('debardeurs.form.migratory_doc_label') }}</p>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-slate-500 font-medium">{{ __('common.type') }}:</span>
                                    <span class="text-xs font-bold">{{ debardeur.migratory_doc_type }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-slate-500 font-medium">{{ __('debardeurs.form.number_label') }}</span>
                                    <span class="text-xs font-black font-mono">{{ debardeur.migratory_doc_number
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Professional/Status -->
            <div class="space-y-8">
                <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-xl shadow-slate-900/20">
                    <div class="flex items-center gap-3 mb-6">
                        <Briefcase class="size-5 text-rdc-blue" />
                        <h2 class="text-xl font-black">{{ __('debardeurs.section.professional_profile') }}</h2>
                    </div>
                    <div class="space-y-6">
                        <div class="space-y-1">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('debardeurs.form.profession_label') }}</label>
                            <p class="font-bold text-lg">{{ debardeur.profession }}</p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('debardeurs.form.cooperative_label') }}</label>
                            <p class="font-bold text-slate-200">{{ debardeur.association_cooperative || __('debardeurs.form.self_employed') }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('debardeurs.form.member_card_label') }}</label>
                            <p class="font-bold text-slate-200 font-mono">{{ debardeur.member_card_number || __('common.none') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-slate-950 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <Info class="size-5 text-rdc-blue" />
                        <h2 class="text-lg font-black">{{ __('debardeurs.section.system_info') }}</h2>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-900">
                            <span class="text-xs text-slate-500">{{ __('debardeurs.form.registered_on') }}</span>
                            <span class="text-xs font-bold">{{ formatDate(debardeur.created_at) }}</span>
                        </div>
                        <div
                            class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-900">
                            <span class="text-xs text-slate-500">{{ __('debardeurs.form.last_updated') }}</span>
                            <span class="text-xs font-bold">{{ formatDate(debardeur.updated_at) }}</span>
                        </div>
                        <div class="pt-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">{{ __('debardeurs.form.qr_code') }}</p>
                            <div
                                class="aspect-square bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-800">
                                <Globe class="size-16 text-slate-200 opacity-50" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End Dashboard View -->

    <!-- PRINTABLE VIEW (Hidden on screen, block on print) -->
    <div id="printable-area"
        class="hidden print:block print:absolute print:inset-0 print:m-0 print:p-0 print:w-full print:h-[100vh] bg-white print:z-[9999]">
        <!-- Header -->
        <img src="/debardeur.png" class="absolute inset-0 w-[210mm] h-[297mm] object-cover -z-10" alt="">

        <div class="block absolute top-60 right-0 w-full pl-40 pr-6">
            <div class="border-2 border-black p-4">{{ __('debardeurs.print.form_title') }}</div>
            <!-- Identité -->
            <div class="block">
                <h1 class="text-xl text-blue-800 font-bold mb-2" style="letter-spacing: -0.5px;">{{ __('debardeurs.print.identity_title') }}
                </h1>
                <div class="flex gap-4">
                    <div class="w-full space-y-2 text-sm">
                        <div class="flex w-full gap-2">
                            <p class="min-w-[60px]">{{ __('debardeurs.print.names') }}</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                debardeur.last_name }} {{ debardeur.middle_name || '' }} {{ debardeur.first_name }}
                            </p>
                        </div>
                        <div class="flex w-full gap-2">
                            <div class="flex w-3/4 gap-2">
                                <p class="min-w-[180px]">{{ __('debardeurs.print.birth_place_date') }}</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    debardeur.birth_place }}, le {{ formatDate(debardeur.birth_date) }}</p>
                            </div>
                            <div class="flex w-1/4 gap-2">
                                <p>{{ __('debardeurs.print.sex') }}</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                    {{ debardeur.gender === 'M' ? __('debardeurs.gender.male') : __('debardeurs.gender.female') }}</p>
                            </div>
                        </div>
                        <div class="flex w-full gap-2">
                            <p class="min-w-[160px]">{{ __('debardeurs.print.father_name') }}</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                debardeur.father_name }}</p>
                        </div>
                        <div class="flex w-full gap-2">
                            <p class="min-w-[160px]">{{ __('debardeurs.print.mother_name') }}</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                debardeur.mother_name }}</p>
                        </div>
                        <div class="flex w-full gap-4">
                            <div class="flex w-1/2">
                                <p class="min-w-[80px]">{{ __('debardeurs.print.marital_status') }}</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    debardeur.marital_status }}</p>
                            </div>
                            <div class="flex w-1/2">
                                <p class="min-w-[80px]">{{ __('debardeurs.print.contact') }}</p>
                                <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                    debardeur.phone }}</p>
                            </div>
                        </div>
                        <div class="flex w-full gap-2">
                            <p class="min-w-[160px]">{{ __('debardeurs.print.residence_address') }}</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                debardeur.residence_address }}</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Documents d'identité -->
            <div class="block">
                <h1 class="text-xl text-blue-800 font-bold mb-2" style="letter-spacing: -0.5px;">{{ __('debardeurs.print.id_documents_title') }}
                </h1>
                <div class="text-sm space-y-2">
                    <div class="flex w-full gap-4">
                        <div class="flex w-1/2">
                            <p class="min-w-[120px]">{{ __('debardeurs.print.id_type') }}</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                debardeur.id_type }}</p>
                        </div>
                        <div class="flex w-1/2">
                            <p class="min-w-[80px]">{{ __('debardeurs.print.id_number') }}</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full uppercase text-center">
                                {{ debardeur.id_number }}</p>
                        </div>
                    </div>
                    <div class="flex w-full gap-4" v-if="debardeur.migratory_doc_type">
                        <div class="flex w-1/2">
                            <p class="min-w-[150px]">{{ __('debardeurs.print.migratory_doc') }}</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                debardeur.migratory_doc_type }}</p>
                        </div>
                        <div class="flex w-1/2">
                            <p class="min-w-[80px]">{{ __('debardeurs.print.migratory_number') }}</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full uppercase text-center">
                                {{ debardeur.migratory_doc_number }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations Professionnelles -->
            <div class="block">
                <h1 class="text-xl text-blue-800 font-bold mb-2" style="letter-spacing: -0.5px;">{{ __('debardeurs.print.professional_title') }}</h1>
                <div class="text-sm space-y-2">
                    <div class="flex w-full gap-4">
                        <div class="flex w-1/2">
                            <p class="min-w-[80px]">{{ __('debardeurs.print.profession') }}</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                debardeur.profession }}</p>
                        </div>
                        <div class="flex w-1/2">
                            <p class="min-w-[80px]">{{ __('debardeurs.print.sector') }}</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                {{ debardeur.sector }}</p>
                        </div>
                    </div>
                    <div class="flex w-full gap-4">
                        <div class="flex w-2/3">
                            <p class="min-w-[180px]">{{ __('debardeurs.print.cooperative') }}</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                debardeur.association_cooperative || __('common.none') }}</p>
                        </div>
                        <div class="flex w-1/3">
                            <p class="min-w-[80px]">{{ __('debardeurs.print.member_card') }}</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                {{ debardeur.member_card_number || __('common.none') }}</p>
                        </div>
                    </div>
                    <div class="flex w-full gap-4">
                        <div class="flex w-1/2">
                            <p class="min-w-[150px]">{{ __('debardeurs.print.assignment_place') }}</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                                debardeur.assignment_place }} ({{ debardeur.province.name }})</p>
                        </div>
                        <div class="flex w-1/2">
                            <p class="min-w-[100px]">{{ __('debardeurs.print.vest_number') }}</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full text-center uppercase">
                                {{ debardeur.vest_number || __('common.none') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Administration -->
            <div class="block">
                <h1 class="text-xl text-blue-800 font-bold mb-2" style="letter-spacing: -0.5px;">{{ __('debardeurs.print.administration_title') }}</h1>
                <div class="text-sm space-y-2">
                    <div class="flex w-full gap-2">
                        <p class="min-w-[200px]">{{ __('debardeurs.print.permanent_number') }}</p>
                        <p class="font-bold border-dashed border-b-2 border-black w-full uppercase">{{
                            debardeur.permanent_number }}</p>
                    </div>
                    <div class="flex w-full gap-4">
                        <div class="flex w-1/2">
                            <p class="min-w-[180px]">{{ __('debardeurs.print.registration_date') }}</p>
                            <p class="font-bold border-dashed border-b-2 border-black w-full uppercase text-center">
                                {{ formatDate(debardeur.created_at) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-between text-sm mt-8 px-4">
                <div class="text-center">
                    <p class="font-bold">{{ __('debardeurs.print.visa_debardeur') }}</p>
                    <p class="text-xs text-gray-600">{{ __('debardeurs.print.signature') }}</p>
                </div>
                <div class="text-center">
                    <p>{{ __('debardeurs.print.fait_a') }} {{ debardeur.province.name }}, {{ __('debardeurs.print.registration_date') }} {{ formatDate(debardeur.created_at) }}</p>
                    <p class="font-bold mt-8">{{ __('debardeurs.print.division_transports') }}</p>
                    <p class="text-[10px] text-gray-600">{{ __('debardeurs.print.delegate') }}</p>
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

h1,
h2,
h3 {
    letter-spacing: -0.02em;
}
</style>
