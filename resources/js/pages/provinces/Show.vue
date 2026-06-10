<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { index as provinces, edit as editProvince } from '@/routes/provinces';
import { usePermissions } from '@/composables/usePermissions';
import { useTrans } from '@/composables/useTrans';

const { can } = usePermissions();
const { __ } = useTrans();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Provinces', href: provinces() },
            { title: 'Détails', href: '#' },
        ],
    },
});

defineProps<{
    province: {
        id: number;
        name: string;
        address: string;
        postal_code: string;
        contacts: string;
        languages: string;
        creation_date: string;
    };
}>();
</script>

<template>
    <Head :title="__('provinces.details')" />

    <div class="mx-auto max-w-2xl space-y-6">
        <div class="space-y-1">
            <h1 class="font-heading text-2xl font-bold">{{ province.name }}</h1>
            <p class="text-sm text-muted-foreground">
                {{ __('provinces.details') }}
            </p>
        </div>

        <div class="rounded-lg border border-sidebar-border p-6">
            <dl class="space-y-4">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-4">
                    <dt class="text-sm font-medium text-muted-foreground w-40">{{ __('common.name') }}</dt>
                    <dd class="text-sm font-medium">{{ province.name }}</dd>
                </div>

                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-4">
                    <dt class="text-sm font-medium text-muted-foreground w-40">{{ __('common.address') }}</dt>
                    <dd class="text-sm">{{ province.address }}</dd>
                </div>

                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-4">
                    <dt class="text-sm font-medium text-muted-foreground w-40">{{ __('provinces.postal_code') }}</dt>
                    <dd class="text-sm">{{ province.postal_code }}</dd>
                </div>

                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-4">
                    <dt class="text-sm font-medium text-muted-foreground w-40">{{ __('provinces.contacts') }}</dt>
                    <dd class="text-sm">{{ province.contacts }}</dd>
                </div>

                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-4">
                    <dt class="text-sm font-medium text-muted-foreground w-40">{{ __('provinces.languages') }}</dt>
                    <dd class="text-sm">{{ province.languages }}</dd>
                </div>

                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-4">
                    <dt class="text-sm font-medium text-muted-foreground w-40">{{ __('provinces.created_on') }}</dt>
                    <dd class="text-sm">{{ province.creation_date }}</dd>
                </div>
            </dl>
        </div>

        <div class="flex gap-3">
            <Button v-if="can('provinces.edit')" as-child>
                <a :href="editProvince(province.id)">{{ __('provinces.edit') }}</a>
            </Button>
            <Button variant="outline" as-child>
                <a :href="provinces()">{{ __('common.back') }}</a>
            </Button>
        </div>
    </div>
</template>
