<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    Users, Car, Anchor, UserCheck, FileCheck,
    Receipt, Building2, TrendingUp, ArrowUpRight,
    ArrowDownRight, MapPin, Activity, Plane, Wallet,
    CreditCard, Landmark, Smartphone,
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
    const labels: Record<string, { label: string; icon: any; color: string; barColor: string }> = {
        routier: { label: __('sector.routier'), icon: Car, color: 'text-rdc-blue', barColor: 'bg-rdc-blue' },
        lacustre: { label: __('sector.lacustre'), icon: Anchor, color: 'text-cyan-500', barColor: 'bg-cyan-500' },
        aerien: { label: __('sector.aerien'), icon: Plane, color: 'text-rdc-yellow', barColor: 'bg-rdc-yellow' },
        ferroviaire: { label: __('sector.ferroviaire'), icon: Activity, color: 'text-rdc-red', barColor: 'bg-rdc-red' },
    };
    return Object.entries(props.stats.conducteurs_par_secteur).map(([key, total]) => {
        const meta = labels[key] || { label: key, icon: Activity, color: 'text-slate-400', barColor: 'bg-slate-400' };
        return { key, total, ...meta };
    });
});

const categorieData = computed(() => {
    const labels: Record<string, { label: string; icon: any; color: string; barColor: string }> = {
        roulant: { label: __('engins.category.roulant'), icon: Car, color: 'text-rdc-blue', barColor: 'bg-rdc-blue' },
        flottant: { label: __('engins.category.flottant'), icon: Anchor, color: 'text-cyan-500', barColor: 'bg-cyan-500' },
    };
    return Object.entries(props.stats.engins_par_categorie).map(([key, total]) => {
        const meta = labels[key] || { label: key, icon: Activity, color: 'text-slate-400', barColor: 'bg-slate-400' };
        return { key, total, ...meta };
    });
});

const maxSecteur = computed(() => Math.max(...secteurData.value.map(d => d.total), 1));
const maxCategorie = computed(() => Math.max(...categorieData.value.map(d => d.total), 1));

