<script setup lang="ts">
import ProvinceIndex from './Index.vue';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

defineProps<{
    province: {
        id: number;
        name: string;
        address: string;
        postal_code: string;
        contacts_email: string | null;
        contacts_phone: string | null;
        language_lingala: boolean;
        language_kikongo: boolean;
        language_kiluba: boolean;
        language_kiswahili: boolean;
        creation_date: string;
    };
}>();
</script>

<template>
    <ProvinceIndex mode="edit" :province="province" />
</template>
