<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

import { UserCircle } from 'lucide-vue-next';

defineProps<{
    form: any;
}>();
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center gap-2">
            <div class="h-4 w-1 bg-rdc-blue rounded-full"></div>
            <h3 class="text-xs font-black uppercase tracking-widest text-slate-400">Identité du Propriétaire</h3>
        </div>

        <div class="grid grid-cols-4 gap-6 items-end">
            <div class="col-span-1">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Photo d'Identité</Label>
                <div class="mt-1 flex items-center gap-4">
                    <div
                        class="size-16 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 overflow-hidden border border-slate-200">
                        <UserCircle v-if="!form.owner_photo" class="size-8" />
                        <img v-else :src="typeof form.owner_photo === 'string' ? form.owner_photo : URL.createObjectURL(form.owner_photo)"
                            class="w-full h-full object-cover" />
                    </div>
                    <Input type="file" @input="form.owner_photo = $event.target.files[0]" accept="image/*"
                        class="text-xs h-9 py-1 px-2 border-none bg-slate-50 cursor-pointer" />
                </div>
            </div>
            <div class="col-span-1 space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Nom complet</Label>
                <Input v-model="form.owner_name" required placeholder="Propriétaire de l'engin" class="rounded-xl h-11" />
            </div>
            <div class="col-span-1 space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Sexe</Label>
                <Select v-model="form.owner_gender">
                    <SelectTrigger class="rounded-xl h-11">
                        <SelectValue placeholder="Choisir..." />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="M">Masculin</SelectItem>
                        <SelectItem value="F">Féminin</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="col-span-1 space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Lieu de Naissance</Label>
                <Input v-model="form.owner_birth_place" required class="rounded-xl h-11" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Nom du Père</Label>
                <Input v-model="form.owner_father_name" required class="rounded-xl h-11" />
            </div>
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Nom de la Mère</Label>
                <Input v-model="form.owner_mother_name" required class="rounded-xl h-11" />
            </div>
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Etat Civil</Label>
                <Input v-model="form.owner_marital_status" required class="rounded-xl h-11" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Nationalité</Label>
                <Input v-model="form.owner_nationality" required class="rounded-xl h-11" />
            </div>
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Contact (Tél)</Label>
                <Input v-model="form.owner_phone" required class="rounded-xl h-11" />
            </div>
            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Email</Label>
                <Input v-model="form.owner_email" type="email" placeholder="Optionnel" class="rounded-xl h-11" />
            </div>
        </div>

        <div class="space-y-1.5">
            <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Adresse de Résidence</Label>
            <Input v-model="form.owner_address" required class="rounded-xl h-11" />
        </div>
    </div>
</template>
