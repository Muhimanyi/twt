<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
import { useTrans } from '@/composables/useTrans';
import { edit } from '@/routes/appearance';

const { __ } = useTrans();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Appearance settings',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head :title="__('settings.appearance')" />

    <h1 class="sr-only">{{ __('settings.appearance') }}</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            :title="__('settings.appearance')"
            :description="__('settings.appearance_description')"
        />
        <AppearanceTabs />
    </div>
</template>
