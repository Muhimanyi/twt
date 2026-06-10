<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ChevronLeft, FileCheck, Car, Anchor, User,
    Calendar, ShieldCheck, Printer, Download, Eye, Globe, MapPin,
    Info, Settings
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const props = defineProps<{
    certificat: any;
}>();

const formatDate = (date: string) => {
    if (!date) return __('common.permanent');
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
            { title: 'Gestion', href: '#' },
            { title: 'Certificats', href: '/certificats' },
            { title: 'Détails', href: '#' }
        ],
    },
});
</script>

<template>

        <Head :title="`${__('certificats.title')}: ${certificat.certificate_number}`" />

        <div class="print-area p-8 max-w-5xl mx-auto space-y-8">
            <!-- Navigation -->
            <Link href="/certificats"
                class="flex items-center gap-2 text-slate-400 hover:text-rdc-blue transition-all group">
                <ChevronLeft class="size-4 group-hover:-translate-x-1 transition-transform" />
                <span class="text-xs font-black uppercase tracking-widest">{{ __('certificats.back_link') }}</span>
            </Link>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left: Document Preview -->
                <div class="lg:col-span-1 space-y-6">
                    <div
                        class="aspect-[1/1.4] bg-white rounded-2xl p-8 shadow-2xl relative overflow-hidden border border-slate-200 dark:bg-slate-900 dark:border-slate-800">
                        <!-- Certificate Design -->
                        <div
                            class="absolute inset-0 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px] opacity-30">
                        </div>

                        <div class="relative h-full flex flex-col items-center text-center space-y-6">
                            <div
                                class="size-20 rounded-full bg-slate-50 flex items-center justify-center border-4 border-slate-100 mb-2">
                                <FileCheck class="size-10 text-rdc-blue" />
                            </div>

                            <div class="space-y-1">
                                <p class="text-[8px] font-black uppercase tracking-[0.3em] text-slate-400">{{ __('certificats.card_country') }}</p>
                                <p
                                    class="text-[12px] font-black uppercase tracking-widest text-slate-900 dark:text-white">
                                    {{ __('certificats.card_ministry') }}</p>
                                <div class="h-1 w-12 bg-rdc-red mx-auto mt-2"></div>
                            </div>

                            <div class="py-6 space-y-1">
                                <p class="text-[10px] font-black uppercase text-rdc-blue">{{ __('certificats.card_cert_label') }}</p>
                                <h3
                                    class="text-xl font-black uppercase tracking-tighter text-slate-900 dark:text-white">
                                    {{ certificat.type }}</h3>
                            </div>

                            <div class="w-full space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <div class="space-y-0.5">
                                    <p class="text-[8px] font-bold uppercase text-slate-400">{{ __('certificats.card_owner_label') }}</p>
                                    <p class="text-sm font-black uppercase text-slate-900 dark:text-white">{{
                                        certificat.owner_name }}</p>
                                </div>
                                <div class="space-y-0.5">
                                    <p class="text-[8px] font-bold uppercase text-slate-400">{{ __('certificats.card_engine_label') }}
                                    </p>
                                    <p class="text-xs font-black text-rdc-blue font-mono">{{
                                        certificat.engin?.identification_number }}</p>
                                </div>
                            </div>

                            <div class="mt-auto pt-8 flex flex-col items-center">
                                <div
                                    class="size-24 bg-slate-100 rounded-xl flex items-center justify-center text-[10px] font-bold text-slate-300">{{ __('certificats.card_qr') }}</div>
                                <p class="text-[10px] font-black font-mono mt-4 text-slate-400">{{
                                    certificat.certificate_number }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <Button class="w-full rounded-xl gap-2 font-bold h-12 bg-slate-900 text-white shadow-xl">
                            <Download class="size-4" /> {{ __('certificats.download') }}
                        </Button>
                        <Button variant="outline" class="w-full rounded-xl gap-2 font-bold h-12 border-slate-200" @click="printDocument">
                            <Printer class="size-4" /> {{ __('certificats.print') }}
                        </Button>
                    </div>
                </div>

                <!-- Right: Detailed Information -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Certificate Info -->
                    <div
                        class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 space-y-8 shadow-xl shadow-slate-200/40">
                        <div
                            class="flex items-center gap-3 text-slate-900 dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-6">
                            <Info class="size-5" />
                            <h2 class="text-lg font-black tracking-tight uppercase">{{ __('certificats.details_section') }}</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-6">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('certificats.field_number') }}</p>
                                    <p class="text-sm font-black text-slate-900 dark:text-slate-100 font-mono">{{
                                        certificat.certificate_number }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('certificats.field_type') }}</p>
                                    <Badge :class="certificat.type === 'Possession' ? 'bg-rdc-red' : 'bg-rdc-blue'"
                                        class="text-white font-black px-4 py-1">
                                        {{ certificat.type === 'Possession' ? __('certificats.type_possession') : __('certificats.type_identification') }}
                                    </Badge>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('certificats.field_issued') }}</p>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">{{
                                        formatDate(certificat.issued_at) }}</p>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('certificats.field_status') }}
                                    </p>
                                    <div class="flex items-center gap-2 text-green-500">
                                        <ShieldCheck class="size-4" />
                                        <span class="text-sm font-black uppercase tracking-widest">{{ __('certificats.status_valid') }}</span>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('certificats.field_owner') }}</p>
                                    <p class="text-sm font-black text-slate-900 dark:text-slate-100 uppercase">{{
                                        certificat.owner_name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Engine Info -->
                    <div
                        class="bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 text-slate-900 dark:text-slate-100">
                                <Settings class="size-5" />
                                <h2 class="text-lg font-black tracking-tight uppercase">{{ __('certificats.engine_section') }}</h2>
                            </div>
                            <Link :href="`/engins/${certificat.engin_id}`"
                                class="text-rdc-blue text-xs font-bold hover:underline">
                                {{ __('certificats.engine_link') }}
                            </Link>
                        </div>

                        <div v-if="certificat.engin" class="flex items-center gap-6">
                            <div
                                class="size-20 rounded-2xl bg-white dark:bg-slate-950 shadow-sm border border-slate-100 flex items-center justify-center">
                                <Car v-if="certificat.engin.category === 'roulant'" class="size-10 text-rdc-blue" />
                                <Anchor v-else class="size-10 text-rdc-blue" />
                            </div>
                            <div class="space-y-2">
                                <p
                                    class="text-xl font-black text-slate-900 dark:text-slate-100 uppercase tracking-tight">
                                    {{ certificat.engin.vehicle_brand }} {{ certificat.engin.vehicle_type }}</p>
                                <div class="flex gap-4 text-xs font-bold text-slate-500">
                                    <span class="flex items-center gap-1 font-mono text-rdc-blue">{{
                                        certificat.engin.identification_number }}</span>
                                    <span class="flex items-center gap-1 uppercase tracking-widest">
                                        <MapPin class="size-3" /> {{ certificat.engin.province?.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>
