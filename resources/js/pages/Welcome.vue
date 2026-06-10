<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import {
    Users, Car, Anchor, FileCheck, Wallet,
    ShieldCheck, ArrowRight, MapPin, Activity,
    Building2
} from 'lucide-vue-next';
import { dashboard, login, register } from '@/routes';
import ThreeDViewer from '@/components/ThreeDViewer.vue';
import { useAnimateOnScroll, useCountUp } from '@/composables/useAnimateOnScroll';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const modules = [
    {
        id: 'drivers',
        title: 'Gestion des Conducteurs',
        description: 'Enregistrement, suivi et gestion des conducteurs de la province.',
        icon: Users,
        color: 'from-rdc-blue to-blue-600',
        bg: 'bg-rdc-blue/10',
    },
    {
        id: 'engines',
        title: 'Gestion des Engins',
        description: "Inventaire et suivi des véhicules et engins motorisés.",
        icon: Car,
        color: 'from-rdc-yellow to-yellow-600',
        bg: 'bg-rdc-yellow/10',
    },
    {
        id: 'stevedores',
        title: 'Gestion des Arrimeurs',
        description: "Gestion des arrimeurs et dockers pour les activités lacustres.",
        icon: Anchor,
        color: 'from-rdc-red to-red-600',
        bg: 'bg-rdc-red/10',
    },
    {
        id: 'licenses',
        title: 'Licences & Permis',
        description: 'Délivrance et renouvellement des licences de transport.',
        icon: FileCheck,
        color: 'from-emerald-500 to-green-600',
        bg: 'bg-emerald-50',
    },
    {
        id: 'taxes',
        title: 'Taxes & Paiements',
        description: 'Gestion des taxes de transport et des paiements.',
        icon: Wallet,
        color: 'from-purple-500 to-purple-600',
        bg: 'bg-purple-50',
    },
    {
        id: 'security',
        title: 'Sécurité Routière',
        description: 'Contrôle technique et conformité des véhicules.',
        icon: ShieldCheck,
        color: 'from-orange-500 to-orange-600',
        bg: 'bg-orange-50',
    },
];

const stats = [
    { label: 'Conducteurs enregistrés', value: 5000, icon: Users },
    { label: 'Engins identifiés', value: 3000, icon: Car },
    { label: 'Arrimeurs', value: 1200, icon: Anchor },
    { label: 'Certificats délivrés', value: 8000, icon: FileCheck },
];

const heroVisible = ref(false);
const { el: statsEl, isVisible: statsVisible } = useAnimateOnScroll(0.3);
const { el: modulesEl, isVisible: modulesVisible } = useAnimateOnScroll(0.1);
const { el: provinceEl, isVisible: provinceVisible } = useAnimateOnScroll(0.3);

const statCounters = stats.map(s => useCountUp(ref(s.value), 2000));

watch(statsVisible, (v) => {
    if (v) statCounters.forEach(c => c.start());
});

onMounted(() => {
    setTimeout(() => { heroVisible.value = true; }, 100);
});

const formatStat = (n: number) => n.toLocaleString('fr-FR') + '+';
</script>

