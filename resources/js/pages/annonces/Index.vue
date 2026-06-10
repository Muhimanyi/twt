<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Plus, Search, Pencil, Trash2, Eye, Newspaper,
    Calendar, User, Globe, Clock, CheckCircle2,
    XCircle, ArrowUpDown, ArrowUp, ArrowDown,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import {
    Dialog, DialogContent, DialogDescription,
    DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog';
import { usePermissions } from '@/composables/usePermissions';
import { useTrans } from '@/composables/useTrans';

const { can } = usePermissions();
const { __ } = useTrans();

const props = defineProps<{
    annonces: any;
    stats: {
        total: number;
        published: number;
        drafts: number;
    };
    filters: {
        search?: string;
    };
}>();

const isFormModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const isViewModalOpen = ref(false);
const formMode = ref<'create' | 'edit'>('create');
const selectedAnnonce = ref<any>(null);
const searchQuery = ref(props.filters.search || '');

const sortKey = ref('created_at');
const sortOrder = ref<'asc' | 'desc'>('desc');

const sortBy = (key: string) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortOrder.value = 'asc';
    }
    router.get('/annonces', { sort: key, order: sortOrder.value }, { preserveState: true });
};

const form = useForm({
    title: '',
    content: '',
    excerpt: '',
    featured_image: '',
    is_published: false,
    published_at: '',
    province_id: '' as any,
});

const openCreateModal = () => {
    form.reset();
    formMode.value = 'create';
    isFormModalOpen.value = true;
};

const openEditModal = (item: any) => {
    formMode.value = 'edit';
    selectedAnnonce.value = item;
    form.title = item.title;
    form.content = item.content;
    form.excerpt = item.excerpt || '';
    form.featured_image = item.featured_image || '';
    form.is_published = item.is_published;
    form.published_at = item.published_at ? item.published_at.split('T')[0] : '';
    form.province_id = item.province_id || '';
    isFormModalOpen.value = true;
};

const openViewModal = (item: any) => {
    selectedAnnonce.value = item;
    isViewModalOpen.value = true;
};

const openDeleteModal = (item: any) => {
    selectedAnnonce.value = item;
    isDeleteModalOpen.value = true;
};

const submitCreate = () => {
    form.post('/annonces', {
        preserveScroll: true,
        onSuccess: () => {
            isFormModalOpen.value = false;
            form.reset();
        },
    });
};

const submitEdit = () => {
    form.transform(d => ({ ...d, _method: 'PUT' })).post(`/annonces/${selectedAnnonce.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            isFormModalOpen.value = false;
            form.reset();
        },
    });
};

const submitDelete = () => {
    router.delete(`/annonces/${selectedAnnonce.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            isDeleteModalOpen.value = false;
        },
    });
};

const applySearch = () => {
    router.get('/annonces', { search: searchQuery.value }, { preserveState: true });
};

const formatDate = (d: string | null) => {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('fr-FR', {
        day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
    });
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Communication', href: '#' },
            { title: 'Annonces', href: '/annonces' },
        ],
    },
});
</script>

