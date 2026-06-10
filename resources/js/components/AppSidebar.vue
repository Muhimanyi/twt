<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();
const page = usePage();
const userPermissions = computed(() => (page.props.auth as any)?.permissions?.keys || []);
const can = (key: string) => userPermissions.value.includes(key);
import {
    Activity,
    AlertTriangle,
    Anchor,
    BadgeCheck,
    Banknote,
    Bell,
    BookOpen,
    Car,
    CreditCard,
    FileCheck,
    FileText,
    Gauge,
    Globe,
    HardDrive,
    LayoutDashboard,
    LockKeyhole,
    MapPin,
    MessageSquare,
    Newspaper,
    Plane,
    Receipt,
    ShieldAlert,
    Users,
    Wallet,
    XCircle,
} from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const mainNavItems = computed<NavItem[]>(() => [
    ...(can('dashboard.view') ? [{
        title: __('nav.dashboard'),
        href: dashboard(),
        icon: LayoutDashboard,
        class: 'rounded-full text-center bg-orange-500 text-black p-2 font-semibold',
    }] : []),
    ...(can('conducteurs.view') || can('debardeurs.view') || can('engins.view') ? [{
        title: __('nav.recensement'),
        icon: BadgeCheck,
        class: "px-2",
        children: [
            {
                title: __('sector.routier'),
                icon: Car,
                children: [
                    ...(can('conducteurs.view') ? [{ title: __('nav.conducteurs'), href: '/conducteurs/sector/routier', icon: Users }] : []),
                    ...(can('debardeurs.view') ? [{ title: __('nav.debardeurs'), href: '/debardeurs/sector/routier', icon: Users }] : []),
                    ...(can('engins.view') ? [{ title: __('nav.engins'), href: '/engins/category/roulant', icon: Car }] : []),
                ],
            },
            {
                title: __('sector.lacustre'),
                icon: Anchor,
                children: [
                    ...(can('conducteurs.view') ? [{ title: __('nav.conducteurs'), href: '/conducteurs/sector/lacustre', icon: Users }] : []),
                    ...(can('debardeurs.view') ? [{ title: __('nav.debardeurs'), href: '/debardeurs/sector/lacustre', icon: Users }] : []),
                    ...(can('engins.view') ? [{ title: __('nav.engins'), href: '/engins/category/flottant', icon: Anchor }] : []),
                ],
            },
            {
                title: __('sector.ferroviaire'),
                icon: Activity,
                children: [
                    ...(can('conducteurs.view') ? [{ title: __('nav.conducteurs'), href: '/conducteurs/sector/ferroviaire', icon: Users }] : []),
                    ...(can('debardeurs.view') ? [{ title: __('nav.debardeurs'), href: '/debardeurs/sector/ferroviaire', icon: Users }] : []),
                ],
            },
            {
                title: __('sector.aerien'),
                icon: Plane,
                children: [
                    ...(can('conducteurs.view') ? [{ title: __('nav.conducteurs'), href: '/conducteurs/sector/aerien', icon: Users }] : []),
                    ...(can('debardeurs.view') ? [{ title: __('nav.debardeurs'), href: '/debardeurs/sector/aerien', icon: Users }] : []),
                ],
            },
        ],
    }] : []),
    ...(can('permis.view') || can('certificats.view') ? [{
        title: __('nav.attestations'),
        icon: FileCheck,
        children: [
            ...(can('permis.view') ? [{ title: __('nav.permis'), href: '/permis', icon: CreditCard }] : []),
            ...(can('certificats.view') ? [{ title: __('nav.certificats'), href: '/certificats', icon: FileCheck }] : []),
        ],
        class: "px-2",

    }] : []),
    ...(can('taxes.view') || can('paiements.view') ? [{
        title: __('nav.finance'),
        icon: Wallet,
        children: [
            ...(can('taxes.view') ? [{ title: __('nav.taxes'), href: '/taxes', icon: Banknote }] : []),
            ...(can('paiements.view') ? [{ title: __('nav.paiements'), href: '/paiements', icon: Receipt }] : []),
            { title: __('nav.recouvrements'), href: '/recouvrements', icon: XCircle },
        ], class: "px-2",

    }] : []),
    {
        title: __('nav.rapports'),
        icon: FileText,
        children: [],
        class: "px-2",

    },
    {
        title: __('nav.security'),
        icon: ShieldAlert,
        children: [
            { title: __('nav.blacklist'), href: '/liste-noire', icon: XCircle },
            { title: __('nav.sanctions'), href: '/sanctions', icon: AlertTriangle },
        ],
        class: "px-2",

    },
    {
        title: __('nav.communication'),
        icon: MessageSquare,
        children: [
            { title: __('nav.annonces'), href: '/annonces', icon: Newspaper },
            { title: __('nav.notifications'), href: '/notifications', icon: Bell },
        ],
        class: "px-2",

    },
    {
        title: __('nav.configuration'),
        icon: Gauge,
        class: "px-2",
        children: [
            ...(can('provinces.view') ? [{
                title: __('nav.administration'),
                href: '/provinces',
                icon: MapPin,
            }] : []),

            // {
            //     title: __('nav.sauvegardes'),
            //     href: '/sauvegardes',
            //     icon: HardDrive,
            // },

            ...(can('users.view') ? [{
                title: __('nav.users'),
                icon: Users,
                children: [
                    { title: __('nav.users_list'), href: '/users', icon: Users },
                    ...(can('users.manage-permissions') ? [{ title: __('nav.permissions'), href: '/permissions', icon: LockKeyhole }] : []),
                ],
            }] : []),

            {
                title: __('nav.langues'),
                icon: Gauge,
                children: [
                    { title: 'Français', href: '/settings/language/fr', icon: Globe },
                    { title: 'Swahili', href: '/settings/language/sw', icon: Globe },
                    { title: 'Lingala', href: '/settings/language/ln', icon: Globe },
                    { title: 'English', href: '/settings/language/en', icon: Globe },
                ],
            },
        ],
    },
]);

const footerNavItems: NavItem[] = [
    //      
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="bg-sidebar print:hidden" :style="{
        boxShadow: 'var(--shadow-futuristic)',
    }">
        <SidebarHeader class="tw-sidebar-section">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child class="tw-sidebar-item">
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="bg-sidebar">
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter class="bg-sidebar tw-sidebar-section">
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
