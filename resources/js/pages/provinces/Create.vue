<script setup lang="ts">
import ProvinceIndex from './Index.vue';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();
</script>

<template>
    <ProvinceIndex mode="create" />
</template>