<template>
    <Head :title="__('annonces.page_title')" />

    <div class="p-4 sm:p-6 lg:p-8 space-y-6 min-h-full">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-3 text-rdc-blue mb-1">
                    <Newspaper class="size-5" />
                    <span class="text-[10px] font-black uppercase tracking-[0.3em]">{{ __('annonces.module_label') }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tighter text-slate-900 dark:text-white">
                    {{ __('annonces.page_title') }}
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">{{ __('annonces.subtitle') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative w-64">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                    <Input v-model="searchQuery" :placeholder="__('annonces.search_placeholder')"
                        class="pl-10 h-12 rounded-xl bg-slate-50 dark:bg-slate-900 border-none"
                        @keyup.enter="applySearch" />
                </div>
                <Button v-if="can('annonces.create')" @click="openCreateModal"
                    class="h-12 px-6 rounded-xl bg-rdc-blue text-white shadow-lg shadow-rdc-blue/20 hover:bg-rdc-blue/90 transition-all font-bold gap-3">
                    <Plus class="size-5" />
                    {{ __('annonces.create') }}
                </Button>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="relative overflow-hidden p-5 rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-900 shadow-sm">
                <div class="absolute inset-0 bg-gradient-to-br from-slate-50 to-transparent dark:from-slate-800/50" />
                <div class="relative">
                    <div class="size-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500 mb-3">
                        <Newspaper class="size-5" />
                    </div>
                    <p class="text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500 tracking-wider">{{ __('annonces.total_label') }}</p>
                    <p class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ stats.total }}</p>
                </div>
            </div>
            <div class="relative overflow-hidden p-5 rounded-xl border border-emerald-100 dark:border-emerald-500/20 bg-white dark:bg-slate-900 shadow-sm">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-transparent dark:from-emerald-500/[0.06]" />
                <div class="relative">
                    <div class="size-10 rounded-lg bg-emerald-500/10 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-3">
                        <CheckCircle2 class="size-5" />
                    </div>
                    <p class="text-[10px] font-bold uppercase text-emerald-600 dark:text-emerald-400 tracking-wider">{{ __('annonces.published_label') }}</p>
                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ stats.published }}</p>
                </div>
            </div>
            <div class="relative overflow-hidden p-5 rounded-xl border border-amber-100 dark:border-amber-500/20 bg-white dark:bg-slate-900 shadow-sm">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-transparent dark:from-amber-500/[0.06]" />
                <div class="relative">
                    <div class="size-10 rounded-lg bg-amber-500/10 dark:bg-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400 mb-3">
                        <Clock class="size-5" />
                    </div>
                    <p class="text-[10px] font-bold uppercase text-amber-600 dark:text-amber-400 tracking-wider">{{ __('annonces.drafts_label') }}</p>
                    <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-0.5">{{ stats.drafts }}</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800">
                            <th class="text-left py-4 px-6 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 cursor-pointer select-none" @click="sortBy('title')">
                                <div class="flex items-center gap-1.5">
                                    {{ __('annonces.table_title') }}
                                    <ArrowUpDown class="size-3" />
                                </div>
                            </th>
                            <th class="text-left py-4 px-4 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('annonces.table_status') }}</th>
                            <th class="text-left py-4 px-4 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('annonces.table_author') }}</th>
                            <th class="text-left py-4 px-4 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('annonces.table_date') }}</th>
                            <th class="text-right py-4 px-6 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        <tr v-for="a in annonces.data" :key="a.id"
                            class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="space-y-0.5">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ a.title }}</p>
                                    <p v-if="a.excerpt" class="text-xs text-slate-400 dark:text-slate-500 line-clamp-1">{{ a.excerpt }}</p>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <Badge v-if="a.is_published"
                                    class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 font-bold text-[10px] px-2.5 py-0.5">
                                    <CheckCircle2 class="size-3 mr-1 inline" />
                                    {{ __('annonces.status_published') }}
                                </Badge>
                                <Badge v-else variant="outline"
                                    class="text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800 font-bold text-[10px] px-2.5 py-0.5">
                                    <Clock class="size-3 mr-1 inline" />
                                    {{ __('annonces.status_draft') }}
                                </Badge>
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ a.author?.name }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ formatDate(a.published_at || a.created_at) }}</span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex justify-end gap-1">
                                    <Button size="icon" variant="ghost" class="size-8 rounded-lg" @click="openViewModal(a)">
                                        <Eye class="size-4" />
                                    </Button>
                                    <Button v-if="can('annonces.edit')" size="icon" variant="ghost" class="size-8 rounded-lg" @click="openEditModal(a)">
                                        <Pencil class="size-4" />
                                    </Button>
                                    <Button v-if="can('annonces.delete')" size="icon" variant="ghost" class="size-8 rounded-lg text-rdc-red hover:text-rdc-red hover:bg-rdc-red/10" @click="openDeleteModal(a)">
                                        <Trash2 class="size-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!annonces.data?.length">
                            <td colspan="5" class="py-16 text-center">
                                <Newspaper class="size-12 mx-auto text-slate-300 dark:text-slate-600 mb-3" />
                                <p class="text-sm text-slate-400 dark:text-slate-500 italic">{{ __('annonces.empty') }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="annonces.last_page > 1" class="flex items-center justify-between px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                <p class="text-xs text-slate-400">
                    {{ annonces.from }}–{{ annonces.to }} / {{ annonces.total }}
                </p>
                <div class="flex gap-2">
                    <Button variant="outline" size="sm" class="rounded-lg text-xs h-8" :disabled="!annonces.prev_page_url"
                        @click="router.get(annonces.prev_page_url, {}, { preserveState: true })">
                        {{ __('common.previous') }}
                    </Button>
                    <Button variant="outline" size="sm" class="rounded-lg text-xs h-8" :disabled="!annonces.next_page_url"
                        @click="router.get(annonces.next_page_url, {}, { preserveState: true })">
                        {{ __('common.next') }}
                    </Button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <Dialog v-model:open="isViewModalOpen">
        <DialogContent class="max-w-2xl rounded-xl border-none shadow-2xl">
            <DialogHeader>
                <DialogTitle class="text-2xl font-black">{{ __('annonces.details_title') }}</DialogTitle>
            </DialogHeader>
            <div v-if="selectedAnnonce" class="space-y-5">
                <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                    <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                        <Newspaper class="size-4" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('annonces.title') }}</p>
                        <p class="text-base font-bold text-slate-900 dark:text-white mt-0.5">{{ selectedAnnonce.title }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                    <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                        <User class="size-4" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('annonces.author') }}</p>
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mt-0.5">{{ selectedAnnonce.author?.name }}</p>
                    </div>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">{{ __('annonces.content') }}</p>
                    <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed">{{ selectedAnnonce.content }}</p>
                </div>
                <div v-if="selectedAnnonce.excerpt" class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                    <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                        <Globe class="size-4" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ __('annonces.excerpt') }}</p>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5 italic">{{ selectedAnnonce.excerpt }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Badge v-if="selectedAnnonce.is_published"
                        class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 font-bold text-[10px]">
                        <CheckCircle2 class="size-3 mr-1 inline" />
                        {{ __('annonces.status_published') }}
                    </Badge>
                    <Badge v-else variant="outline"
                        class="text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800 font-bold text-[10px]">
                        <Clock class="size-3 mr-1 inline" />
                        {{ __('annonces.status_draft') }}
                    </Badge>
                    <span class="text-xs text-slate-400 ml-auto">{{ formatDate(selectedAnnonce.published_at || selectedAnnonce.created_at) }}</span>
                </div>
            </div>
        </DialogContent>
    </Dialog>

    <!-- Create / Edit Modal -->
    <Dialog v-model:open="isFormModalOpen">
        <DialogContent class="max-w-3xl p-0 rounded-xl overflow-hidden border-none shadow-2xl">
            <div class="bg-white dark:bg-slate-950 max-h-[85vh] overflow-y-auto custom-scrollbar">
                <DialogHeader class="p-8 pb-0">
                    <div class="size-12 rounded-xl bg-rdc-blue/10 flex items-center justify-center text-rdc-blue mb-4">
                        <Newspaper class="size-6" />
                    </div>
                    <DialogTitle class="text-2xl font-black">{{ formMode === 'create' ? __('annonces.create_title') : __('annonces.edit_title') }}</DialogTitle>
                    <DialogDescription>{{ formMode === 'create' ? __('annonces.create_description') : __('annonces.edit_description') }}</DialogDescription>
                </DialogHeader>

                <form @submit.prevent="formMode === 'create' ? submitCreate() : submitEdit()" class="p-8 pt-6 space-y-6">
                    <div class="space-y-1.5">
                        <Label for="title" class="text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500 ml-1">{{ __('annonces.title') }}</Label>
                        <Input id="title" v-model="form.title" :placeholder="__('annonces.title_placeholder')"
                            class="h-12 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-rdc-blue" />
                        <p v-if="form.errors.title" class="text-xs text-rdc-red font-bold ml-1">{{ form.errors.title }}</p>
                    </div>

                    <div class="space-y-1.5">
                        <Label for="excerpt" class="text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500 ml-1">{{ __('annonces.excerpt') }}</Label>
                        <Input id="excerpt" v-model="form.excerpt" :placeholder="__('annonces.excerpt_placeholder')"
                            class="h-12 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-rdc-blue" />
                    </div>

                    <div class="space-y-1.5">
                        <Label for="content" class="text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500 ml-1">{{ __('annonces.content') }}</Label>
                        <textarea id="content" v-model="form.content" :placeholder="__('annonces.content_placeholder')" rows="8"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 text-sm focus:outline-none focus:ring-2 focus:ring-rdc-blue/50 focus:border-rdc-blue resize-y" />
                        <p v-if="form.errors.content" class="text-xs text-rdc-red font-bold ml-1">{{ form.errors.content }}</p>
                    </div>

                    <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-700/30">
                        <div class="size-9 rounded-lg bg-rdc-blue/10 dark:bg-rdc-blue/20 flex items-center justify-center text-rdc-blue shrink-0 mt-0.5">
                            <Clock class="size-4" />
                        </div>
                        <div class="flex-1 space-y-4">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="is_published" v-model="form.is_published"
                                    class="size-5 rounded border-slate-300 text-rdc-blue focus:ring-rdc-blue cursor-pointer" />
                                <Label for="is_published" class="text-sm font-bold text-slate-700 dark:text-slate-300 cursor-pointer select-none">{{ __('annonces.publish') }}</Label>
                            </div>
                            <div v-if="form.is_published" class="space-y-1.5">
                                <Label for="published_at" class="text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500">{{ __('annonces.published_at') }}</Label>
                                <Input id="published_at" v-model="form.published_at" type="datetime-local"
                                    class="h-11 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900" />
                            </div>
                        </div>
                    </div>

                    <DialogFooter class="pt-4 border-t border-slate-100 dark:border-slate-800">
                        <Button type="button" variant="ghost" @click="isFormModalOpen = false"
                            class="rounded-xl font-bold">{{ __('common.cancel') }}</Button>
                        <Button type="submit" :disabled="form.processing"
                            class="h-12 px-10 rounded-xl bg-rdc-blue text-white shadow-xl shadow-rdc-blue/20 font-black">
                            {{ formMode === 'create' ? __('annonces.confirm_create') : __('annonces.confirm_update') }}
                        </Button>
                    </DialogFooter>
                </form>
            </div>
        </DialogContent>
    </Dialog>

    <!-- Delete Modal -->
    <Dialog v-model:open="isDeleteModalOpen">
        <DialogContent class="sm:max-w-[400px] rounded-xl border-none shadow-2xl">
            <div class="text-center py-4">
                <div class="size-16 rounded-2xl bg-rdc-red/10 flex items-center justify-center mx-auto mb-4">
                    <Trash2 class="size-8 text-rdc-red" />
                </div>
                <DialogTitle class="text-xl font-black mb-2">{{ __('annonces.delete_title') }}</DialogTitle>
                <DialogDescription class="text-sm text-slate-500 dark:text-slate-400">
                    {{ __('annonces.delete_confirm', { title: selectedAnnonce?.title }) }}
                </DialogDescription>
            </div>
            <DialogFooter class="gap-3 sm:justify-center">
                <Button type="button" variant="outline" @click="isDeleteModalOpen = false"
                    class="rounded-xl font-bold min-w-[100px]">{{ __('common.cancel') }}</Button>
                <Button type="button" @click="submitDelete"
                    class="rounded-xl font-bold min-w-[100px] bg-rdc-red text-white hover:bg-rdc-red/90 shadow-lg shadow-rdc-red/20">
                    {{ __('annonces.confirm_delete') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
