<script setup lang="ts">
import { ref } from 'vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { User, Phone, Mail, MapPin, Calendar, CreditCard, Search, Car, Anchor, Plane, Activity, Info, AlertCircle, Camera, X, Upload } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps<{
    form: any;
}>();

const searchingEngin = ref(false);
const enginError = ref('');
const engineDetails = ref<any>(null);

const searchEngin = async () => {
    if (!props.form.engin_id_number) return;
    
    searchingEngin.ref = true;
    enginError.value = '';
    engineDetails.value = null;
    
    try {
        const response = await axios.get(`/conducteurs/search-engin/${props.form.engin_id_number}`);
        engineDetails.value = response.data;
        props.form.engin_id = response.data.id;
        
        // Auto-fill form fields if they are empty
        if (response.data.vehicle_genre) props.form.engine_type = response.data.vehicle_genre;
        if (response.data.vehicle_brand) props.form.engine_brand = response.data.vehicle_brand;
        if (response.data.chassis_number) props.form.engine_chassis = response.data.chassis_number;
        if (response.data.engine_number) props.form.engine_number = response.data.engine_number;
        if (response.data.manufacture_year) props.form.engine_year = response.data.manufacture_year;
        if (response.data.vehicle_color) props.form.engine_color = response.data.vehicle_color;
        if (response.data.owner_name) props.form.owner_name = response.data.owner_name;
        if (response.data.owner_phone) props.form.owner_phone = response.data.owner_phone;
        if (response.data.owner_email) props.form.owner_email = response.data.owner_email;
        if (response.data.owner_address) props.form.owner_address = response.data.owner_address;
        
    } catch (error: any) {
        enginError.value = error.response?.data?.message || 'Erreur lors de la recherche';
        props.form.engin_id = null;
    } finally {
        searchingEngin.value = false;
    }
};

const genders = [
    { value: 'M', label: 'Masculin' },
    { value: 'F', label: 'Féminin' },
];

const maritalStatuses = [
    { value: 'Célibataire', label: 'Célibataire' },
    { value: 'Marié(e)', label: 'Marié(e)' },
    { value: 'Divorcé(e)', label: 'Divorcé(e)' },
    { value: 'Veuf(ve)', label: 'Veuf(ve)' },
];

const transportModes = [
    { value: 'Diurne', label: 'Diurne' },
    { value: 'Nocturne', label: 'Nocturne' },
    { value: 'Les deux', label: 'Diurne & Nocturne' },
];

const usages = [
    { value: 'Personnel', label: 'Personnel' },
    { value: 'Commercial', label: 'Commercial' },
    { value: 'Etat', label: 'Etat' },
    { value: 'Autre', label: 'Autre' },
];

const photoPreview = ref<string | null>(null);

const handlePhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        props.form.photo = file;
        photoPreview.value = URL.createObjectURL(file);
    }
};

const removePhoto = () => {
    props.form.photo = null;
    if (photoPreview.value) {
        URL.revokeObjectURL(photoPreview.value);
        photoPreview.value = null;
    }
};
</script>