const paymentMethodMeta: Record<string, { label: string; icon: any; class: string }> = {
    cash: { label: 'Espèces', icon: Wallet, class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' },
    bank: { label: 'Banque', icon: Landmark, class: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400' },
    mobile_money: { label: 'Mobile', icon: Smartphone, class: 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400' },
};

const getPaymentMethod = (method: string) => {
    return paymentMethodMeta[method] || { label: method, icon: CreditCard, class: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400' };
};

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

    <div class="p-4 sm:p-6 lg:p-8 space-y-6 min-h-full">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-2xl sm:text-3xl font-black tracking-tighter text-slate-900 dark:text-white">
                    {{ __('dashboard.title') }}
                </h1>
                <div class="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400">
                    <MapPin class="size-3.5 shrink-0" />
                    <span>{{ stats.province }}</span>
                </div>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-400 dark:text-slate-500">
                <div class="flex items-center gap-1.5">
                    <div class="size-2 rounded-full bg-emerald-500 animate-pulse" />
                    <span>Système opérationnel</span>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <Link href="/conducteurs"
                class="group relative overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 shadow-sm hover:shadow-md hover:border-rdc-blue/40 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-rdc-blue/[0.04] to-transparent dark:from-rdc-blue/[0.08]" />
                <div class="relative p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="size-11 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue group-hover:scale-110 group-hover:shadow-sm transition-all duration-300">
                            <Users class="size-5" />
                        </div>
                    </div>
                    <p class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.total_conducteurs }}</p>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ __('dashboard.drivers') }}
                    </p>
                </div>
            </Link>

            <Link href="/engins"
                class="group relative overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 shadow-sm hover:shadow-md hover:border-amber-400/40 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-400/[0.04] to-transparent dark:from-amber-400/[0.08]" />
                <div class="relative p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="size-11 rounded-lg bg-amber-400/10 dark:bg-amber-400/20 flex items-center justify-center text-amber-600 dark:text-amber-400 group-hover:scale-110 group-hover:shadow-sm transition-all duration-300">
                            <Car class="size-5" />
                        </div>
                    </div>
                    <p class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.total_engins }}</p>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ __('dashboard.engines') }}
                    </p>
                </div>
            </Link>

            <Link href="/debardeurs"
                class="group relative overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 shadow-sm hover:shadow-md hover:border-rdc-red/40 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-rdc-red/[0.04] to-transparent dark:from-rdc-red/[0.08]" />
                <div class="relative p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="size-11 rounded-lg bg-rdc-red/10 dark:bg-rdc-red/20 flex items-center justify-center text-rdc-red group-hover:scale-110 group-hover:shadow-sm transition-all duration-300">
                            <Anchor class="size-5" />
                        </div>
                    </div>
                    <p class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.total_debardeurs }}</p>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ __('dashboard.stevedores') }}
                    </p>
                </div>
            </Link>

            <Link href="/permis"
                class="group relative overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 shadow-sm hover:shadow-md hover:border-emerald-400/40 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-400/[0.04] to-transparent dark:from-emerald-400/[0.08]" />
                <div class="relative p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="size-11 rounded-lg bg-emerald-400/10 dark:bg-emerald-400/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 group-hover:shadow-sm transition-all duration-300">
                            <FileCheck class="size-5" />
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ stats.permis_valides }}</p>
                            <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('dashboard.valid') }}</p>
                        </div>
                    </div>
                    <p class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.total_permis }}</p>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ __('dashboard.licenses') }}
                    </p>
                </div>
            </Link>
        </div>

        <!-- Secondary Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="relative overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="p-5">
                    <div class="flex items-center gap-4">
                        <div class="size-11 rounded-lg bg-purple-500/10 dark:bg-purple-500/20 flex items-center justify-center text-purple-600 dark:text-purple-400">
                            <Wallet class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('dashboard.revenue') }}</p>
                            <p class="text-xl font-black text-slate-900 dark:text-white truncate">{{ formatCurrency(stats.revenus_total) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="p-5">
                    <div class="flex items-center gap-4">
                        <div class="size-11 rounded-lg bg-amber-400/10 dark:bg-amber-400/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <Receipt class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('dashboard.payments') }}</p>
                            <p class="text-xl font-black text-slate-900 dark:text-white">{{ stats.total_paiements }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="p-5">
                    <div class="flex items-center gap-4">
                        <div class="size-11 rounded-lg bg-sky-500/10 dark:bg-sky-500/20 flex items-center justify-center text-sky-600 dark:text-sky-400">
                            <Building2 class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('dashboard.certificates') }}</p>
                            <p class="text-xl font-black text-slate-900 dark:text-white">{{ stats.total_certificats }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Conducteurs par Secteur -->
            <div class="rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 shadow-sm">
                <div class="p-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-5">
                        {{ __('dashboard.by_sector') }}
                    </h3>
                    <div class="space-y-4">
                        <div v-for="item in secteurData" :key="item.key" class="flex items-center gap-3">
                            <div class="size-9 rounded-lg bg-slate-100 dark:bg-slate-700/50 flex items-center justify-center shrink-0">
                                <component :is="item.icon" class="size-4.5" :class="item.color" />
                            </div>
                            <div class="flex-1 min-w-0 space-y-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate">{{ item.label }}</span>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white shrink-0">{{ item.total }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-700/50 overflow-hidden">
                                    <div
                                        class="h-full rounded-full transition-all duration-1000 ease-out"
                                        :class="item.barColor"
                                        :style="{ width: `${(item.total / maxSecteur) * 100}%` }"
                                    />
                                </div>
                            </div>
                        </div>
                        <p v-if="!secteurData.length" class="text-sm text-slate-400 italic text-center py-8">
                            {{ __('dashboard.no_data') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Engins par Catégorie -->
            <div class="rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 shadow-sm">
                <div class="p-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-5">
                        {{ __('dashboard.by_category') }}
                    </h3>
                    <div class="space-y-4">
                        <div v-for="item in categorieData" :key="item.key" class="flex items-center gap-3">
                            <div class="size-9 rounded-lg bg-slate-100 dark:bg-slate-700/50 flex items-center justify-center shrink-0">
                                <component :is="item.icon" class="size-4.5" :class="item.color" />
                            </div>
                            <div class="flex-1 min-w-0 space-y-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate">{{ item.label }}</span>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white shrink-0">{{ item.total }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-700/50 overflow-hidden">
                                    <div
                                        class="h-full rounded-full transition-all duration-1000 ease-out"
                                        :class="item.barColor"
                                        :style="{ width: `${(item.total / maxCategorie) * 100}%` }"
                                    />
                                </div>
                            </div>
                        </div>
                        <p v-if="!categorieData.length" class="text-sm text-slate-400 italic text-center py-8">
                            {{ __('dashboard.no_data') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/50 shadow-sm">
            <div class="p-5">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        {{ __('dashboard.recent_payments') }}
                    </h3>
                    <Link href="/paiements"
                        class="text-xs font-semibold text-rdc-blue hover:text-rdc-blue/80 transition-colors flex items-center gap-1">
                        {{ __('dashboard.view_all') }}
                        <ArrowUpRight class="size-3" />
                    </Link>
                </div>
                <div class="overflow-x-auto -mx-5 px-5">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700/60">
                                <th class="text-left py-3 pr-4 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                                    {{ __('dashboard.reference') }}
                                </th>
                                <th class="text-left py-3 px-4 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                                    {{ __('dashboard.amount') }}
                                </th>
                                <th class="text-left py-3 px-4 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                                    {{ __('dashboard.method') }}
                                </th>
                                <th class="text-left py-3 pl-4 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                                    {{ __('dashboard.date') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in stats.paiements_recents" :key="p.id"
                                class="border-b border-slate-100 dark:border-slate-700/30 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                                <td class="py-3.5 pr-4">
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ p.reference }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-rdc-blue">{{ formatCurrency(p.amount) }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-semibold uppercase whitespace-nowrap"
                                        :class="getPaymentMethod(p.payment_method).class"
                                    >
                                        <component :is="getPaymentMethod(p.payment_method).icon" class="size-3" />
                                        {{ getPaymentMethod(p.payment_method).label }}
                                    </span>
                                </td>
                                <td class="py-3.5 pl-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ formatDate(p.paid_at) }}</td>
                            </tr>
                            <tr v-if="!stats.paiements_recents.length">
                                <td colspan="4" class="py-12 text-center text-sm text-slate-400 italic">
                                    {{ __('dashboard.no_payments') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
