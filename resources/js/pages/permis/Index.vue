<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Search, CreditCard, User, Calendar, ShieldCheck,
    ChevronRight, Printer, Download, Eye, Globe, AlertTriangle
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const props = defineProps<{
    permis: Array<any>;
    stats: {
        total: number;
        active: number;
        expired: number;
    };
}>();

const searchQuery = ref('');

const filterPermis = () => {
    router.get('/permis', { search: searchQuery.value }, { preserveState: true });
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Gestion', href: '#' },
            { title: 'Permis', href: '/permis' },
        ],
    },
});
</script>

<template>

        <Head :title="__('permis.title_list')" />

        <div class="space-y-8 p-6">
            <!-- Header Section -->
            <div
                class="flex items-end justify-between bg-white dark:bg-slate-900 p-6 rounded-xl shadow-lg border border-slate-200 dark:border-slate-800">
                <div class="space-y-1">
                    <div class="flex items-center gap-3 text-rdc-blue mb-1">
                        <CreditCard class="size-5" />
                        <span class="text-[10px] font-black uppercase tracking-[0.3em]">{{ __('permis.module_label') }}</span>
                    </div>
                    <h1 class="text-4xl font-black tracking-tighter">{{ __('permis.h1_prefix') }} <span class="text-rdc-red">{{ __('permis.h1_suffix') }}</span></h1>
                    <p class="text-sm text-slate-400 font-medium">{{ __('permis.subtitle') }}</p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="relative w-72">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                        <Input v-model="searchQuery" @keyup.enter="filterPermis"
                            :placeholder="__('permis.search_placeholder')"
                            class="pl-10 h-12 rounded-xl bg-slate-50 border-none" />
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="relative overflow-hidden p-6 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/60 shadow-sm">
                    <div class="absolute inset-0 bg-gradient-to-br from-slate-50 to-transparent dark:from-slate-800/50" />
                    <div class="relative">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="size-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500">
                                <CreditCard class="size-5" />
                            </div>
                        </div>
                        <p class="text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500 tracking-wider">{{ __('permis.total_label') }}</p>
                        <p class="text-3xl font-black text-slate-900 dark:text-white mt-0.5">{{ stats.total }}</p>
                    </div>
                </div>
                <div class="relative overflow-hidden p-6 rounded-xl bg-white dark:bg-slate-900 border border-emerald-100 dark:border-emerald-500/20 shadow-sm">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-transparent dark:from-emerald-500/[0.06]" />
                    <div class="relative">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="size-10 rounded-lg bg-emerald-500/10 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                <ShieldCheck class="size-5" />
                            </div>
                        </div>
                        <p class="text-[10px] font-bold uppercase text-emerald-600 dark:text-emerald-400 tracking-wider">{{ __('permis.valid_label') }}</p>
                        <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ stats.active }}</p>
                    </div>
                </div>
                <div class="relative overflow-hidden p-6 rounded-xl bg-white dark:bg-slate-900 border border-red-100 dark:border-rdc-red/20 shadow-sm">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-transparent dark:from-rdc-red/[0.06]" />
                    <div class="relative">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="size-10 rounded-lg bg-rdc-red/10 dark:bg-rdc-red/20 flex items-center justify-center text-rdc-red">
                                <AlertTriangle class="size-5" />
                            </div>
                        </div>
                        <p class="text-[10px] font-bold uppercase text-rdc-red tracking-wider">{{ __('permis.expired_label') }}</p>
                        <p class="text-3xl font-black text-rdc-red mt-0.5">{{ stats.expired }}</p>
                    </div>
                </div>
            </div>

            <!-- List View -->
            <div
                class="bg-white shadow-xl overflow-hidden rounded-xl border border-slate-200 dark:bg-slate-950 dark:border-slate-800">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800">
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">
                                {{ __('permis.header_number') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">
                                {{ __('permis.header_holder') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">
                                {{ __('permis.header_category') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">
                                {{ __('permis.header_validity') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">
                                {{ __('permis.header_status') }}</th>
                            <th
                                class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest text-slate-400">
                                {{ __('permis.header_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        <tr v-for="p in permis" :key="p.id" class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-5">
                                <span class="text-xs font-black text-rdc-blue font-mono">{{ p.license_number }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="size-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <User class="size-4" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{
                                            p.conducteur?.name }}</span>
                                        <span class="text-[10px] text-slate-400 font-medium">{{
                                            p.conducteur?.province?.name }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <Badge variant="secondary" class="font-black px-3">{{ __('permis.category_prefix') }} {{ p.category }}</Badge>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase">{{ __('permis.expires_label') }}</span>
                                    <span class="text-xs font-black text-slate-700 dark:text-slate-300">{{ new
                                        Date(p.expires_at).toLocaleDateString() }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <Badge :class="p.status === 'Valide' ? 'bg-green-500' : 'bg-red-500'"
                                    class="text-white font-black uppercase text-[8px] tracking-widest">
                                    {{ p.status === 'Valide' ? __('permis.status_valid') : __('permis.status_expired') }}
                                </Badge>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <Button size="icon" variant="ghost" as-child class="size-8 rounded-lg">
                                        <Link :href="`/permis/${p.id}`">
                                            <Eye class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button size="icon" variant="ghost" class="size-8 rounded-lg">
                                        <Printer class="size-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
</template>