<template>
    <div class="space-y-12">
        <!-- Engine Link Section -->
        <div class="space-y-6 p-6 bg-rdc-blue/5 rounded-2xl border border-rdc-blue/10">
            <div class="flex items-center gap-3 text-rdc-blue">
                <Search class="size-5" />
                <h3 class="text-sm font-black uppercase tracking-[0.2em]">Lien avec l'Engin</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">N° d'Identification ou Plaque de l'Engin</Label>
                    <div class="flex gap-2">
                        <Input v-model="form.engin_id_number" placeholder="Ex: ROU-2026-00001" class="rounded-xl h-11 flex-1 bg-white" />
                        <button type="button" @click="searchEngin" :disabled="searchingEngin"
                            class="px-4 bg-rdc-blue text-white rounded-xl hover:bg-rdc-blue/90 transition-all disabled:opacity-50">
                            <Search v-if="!searchingEngin" class="size-4" />
                            <span v-else class="animate-spin size-4 border-2 border-white border-t-transparent rounded-full"></span>
                        </button>
                    </div>
                    <p v-if="enginError" class="text-[10px] text-rdc-red font-bold ml-1 flex items-center gap-1">
                        <AlertCircle class="size-3" /> {{ enginError }}
                    </p>
                </div>
                
                <div v-if="engineDetails" class="flex items-center gap-4 p-3 bg-white rounded-xl border border-rdc-blue/20 shadow-sm animate-in fade-in slide-in-from-left-2">
                    <div class="size-10 rounded-lg bg-rdc-blue/10 flex items-center justify-center text-rdc-blue">
                        <Car v-if="engineDetails.category === 'roulant'" class="size-5" />
                        <Anchor v-else class="size-5" />
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-black text-slate-900">{{ engineDetails.vehicle_brand }} {{ engineDetails.vehicle_type }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ engineDetails.identification_number }}</span>
                    </div>
                    <Badge variant="secondary" class="ml-auto text-[8px] uppercase font-black">{{ engineDetails.category }}</Badge>
                </div>
            </div>
        </div>

        <!-- Identity Section -->
        <div class="space-y-6">
            <div class="flex items-center gap-3 text-slate-900 dark:text-slate-100">
                <User class="size-5" />
                <h3 class="text-sm font-black uppercase tracking-[0.2em]">Identité du Conducteur</h3>
            </div>

            <!-- Photo -->
            <div class="flex items-start gap-8 p-6 bg-slate-50 dark:bg-slate-900/30 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="shrink-0">
                    <div class="size-32 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 flex items-center justify-center overflow-hidden bg-white dark:bg-slate-950 relative group">
                        <img v-if="photoPreview || form.existing_photo_url" :src="photoPreview || form.existing_photo_url"
                            class="w-full h-full object-cover" />
                        <div v-else class="flex flex-col items-center gap-1 text-slate-400">
                            <Camera class="size-8" />
                            <span class="text-[9px] font-bold uppercase">Photo</span>
                        </div>
                        <button v-if="photoPreview || form.existing_photo_url" type="button" @click="removePhoto"
                            class="absolute top-1 right-1 size-6 rounded-full bg-black/60 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <X class="size-3" />
                        </button>
                    </div>
                </div>
                <div class="flex-1 space-y-2">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Photo du Conducteur</Label>
                    <label
                        class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 cursor-pointer hover:border-rdc-blue transition-colors">
                        <Upload class="size-4 text-slate-400" />
                        <span class="text-sm font-medium text-slate-500">Choisir une photo...</span>
                        <input type="file" accept="image/*" @change="handlePhotoChange"
                            class="hidden" />
                    </label>
                    <p class="text-[10px] text-slate-400 font-medium">Format JPG/PNG, max 2 Mo</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-1.5 md:col-span-2">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Noms Complets</Label>
                    <Input v-model="form.name" required placeholder="Nom, Postnom & Prénom" class="rounded-xl h-11" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Sexe</Label>
                    <Select v-model="form.gender">
                        <SelectTrigger class="rounded-xl h-11">
                            <SelectValue placeholder="Sélectionner..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="g in genders" :key="g.value" :value="g.value">{{ g.label }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-1.5 md:col-span-2">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Lieu de Naissance</Label>
                    <Input v-model="form.birth_place" required class="rounded-xl h-11" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Date de Naissance</Label>
                    <Input v-model="form.birth_date" type="date" required class="rounded-xl h-11" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Nom du Père</Label>
                    <Input v-model="form.father_name" required class="rounded-xl h-11" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Nom de la Mère</Label>
                    <Input v-model="form.mother_name" required class="rounded-xl h-11" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Etat-Civil</Label>
                    <Select v-model="form.marital_status">
                        <SelectTrigger class="rounded-xl h-11">
                            <SelectValue placeholder="Sélectionner..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="s in maritalStatuses" :key="s.value" :value="s.value">{{ s.label }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Nationalité</Label>
                    <Input v-model="form.nationality" required class="rounded-xl h-11" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Profession</Label>
                    <Input v-model="form.profession" required class="rounded-xl h-11" />
                </div>
            </div>
        </div>

        <!-- License & ID Section -->
        <div class="space-y-6 pt-8 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3 text-slate-900 dark:text-slate-100">
                <CreditCard class="size-5" />
                <h3 class="text-sm font-black uppercase tracking-[0.2em]">Permis & Pièce d'Identité</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-6 bg-slate-50 dark:bg-slate-900/30 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">N° Permis de Conduire</Label>
                    <Input v-model="form.license_number" class="rounded-xl h-11 bg-white dark:bg-slate-950" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Catégorie</Label>
                    <Input v-model="form.license_category" placeholder="Ex: A, B, C" class="rounded-xl h-11 bg-white dark:bg-slate-950" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Délivré le</Label>
                    <Input v-model="form.license_issued_at" type="date" class="rounded-xl h-11 bg-white dark:bg-slate-950" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Expire le</Label>
                    <Input v-model="form.license_expires_at" type="date" class="rounded-xl h-11 bg-white dark:bg-slate-950" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Type de Pièce d'Identité</Label>
                    <Input v-model="form.id_piece_type" placeholder="Ex: Carte d'Electeur, Passeport" class="rounded-xl h-11" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">N° de la Pièce</Label>
                    <Input v-model="form.id_piece_number" class="rounded-xl h-11" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Lieu de Délivrance</Label>
                    <Input v-model="form.id_piece_issued_place" class="rounded-xl h-11" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Date de Délivrance</Label>
                    <Input v-model="form.id_piece_issued_at" type="date" class="rounded-xl h-11" />
                </div>
            </div>
            
            <!-- Foreigners Section -->
            <div v-if="form.nationality && form.nationality.toLowerCase() !== 'congolaise'" 
                 class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-orange-50 dark:bg-orange-900/10 rounded-2xl border border-orange-100 dark:border-orange-800 animate-in fade-in zoom-in-95">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-orange-600 dark:text-orange-400 ml-1">Type de Document Migratoire</Label>
                    <Input v-model="form.migratory_document_type" class="rounded-xl h-11 bg-white dark:bg-slate-950" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-orange-600 dark:text-orange-400 ml-1">Type de Visa</Label>
                    <Input v-model="form.visa_type" class="rounded-xl h-11 bg-white dark:bg-slate-950" />
                </div>
            </div>
        </div>

        <!-- Engine Information (Read-only or Pre-filled) -->
        <div class="space-y-6 pt-8 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3 text-slate-900 dark:text-slate-100">
                <Info class="size-5" />
                <h3 class="text-sm font-black uppercase tracking-[0.2em]">Informations Relatives à l'Engin</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Type d'Engin</Label>
                    <Input v-model="form.engine_type" class="rounded-xl h-11 bg-slate-50" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Marque</Label>
                    <Input v-model="form.engine_brand" class="rounded-xl h-11 bg-slate-50" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">N° Châssis</Label>
                    <Input v-model="form.engine_chassis" class="rounded-xl h-11 bg-slate-50" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">N° Moteur</Label>
                    <Input v-model="form.engine_number" class="rounded-xl h-11 bg-slate-50" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Année de Fab.</Label>
                    <Input v-model="form.engine_year" class="rounded-xl h-11 bg-slate-50" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Couleur</Label>
                    <Input v-model="form.engine_color" class="rounded-xl h-11 bg-slate-50" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Assoc./Agence affiliée</Label>
                    <Input v-model="form.affiliation" class="rounded-xl h-11" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Usage</Label>
                    <Select v-model="form.usage">
                        <SelectTrigger class="rounded-xl h-11">
                            <SelectValue placeholder="Sélectionner..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="u in usages" :key="u.value" :value="u.value">{{ u.label }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Mode de Transport</Label>
                    <Select v-model="form.transport_mode">
                        <SelectTrigger class="rounded-xl h-11">
                            <SelectValue placeholder="Sélectionner..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="m in transportModes" :key="m.value" :value="m.value">{{ m.label }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="space-y-6 pt-8 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3 text-slate-900 dark:text-slate-100">
                <Phone class="size-5" />
                <h3 class="text-sm font-black uppercase tracking-[0.2em]">Contacts & Résidence</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Téléphone</Label>
                    <div class="relative">
                        <Phone class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                        <Input v-model="form.phone" required class="rounded-xl h-11 pl-10" />
                    </div>
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">E-mail</Label>
                    <div class="relative">
                        <Mail class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                        <Input v-model="form.email" type="email" class="rounded-xl h-11 pl-10" />
                    </div>
                </div>
            </div>

            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Adresse de Résidence</Label>
                <div class="relative">
                    <MapPin class="absolute left-3 top-4 size-4 text-slate-400" />
                    <textarea v-model="form.address" required rows="3" 
                        class="w-full rounded-xl border border-slate-200 bg-white px-10 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rdc-blue transition-all"
                    ></textarea>
                </div>
            </div>
        </div>

        <!-- Owner Information (Pre-filled from Engin) -->
        <div class="space-y-6 pt-8 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3 text-slate-900 dark:text-slate-100">
                <User class="size-5" />
                <h3 class="text-sm font-black uppercase tracking-[0.2em]">Propriétaire de l'Engin</h3>
            </div>

            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Nom du Propriétaire</Label>
                <Input v-model="form.owner_name" class="rounded-xl h-11 bg-slate-50" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Contact Propriétaire</Label>
                    <Input v-model="form.owner_phone" class="rounded-xl h-11 bg-slate-50" />
                </div>
                <div class="space-y-1.5">
                    <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">E-mail Propriétaire</Label>
                    <Input v-model="form.owner_email" class="rounded-xl h-11 bg-slate-50" />
                </div>
            </div>

            <div class="space-y-1.5">
                <Label class="text-[10px] font-bold uppercase text-slate-400 ml-1">Adresse du Propriétaire</Label>
                <Input v-model="form.owner_address" class="rounded-xl h-11 bg-slate-50" />
            </div>
        </div>
    </div>
</template>
