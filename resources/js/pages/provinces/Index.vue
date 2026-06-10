<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Eye, Pencil, Trash2, Plus, MapPin, Mail, Phone, Globe, Calendar } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { index as provincesRoute, store, update, destroy as destroyProvince, show as showProvince } from '@/routes/provinces';
import { usePermissions } from '@/composables/usePermissions';
import { useTrans } from '@/composables/useTrans';

const { can } = usePermissions();
const { __ } = useTrans();
const props = defineProps<{
    provinces: Array<{
        id: number;
        name: string;
        address: string;
        postal_code: string;
        contacts_email: string | null;
        contacts_phone: string | null;
        language_lingala: boolean | number;
        language_kikongo: boolean | number;
        language_kiluba: boolean | number;
        language_kiswahili: boolean | number;
        creation_date: string;
    }>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Provinces', href: provincesRoute() },
        ],
    },
});

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedProvince = ref<any>(null);

const form = useForm({
    name: '',
    address: '',
    postal_code: '',
    contacts_email: '',
    contacts_phone: '',
    language_lingala: false,
    language_kikongo: false,
    language_kiluba: false,
    language_kiswahili: false,
    optional_languages: [] as string[],
    creation_date: '',
});

const newOptionalLanguage = ref('');

function addOptionalLanguage() {
    if (newOptionalLanguage.value.trim()) {
        form.optional_languages.push(newOptionalLanguage.value.trim());
        newOptionalLanguage.value = '';
    }
}

function removeOptionalLanguage(index: number) {
    form.optional_languages.splice(index, 1);
}

function formatDate(dateString: string) {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        }).format(date);
    } catch (e) {
        return dateString;
    }
}

function openCreateModal() {
    form.reset();
    form.optional_languages = [];
    form.clearErrors();
    isCreateModalOpen.value = true;
}

function openEditModal(province: any) {
    selectedProvince.value = province;

    // Format date to YYYY-MM-DD for the HTML5 date input
    let formattedDate = province.creation_date;
    if (formattedDate && formattedDate.includes('T')) {
        formattedDate = formattedDate.split('T')[0];
    } else if (formattedDate && formattedDate.includes(' ')) {
        formattedDate = formattedDate.split(' ')[0];
    }

    // Explicitly convert 1/0 to true/false for Checkbox compatibility
    form.defaults({
        name: province.name,
        address: province.address,
        postal_code: province.postal_code,
        contacts_email: province.contacts_email || '',
        contacts_phone: province.contacts_phone || '',
        language_lingala: province.language_lingala == 1 || province.language_lingala === true,
        language_kikongo: province.language_kikongo == 1 || province.language_kikongo === true,
        language_kiluba: province.language_kiluba == 1 || province.language_kiluba === true,
        language_kiswahili: province.language_kiswahili == 1 || province.language_kiswahili === true,
        optional_languages: province.optional_languages || [],
        creation_date: formattedDate,
    });
    form.reset();
    form.clearErrors();
    isEditModalOpen.value = true;
}

function openDeleteModal(province: any) {
    selectedProvince.value = province;
    isDeleteModalOpen.value = true;
}

function submitCreate() {
    form.post(store().url, {
        onSuccess: () => {
            isCreateModalOpen.value = false;
            form.reset();
        },
    });
}

function submitEdit() {
    form.put(update(selectedProvince.value.id).url, {
        onSuccess: () => {
            isEditModalOpen.value = false;
            form.reset();
        },
    });
}

