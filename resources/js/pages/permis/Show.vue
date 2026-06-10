<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ChevronLeft, User, CreditCard, Calendar,
    ShieldCheck, Printer, Download, Globe, MapPin,
    Car, Anchor, Info
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const props = defineProps<{
    permis: any;
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
            { title: 'Permis', href: '/permis' },
            { title: 'Détails', href: '#' }
        ],
    },
});
</script>

<template>

        <Head :title="`${__('permis.title')}: ${permis.license_number}`" />

        <div class="print-area p-8 max-w-5xl mx-auto space-y-8">
            <!-- Navigation -->
            <Link href="/permis"
                class="flex items-center gap-2 text-slate-400 hover:text-rdc-blue transition-all group">
                <ChevronLeft class="size-4 group-hover:-translate-x-1 transition-transform" />
                <span class="text-xs font-black uppercase tracking-widest">{{ __('permis.back_link') }}</span>
            </Link>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left: Document Preview -->
                <div class="lg:col-span-1 space-y-6">
                    <div
                        class="aspect-[1.586/1] bg-gradient-to-br from-rdc-blue to-blue-800 rounded-2xl p-6 shadow-2xl relative overflow-hidden text-white border border-white/20">
                        <!-- Card Design -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16"></div>
                        <div class="relative h-full flex flex-col justify-between">
                            <div class="flex justify-between items-start">
                                <div class="space-y-0.5">
                                    <p class="text-[8px] font-black uppercase tracking-[0.2em] opacity-80">{{ __('permis.card_country') }}</p>
                                    <p class="text-[10px] font-black uppercase tracking-widest">{{ __('permis.card_title') }}</p>
                                </div>
                                <div class="size-8 rounded-full bg-white/10 flex items-center justify-center">
                                    <ShieldCheck class="size-4" />
                                </div>
                            </div>

                            <div class="flex gap-4 items-center">
                                <div
                                    class="size-16 rounded-lg bg-white/10 border border-white/20 overflow-hidden flex items-center justify-center">
                                    <img v-if="permis.conducteur?.photo_url" :src="permis.conducteur.photo_url"
                                        class="w-full h-full object-cover" />
                                    <User v-else class="size-8 opacity-50" />
                                </div>
                                <div class="space-y-0.5">
                                    <p class="text-[7px] font-bold uppercase opacity-60">{{ __('permis.card_name_label') }}</p>
                                    <p class="text-sm font-black tracking-tight uppercase">{{ permis.conducteur?.name }}
                                    </p>
                                    <p class="text-[10px] font-black text-white font-mono mt-1">{{ permis.license_number
                                        }}</p>
                                </div>
                            </div>

                            <div class="flex justify-between items-end border-t border-white/10 pt-3">
                                <div class="flex gap-4">
                                    <div>
                                        <p class="text-[6px] font-bold uppercase opacity-60">{{ __('permis.card_category') }}</p>
                                        <p class="text-xs font-black">{{ permis.category }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[6px] font-bold uppercase opacity-60">{{ __('permis.card_expires') }}</p>
                                        <p class="text-xs font-black">{{ new
                                            Date(permis.expires_at).toLocaleDateString() }}</p>
                                    </div>
                                </div>
                                <div class="size-10 bg-white rounded p-1">
                                    <!-- Placeholder for QR -->
                                    <div
                                        class="w-full h-full bg-slate-100 flex items-center justify-center text-[6px] text-slate-400 font-bold">
                                        {{ __('permis.card_qr') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <Button variant="outline" class="flex-1 rounded-xl gap-2 font-bold h-12 border-slate-200" @click="printDocument">
                            <Printer class="size-4" /> {{ __('permis.print_btn') }}
                        </Button>
                        <Button class="flex-1 rounded-xl gap-2 font-bold h-12 bg-rdc-blue shadow-lg">
                            <Download class="size-4" /> {{ __('permis.download_btn') }}
                        </Button>
                    </div>
                </div>

                <!-- Right: Detailed Information -->
                <div class="lg:col-span-2 space-y-8">
                    <div
                        class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 space-y-8 shadow-xl shadow-slate-200/40">
                        <div
                            class="flex items-center gap-3 text-slate-900 dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-6">
                            <Info class="size-5" />
                            <h2 class="text-lg font-black tracking-tight uppercase">{{ __('permis.details_section') }}</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-6">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('permis.field_number') }}</p>
                                    <p class="text-sm font-black text-rdc-blue font-mono">{{ permis.license_number }}
                                    </p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('permis.field_issued') }}</p>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">{{
                                        formatDate(permis.issued_at) }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('permis.field_expires') }}</p>
                                    <p class="text-sm font-black text-rdc-red">{{ formatDate(permis.expires_at) }}</p>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('permis.field_status') }}</p>
                                    <Badge :class="permis.status === 'Valide' ? 'bg-green-500' : 'bg-red-500'"
                                        class="text-white font-black px-4 py-1">
                                        {{ permis.status === 'Valide' ? __('permis.status_valid') : __('permis.status_expired') }}
                                    </Badge>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('permis.field_category') }}</p>
                                    <p class="text-sm font-black text-slate-900 dark:text-slate-100">{{ __('permis.category_prefix') }} {{ permis.category }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 text-slate-900 dark:text-slate-100">
                                <User class="size-5" />
                                <h2 class="text-lg font-black tracking-tight uppercase">{{ __('permis.driver_section') }}</h2>
                            </div>
                            <Link :href="`/conducteurs/${permis.conducteur_id}`"
                                class="text-rdc-blue text-xs font-bold hover:underline">
                                {{ __('permis.driver_link') }}
                            </Link>
                        </div>

                        <div class="flex items-center gap-6">
                            <div
                                class="size-20 rounded-2xl bg-white dark:bg-slate-950 shadow-sm border border-slate-100 overflow-hidden flex items-center justify-center">
                                <img v-if="permis.conducteur?.photo_url" :src="permis.conducteur.photo_url"
                                    class="w-full h-full object-cover" />
                                <User v-else class="size-10 text-slate-300" />
                            </div>
                            <div class="space-y-2">
                                <p class="text-xl font-black text-slate-900 dark:text-slate-100">{{
                                    permis.conducteur?.name }}</p>
                                <div class="flex gap-4 text-xs font-bold text-slate-500">
                                    <span class="flex items-center gap-1">
                                        <MapPin class="size-3" /> {{ permis.conducteur?.province?.name }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <Globe class="size-3" /> {{ permis.conducteur?.nationality }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>
