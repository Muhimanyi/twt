<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    Users, Car, Anchor, UserCheck, FileCheck,
    Receipt, Building2, TrendingUp, ArrowUpRight,
    ArrowDownRight, MapPin, Activity, Plane, Wallet
} from 'lucide-vue-next';
import { dashboard } from '@/routes';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const props = defineProps<{
    stats: {
        total_conducteurs: number;
        total_engins: number;
        total_debardeurs: number;
        total_permis: number;
        permis_valides: number;
        total_certificats: number;
        total_paiements: number;
        revenus_total: number;
        conducteurs_par_secteur: Record<string, number>;
        engins_par_categorie: Record<string, number>;
        paiements_recents: Array<any>;
        province: string;
    };
}>();

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('fr-CD', {
        style: 'currency',
        currency: 'CDF',
        minimumFractionDigits: 0,
    }).format(amount);
};

const formatDate = (date: string) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const secteurData = computed(() => {
    const labels: Record<string, { label: string; icon: any; color: string }> = {
        routier: { label: __('sector.routier'), icon: Car, color: 'bg-rdc-blue' },
        lacustre: { label: __('sector.lacustre'), icon: Anchor, color: 'bg-cyan-500' },
        aerien: { label: __('sector.aerien'), icon: Plane, color: 'bg-rdc-yellow' },
        ferroviaire: { label: __('sector.ferroviaire'), icon: Activity, color: 'bg-rdc-red' },
    };
    return Object.entries(props.stats.conducteurs_par_secteur).map(([key, total]) => {
        const meta = labels[key] || { label: key, icon: Activity, color: 'bg-slate-400' };
        return { key, total, ...meta };
    });
});

const categorieData = computed(() => {
    const labels: Record<string, { label: string; icon: any; color: string }> = {
        roulant: { label: __('engins.category.roulant'), icon: Car, color: 'bg-rdc-blue' },
        flottant: { label: __('engins.category.flottant'), icon: Anchor, color: 'bg-cyan-500' },
    };
    return Object.entries(props.stats.engins_par_categorie).map(([key, total]) => {
        const meta = labels[key] || { label: key, icon: Activity, color: 'bg-slate-400' };
        return { key, total, ...meta };
    });
});

const maxSecteur = computed(() => Math.max(...secteurData.value.map(d => d.total), 1));
const maxCategorie = computed(() => Math.max(...categorieData.value.map(d => d.total), 1));

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
        ],
    },
});
</script>

