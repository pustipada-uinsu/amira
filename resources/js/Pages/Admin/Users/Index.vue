<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import debounce from 'lodash/debounce'
import Swal from 'sweetalert2'

const props = defineProps({
  users: Object,
  filters: Object,
})

const search = ref(props.filters?.search || '')
const status = ref(props.filters?.status ?? '')

watch([search, status], debounce(() => {
  router.get('/users', {
    search: search.value,
    status: status.value,
  }, {
    preserveState: true,
    replace: true,
  })
}, 400))

const toggleSuspend = (user) => {
  router.patch(`/users/${user.id}/suspend`)
}

const hapus = (id) => {
  if (confirm('Hapus user ini?')) {
    router.delete(`/users/${id}`)
  }
}

const showModal = ref(false)
const isEdit = ref(false)
const selectedUser = ref(null)

const form = useForm({
  name: '',
  email: '',
  role: '',

  nama_lengkap: '',
  jenis_kelamin: '',
  alamat: '',
  no_telp: '',
  no_wa: '',
  jabatan: '',
  alamat_kantor: '',
  nama_bank: '',
  no_rekening: '',
  ukuran_baju: '',
  is_smoking: 0,
  institusi_id: '',
  pin: ''
})

const openEdit = (user) => {
  showModal.value = true
  selectedUser.value = user

  form.name = user.name
  form.email = user.email
  form.role = user.role

  // PROFILE (AMAN walau null)
  form.nama_lengkap = user.profile?.nama_lengkap || ''
  form.jenis_kelamin = user.profile?.jenis_kelamin || ''
  form.alamat = user.profile?.alamat || ''
  form.no_telp = user.profile?.no_telp || ''
  form.no_wa = user.profile?.no_wa || ''
  form.jabatan = user.profile?.jabatan || ''
  form.alamat_kantor = user.profile?.alamat_kantor || ''
  form.nama_bank = user.profile?.nama_bank || ''
  form.no_rekening = user.profile?.no_rekening || ''
  form.ukuran_baju = user.profile?.ukuran_baju || ''
  form.is_smoking = user.profile?.is_smoking ?? 0
  form.institusi_id = user.profile?.institusi_id || ''
}

const closeModal = () => {
  showModal.value = false
  form.reset()
}

const submit = async () => {
  const { value: pin } = await Swal.fire({
    title: '🔐 Konfirmasi PIN',
    text: 'Masukkan PIN admin untuk menyimpan perubahan',
    input: 'password',
    inputPlaceholder: '••••••',
    inputAttributes: {
      maxlength: 6,
      autocapitalize: 'off',
      autocorrect: 'off'
    },
    showCancelButton: true,
    confirmButtonText: 'Simpan',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#059669',
    inputValidator: (value) => {
      if (!value) {
        return 'PIN wajib diisi!'
      }
    }
  })

  if (!pin) return

  form.pin = pin

  form.put(`/users/${selectedUser.value.id}`, {
  onSuccess: () => {
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: 'User berhasil diperbarui'
    })
    closeModal()
  },
  onError: (errors) => {
    if (errors.pin) {
      Swal.fire({
        icon: 'error',
        title: 'PIN Salah',
        text: errors.pin
      })
    }
  }
})
}
</script>

<template>
  <Head title="Manajemen User" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto p-6">

      <!-- HEADER -->
      <h1 class="text-2xl font-bold mb-6">Manajemen User</h1>

      <!-- FILTER -->
      <div class="flex gap-3 mb-4">
        <input v-model="search" placeholder="Cari user..." class="input w-1/3"/>

        <select v-model="status" class="input w-1/4">
          <option value="">Semua Status</option>
          <option value="1">Aktif</option>
          <option value="0">Suspend</option>
        </select>
      </div>

      <!-- TABLE -->
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="p-3 text-left">User</th>
              <th>Email</th>
              <th>Role</th>
              <th>Status</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="u in users.data" :key="u.id" class="border-t hover:bg-gray-50">

              <!-- USER -->
              <td class="p-3">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center font-bold text-emerald-600">
                    {{ u.name.charAt(0) }}
                  </div>
                  <div>
                    <div class="font-medium">{{ u.name }}</div>
                    <div class="text-xs text-gray-400">ID: {{ u.id }}</div>
                  </div>
                </div>
              </td>

              <!-- EMAIL -->
              <td>{{ u.email }}</td>

              <!-- ROLE -->
              <td>
                <span class="px-2 py-1 text-xs bg-gray-100 rounded">
                  {{ u.role }}
                </span>
              </td>

              <!-- STATUS -->
              <td>
                <span
                  :class="u.is_active
                    ? 'bg-emerald-100 text-emerald-600'
                    : 'bg-red-100 text-red-600'"
                  class="px-2 py-1 rounded text-xs"
                >
                  {{ u.is_active ? 'Aktif' : 'Suspend' }}
                </span>
              </td>

              <!-- AKSI -->
              <td class="text-center">
                <div class="flex justify-center gap-2">

                  <button
                    @click="toggleSuspend(u)"
                    class="px-3 py-1.5 text-xs rounded-md transition"
                    :class="u.is_active
                      ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'
                      : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'"
                  >
                    {{ u.is_active ? 'Suspend' : 'Aktifkan' }}
                  </button>

                  <button
                    @click="hapus(u.id)"
                    class="px-3 py-1.5 text-xs rounded-md bg-red-100 text-red-600 hover:bg-red-200 transition"
                  >
                    Hapus
                  </button>
                  <button
                    @click="openEdit(u)"
                    class="px-3 py-1.5 text-xs rounded-md bg-blue-100 text-blue-600 hover:bg-blue-200 transition"
                    >
                    Edit
                  </button>

                </div>
              </td>

            </tr>

            <tr v-if="!users.data.length">
              <td colspan="5" class="text-center py-6 text-gray-400">
                Belum ada user
              </td>
            </tr>
          </tbody>
        </table>

        <!-- PAGINATION -->
        <div class="p-4 flex justify-center">
          <button
            v-for="(link, i) in users.links"
            :key="i"
            v-html="link.label"
            @click="link.url && router.visit(link.url)"
            class="px-3 py-1 border rounded mx-1"
          />
        </div>
      </div>

    </div>
  </AuthenticatedLayout>