function submitDelete() {
    form.delete(destroyProvince(selectedProvince.value.id).url, {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
}

const languageOptions = [
    { key: 'language_lingala', label: 'Lingala', code: 'LG' },
    { key: 'language_kikongo', label: 'Kikongo', code: 'KK' },
    { key: 'language_kiluba', label: 'Kiluba', code: 'KL' },
    { key: 'language_kiswahili', label: 'Swahili', code: 'SW' },
];
</script>

<template>

    <Head :title="__('provinces.management')" />

    <div class="space-y-8 p-1">
        <!-- Header Section -->
        <div
            class="flex items-end justify-between bg-white/95 text-slate-900 dark:bg-slate-900/95 dark:text-slate-100 backdrop-blur-sm p-6 rounded-3xl shadow-lg shadow-slate-200/40 dark:shadow-black/20 border border-slate-200/70 dark:border-slate-800/80">
            <div class="space-y-2">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rdc-blue/10 text-rdc-blue text-xs font-bold uppercase tracking-widest dark:bg-rdc-blue/15 dark:text-rdc-blue/90">
                    <MapPin class="size-3" />
                    {{ __('provinces.drc') }}
                </div>
                <h1 class="font-heading text-3xl font-black tracking-tight">
                    {{ __('provinces.title') }}
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm max-w-md leading-relaxed">
                    {{ __('provinces.description') }}
                </p>
            </div>
            <Button v-if="can('provinces.create')" @click="openCreateModal"
                class="h-12 px-6 rounded-xl bg-rdc-blue text-white shadow-lg shadow-rdc-blue/20 hover:bg-rdc-blue/90 transition-all hover:-translate-y-0.5 active:scale-95 font-bold gap-3">
                <Plus class="size-5" />
                {{ __('provinces.create') }}
            </Button>
        </div>

        <!-- Table View -->
        <div
            class="bg-white shadow-xl shadow-slate-200/50 overflow-hidden rounded-3xl border border-slate-200/70 dark:bg-slate-950 dark:border-slate-800/80">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-slate-900/90">
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                {{ __('provinces.region') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                {{ __('provinces.location') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 hidden md:table-cell">
                                {{ __('provinces.communication') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                {{ __('provinces.languages') }}</th>
                            <th
                                class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                {{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-for="province in provinces" :key="province.id"
                            class="group transition-all duration-300 hover:bg-slate-50/50 dark:hover:bg-slate-800/60">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="size-10 rounded-xl bg-rdc-blue/5 flex items-center justify-center text-rdc-blue group-hover:bg-rdc-blue group-hover:text-white transition-all duration-500 dark:bg-rdc-blue/10 dark:text-rdc-blue/80 dark:group-hover:bg-rdc-blue">
                                        <MapPin class="size-5" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                            {{ province.name }}
                                        </span>
                                        <span
                                            class="text-[10px] text-slate-400 font-medium flex items-center gap-1 mt-0.5 dark:text-slate-500">
                                            <Calendar class="size-3" />
                                            {{ formatDate(province.creation_date) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm text-slate-600 dark:text-slate-300 leading-tight">{{
                                        province.address }}</span>
                                    <span class="text-[10px] text-slate-400 mt-1 font-mono dark:text-slate-500">CP: {{
                                        province.postal_code }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 hidden md:table-cell">
                                <div class="flex flex-col gap-1.5">
                                    <div v-if="province.contacts_email"
                                        class="flex items-center gap-2 text-xs text-slate-500 hover:text-rdc-blue transition-colors dark:text-slate-400 dark:hover:text-rdc-blue/80">
                                        <Mail class="size-3.5 opacity-60" />
                                        {{ province.contacts_email }}
                                    </div>
                                    <div v-if="province.contacts_phone"
                                        class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                        <Phone class="size-3.5 opacity-60 text-rdc-yellow" />
                                        {{ province.contacts_phone }}
                                    </div>
                                    <span v-if="!province.contacts_email && !province.contacts_phone"
                                        class="text-xs text-slate-300 dark:text-slate-500">{{ __('provinces.no_contact') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap gap-1.5">
                                    <template v-for="lang in languageOptions" :key="lang.key">
                                        <Badge v-if="province[lang.key as keyof typeof province]" variant="secondary"
                                            class="h-5 px-2 text-[9px] font-bold rounded-lg bg-slate-100 text-slate-600 border-none group-hover:bg-rdc-blue/10 group-hover:text-rdc-blue transition-all dark:bg-slate-800 dark:text-slate-300 dark:group-hover:bg-rdc-blue/15">
                                            {{ lang.code }}
                                        </Badge>
                                    </template>
                                    <Badge v-for="(optLang, idx) in province.optional_languages" :key="idx"
                                        variant="outline"
                                        class="h-5 px-2 text-[9px] font-bold rounded-lg border-slate-200 text-slate-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                                        {{ optLang }}
                                    </Badge>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex justify-end items-center gap-2">
                                    <Button size="icon" variant="ghost" as-child
                                        class="size-9 rounded-xl hover:bg-rdc-blue/10 hover:text-rdc-blue group/btn dark:hover:bg-rdc-blue/15">
                                        <a :href="showProvince(province.id).url">
                                            <Eye class="size-4 transition-transform group-hover/btn:scale-110" />
                                        </a>
                                    </Button>
                                    <Button v-if="can('provinces.edit')" size="icon" variant="ghost" @click="openEditModal(province)"
                                        class="size-9 rounded-xl hover:bg-rdc-yellow/10 hover:text-rdc-yellow group/btn dark:hover:bg-rdc-yellow/15">
                                        <Pencil class="size-4 transition-transform group-hover/btn:scale-110" />
                                    </Button>
                                    <Button v-if="can('provinces.delete')" size="icon" variant="ghost" @click="openDeleteModal(province)"
                                        class="size-9 rounded-xl hover:bg-rdc-red/10 hover:text-rdc-red group/btn dark:hover:bg-rdc-red/15">
                                        <Trash2 class="size-4 transition-transform group-hover/btn:scale-110" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!provinces || provinces.length === 0">
                            <td colspan="5" class="px-4 py-24 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div
                                        class="size-20 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center">
                                        <Globe class="size-10 text-slate-200 dark:text-slate-500" />
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ __('provinces.no_data') }}</p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500">{{ __('provinces.no_data_description') }}</p>
                                    </div>
                                    <Button v-if="can('provinces.create')" variant="outline" @click="openCreateModal"
                                        class="rounded-xl mt-2 border-slate-200 text-slate-600 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800/70">
                                        {{ __('provinces.initialize') }}
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
    <Dialog v-model:open="isCreateModalOpen" @update:open="(val) => !val && form.reset()">
        <DialogContent class="sm:max-w-[550px] p-0 rounded-3xl overflow-hidden border-none shadow-2xl">
            <div class="p-8 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100">
                <DialogHeader class="mb-8">
                    <div
                        class="size-12 rounded-xl bg-rdc-blue/10 flex items-center justify-center text-rdc-blue mb-4 dark:bg-rdc-blue/15 dark:text-rdc-blue/90">
                        <Plus class="size-6" />
                    </div>
                    <DialogTitle class="text-2xl font-black">{{ __('provinces.create_region') }}</DialogTitle>
                    <DialogDescription class="text-slate-500 dark:text-slate-400">
                        {{ __('provinces.create_description') }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitCreate" class="space-y-6">
                    <div class="grid gap-6">
                        <div class="space-y-2">
                            <Label for="name"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 ml-1">{{ __('provinces.name') }}</Label>
                            <Input id="name" v-model="form.name" :placeholder="__('provinces.name_placeholder')" required
                                class="h-12 rounded-xl border-slate-100 bg-slate-50/70 dark:bg-slate-900/80 dark:border-slate-700 focus:bg-white dark:focus:bg-slate-900 focus:ring-rdc-blue" />
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="postal_code"
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 ml-1">{{ __('provinces.postal_code') }}</Label>
                                <Input id="postal_code" v-model="form.postal_code" :placeholder="__('provinces.postal_code_placeholder')" required
                                    class="h-12 rounded-xl border-slate-100 dark:border-slate-700" />
                            </div>
                            <div class="space-y-2">
                                <Label for="creation_date"
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 ml-1">{{ __('provinces.integration_date') }}</Label>
                                <Input id="creation_date" v-model="form.creation_date" type="date" required
                                    class="h-12 rounded-xl border-slate-100 dark:border-slate-700" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="address"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 ml-1">{{ __('provinces.address') }}</Label>
                            <Input id="address" v-model="form.address" :placeholder="__('provinces.address_placeholder')" required
                                class="h-12 rounded-xl border-slate-100 dark:border-slate-700" />
                        </div>

                        <div class="bg-slate-50 p-6 rounded-3xl space-y-4 dark:bg-slate-900/90">
                            <p
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 flex items-center gap-2">
                                <Globe class="size-3 text-rdc-blue" /> {{ __('provinces.work_languages') }}
                            </p>
                            <div class="flex flex-wrap gap-x-8 gap-y-4">
                                <div v-for="lang in languageOptions" :key="lang.key"
                                    class="flex items-center space-x-3">
                                    <input type="checkbox" :id="'c_' + lang.key" v-model="(form as any)[lang.key]"
                                        class="size-5 rounded border-slate-300 text-rdc-blue focus:ring-rdc-blue transition-all cursor-pointer dark:border-slate-600" />
                                    <label :for="'c_' + lang.key"
                                        class="text-sm font-bold text-slate-600 dark:text-slate-300 cursor-pointer select-none">
                                        {{ lang.label }}
                                    </label>
                                </div>
                            </div>

                            <Separator class="bg-slate-200/50 dark:bg-slate-700/50 my-4" />

                            <div class="space-y-3">
                                <p
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">
                                    {{ __('provinces.other_languages') }}</p>
                                <div class="flex gap-2">
                                    <Input v-model="newOptionalLanguage" :placeholder="__('provinces.other_languages_placeholder')"
                                        @keydown.enter.prevent="addOptionalLanguage"
                                        class="h-10 rounded-xl border-slate-200 bg-white dark:bg-slate-950 dark:border-slate-700" />
                                    <Button type="button" @click="addOptionalLanguage" size="icon" variant="outline"
                                        class="size-10 rounded-xl border-slate-200 dark:border-slate-700">
                                        <Plus class="size-4" />
                                    </Button>
                                </div>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    <Badge v-for="(optLang, idx) in form.optional_languages" :key="idx"
                                        class="pl-3 pr-1 py-1 rounded-lg bg-white border-slate-200 text-slate-600 font-bold text-[10px] flex items-center gap-2 dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300">
                                        {{ optLang }}
                                        <button type="button" @click="removeOptionalLanguage(idx)"
                                            class="size-4 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center transition-colors">
                                            <Trash2 class="size-3 text-rdc-red" />
                                        </button>
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>

                    <DialogFooter class="mt-8 gap-3 sm:justify-end">
                        <Button type="button" variant="ghost" @click="isCreateModalOpen = false"
                            class="rounded-xl font-bold text-slate-400 dark:text-slate-300">{{ __('common.cancel') }}</Button>
                        <Button type="submit" :disabled="form.processing"
                            class="h-12 px-8 rounded-xl bg-rdc-blue text-white hover:bg-rdc-blue/90 shadow-lg shadow-rdc-blue/20 font-bold">
                            {{ __('provinces.confirm_create') }}
                        </Button>
                    </DialogFooter>
                </form>
            </div>
        </DialogContent>
    </Dialog>

    <!-- Edit Modal -->
    <Dialog v-model:open="isEditModalOpen" @update:open="(val) => !val && form.reset()">
        <DialogContent class="sm:max-w-[550px] p-0 rounded-3xl overflow-hidden border-none shadow-2xl">
            <div class="p-8 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100">
                <DialogHeader class="mb-8">
                    <div
                        class="size-12 rounded-xl bg-rdc-yellow/10 flex items-center justify-center text-rdc-yellow mb-4 dark:bg-rdc-yellow/15 dark:text-rdc-yellow/90">
                        <Pencil class="size-6" />
                    </div>
                    <DialogTitle class="text-2xl font-black">{{ __('provinces.edit') }}</DialogTitle>
                    <DialogDescription class="text-slate-500 dark:text-slate-400">
                        {{ __('provinces.edit_description', { name: selectedProvince?.name }) }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitEdit" class="space-y-6">
                    <div class="grid gap-6">
                        <div class="space-y-2">
                            <Label for="e_name"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 ml-1">{{ __('provinces.label') }}</Label>
                            <Input id="e_name" v-model="form.name" required
                                class="h-12 rounded-xl border-slate-100 bg-slate-50/70 dark:bg-slate-900/80 dark:border-slate-700" />
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="e_postal_code"
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 ml-1">{{ __('provinces.postal_code') }}</Label>
                                <Input id="e_postal_code" v-model="form.postal_code" required
                                    class="h-12 rounded-xl border-slate-100 dark:border-slate-700" />
                            </div>
                            <div class="space-y-2">
                                <Label for="e_creation_date"
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 ml-1">{{ __('provinces.integration') }}</Label>
                                <Input id="e_creation_date" v-model="form.creation_date" type="date" required
                                    class="h-12 rounded-xl border-slate-100 dark:border-slate-700" />
                            </div>
                        </div>

                        <div class="bg-slate-50 p-6 rounded-3xl space-y-6 dark:bg-slate-900/90">
                            <div class="space-y-4">
                                <p
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">
                                    {{ __('provinces.communication') }}</p>
                                <div class="grid grid-cols-2 gap-4">
                                    <Input v-model="form.contacts_email" :placeholder="__('common.email')"
                                        class="rounded-xl border-none bg-white dark:bg-slate-950 shadow-sm h-10 text-xs" />
                                    <Input v-model="form.contacts_phone" :placeholder="__('common.phone')"
                                        class="rounded-xl border-none bg-white dark:bg-slate-950 shadow-sm h-10 text-xs" />
                                </div>
                            </div>

                            <div class="space-y-4">
                                <p
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">
                                    {{ __('provinces.national_languages') }}</p>
                                <div class="flex flex-wrap gap-x-8 gap-y-4">
                                    <div v-for="lang in languageOptions" :key="lang.key"
                                        class="flex items-center space-x-3">
                                        <input type="checkbox" :id="'e_' + lang.key" v-model="(form as any)[lang.key]"
                                            class="size-5 rounded border-slate-300 text-rdc-yellow focus:ring-rdc-yellow transition-all cursor-pointer dark:border-slate-600" />
                                        <label :for="'e_' + lang.key"
                                            class="text-sm font-bold text-slate-600 dark:text-slate-300 cursor-pointer">
                                            {{ lang.label }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <p
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">
                                    {{ __('provinces.other_languages') }}</p>
                                <div class="flex gap-2">
                                    <Input v-model="newOptionalLanguage" :placeholder="__('provinces.other_languages_placeholder_edit')"
                                        @keydown.enter.prevent="addOptionalLanguage"
                                        class="h-10 rounded-xl border-slate-200 bg-white dark:bg-slate-950 dark:border-slate-700" />
                                    <Button type="button" @click="addOptionalLanguage" size="icon" variant="outline"
                                        class="size-10 rounded-xl border-slate-200 dark:border-slate-700">
                                        <Plus class="size-4" />
                                    </Button>
                                </div>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    <Badge v-for="(optLang, idx) in form.optional_languages" :key="idx"
                                        class="pl-3 pr-1 py-1 rounded-lg bg-white border-slate-200 text-slate-600 font-bold text-[10px] flex items-center gap-2 dark:bg-slate-900 dark:border-slate-700 dark:text-slate-300">
                                        {{ optLang }}
                                        <button type="button" @click="removeOptionalLanguage(idx)"
                                            class="size-4 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center transition-colors">
                                            <Trash2 class="size-3 text-rdc-red" />
                                        </button>
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>

                    <DialogFooter class="mt-8 gap-3 sm:justify-end">
                        <Button type="button" variant="ghost" @click="isEditModalOpen = false"
                            class="rounded-xl font-bold text-slate-700 dark:text-slate-300">{{ __('common.cancel') }}</Button>
                        <Button type="submit" :disabled="form.processing"
                            class="h-12 px-8 rounded-xl bg-slate-900 text-white hover:bg-slate-800 shadow-lg font-bold">
                            {{ __('common.save') }}
                        </Button>
                    </DialogFooter>
                </form>
            </div>
        </DialogContent>
    </Dialog>

    <!-- Delete Confirmation Modal -->
    <Dialog v-model:open="isDeleteModalOpen">
        <DialogContent class="sm:max-w-[420px] p-0 rounded-3xl overflow-hidden border-none shadow-2xl">
            <div class="p-8 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100">
                <DialogHeader class="mb-6">
                    <div
                        class="size-12 rounded-xl bg-rdc-red/10 flex items-center justify-center text-rdc-red mb-4 dark:bg-rdc-red/15 dark:text-rdc-red/90">
                        <Trash2 class="size-6" />
                    </div>
                    <DialogTitle class="text-xl font-black">{{ __('provinces.delete_title') }}</DialogTitle>
                    <DialogDescription class="text-slate-500 dark:text-slate-400 pt-2 leading-relaxed">
                        {{ __('provinces.delete_confirm', { name: selectedProvince?.name }) }}
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="mt-8 gap-3">
                    <Button type="button" variant="ghost" @click="isDeleteModalOpen = false"
                        class="flex-1 rounded-xl font-bold text-slate-400 dark:text-slate-300">{{ __('common.cancel') }}</Button>
                    <Button type="button" :disabled="form.processing" @click="submitDelete"
                        class="flex-1 h-12 rounded-xl bg-rdc-red text-white hover:bg-rdc-red/90 shadow-lg shadow-rdc-red/20 font-bold">
                        {{ __('common.delete') }}
                    </Button>
                </DialogFooter>
            </div>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
/* Haute densité et typographie pro */
th {
    letter-spacing: 0.15em;
}

td {
    vertical-align: middle;
}
</style>
