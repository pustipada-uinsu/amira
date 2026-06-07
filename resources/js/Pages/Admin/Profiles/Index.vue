<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import debounce from 'lodash/debounce'

const props = defineProps({
  profiles: Object,
  filters: Object,
  institusi: Array,
  zona: Array
})

// =====================
// STATE FILTER
// =====================
const search = ref(props.filters?.search || '')
const selectedZona = ref(props.filters?.zona || '')
const selectedInstitusi = ref(props.filters?.institusi || '')

// =====================
// REALTIME FILTER
// =====================
watch(
  [search, selectedZona, selectedInstitusi],
  debounce(() => {
    router.get('/admin/profiles', {
      search: search.value,
      zona: selectedZona.value,
      institusi: selectedInstitusi.value,
    }, {
      preserveState: true,
      replace: true,
    })
  }, 400)
)
</script>

<template>
  <Head title="Data Peserta" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Data Peserta
        </h1>

        <a
        :href="`/admin/profiles/export?search=${search}&zona=${selectedZona}&institusi=${selectedInstitusi}`"
        class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-xl shadow-md transition"
        >
        ⬇ Export CSV
        </a>
        </div>

        <!-- ===================== -->
        <!-- FILTER BAR -->
        <!-- ===================== -->
        <div class="bg-white p-4 rounded-2xl shadow border mb-6 flex flex-col md:flex-row gap-3">

        <!-- SEARCH -->
        <input
            v-model="search"
            type="text"
            placeholder="Cari nama / email..."
            class="input w-full md:w-1/3"
        />

        <!-- ZONA -->
        <!-- <select v-model="selectedZona" class="input w-full md:w-1/4">
            <option value="">Semua Zona</option>
            <option v-for="z in zona" :key="z.id_zona" :value="z.id_zona">
            {{ z.nama_zona }}
            </option>
        </select> -->

        <!-- INSTITUSI -->
        <select v-model="selectedInstitusi" class="input w-full md:w-1/3">
            <option value="">Semua Institusi</option>
            <option
            v-for="i in institusi.filter(x => !selectedZona || x.id_zona == selectedZona)"
            :key="i.id_institusi"
            :value="i.id_institusi"
            >
            {{ i.nama_institusi }}
            </option>
        </select>

        </div>

        <!-- ===================== -->
        <!-- TABLE -->
        <!-- ===================== -->
        <div class="bg-white rounded-2xl shadow-lg border overflow-hidden">

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">

            <!-- HEADER -->
            <thead class="bg-gray-50 text-gray-600 sticky top-0 z-10">
                <tr class="text-xs uppercase tracking-wider">
                <th class="p-4 text-left">Peserta</th>
                <th>Email</th>
                <th>Institusi</th>
                <th>Zona</th>
                <th>Kontak</th>
                <th>Jabatan</th>
                <th>Baju</th>
                <th class="text-center">Status</th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody class="divide-y">

                <tr
                v-for="p in profiles.data"
                :key="p.id"
                class="hover:bg-gray-50 transition"
                >

                <!-- NAMA -->
                <td class="p-4">
                    <div class="flex items-center gap-3">

                    <!-- Avatar -->
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                        {{ p.nama_lengkap?.charAt(0)?.toUpperCase() || 'U' }}
                    </div>

                    <div>
                        <div class="font-semibold text-gray-800">
                        {{ p.nama_lengkap }}
                        </div>
                        <div class="text-xs text-gray-500">
                        ID: {{ p.user_id }}
                        </div>
                    </div>

                    </div>
                </td>

                <!-- EMAIL -->
                <td class="text-gray-600">
                    {{ p.user?.email || '-' }}
                </td>

                <!-- INSTITUSI -->
                <td>
                    <div class="font-medium text-gray-800">
                    {{ p.institusi?.nama_institusi || '-' }}
                    </div>
                </td>

                <!-- ZONA -->
                <td class="text-gray-600">
                    {{ p.institusi?.nama_zona || '-' }}
                </td>

                <!-- KONTAK -->
                <td>
                    <div class="text-gray-800">
                    {{ p.no_telp }}
                    </div>
                    <div class="text-xs text-gray-500">
                    WA: {{ p.no_wa }}
                    </div>
                </td>

                <!-- JABATAN -->
                <td class="text-gray-700">
                    {{ p.jabatan }}
                </td>

                <!-- BAJU -->
                <td>
                    <span class="px-2 py-1 bg-gray-100 rounded-lg text-xs font-medium">
                    {{ p.ukuran_baju }}
                    </span>
                </td>

                <!-- STATUS -->
                <td class="text-center">
                    <span
                    :class="p.is_smoking
                        ? 'bg-red-100 text-red-600'
                        : 'bg-emerald-100 text-emerald-600'"
                    class="px-3 py-1 rounded-full text-xs font-medium"
                    >
                    {{ p.is_smoking ? 'Smoking' : 'Non-Smoking' }}
                    </span>
                </td>

                </tr>

                <!-- EMPTY -->
                <tr v-if="!profiles.data.length">
                <td colspan="8" class="text-center py-10 text-gray-400">
                    Belum ada data peserta
                </td>
                </tr>

            </tbody>
            </table>
        </div>

        <!-- ===================== -->
        <!-- PAGINATION -->
        <!-- ===================== -->
        <div class="flex justify-center p-4 border-t">
            <div class="flex flex-wrap gap-1">

            <button
                v-for="(link, i) in profiles.links"
                :key="i"
                v-html="link.label"
                @click="link.url && router.visit(link.url)"
                class="px-3 py-1 rounded-lg border text-sm transition"
                :class="[
                link.active
                    ? 'bg-emerald-500 text-white border-emerald-500'
                    : 'bg-white text-gray-600 hover:bg-gray-100',
                !link.url && 'opacity-50 cursor-not-allowed'
                ]"
            />

            </div>
        </div>

        </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.input {
  width: 100%;
  padding: 10px;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  outline: none;
  transition: 0.2s;
}

.input:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 2px rgba(16,185,129,0.2);
}
</style>