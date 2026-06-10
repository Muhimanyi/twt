<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronDown } from 'lucide-vue-next';
import { ref } from 'vue';
import {
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineOptions({
    name: 'TwNavSection',
    inheritAttrs: false,
});

const props = withDefaults(defineProps<{
    items: NavItem[];
    isSub?: boolean;
}>(), {
    isSub: false,
});

const { isCurrentUrl } = useCurrentUrl();

const openItems = ref<Set<string>>(new Set());

function isActive(item: NavItem): boolean {
    if (item.href && isCurrentUrl(item.href)) return true;
    if (item.children) {
        return item.children.some(child => isActive(child));
    }
    return false;
}

function toggleItem(title: string) {
    if (openItems.value.has(title)) {
        openItems.value.delete(title);
    } else {
        openItems.value.add(title);
    }
    openItems.value = new Set(openItems.value);
}

function hasActiveChildren(item: NavItem): boolean {
    if (!item.children) return false;
    return item.children.some(child => isActive(child));
}

function handleParentClick(event: MouseEvent, item: NavItem) {
    if (item.children && item.children.length > 0) {
        event.preventDefault();
        event.stopPropagation();
        toggleItem(item.title);
    }
}
</script>

<template>
    <template v-for="item in items" :key="item.title">
        <SidebarMenuItem v-if="!item.children || item.children.length === 0" :class="[!props.isSub ? 'mb-2' : '', item.class]">
            <SidebarMenuButton
                as-child
                :is-active="item.href ? isCurrentUrl(item.href) : false"
                :tooltip="item.title"
                :class="[!item.buttonClass ? (props.isSub ? 'tw-submenu-item' : 'tw-sidebar-item') : '', item.buttonClass]"
            >
                <Link :href="item.href ?? '#'">
                    <component :is="item.icon" v-if="item.icon" :class="props.isSub ? 'size-3.5' : 'tw-sidebar-icon'" />
                    <span :class="props.isSub ? 'flex-1 truncate text-xs' : 'tw-sidebar-title'">{{ item.title }}</span>
                    <span v-if="item.badge" :class="props.isSub ? 'tw-sidebar-badge tw-sidebar-badge-sm' : 'tw-sidebar-badge'">
                        {{ item.badge }}
                    </span>
                </Link>
            </SidebarMenuButton>
        </SidebarMenuItem>

        <SidebarMenuItem v-else :class="[!props.isSub ? 'mb-2' : '', item.class]">
            <SidebarMenuButton
                :is-active="hasActiveChildren(item)"
                :tooltip="item.title"
                class="tw-sidebar-parent-btn"
                :data-open="openItems.has(item.title)"
                @click="handleParentClick($event, item)"
            >
                <component :is="item.icon" v-if="item.icon" class="tw-sidebar-icon" />
                <span class="tw-sidebar-title">{{ item.title }}</span>
                <ChevronDown
                    class="tw-submenu-expand-icon"
                    :data-open="openItems.has(item.title)"
                />
            </SidebarMenuButton>

            <SidebarMenuSub
                class="tw-sidebar-submenu"
                :data-open="openItems.has(item.title)"
            >
                <SidebarMenuSubItem class="tw-submenu-recursive">
                    <TwNavSection :items="item.children ?? []" :is-sub="true" />
                </SidebarMenuSubItem>
            </SidebarMenuSub>
        </SidebarMenuItem>
    </template>
</template>
