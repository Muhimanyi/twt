<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
} from '@/components/ui/sidebar';
import TwNavSection from '@/components/TwNavSection.vue';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();
</script>

<template>
    <SidebarGroup class="tw-sidebar-section">
        <SidebarMenu>
            <TwNavSection :items="items" />
        </SidebarMenu>
    </SidebarGroup>
</template>
