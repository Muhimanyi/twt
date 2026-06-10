<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ShieldAlert, Home, ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

defineProps<{
    message?: string;
}>();
</script>

<template>
    <Head :title="__('error.403.title')" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900 p-6">
        <div class="max-w-md w-full text-center space-y-8">
            <div class="inline-flex items-center justify-center size-24 rounded-3xl bg-red-50 dark:bg-red-950/40 border border-red-100 dark:border-red-900/50 shadow-lg shadow-red-500/5">
                <ShieldAlert class="size-12 text-red-500" />
            </div>

            <div class="space-y-3">
                <h1 class="font-heading text-5xl font-black text-slate-900 dark:text-slate-100">403</h1>
                <h2 class="text-lg font-bold text-slate-600 dark:text-slate-400">{{ __('error.403.title') }}</h2>
                <p class="text-sm text-slate-500 dark:text-slate-500 max-w-xs mx-auto leading-relaxed">
                    {{ message || __('error.403.message') }}
                </p>
            </div>

            <div class="flex items-center justify-center gap-3 pt-4">
                <Button variant="outline" class="rounded-xl gap-2 h-11" @click="$router.back()">
                    <ArrowLeft class="size-4" />
                    {{ __('error.403.back') }}
                </Button>
                <Link href="/">
                    <Button class="rounded-xl gap-2 h-11 bg-rdc-blue text-white shadow-lg shadow-rdc-blue/20 hover:bg-rdc-blue/90">
                        <Home class="size-4" />
                        {{ __('error.403.home') }}
                    </Button>
                </Link>
            </div>
        </div>
    </div>
</template>
