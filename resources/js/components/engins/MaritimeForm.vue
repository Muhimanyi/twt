<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Anchor, Ship, Users, Settings } from 'lucide-vue-next';
import { Checkbox } from '@/components/ui/checkbox';

const props = defineProps<{
    form: any;
}>();

const usageOptions = [
    { id: 'personnel', label: 'Personnel' },
    { id: 'marchandise', label: 'Marchandise' },
    { id: 'peche', label: 'Pêche' },
    { id: 'privee', label: 'Privée' },
    { id: 'autres', label: 'Autres' },
];

const toggleUsage = (optionId: string) => {
    const currentUsage = [...(props.form.usage || [])];
    const index = currentUsage.indexOf(optionId);
    if (index > -1) {
        currentUsage.splice(index, 1);
    } else {
        currentUsage.push(optionId);
    }
    props.form.usage = currentUsage;
};
</script>

<template>
    <div class="space-y-8 pt-4 border-t border-slate-100 dark:border-slate-800">
        <!-- Vessel Basics -->
        <div class="space-y-6">
            <div class="flex items-center gap-2 text-rdc-blue">
                <Anchor class="size-4" />
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em]">Spécifications de l'Unité Flottante</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Dénomination de l'engin</Label>
                    <Input v-model="form.vessel_name" required placeholder="Nom du navire/barque" class="rounded-xl h-11" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Type d'embarcation</Label>
                    <Input v-model="form.vehicle_genre" required placeholder="Ex: Baleinière, Vedette" class="rounded-xl h-11" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Modèle de fabrication</Label>
                    <Input v-model="form.vehicle_brand" required class="rounded-xl h-11" />
                </div>
            </div>

            <!-- Material & Motorization Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="space-y-4">
                    <Label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Type de construction (Matériau)</Label>
                    <div class="inline-flex p-1 bg-slate-200/50 dark:bg-slate-800 rounded-xl w-full">
                        <button 
                            type="button"
                            @click="form.construction_type = 'bois'"
                            class="flex-1 h-10 rounded-lg transition-all flex items-center justify-center gap-2 text-xs font-black uppercase tracking-tight"
                            :class="form.construction_type === 'bois' ? 'bg-white dark:bg-slate-950 text-rdc-blue shadow-sm ring-1 ring-slate-200 dark:ring-slate-700' : 'text-slate-500 hover:text-slate-700'"
                        >
                            <Ship class="size-3" />
                            En Bois
                        </button>
                        <button 
                            type="button"
                            @click="form.construction_type = 'acier'"
                            class="flex-1 h-10 rounded-lg transition-all flex items-center justify-center gap-2 text-xs font-black uppercase tracking-tight"
                            :class="form.construction_type === 'acier' ? 'bg-white dark:bg-slate-950 text-rdc-blue shadow-sm ring-1 ring-slate-200 dark:ring-slate-700' : 'text-slate-500 hover:text-slate-700'"
                        >
                            <Settings class="size-3" />
                            En Acier / Métal
                        </button>
                    </div>
                </div>
                <div class="space-y-4">
                    <Label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Motorisation</Label>
                    <div class="inline-flex p-1 bg-slate-200/50 dark:bg-slate-800 rounded-xl w-full">
                        <button 
                            type="button"
                            @click="form.sub_category = 'avec moteur'"
                            class="flex-1 h-10 rounded-lg transition-all flex items-center justify-center gap-2 text-xs font-black uppercase tracking-tight"
                            :class="form.sub_category === 'avec moteur' ? 'bg-white dark:bg-slate-950 text-rdc-red shadow-sm ring-1 ring-slate-200 dark:ring-slate-700' : 'text-slate-500 hover:text-slate-700'"
                        >
                            <Settings class="size-3" />
                            Avec Moteur
                        </button>
                        <button 
                            type="button"
                            @click="form.sub_category = 'sans moteur'"
                            class="flex-1 h-10 rounded-lg transition-all flex items-center justify-center gap-2 text-xs font-black uppercase tracking-tight"
                            :class="form.sub_category === 'sans moteur' ? 'bg-white dark:bg-slate-950 text-slate-900 shadow-sm ring-1 ring-slate-200 dark:ring-slate-700' : 'text-slate-500 hover:text-slate-700'"
                        >
                            <Anchor class="size-3" />
                            Sans Moteur
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">N° d'immatriculation</Label>
                    <Input v-model="form.registration_number" class="rounded-xl h-11" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Lieu d'immatriculation</Label>
                    <Input v-model="form.registration_place" class="rounded-xl h-11" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Date d'immatriculation</Label>
                    <Input v-model="form.registration_date" type="date" class="rounded-xl h-11" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Date de fabrication</Label>
                    <Input v-model="form.manufacture_year" type="number" class="rounded-xl h-11" />
                </div>
            </div>
        </div>

        <!-- Technical Dimensions -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800">
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Capacité (T/Passagers)</Label>
                <Input v-model="form.capacity" class="rounded-xl h-11 bg-white dark:bg-slate-950" />
            </div>
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Hauteur (m)</Label>
                <Input v-model="form.height" type="number" step="0.01" class="rounded-xl h-11 bg-white dark:bg-slate-950" />
            </div>
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Longueur (m)</Label>
                <Input v-model="form.length" type="number" step="0.01" class="rounded-xl h-11 bg-white dark:bg-slate-950" />
            </div>
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Largeur (m)</Label>
                <Input v-model="form.width" type="number" step="0.01" class="rounded-xl h-11 bg-white dark:bg-slate-950" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Ligne de navigation</Label>
                <Input v-model="form.navigation_line" placeholder="Ex: Kinshasa - Kisangani" class="rounded-xl h-11" />
            </div>
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Couleur de la barque</Label>
                <Input v-model="form.vehicle_color" class="rounded-xl h-11" />
            </div>
        </div>

        <!-- Engine / Propulsion -->
        <div v-if="form.sub_category === 'avec moteur'" class="space-y-6">
            <div class="flex items-center gap-2 text-rdc-red">
                <Settings class="size-4" />
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em]">Motorisation</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="space-y-1.5"><Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Marque Moteur</Label><Input v-model="form.engine_brand" class="rounded-xl h-11" /></div>
                <div class="space-y-1.5"><Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Puissance (CV)</Label><Input v-model="form.horsepower" type="number" class="rounded-xl h-11" /></div>
                <div class="space-y-1.5"><Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">N° de série moteur</Label><Input v-model="form.engine_number" class="rounded-xl h-11" /></div>
                <div class="space-y-1.5"><Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Type de moteur</Label><Input v-model="form.vehicle_type" placeholder="Hors-bord, In-board" class="rounded-xl h-11" /></div>
            </div>
        </div>
        <div v-else class="space-y-1.5">
            <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Type de propulsion</Label>
            <Input v-model="form.propulsion_type" placeholder="Ex: Rames, Voiles, Perches" class="rounded-xl h-11" />
        </div>

        <!-- Usage Checkboxes -->
        <div class="space-y-4">
            <Label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Usage de l'embarcation</Label>
            <div class="flex flex-wrap gap-6 p-4 bg-slate-50 dark:bg-slate-900/30 rounded-xl border border-slate-100 dark:border-slate-800">
                <div v-for="option in usageOptions" :key="option.id" class="flex items-center space-x-2">
                    <Checkbox :id="option.id" :checked="form.usage?.includes(option.id)" @update:checked="toggleUsage(option.id)" />
                    <label :for="option.id" class="text-sm font-bold leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">{{ option.label }}</label>
                </div>
            </div>
        </div>

        <!-- Crew Info -->
        <div class="space-y-6 pt-4 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2 text-slate-900 dark:text-slate-100">
                <Users class="size-4" />
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em]">Informations sur l'Équipage</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Nombre d'équipage / Pêcheurs</Label>
                    <Input v-model="form.crew_count" type="number" class="rounded-xl h-11" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Nombre de conducteurs</Label>
                    <Input v-model="form.driver_count" type="number" class="rounded-xl h-11" />
                </div>
            </div>
        </div>
    </div>
</template>
