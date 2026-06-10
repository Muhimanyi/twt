<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        router.visit('/dashboard');
    }
}
</script>

<template>
    <Button variant="ghost" size="icon" @click="goBack"
        class="size-8 rounded-xl text-slate-400 hover:text-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-100 shrink-0"
        :title="__('common.back')">
        <ArrowLeft class="size-4" />
    </Button>
</template>
