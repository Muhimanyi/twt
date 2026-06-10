<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Car } from 'lucide-vue-next';

defineProps<{
    form: any;
}>();
</script>

<template>
    <div class="space-y-6 pt-4 border-t border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-2 text-rdc-red">
            <Car class="size-4" />
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em]">Spécifications Techniques Terrestres</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Genre</Label>
                <Input v-model="form.vehicle_genre" required placeholder="Ex: Moto, Jeep" class="rounded-xl h-11" />
            </div>
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Marque</Label>
                <Input v-model="form.vehicle_brand" required class="rounded-xl h-11" />
            </div>
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Type/Modèle</Label>
                <Input v-model="form.vehicle_type" required class="rounded-xl h-11" />
            </div>
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Couleur</Label>
                <Input v-model="form.vehicle_color" required class="rounded-xl h-11" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Plaque</Label>
                <Input v-model="form.plate_number" placeholder="S'il y a lieu" class="rounded-xl h-11" />
            </div>
            <div v-if="form.sub_category === 'avec moteur'" class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Année Fab.</Label>
                    <Input v-model="form.manufacture_year" type="number" class="rounded-xl h-11" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">CV</Label>
                    <Input v-model="form.horsepower" type="number" class="rounded-xl h-11" />
                </div>
            </div>
        </div>

        <div v-if="form.sub_category === 'avec moteur'"
            class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-slate-900 rounded-2xl">
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-500 ml-1">Numéro Moteur</Label>
                <Input v-model="form.engine_number" required
                    class="rounded-xl h-11 bg-white/5 border-white/10 text-white placeholder:text-white/20" />
            </div>
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-500 ml-1">Numéro Chassis</Label>
                <Input v-model="form.chassis_number" required
                    class="rounded-xl h-11 bg-white/5 border-white/10 text-white placeholder:text-white/20" />
            </div>
        </div>
    </div>
</template>