<div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
  <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl p-6 overflow-y-auto max-h-[90vh]">

    <div class="flex justify-between mb-4">
      <h2 class="font-semibold">✏️ Edit User</h2>
      <button @click="closeModal">✕</button>
    </div>
    <div class="space-y-4">

      <div class="grid md:grid-cols-2 gap-3">
        <div>
          <label class="text-sm font-medium">Nama</label>
          <input v-model="form.name" class="input" />
          <p v-if="form.errors.name" class="text-red-500 text-xs">
            {{ form.errors.name }}
          </p>
        </div>

        <div>
          <label class="text-sm font-medium">Email</label>
          <input v-model="form.email" class="input" />
          <p v-if="form.errors.email" class="text-red-500 text-xs">
            {{ form.errors.email }}
          </p>
        </div>
      </div>

      <div>
        <label class="text-sm font-medium">Role</label>
        <select v-model="form.role" class="input">
          <option value="">Pilih Role</option>
          <option>admin</option>
          <option>user</option>
        </select>
        <p v-if="form.errors.role" class="text-red-500 text-xs">
          {{ form.errors.role }}
        </p>
      </div>

      <div class="border-t pt-4 space-y-3">
        <p class="text-sm font-semibold">Data Profil</p>
        <div>
          <label class="text-sm font-medium">Nama Lengkap</label>
          <input v-model="form.nama_lengkap" class="input"/>
          <p v-if="form.errors.nama_lengkap" class="text-red-500 text-xs">
            {{ form.errors.nama_lengkap }}
          </p>
        </div>

        <div>
          <label class="text-sm font-medium">Jenis Kelamin</label>
          <select v-model="form.jenis_kelamin" class="input">
            <option value="">Pilih</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
          <p v-if="form.errors.jenis_kelamin" class="text-red-500 text-xs">
            {{ form.errors.jenis_kelamin }}
          </p>
        </div>

        <div>
          <label class="text-sm font-medium">Alamat</label>
          <textarea v-model="form.alamat" class="input"></textarea>
          <p v-if="form.errors.alamat" class="text-red-500 text-xs">
            {{ form.errors.alamat }}
          </p>
        </div>

        <div class="grid md:grid-cols-2 gap-3">
          <div>
            <label class="text-sm font-medium">No Telepon</label>
            <input v-model="form.no_telp" class="input"/>
            <p v-if="form.errors.no_telp" class="text-red-500 text-xs">
              {{ form.errors.no_telp }}
            </p>
          </div>

          <div>
            <label class="text-sm font-medium">No WhatsApp</label>
            <input v-model="form.no_wa" class="input"/>
            <p v-if="form.errors.no_wa" class="text-red-500 text-xs">
              {{ form.errors.no_wa }}
            </p>
          </div>

        </div>

        <div>
          <label class="text-sm font-medium">Jabatan</label>
          <input v-model="form.jabatan" class="input"/>
          <p v-if="form.errors.jabatan" class="text-red-500 text-xs">
            {{ form.errors.jabatan }}
          </p>
        </div>

        <div>
          <label class="text-sm font-medium">Alamat Kantor</label>
          <textarea v-model="form.alamat_kantor" class="input"></textarea>
          <p v-if="form.errors.alamat_kantor" class="text-red-500 text-xs">
            {{ form.errors.alamat_kantor }}
          </p>
        </div>

        <div class="grid md:grid-cols-2 gap-3">
          <div>
            <label class="text-sm font-medium">Nama Bank</label>
            <input v-model="form.nama_bank" class="input"/>
            <p v-if="form.errors.nama_bank" class="text-red-500 text-xs">
              {{ form.errors.nama_bank }}
            </p>
          </div>

          <div>
            <label class="text-sm font-medium">No Rekening</label>
            <input v-model="form.no_rekening" class="input"/>
            <p v-if="form.errors.no_rekening" class="text-red-500 text-xs">
              {{ form.errors.no_rekening }}
            </p>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-3">

          <div>
            <label class="text-sm font-medium">Ukuran Baju</label>
            <select v-model="form.ukuran_baju" class="input">
              <option value="">Pilih</option>
              <option>M</option>
              <option>L</option>
              <option>XL</option>
            </select>
            <p v-if="form.errors.ukuran_baju" class="text-red-500 text-xs">
              {{ form.errors.ukuran_baju }}
            </p>
          </div>

          <div>
            <label class="text-sm font-medium">Smoking?</label>
            <select v-model="form.is_smoking" class="input">
              <option :value="0">Tidak</option>
              <option :value="1">Ya</option>
            </select>
            <p v-if="form.errors.is_smoking" class="text-red-500 text-xs">
              {{ form.errors.is_smoking }}
            </p>
          </div>
        </div>

      </div>
      <div class="flex justify-end gap-2 pt-3">
        <button @click="closeModal" class="px-4 py-2 border rounded-lg">
          Batal
        </button>
        <button
          @click="submit"
          class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg"
          :disabled="form.processing"
        >
          {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
        </button>
      </div>
    </div>

  </div>
</div>
</template>