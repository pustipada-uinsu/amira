<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import debounce from 'lodash/debounce'

const props = defineProps({
  venues: Object,
  filters: Object
})

const search = ref(props.filters?.search || '')
const status = ref(props.filters?.status ?? '')

watch(
  [search, status],
  debounce(() => {
    router.get('/venue', {
      search: search.value,
      status: status.value,
    }, {
      preserveState: true,
      replace: true,
    })
  }, 400)
)

const showModal = ref(false)
const isEdit = ref(false)
const selectedId = ref(null)

const form = useForm({
  name: '',
  address: '',
  maps_url: '',
  status: true,
  keterangan: '',
})

// OPEN CREATE
const openCreate = () => {
  form.reset()
  form.status = true
  isEdit.value = false
  showModal.value = true
}

// OPEN EDIT
const openEdit = (v) => {
  form.name = v.name
  form.address = v.address
  form.maps_url = v.maps_url
  form.status = !!v.status
  form.keterangan = v.keterangan

  selectedId.value = v.id
  isEdit.value = true
  showModal.value = true
}

const submit = () => {
  if (isEdit.value) {
    form.put(`/venue/${selectedId.value}`, {
      onSuccess: () => closeModal()
    })
  } else {
    form.post('/venue', {
      onSuccess: () => closeModal()
    })
  }
}

const closeModal = () => {
  showModal.value = false
  form.reset()
}

const hapus = (id) => {
  if (confirm('Yakin hapus venue?')) {
    router.delete(`/venue/${id}`)
  }
}

const openDropdown = ref(null)
const toggleDropdown = (id) => {
  openDropdown.value = openDropdown.value === id ? null : id
}
</script>

<template>
  <Head title="Data Master Venue" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto p-6">

      <!-- HEADER -->
      <div class="flex justify-between mb-6">
        <h1 class="text-2xl font-bold">Data Master Venue</h1>

        <button @click="openCreate" class="btn-primary">
          + Tambah Venue
        </button>
      </div>

      <!-- FILTER -->
      <div class="flex gap-3 mb-4">
        <input v-model="search" placeholder="Cari venue..." class="input w-1/3"/>

        <select v-model="status" class="input w-1/4">
          <option value="">Semua Status</option>
          <option value="1">Aktif</option>
          <option value="0">Nonaktif</option>
        </select>
      </div>

      <!-- TABLE -->
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="p-3 text-left">Nama</th>
              <th class="p-3 text-left">Alamat</th>
              <th class="p-3 text-left">Status</th>
              <th class="p-3 text-left">Maps</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="v in venues.data" :key="v.id" class="border-t hover:bg-gray-50">
              <td class="p-3 font-medium">{{ v.name }}</td>
              <td class="text-gray-600">{{ v.address }}</td>

              <td>
                <span
                  :class="v.status
                    ? 'bg-emerald-100 text-emerald-600'
                    : 'bg-gray-200 text-gray-600'"
                  class="px-2 py-1 rounded text-xs"
                >
                  {{ v.status ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>

              <td>
                <a
                v-if="v.maps_url"
                :href="v.maps_url"
                target="_blank"
                class="inline-block px-3 py-1.5 text-xs rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                >
                Lihat Maps
                </a>

                <span v-else class="text-gray-400 text-xs">
                    -
                </span>
                </td>

                <td class="text-center">
                    <div class="flex justify-center gap-2">

                        <button
                        @click="openEdit(v)"
                        class="px-3 py-1.5 text-xs font-medium rounded-md border text-yellow-600 border-yellow-200 bg-yellow-100 hover:bg-yellow-50 transition"
                        >
                        Edit
                        </button>

                        <button
                        @click="hapus(v.id)"
                        class="px-3 py-1.5 text-xs font-medium rounded-md bg-red-200 text-red-600 hover:bg-red-300 transition"
                        >
                        Hapus
                        </button>

                    </div>
                </td>
            </tr>

            <tr v-if="!venues.data.length">
              <td colspan="5" class="text-center py-6 text-gray-400">
                Belum ada data venue
              </td>
            </tr>
          </tbody>
        </table>

        <!-- PAGINATION -->
        <div class="p-4 flex justify-center">
          <button
            v-for="(link, i) in venues.links"
            :key="i"
            v-html="link.label"
            @click="link.url && router.visit(link.url)"
            class="px-3 py-1 border rounded mx-1"
          />
        </div>
      </div>

      <!-- ===================== -->
      <!-- MODAL -->
      <!-- ===================== -->
      <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6">

          <!-- HEADER -->
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">
              {{ isEdit ? '✏️ Edit Venue' : '➕ Tambah Venue' }}
            </h2>
            <button @click="closeModal" class="text-gray-400">✕</button>
          </div>

          <!-- FORM -->
          <div class="space-y-4">

            <!-- NAMA -->
            <div>
                <label class="label">Nama Venue</label>
                <input v-model="form.name" class="input"/>
                <p v-if="form.errors.name" class="error">
                {{ form.errors.name }}
                </p>
            </div>

            <!-- ALAMAT -->
            <div>
                <label class="label">Alamat</label>
                <input v-model="form.address" class="input"/>
                <p v-if="form.errors.address" class="error">
                {{ form.errors.address }}
                </p>
            </div>

            <!-- MAPS -->
            <div>
                <label class="label">Google Maps URL</label>
                <input v-model="form.maps_url" class="input"/>
                <p v-if="form.errors.maps_url" class="error">
                {{ form.errors.maps_url }}
                </p>
            </div>

            <!-- STATUS -->
            <div>
                <label class="label">Status</label>
                <select v-model="form.status" class="input">
                <option :value="true">Aktif</option>
                <option :value="false">Nonaktif</option>
                </select>
                <p v-if="form.errors.status" class="error">
                {{ form.errors.status }}
                </p>
            </div>

            <!-- KETERANGAN -->
            <div>
                <label class="label">Keterangan</label>
                <textarea v-model="form.keterangan" class="input"></textarea>
                <p v-if="form.errors.keterangan" class="error">
                {{ form.errors.keterangan }}
                </p>
            </div>

            <!-- ACTION -->
            <div class="flex justify-end gap-2 pt-3">
                <button @click="closeModal" class="px-4 py-2 border rounded-lg">
                Batal
                </button>

                <button @click="submit" class="btn-primary" :disabled="form.processing">
                <span v-if="form.processing">Menyimpan...</span>
                <span v-else>{{ isEdit ? 'Update' : 'Simpan' }}</span>
                </button>
            </div>

            </div>

        </div>
      </div>

    </div>
  </AuthenticatedLayout>
</template>

<style scoped>

.btn-primary {
  background: #10b981;
  color: white;
  border-radius: 10px;
  padding: 10px 14px;
}

.label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 4px;
  color: #374151;
}

.input {
  width: 100%;
  padding: 10px;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  outline: none;
  transition: 0.2s;
}

.input:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 2px rgba(16,185,129,0.2);
}
</style>