<template>

    <Head :title="__('dashboard.title')" />

    <div
        class="p-6 space-y-8 bg-gradient-to-br from-slate-50/50 via-white to-slate-50/30 dark:from-slate-950/50 dark:via-slate-900 dark:to-slate-950/30 min-h-full">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h1 class="text-3xl font-black tracking-tighter text-slate-900 dark:text-slate-100">
                    {{ __('dashboard.title') }}
                </h1>
                <p class="text-sm text-white font-medium flex items-center gap-2">
                    <MapPin class="size-3.5" />
                    {{ stats.province }}
                </p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-5">
            <Link href="/conducteurs"
                class="group relative overflow-hidden rounded-2xl border border-slate-100 dark:border-slate-800 bg-gradient-to-br from-blue-600/50 to-blue-900/50 dark:from-rdc-blue/[0.30] dark:via-rdc-blue/[0.08] dark:to-slate-900 shadow-sm hover:shadow-xl hover:border-rdc-blue/40 transition-all duration-500">
                <div
                    class="absolute -right-6 -top-6 text-blue-600/80 dark:text-rdc-blue/[0.15] group-hover:scale-110 group-hover:opacity-100 transition-all duration-700 ease-out">
                    <Users class="size-36 animate-drift" stroke-width="1" />
                </div>
                <div class="relative z-10 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="size-12 rounded-2xl bg-blue-600/20 flex items-center justify-center text-white group-hover:scale-110 transition-transform duration-300">
                            <Users class="size-6" />
                        </div>
                        <ArrowUpRight class="size-4 text-emerald-500" />
                    </div>
                    <p class="text-3xl font-black text-white">{{ stats.total_conducteurs }}</p>
                    <p class="text-xs font-bold text-white uppercase tracking-widest mt-1">{{
                        __('dashboard.drivers') }}</p>
                </div>
            </Link>

            <Link href="/engins"
                class="group relative overflow-hidden rounded-2xl border border-slate-100 dark:border-slate-800 bg-gradient-to-br from-amber-600/50 to-amber-900/50 dark:from-rdc-yellow/[0.30] dark:via-rdc-yellow/[0.08] dark:to-slate-900 shadow-sm hover:shadow-xl hover:border-rdc-yellow/40 transition-all duration-500">
                <div
                    class="absolute -right-6 -top-6 text-amber-600/80 dark:text-rdc-yellow/[0.15] group-hover:scale-110 group-hover:opacity-100 transition-all duration-700 ease-out">
                    <Car class="size-36 animate-drift" stroke-width="1" style="animation-delay: -3s;" />
                </div>
                <div class="relative z-10 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="size-12 rounded-2xl bg-amber-600/20 flex items-center justify-center text-white group-hover:scale-110 transition-transform duration-300">
                            <Car class="size-6" />
                        </div>
                        <ArrowUpRight class="size-4 text-emerald-500" />
                    </div>
                    <p class="text-3xl font-black text-white">{{ stats.total_engins }}</p>
                    <p class="text-xs font-bold text-white uppercase tracking-widest mt-1">{{
                        __('dashboard.engines') }}</p>
                </div>
            </Link>

            <Link href="/debardeurs"
                class="group relative overflow-hidden rounded-2xl border border-slate-100 dark:border-slate-800 bg-gradient-to-br from-red-600/50 to-red-900/50 dark:from-rdc-red/[0.30] dark:via-rdc-red/[0.08] dark:to-slate-900 shadow-sm hover:shadow-xl hover:border-rdc-red/40 transition-all duration-500">
                <div
                    class="absolute -right-6 -top-6 text-red-600/80 dark:text-rdc-red/[0.15] group-hover:scale-110 group-hover:opacity-100 transition-all duration-700 ease-out">
                    <Anchor class="size-36 animate-drift" stroke-width="1" style="animation-delay: -6s;" />
                </div>
                <div class="relative z-10 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="size-12 rounded-2xl bg-red-600/20 flex items-center justify-center text-white group-hover:scale-110 transition-transform duration-300">
                            <Anchor class="size-6" />
                        </div>
                        <ArrowUpRight class="size-4 text-emerald-500" />
                    </div>
                    <p class="text-3xl font-black text-white">{{ stats.total_debardeurs }}</p>
                    <p class="text-xs font-bold text-white uppercase tracking-widest mt-1">{{
                        __('dashboard.stevedores') }}</p>
                </div>
            </Link>

            <Link href="/permis"
                class="group relative overflow-hidden rounded-2xl border border-slate-100 dark:border-slate-800 bg-gradient-to-br from-emerald-600/50 to-emerald-900/50 dark:from-emerald-400/[0.30] dark:via-emerald-400/[0.08] dark:to-slate-900 shadow-sm hover:shadow-xl hover:border-emerald-400/40 transition-all duration-500">
                <div
                    class="absolute -right-6 -top-6 text-emerald-600/80 dark:text-emerald-400/[0.15] group-hover:scale-110 group-hover:opacity-100 transition-all duration-700 ease-out">
                    <FileCheck class="size-36 animate-drift" stroke-width="1" style="animation-delay: -9s;" />
                </div>
                <div class="relative z-10 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="size-12 rounded-2xl bg-emerald-600/20 flex items-center justify-center text-white group-hover:scale-110 transition-transform duration-300">
                            <FileCheck class="size-6" />
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-black text-emerald-200">{{ stats.permis_valides }}</p>
                            <p class="text-[9px] text-white uppercase tracking-wider">{{ __('dashboard.valid') }}
                            </p>
                        </div>
                    </div>
                    <p class="text-3xl font-black text-white">{{ stats.total_permis }}</p>
                    <p class="text-xs font-bold text-white uppercase tracking-widest mt-1">{{
                        __('dashboard.licenses') }}</p>
                </div>
            </Link>
        </div>

        <!-- Secondary Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div
                class="group relative overflow-hidden rounded-2xl border border-slate-100 dark:border-slate-800 bg-gradient-to-br from-purple-600/50 to-purple-900/50 dark:from-purple-400/[0.30] dark:via-purple-400/[0.08] dark:to-slate-900 shadow-sm hover:shadow-xl hover:border-purple-300/40 transition-all duration-500">
                <div class="absolute -right-4 -top-4 text-purple-600/80 dark:text-purple-400/[0.15]">
                    <Wallet class="size-28 animate-float-slow" stroke-width="1" />
                </div>
                <div class="relative z-10 p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="size-10 rounded-xl bg-purple-600/20 flex items-center justify-center text-white">
                            <Wallet class="size-5" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white uppercase tracking-widest">{{
                                __('dashboard.revenue') }}</p>
                            <p class="text-2xl font-black text-white">{{ formatCurrency(stats.revenus_total) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="group relative overflow-hidden rounded-2xl border border-slate-100 dark:border-slate-800 bg-gradient-to-br from-amber-600/50 to-amber-900/50 dark:from-amber-400/[0.30] dark:via-amber-400/[0.08] dark:to-slate-900 shadow-sm hover:shadow-xl hover:border-amber-300/40 transition-all duration-500">
                <div class="absolute -right-4 -top-4 text-amber-600/80 dark:text-amber-400/[0.15]">
                    <Receipt class="size-28 animate-float-slow" stroke-width="1" style="animation-delay: -4s;" />
                </div>
                <div class="relative z-10 p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="size-10 rounded-xl bg-amber-600/20 flex items-center justify-center text-white">
                            <Receipt class="size-5" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white uppercase tracking-widest">{{
                                __('dashboard.payments') }}</p>
                            <p class="text-2xl font-black text-white">{{ stats.total_paiements }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="group relative overflow-hidden rounded-2xl border border-slate-100 dark:border-slate-800 bg-gradient-to-br from-sky-600/50 to-sky-900/50 dark:from-sky-400/[0.30] dark:via-sky-400/[0.08] dark:to-slate-900 shadow-sm hover:shadow-xl hover:border-sky-300/40 transition-all duration-500">
                <div class="absolute -right-4 -top-4 text-sky-600/80 dark:text-sky-400/[0.15]">
                    <Building2 class="size-28 animate-float-slow" stroke-width="1" style="animation-delay: -8s;" />
                </div>
                <div class="relative z-10 p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="size-10 rounded-xl bg-sky-600/20 flex items-center justify-center text-white">
                            <Building2 class="size-5" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white uppercase tracking-widest">{{
                                __('dashboard.certificates') }}</p>
                            <p class="text-2xl font-black text-white">{{ stats.total_certificats }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts & Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Conducteurs par Secteur -->
            <div
                class="relative overflow-hidden rounded-2xl border border-slate-100 dark:border-slate-800 bg-gradient-to-br from-blue-600/20 via-blue-800/10 to-white dark:from-rdc-blue/[0.20] dark:via-rdc-blue/[0.06] dark:to-slate-900 shadow-sm">
                <div class="absolute -left-8 top-1/2 -translate-y-1/2 text-blue-600/30 dark:text-rdc-blue/[0.12]">
                    <Activity class="size-48 animate-float-slow" stroke-width="1" />
                </div>
                <div class="relative z-10 p-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-white mb-6">{{
                        __('dashboard.by_sector') }}</h3>
                    <div class="space-y-4">
                        <div v-for="item in secteurData" :key="item.key" class="flex items-center gap-4">
                            <div
                                class="size-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                <component :is="item.icon" class="size-5"
                                    :class="item.key === 'routier' ? 'text-rdc-blue' : item.key === 'lacustre' ? 'text-cyan-500' : item.key === 'aerien' ? 'text-rdc-yellow' : 'text-rdc-red'" />
                            </div>
                            <div class="flex-1 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ item.label
                                        }}</span>
                                    <span class="text-sm font-black text-slate-900 dark:text-slate-100">{{ item.total
                                        }}</span>
                                </div>
                                <div class="h-2.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-1000" :class="item.color"
                                        :style="{ width: `${(item.total / maxSecteur) * 100}%` }" />
                                </div>
                            </div>
                        </div>
                        <p v-if="!secteurData.length" class="text-sm text-white italic text-center py-8">{{
                            __('dashboard.no_data') }}</p>
                    </div>
                </div>
            </div>

            <!-- Engins par Catégorie -->
            <div
                class="relative overflow-hidden rounded-2xl border border-slate-100 dark:border-slate-800 bg-gradient-to-br from-cyan-600/20 via-cyan-800/10 to-white dark:from-cyan-400/[0.20] dark:via-cyan-400/[0.06] dark:to-slate-900 shadow-sm">
                <div class="absolute -right-8 top-1/2 -translate-y-1/2 text-cyan-600/30 dark:text-cyan-400/[0.12]">
                    <Car class="size-48 animate-float-slow" stroke-width="1" style="animation-delay: -5s;" />
                </div>
                <div class="relative z-10 p-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-white mb-6">{{
                        __('dashboard.by_category') }}</h3>
                    <div class="space-y-4">
                        <div v-for="item in categorieData" :key="item.key" class="flex items-center gap-4">
                            <div
                                class="size-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                <component :is="item.icon" class="size-5"
                                    :class="item.key === 'roulant' ? 'text-rdc-blue' : 'text-cyan-500'" />
                            </div>
                            <div class="flex-1 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ item.label
                                        }}</span>
                                    <span class="text-sm font-black text-slate-900 dark:text-slate-100">{{ item.total
                                        }}</span>
                                </div>
                                <div class="h-2.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-1000" :class="item.color"
                                        :style="{ width: `${(item.total / maxCategorie) * 100}%` }" />
                                </div>
                            </div>
                        </div>
                        <p v-if="!categorieData.length" class="text-sm text-white italic text-center py-8">{{
                            __('dashboard.no_data') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div
            class="relative overflow-hidden rounded-2xl border border-slate-100 dark:border-slate-800 bg-gradient-to-br from-blue-600/20 via-blue-800/10 to-white dark:from-rdc-blue/[0.18] dark:via-rdc-blue/[0.05] dark:to-slate-900 shadow-sm">
            <div class="absolute -bottom-8 -right-8 text-blue-600/30 dark:text-rdc-blue/[0.10]">
                <Receipt class="size-40 animate-float-slow" stroke-width="1" style="animation-delay: -2s;" />
            </div>
            <div class="relative z-10 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-white">{{
                        __('dashboard.recent_payments') }}</h3>
                    <Link href="/paiements"
                        class="text-xs font-bold text-rdc-blue hover:underline flex items-center gap-1">
                        {{ __('dashboard.view_all') }}
                        <ArrowUpRight class="size-3" />
                    </Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800">
                                <th
                                    class="text-left py-3 px-2 text-[10px] font-black uppercase tracking-widest text-white">
                                    {{ __('dashboard.reference') }}</th>
                                <th
                                    class="text-left py-3 px-2 text-[10px] font-black uppercase tracking-widest text-white">
                                    {{ __('dashboard.amount') }}</th>
                                <th
                                    class="text-left py-3 px-2 text-[10px] font-black uppercase tracking-widest text-white">
                                    {{ __('dashboard.method') }}</th>
                                <th
                                    class="text-left py-3 px-2 text-[10px] font-black uppercase tracking-widest text-white">
                                    {{ __('dashboard.date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in stats.paiements_recents" :key="p.id"
                                class="border-b border-slate-50 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="py-3 px-2 font-bold text-slate-900 dark:text-slate-100">{{ p.reference }}
                                </td>
                                <td class="py-3 px-2 font-black text-rdc-blue">{{ formatCurrency(p.amount) }}</td>
                                <td class="py-3 px-2">
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-500">
                                        {{ p.payment_method }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 text-slate-500">{{ formatDate(p.paid_at) }}</td>
                            </tr>
                            <tr v-if="!stats.paiements_recents.length">
                                <td colspan="4" class="py-12 text-center text-sm text-white italic">{{
                                    __('dashboard.no_payments') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
