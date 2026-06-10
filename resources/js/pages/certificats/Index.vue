<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Search, FileCheck, Car, Anchor, User,
    Calendar, ShieldCheck, Printer, Download, Eye
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const props = defineProps<{
    certificats: Array<any>;
    types: Array<string>;
    stats: {
        total: number;
        identification: number;
        possession: number;
    };
}>();

const searchQuery = ref('');
const typeFilter = ref('all');

const applyFilters = () => {
    router.get('/certificats', {
        search: searchQuery.value,
        type: typeFilter.value === 'all' ? null : typeFilter.value
    }, { preserveState: true });
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Gestion', href: '#' },
            { title: 'Certificats', href: '/certificats' },
        ],
    },
});
</script>

<template>

        <Head :title="__('certificats.page_title')" />

        <div class="space-y-8 p-6">
            <!-- Header Section -->
            <div
                class="flex items-end justify-between bg-white dark:bg-slate-900 p-6 rounded-xl shadow-lg border border-slate-200 dark:border-slate-800">
                <div class="space-y-1">
                    <div class="flex items-center gap-3 text-rdc-blue mb-1">
                        <FileCheck class="size-5" />
                        <span class="text-[10px] font-black uppercase tracking-[0.3em]">{{ __('certificats.module_label') }}</span>
                    </div>
                    <h1 class="text-4xl font-black tracking-tighter">{{ __('certificats.h1_prefix') }} <span class="text-rdc-red">{{ __('certificats.h1_suffix') }}</span></h1>
                    <p class="text-sm text-slate-400 font-medium">{{ __('certificats.subtitle') }}</p>
                </div>

                <div class="flex items-center gap-4">
                    <Select v-model="typeFilter" @update:model-value="applyFilters">
                        <SelectTrigger class="w-48 h-12 rounded-xl bg-slate-50 border-none">
                            <SelectValue :placeholder="__('certificats.type_all')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ __('certificats.type_all') }}</SelectItem>
                            <SelectItem v-for="t in types" :key="t" :value="t">{{ t }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <div class="relative w-72">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                        <Input v-model="searchQuery" @keyup.enter="applyFilters"
                            :placeholder="__('certificats.search_placeholder')"
                            class="pl-10 h-12 rounded-xl bg-slate-50 border-none" />
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-xl bg-white border border-slate-200 shadow-sm dark:bg-slate-950">
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">{{ __('certificats.total_label') }}
                    </p>
                    <p class="text-3xl font-black text-slate-900 dark:text-slate-100">{{ stats.total }}</p>
                </div>
                <div class="p-6 rounded-xl bg-white border border-blue-100 shadow-sm dark:bg-slate-950">
                    <p class="text-[10px] font-black uppercase text-rdc-blue tracking-widest mb-1">{{ __('certificats.id_label') }}</p>
                    <p class="text-3xl font-black text-rdc-blue">{{ stats.identification }}</p>
                </div>
                <div class="p-6 rounded-xl bg-white border border-red-100 shadow-sm dark:bg-slate-950">
                    <p class="text-[10px] font-black uppercase text-rdc-red tracking-widest mb-1">{{ __('certificats.possession_label') }}
                    </p>
                    <p class="text-3xl font-black text-rdc-red">{{ stats.possession }}</p>
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
                                {{ __('certificats.header_number') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">
                                {{ __('certificats.header_type') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">
                                {{ __('certificats.header_owner') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">
                                {{ __('certificats.header_engine') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">
                                {{ __('certificats.header_issued') }}</th>
                            <th
                                class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest text-slate-400">
                                {{ __('certificats.header_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        <tr v-for="c in certificats" :key="c.id" class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-5">
                                <span class="text-xs font-black text-slate-900 font-mono">{{ c.certificate_number
                                    }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <Badge :class="c.type === 'Possession' ? 'bg-rdc-red' : 'bg-rdc-blue'"
                                    class="text-white font-black uppercase text-[8px] tracking-widest">
                                    {{ c.type === 'Possession' ? __('certificats.type_possession') : __('certificats.type_identification') }}
                                </Badge>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="size-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <User class="size-4" />
                                    </div>
                                    <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ c.owner_name
                                        }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div v-if="c.engin" class="flex items-center gap-3">
                                    <div
                                        class="size-8 rounded-lg bg-slate-100 flex items-center justify-center text-rdc-blue">
                                        <Car v-if="c.engin.category === 'roulant'" class="size-4" />
                                        <Anchor v-else class="size-4" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-slate-900 uppercase">{{
                                            c.engin.vehicle_brand }}</span>
                                        <span class="text-[9px] font-bold text-rdc-blue font-mono">{{
                                            c.engin.identification_number }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-xs font-bold text-slate-600">{{ new
                                    Date(c.issued_at).toLocaleDateString() }}</span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <Button size="icon" variant="ghost" as-child class="size-8 rounded-lg">
                                        <Link :href="`/certificats/${c.id}`">
                                            <Eye class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button size="icon" variant="ghost" class="size-8 rounded-lg">
                                        <Download class="size-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
</template>
