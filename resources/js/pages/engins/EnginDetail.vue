<template>
  <div class="flex overflow-scroll text-gray-900 font-sans">
    <div class="md:min-w-[550px] bg-white border-x mx-auto w-full p-4">
      <header class="text-center">
        <i class="text-center">{{ __('engins.detail.republic') }}</i>
        <h1>{{ __('engins.detail.province') }}</h1>
        <em class="text-center text-sm">{{ __('engins.detail.ministry') }}</em>
        <img src="/armoiry.png" class="h-20 w-20 mx-auto bg-gray-500 rounded-full object-cover" alt="" />
        <h2 class="text-xs">{{ __('engins.detail.division') }}</h2>
        <h1 class="font-bold">{{ __('engins.detail.fiche_roulant_moteur') }}</h1>
        <div class="mx-auto min-w-2xl max-w-4xl">************************************************************</div>
      </header>

      <!-- Owner Information -->
      <section class="w-min-2xl max-w-4xl border-4 border-black p-2 mt-4">
        <h1 class="text-sm text-blue-700 font-bold" style="letter-spacing: -1px;">{{ __('engins.detail.owner_section') }}</h1>
        <div class="text-sm space-y-2">
          <div class="flex w-full">
            <div class="flex w-4/5 items-center">
              <p class="mr-2">{{ __('common.name') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full">{{ owner.name }}</p>
            </div>
            <div class="flex w-1/5 items-center">
              <p class="mr-2">{{ __('engins.form.gender') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full text-center">{{ owner.gender }}</p>
            </div>
          </div>

          <div class="flex w-full">
            <div class="flex w-3/5 items-center">
              <p class="min-w-[160px] mr-2">{{ __('engins.detail.birth_place_date') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full">{{ owner.birthPlace }}</p>
            </div>
            <div class="flex w-2/5 items-center space-x-1">
              <p>{{ __('engins.detail.on_date') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full text-center">{{ owner.birthDay }}</p>
              <p>/</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full text-center">{{ owner.birthMonth }}</p>
              <p>/</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full text-center">{{ owner.birthYear }}</p>
            </div>
          </div>

          <div class="flex w-full">
            <p class="min-w-[160px] mr-2">{{ __('engins.form.father_name') }}:</p>
            <p class="font-bold border-dashed border-b-2 border-black w-full">{{ owner.fatherName }}</p>
          </div>
          <div class="flex w-full">
            <p class="min-w-[160px] mr-2">{{ __('engins.form.mother_name') }}:</p>
            <p class="font-bold border-dashed border-b-2 border-black w-full">{{ owner.motherName }}</p>
          </div>

          <div class="flex w-full">
            <div class="flex w-1/2 items-center">
              <p class="min-w-[80px] mr-2">{{ __('engins.form.marital_status') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full">{{ owner.civilStatus }}</p>
            </div>
            <div class="flex w-1/2 items-center">
              <p class="min-w-[80px] mr-2">{{ __('engins.form.nationality') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full text-center">{{ owner.nationality }}</p>
            </div>
          </div>

          <div class="flex w-full">
            <div class="flex w-1/2 items-center">
              <p class="min-w-[80px] mr-2">{{ __('engins.contact') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full">{{ owner.contact }}</p>
            </div>
            <div class="flex w-1/2 items-center">
              <p class="min-w-[80px] mr-2">{{ __('common.email') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full text-center">{{ owner.email }}</p>
            </div>
          </div>

          <div class="flex w-full">
            <p class="min-w-[160px] mr-2">{{ __('common.address') }}:</p>
            <p class="font-bold border-dashed border-b-2 border-black w-full">{{ owner.address }}</p>
          </div>
        </div>
      </section>

      <!-- Vehicle Information -->
      <section class="mt-5">
        <h1 class="text-sm text-blue-700 font-bold" style="letter-spacing: -1px;">{{ __('engins.detail.vehicle_section') }}</h1>
        <div class="text-sm space-y-2">
          <div class="flex w-full">
            <div class="flex w-2/3 items-center">
              <p class="min-w-[160px] mr-2">{{ __('engins.detail.vehicle_genre') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full">{{ vehicle.type }}</p>
            </div>
            <div class="flex w-1/3 items-center">
              <p class="min-w-[50px] mr-2">{{ __('engins.form.brand') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full text-center">{{ vehicle.brand }}</p>
            </div>
          </div>

          <div class="flex w-full">
            <div class="flex w-1/2 items-center">
              <p class="min-w-[80px] mr-2">{{ __('engins.detail.type') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full">{{ vehicle.subtype }}</p>
            </div>
            <div class="flex w-1/2 items-center">
              <p class="min-w-[80px] mr-2">{{ __('engins.form.color') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full text-center">{{ vehicle.color }}</p>
            </div>
          </div>

          <div class="flex w-full gap-6 items-center">
            <p>{{ __('engins.form.usage') }}:</p>
            <div class="flex gap-4">
              <label class="flex gap-2 items-center"><p>{{ __('engins.detail.personal') }}</p><input type="checkbox" v-model="vehicle.usage.personal" disabled /></label>
              <label class="flex gap-2 items-center"><p>{{ __('engins.detail.commercial') }}</p><input type="checkbox" v-model="vehicle.usage.commercial" disabled /></label>
              <label class="flex gap-2 items-center"><p>{{ __('engins.detail.state') }}</p><input type="checkbox" v-model="vehicle.usage.state" disabled /></label>
              <label class="flex gap-2 items-center"><p>{{ __('common.other') }}</p><input type="checkbox" v-model="vehicle.usage.other" disabled /></label>
            </div>
          </div>

          <div class="flex w-full items-center">
            <p class="min-w-[200px] mr-2">{{ __('engins.detail.plate_number') }}:</p>
            <p class="font-bold border-dashed border-b-2 border-black w-full">{{ vehicle.plate }}</p>
          </div>

          <div class="flex w-full">
            <div class="flex w-1/2 items-center">
              <p class="min-w-[120px] mr-2">{{ __('engins.form.engine_number') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full">{{ vehicle.engineNumber }}</p>
            </div>
            <div class="flex w-1/2 items-center">
              <p class="min-w-[120px] mr-2">{{ __('engins.form.chassis_number') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full text-center">{{ vehicle.chassisNumber }}</p>
            </div>
          </div>

          <div class="flex w-full">
            <div class="flex w-1/2 items-center">
              <p class="min-w-[140px] mr-2">{{ __('engins.detail.manufacture_year') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full">{{ vehicle.year }}</p>
            </div>
            <div class="flex w-1/2 items-center">
              <p class="min-w-[100px] mr-2">{{ __('engins.detail.horsepower') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full text-center">{{ vehicle.hp }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Administration Section -->
      <section class="mt-5">
        <h1 class="text-sm text-blue-700 font-bold" style="letter-spacing: -1px;">{{ __('engins.detail.admin_section') }}</h1>
        <div class="text-sm space-y-2">
          <div class="flex w-full items-center">
            <p class="min-w-[250px] mr-2">{{ __('engins.detail.identification_number') }}:</p>
            <p class="font-bold border-dashed border-b-2 border-black w-full">{{ admin.id }}</p>
          </div>
          <div class="flex w-full">
            <div class="flex w-3/5 items-center">
              <p class="min-w-[180px] mr-2">{{ __('engins.detail.identification_place_date') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full">{{ admin.place }}</p>
            </div>
            <div class="flex w-2/5 items-center space-x-1">
              <p>{{ __('engins.detail.on_date') }}:</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full text-center">{{ admin.day }}</p>
              <p>/</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full text-center">{{ admin.month }}</p>
              <p>/</p>
              <p class="font-bold border-dashed border-b-2 border-black w-full text-center">{{ admin.year }}</p>
            </div>
          </div>
        </div>
      </section>

      <div class="text-red-600 text-xs mt-5">
        <h1 class="font-bold" style="letter-spacing: -1px;">{{ __('engins.detail.important_notice') }}</h1>
        <p>{{ __('engins.detail.transfer_notice') }}</p>
      </div>

      <div class="w-full flex justify-between text-xs mt-5">
        <div>
          <p class="font-bold">{{ __('engins.form.owner_signature') }}</p>
          ({{ __('engins.form.name_signature') }})
        </div>
        <div class="block">
          <p>{{ __('engins.detail.done_at') }} {{ admin.place }}, {{ __('engins.detail.on_date_short') }} {{ admin.day }}/{{ admin.month }}/{{ admin.year }}</p>
          <p class="font-semibold">{{ __('engins.detail.for_division') }}</p>
          <p class="text-[10px]">{{ __('engins.detail.delegue_signature') }}</p>
        </div>
      </div>

      <div class="flex justify-end space-x-2 mt-5">
        <button @click="print" class="px-4 py-2 bg-gray-200 text-sm rounded">{{ __('tools.print') }}</button>
        <button @click="exportPdf" class="px-4 py-2 bg-blue-500 text-white text-sm rounded">{{ __('tools.pdf') }}</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { jsPDF } from 'jspdf'
import { useTrans } from '@/composables/useTrans'

const { __ } = useTrans()

const owner = ref({
  name: 'MUKOKO MUKOKO',
  gender: 'M',
  birthPlace: 'Goma',
  birthDay: '21',
  birthMonth: '8',
  birthYear: '2002',
  fatherName: 'MUKOKO MUKOKO',
  motherName: 'MUKOKO MUKOKO',
  civilStatus: 'Célibataire',
  nationality: 'Congolaise',
  contact: '+243990844336',
  email: 'nabirudaniel4@gmail.com',
  address: '+243990844336',
})

const vehicle = ref({
  type: 'mercedes benz',
  brand: 'M73689',
  subtype: 'Camion',
  color: 'gris',
  usage: { personal: true, commercial: false, state: false, other: true },
  plate: 'NK-8787AB',
  engineNumber: '45KIOU',
  chassisNumber: 'GRS-789',
  year: '2020',
  hp: '150',
})

const admin = ref({
  id: 'IM-Gt908',
  place: 'Goma',
  day: '21',
  month: '8',
  year: '2002',
})

function print() {
  window.print()
}

function exportPdf() {
  const doc = new jsPDF('p', 'pt', 'a4')
  doc.html(document.body, {
    callback: function (pdf) {
      pdf.save('engins.pdf')
    },
    x: 10,
    y: 10,
    width: 580,
  })
}

onMounted(() => {
  // Placeholder for fetching data based on route param if needed
})
</script>

<style scoped>
@media print {
  button { display: none; }
}
</style>