<template>
    <Head title="TUJIKINGE Transport - Province du Tanganyika">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="fixed inset-0 z-0 pointer-events-none opacity-20">
        <ThreeDViewer
            model="/3D/car1.obj"
            class="w-full h-full"
        />
    </div>

    <div class="min-h-screen bg-white/40 relative z-10">
        <!-- Navbar -->
        <nav class="fixed top-0 left-0 right-0 z-50 bg-white/50 backdrop-blur-xl border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
                <Link href="/" class="flex items-center gap-3">
                    <img src="/LOGO-TW-03-1.png" class="size-9" />
                    <div class="flex flex-col leading-tight">
                        <span class="text-sm font-black tracking-tight text-slate-900">TUJIKINGE</span>
                        <span class="text-[10px] font-bold text-rdc-blue uppercase tracking-widest">Transport</span>
                    </div>
                </Link>
                <div class="flex items-center gap-3">
                    <Link v-if="$page.props.auth?.user" :href="dashboard()"
                        class="h-10 px-5 rounded-xl bg-rdc-blue text-white text-sm font-bold shadow-lg shadow-rdc-blue/20 hover:bg-rdc-blue/90 transition-all flex items-center gap-2">
                        Tableau de bord
                    </Link>
                    <template v-else>
                        <Link :href="login()"
                            class="h-10 px-5 rounded-xl border border-slate-200 text-slate-700 text-sm font-bold hover:bg-slate-50 transition-all flex items-center">
                            Connexion
                        </Link>
                        <Link v-if="canRegister" :href="register()"
                            class="h-10 px-5 rounded-xl bg-rdc-blue text-white text-sm font-bold shadow-lg shadow-rdc-blue/20 hover:bg-rdc-blue/90 transition-all flex items-center">
                            Inscription
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section class="relative pt-32 pb-24 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-rdc-blue/5 via-white/30 to-rdc-yellow/5" />
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-rdc-blue/5 rounded-full blur-3xl animate-pulse-slow" />
            <div class="max-w-7xl mx-auto px-6 relative">
                <div class="flex flex-col lg:flex-row items-center gap-16">
                    <div class="flex-1 space-y-8">
                        <div :class="['transition-all duration-700 ease-out', heroVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8']"
                             class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-rdc-blue/10 text-rdc-blue text-xs font-bold uppercase tracking-widest">
                            <MapPin class="size-3" />
                            Province du Tanganyika
                        </div>
                        <h1 :class="['transition-all duration-700 delay-200 ease-out', heroVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8']"
                            class="text-5xl lg:text-7xl font-black tracking-tighter text-slate-900 leading-[1.05]">
                           Système Intégré de 
                            <span class="text-rdc-blue animate-glow"> Gestion des Transports</span>
                        </h1>
                        <p :class="['transition-all duration-700 delay-400 ease-out', heroVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8']"
                            class="text-lg text-slate-500 leading-relaxed max-w-xl">
                            Système des gestions et des contrôles des transports en République Démocratique du Congo
                        </p>
                        <div :class="['transition-all duration-700 delay-600 ease-out', heroVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8']"
                            class="flex items-center gap-4 pt-4">
                            <Link v-if="!$page.props.auth?.user" :href="login()"
                                class="h-14 px-8 rounded-2xl bg-rdc-blue text-white font-black shadow-2xl shadow-rdc-blue/30 hover:bg-rdc-blue/90 hover:scale-105 transition-all duration-300 flex items-center gap-3 text-base">
                                Accéder à la plateforme
                                <ArrowRight class="size-5 group-hover:translate-x-1 transition-transform" />
                            </Link>
                            <Link v-else :href="dashboard()"
                                class="h-14 px-8 rounded-2xl bg-rdc-blue text-white font-black shadow-2xl shadow-rdc-blue/30 hover:bg-rdc-blue/90 hover:scale-105 transition-all duration-300 flex items-center gap-3 text-base">
                                Tableau de bord
                                <ArrowRight class="size-5" />
                            </Link>
                            <a href="#modules"
                                class="h-14 px-8 rounded-2xl border-2 border-slate-200 text-slate-700 font-bold hover:border-rdc-blue hover:text-rdc-blue hover:scale-105 transition-all duration-300 flex items-center gap-2 text-base">
                                En savoir plus
                            </a>
                        </div>
                    </div>
                    <div :class="['transition-all duration-1000 delay-300 ease-out', heroVisible ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 translate-y-12 scale-95']"
                        class="flex-1 relative">
                        <div class="relative w-full aspect-square max-w-md mx-auto">
                            <div class="absolute inset-0 bg-gradient-to-br from-rdc-blue/20 to-rdc-yellow/20 rounded-3xl rotate-6" />
                            <div class="relative bg-white/30 backdrop-blur-sm rounded-3xl border border-slate-200 p-8 shadow-2xl hover:shadow-rdc hover:-translate-y-1 transition-all duration-500 flex items-center justify-center">
                                <img src="/LOGO-TW-03-1.png" class="size-48 object-contain animate-float" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats -->
        <section ref="statsEl" class="py-16 bg-slate-50/30 backdrop-blur-sm border-y border-slate-100">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div v-for="(stat, i) in stats" :key="stat.label"
                        :class="['transition-all duration-700 ease-out', statsVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12']"
                        :style="{ transitionDelay: `${i * 150}ms` }"
                        class="text-center space-y-2 group">
                        <div class="flex justify-center">
                            <div class="size-12 rounded-2xl bg-white/30 backdrop-blur-sm shadow-sm border border-slate-200 flex items-center justify-center text-rdc-blue group-hover:scale-110 group-hover:shadow-rdc transition-all duration-300">
                                <component :is="stat.icon" class="size-6" />
                            </div>
                        </div>
                        <p class="text-3xl font-black text-slate-900 tabular-nums">
                            {{ statsVisible ? formatStat(statCounters[i].current.value) : '0+' }}
                        </p>
                        <p class="text-sm font-medium text-slate-400">{{ stat.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modules -->
        <section ref="modulesEl" id="modules" class="py-24">
            <div class="max-w-7xl mx-auto px-6">
                <div :class="['transition-all duration-700 ease-out', modulesVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8']"
                    class="text-center space-y-4 mb-16">
                    <span class="inline-block px-4 py-1.5 rounded-full bg-rdc-yellow/10 text-rdc-yellow text-xs font-black uppercase tracking-widest">Modules</span>
                    <h2 class="text-4xl font-black tracking-tighter text-slate-900">
                        Une Solution  <span class="text-rdc-blue">Complete</span>
                    </h2>
                    <p class="text-slate-400 max-w-xl mx-auto">
                        Votre Plateforme numerique pour la gestion et le controle des transporteurs
                    </p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="(mod, i) in modules" :key="mod.title"
                        :class="['transition-all duration-700 ease-out', modulesVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-16']"
                        :style="{ transitionDelay: `${i * 100}ms` }"
                        class="group p-8 rounded-3xl border border-slate-100 bg-white/30 backdrop-blur-sm hover:shadow-2xl hover:shadow-slate-200/50 hover:border-slate-200 hover:-translate-y-1 transition-all duration-300">
                        <div :class="`size-14 rounded-2xl ${mod.bg} flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300`">
                            <component :is="mod.icon" class="size-7" :class="mod.id === 'drivers' ? 'text-rdc-blue' : mod.id === 'engines' ? 'text-rdc-yellow' : mod.id === 'stevedores' ? 'text-rdc-red' : 'text-slate-700'" />
                        </div>
                        <h3 class="text-lg font-black text-slate-900 mb-3 group-hover:text-rdc-blue transition-colors duration-300">{{ mod.title }}</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">{{ mod.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Province -->
        <section ref="provinceEl" class="py-24 bg-gradient-to-r from-rdc-blue/50 to-rdc-blue/30 backdrop-blur-sm relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 w-64 h-64 border border-white rounded-full animate-spin-slow" />
                <div class="absolute bottom-10 right-10 w-96 h-96 border border-white rounded-full animate-spin-slower" />
            </div>
            <div :class="['transition-all duration-700 ease-out', provinceVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12']"
                class="max-w-7xl mx-auto px-6 relative text-center text-white space-y-8">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 text-white text-xs font-bold uppercase tracking-widest backdrop-blur-sm">
                    <Building2 class="size-3" />
                    Province
                </div>
                <h2 class="text-4xl lg:text-5xl font-black tracking-tighter uppercase">
                    Tujikinge Wenyewe<br />
                    <span class="text-rdc-yellow uppercase">Transcom</span>
                </h2>
                <p class="text-lg text-white/70 max-w-2xl mx-auto leading-relaxed">
                    Votre Plateforme numérique pour la gestion et le contrôle des transporteurs
                </p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 border-t border-slate-100 bg-white/30 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <img src="/LOGO-TW-03-1.png" class="size-8" />
                    <div class="flex flex-col leading-tight">
                        <span class="text-sm font-black text-slate-900">TUJIKINGE Transport</span>
                        <span class="text-[10px] font-bold text-rdc-blue uppercase tracking-widest">RDC - Tanganyika</span>
                    </div>
                </div>
                <p class="text-sm text-slate-400">
                    &copy; {{ new Date().getFullYear() }} Tous droits réservés. Province du Tanganyika, RDC.
                </p>
            </div>
        </footer>
    </div>
</template>
