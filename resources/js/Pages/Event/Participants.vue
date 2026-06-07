<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import debounce from 'lodash/debounce'
import { toast } from 'vue3-toastify'
import axios from 'axios'

const props = defineProps({
  event: Object,
  participants: Object,
  filters: Object
})

const search = ref(props.filters?.search || '')
const status = ref(props.filters?.status || '')

watch(
  [search, status],
  debounce(() => {
    router.get(`/events/${props.event.hashid}/participants`, {
      search: search.value,
      status: status.value
    }, {
      preserveState: true,
      replace: true
    })
  }, 400)
)


const showAddModal = ref(false)
const users = ref([])
const selectedUser = ref(null)
const userSearch = ref('')
watch(
  [userSearch, showAddModal],
  debounce(([searchValue, modalOpen]) => {
    if (!modalOpen) return

    axios.get(`/events/${props.event.hashid}/available-users`, {
      params: { search: searchValue || null }
    }).then(res => {
      users.value = res.data
    })
  }, 300)
)

const addParticipant = () => {
  if (!selectedUser.value) return

  router.post(`/events/${props.event.hashid}/participants`, {
    user_id: selectedUser.value.id
  }, {
    onSuccess: () => {
      toast.success('Peserta berhasil ditambahkan')
      showAddModal.value = false
      selectedUser.value = null
      userSearch.value = ''
      users.value = []
    }
  })
}

const approve = (id) => {
  router.post(`/participants/${id}/approve`, {}, {
    onSuccess: () => toast.success('Peserta disetujui')
  })
}

const unapprove = (id) => {
  if (!confirm('Yakin ingin membatalkan approval peserta ini?')) return
  router.post(`/participants/${id}/unapprove`, {}, {
    onSuccess: () => toast.info('Status dikembalikan ke pending')
  })
}

const reject = (id) => {
  router.post(`/participants/${id}/reject`, {}, {
    onSuccess: () => toast.error('Peserta ditolak')
  })
}

const statusBadge = (status) => {
  switch (status) {
    case 'approved':
      return 'bg-emerald-100 text-emerald-600'
    case 'pending':
      return 'bg-yellow-100 text-yellow-600'
    case 'rejected':
      return 'bg-red-100 text-red-600'
    default:
      return 'bg-gray-100 text-gray-600'
  }
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  }).format(new Date(date))
}

const getLocation = () => {
  return props.event.venue?.name || props.event.custom_location || '-'
}
const showModal = ref(false)
const selectedParticipant = ref(null)
const openDetail = (p) => {
  selectedParticipant.value = p
  showModal.value = true
}
const closeModal = () => {
  showModal.value = false
  selectedParticipant.value = null
}
const formatDateTime = (date) => {
  if (!date) return '-'

  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}
</script>

<template>
  <Head :title="`Peserta - ${event.title}`" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

      <!-- HEADER -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
          Peserta Event
        </h1>

        <a
          :href="`/events/${event.hashid}`"
          class="text-sm text-gray-500 hover:underline"
        >
          ← Kembali ke Event
        </a>
      </div>

    <!-- EVENT INFO CARD -->
    <div class="bg-white rounded-2xl shadow border overflow-hidden mb-6">

        <!-- HEADER (banner / gradient) -->
        <div class="h-50 relative">
            <!-- banner -->
            <img
            v-if="event.banner"
            :src="`/storage/${event.banner}`"
            class="absolute inset-0 w-full h-full object-cover"
            />

            <!-- fallback gradient -->
            <div
            v-else
            class="absolute inset-0 bg-gradient-to-r from-emerald-500 via-blue-500 to-purple-500"
            ></div>

            <!-- overlay -->
            <div class="absolute inset-0 bg-black/40"></div>

            <!-- title -->
            <div class="absolute bottom-4 left-5 text-white">
            <h2 class="text-xl font-bold">
                {{ event.title }}
            </h2>
            <p class="text-sm opacity-80">
                Event Information
            </p>
            </div>

        </div>

        <!-- BODY -->
        <div class="p-5 grid md:grid-cols-3 gap-4 text-sm">

            <!-- tanggal -->
            <div>
            <p class="text-gray-500">Tanggal Event</p>
            <p class="font-semibold text-gray-800">
                {{ formatDate(event.start_date) }} - {{ formatDate(event.end_date) }}
            </p>
            </div>

            <!-- lokasi -->
            <div>
            <p class="text-gray-500">Lokasi</p>
            <p class="font-semibold text-gray-800">
                {{ getLocation() }}
            </p>
            </div>

            <!-- status -->
            <div>
            <p class="text-gray-500">Status</p>
            <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-600">
                {{ event.status }}
            </span>
            </div>

            <div>
            <p class="text-gray-500">Total Peserta</p>
            <p class="font-bold text-emerald-600 text-lg">
                {{ participants.total }}
            </p>
            </div>

        </div>

    </div>

      <!-- FILTER -->
     <div class="bg-white p-4 rounded-2xl shadow border mb-0 flex flex-col md:flex-row md:items-center md:justify-between gap-3">

      <!-- LEFT: FILTER -->
      <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
        <input
          v-model="search"
          type="text"
          placeholder="Cari nama / email..."
          class="input md:w-64"
        />

        <select v-model="status" class="input md:w-48">
          <option value="">Semua Status</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>

      <!-- RIGHT: ACTION -->
      <div class="flex justify-end">

        <button
          @click="showAddModal = true"
          class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm hover:bg-blue-700 mr-2"
        >
          + Tambah Peserta
        </button>

        <a
          :href="`/events/${event.hashid}/participants/export?status=${status}`"
          class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-xl text-sm font-medium shadow hover:bg-emerald-700 transition"
        >
         Export CSV
        </a>
      </div>

    </div>

      <!-- TABLE -->
      <div class="bg-white rounded-2xl shadow-lg border overflow-hidden">

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">

            <!-- HEADER -->
            <thead class="bg-gray-50 text-gray-600">
              <tr class="text-xs uppercase">
                <th class="p-4 text-left">Peserta</th>
                <th>Email</th>
                <th>Daftar</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>

            <!-- BODY -->
            <tbody class="divide-y">
              <tr
                v-for="p in participants.data"
                :key="p.id"
                class="hover:bg-emerald-50 cursor-pointer transition"
                @click="openDetail(p)">

                <!-- USER -->
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                      {{ p.user.name.charAt(0) }}
                    </div>

                    <div>
                      <div class="font-semibold text-gray-800">
                        {{ p.user.name }}
                      </div>
                      <div class="text-xs text-gray-500">
                        ID: {{ p.user.id }}
                      </div>
                    </div>
                  </div>
                </td>
                <!-- EMAIL -->
                <td class="text-gray-600">
                  {{ p.user.email }}
                </td>
                <!-- REGISTERED -->
                <td class="text-gray-500">
                  {{ formatDateTime(p.registered_at) }}
                </td>
                <!-- STATUS -->
                <td>
                  <span
                    :class="statusBadge(p.status)"
                    class="px-3 py-1 rounded-full text-xs font-medium"
                  >
                    {{ p.status }}
                  </span>
                </td>
                <!-- ACTION -->
                <td class="text-center">
                    <div class="flex justify-center gap-2">
                        <button
                        v-if="p.status === 'pending'"
                        @click.stop="approve(p.id)"
                        class="px-3 py-1 text-xs rounded-lg bg-emerald-100 text-emerald-600 hover:bg-emerald-200"
                        >
                        Approve
                        </button>
                        <button
                        v-if="p.status === 'pending'"
                        @click.stop="reject(p.id)"
                        class="px-3 py-1 text-xs rounded-lg bg-red-100 text-red-600 hover:bg-red-200"
                        >
                        Reject
                        </button>
                        <button
                          v-if="p.status === 'approved'"
                          @click.stop="unapprove(p.id)"
                          class="px-3 py-1 text-xs bg-yellow-300 text-yellow-600 rounded-lg hover:bg-yellow-400"
                        >
                          Batalkan Approval
                        </button>
                    </div>
                </td>

              </tr>

              <!-- EMPTY -->
              <tr v-if="!participants.data.length">
                <td colspan="5" class="text-center py-10 text-gray-400">
                  Belum ada peserta
                </td>
              </tr>

            </tbody>
          </table>
        </div>

        <!-- PAGINATION -->
        <div class="flex justify-center p-4 border-t">
          <div class="flex gap-1">

            <button
              v-for="(link, i) in participants.links"
              :key="i"
              v-html="link.label"
              @click="link.url && router.visit(link.url)"
              class="px-3 py-1 rounded-lg border text-sm"
              :class="[
                link.active
                  ? 'bg-emerald-500 text-white'
                  : 'bg-white text-gray-600 hover:bg-gray-100',
                !link.url && 'opacity-50'
              ]"
            />

          </div>
        </div>

      </div>

    </div>
  </AuthenticatedLayout>


<!-- MODAL DETAIL -->
<div
  v-if="showModal"
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
>
  <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
    <!-- HEADER -->
    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 p-6 text-white relative">
      <button
        @click="closeModal"
        class="absolute top-3 right-3 text-white/70 hover:text-white"
      >
        ✕
      </button>
      <div class="flex items-center gap-4">
        <!-- AVATAR -->
        <img
          v-if="selectedParticipant?.user?.profile?.avatar"
          :src="`/storage/${selectedParticipant.user.profile.avatar}`"
          class="w-16 h-16 rounded-full object-cover border-2 border-white"
        />
        <div
          v-else
          class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-xl font-bold"
        >
          {{ selectedParticipant?.user?.name?.charAt(0) }}
        </div>
        <div>
          <h3 class="text-lg font-bold">
            {{ selectedParticipant?.user?.profile?.nama_lengkap || selectedParticipant?.user?.name }}
          </h3>
          <p class="text-sm opacity-90">
            {{ selectedParticipant?.user?.email }}
          </p>
        </div>

      </div>
    </div>

    <!-- BODY -->
    <div class="p-6 space-y-4 text-sm">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <p class="text-gray-500">User ID</p>
          <p class="font-semibold">{{ selectedParticipant?.user?.id }}</p>
        </div>
        <div>
          <p class="text-gray-500">Status</p>
          <span
            :class="statusBadge(selectedParticipant?.status)"
            class="px-3 py-1 rounded-full text-xs font-medium"
          >
            {{ selectedParticipant?.status }}
          </span>
        </div>
        <div>
          <p class="text-gray-500">Tanggal Daftar</p>
          <p class="font-medium">
            {{ formatDateTime(selectedParticipant?.registered_at) }}
          </p>
        </div>
        <div>
          <p class="text-gray-500">Check-in</p>
          <p class="font-medium">
            {{ selectedParticipant?.checked_in_at ?? '-' }}
          </p>
        </div>
      </div>

      <!-- PROFILE DETAIL -->
      <div class="border-t pt-4 space-y-3">
        <h4 class="font-semibold text-gray-700">Informasi Peserta</h4>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <p class="text-gray-500">No HP</p>
            <p class="font-medium">
              {{ selectedParticipant?.user?.profile?.no_telp || '-' }}
            </p>
          </div>
          <div>
            <p class="text-gray-500">WhatsApp</p>
            <p class="font-medium">
              {{ selectedParticipant?.user?.profile?.no_wa || '-' }}
            </p>
          </div>
          <div>
            <p class="text-gray-500">Jabatan</p>
            <p class="font-medium">
              {{ selectedParticipant?.user?.profile?.jabatan || '-' }}
            </p>
          </div>
          <div>
            <p class="text-gray-500">Ukuran Baju</p>
            <p class="font-medium">
              {{ selectedParticipant?.user?.profile?.ukuran_baju || '-' }}
            </p>
          </div>
          <div class="col-span-2">
            <p class="text-gray-500">Institusi</p>
            <p class="font-medium">
              {{ selectedParticipant?.user?.profile?.institusi?.nama_institusi || '-' }}
            </p>
          </div>
          <div class="col-span-2">
            <p class="text-gray-500">Alamat</p>
            <p class="font-medium">
              {{ selectedParticipant?.user?.profile?.alamat || '-' }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- ACTION -->
    <div class="flex justify-end gap-2 p-4 border-t">

      <button
        v-if="selectedParticipant?.status === 'pending'"
        @click="approve(selectedParticipant.id); closeModal()"
        class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600"
      >
        Approve
      </button>
      <button
        v-if="selectedParticipant?.status === 'pending'"
        @click="reject(selectedParticipant.id); closeModal()"
        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600"
      >
        Reject
      </button>
    </div>
  </div>
</div>


<div
  v-if="showAddModal"
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
>
  <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6">

    <h3 class="text-lg font-bold mb-4">Tambah Peserta</h3>

    <!-- SEARCH -->
    <input
      v-model="userSearch"
      type="text"
      placeholder="Cari nama / email..."
      class="input w-full mb-3"
    />

    <!-- LIST USER -->
    <div class="max-h-60 overflow-y-auto border rounded-xl">
      <div
        v-for="u in users"
        :key="u.id"
        @click="selectedUser = u"
        class="p-3 cursor-pointer hover:bg-emerald-50 flex justify-between"
        :class="selectedUser?.id === u.id && 'bg-emerald-100'"
      >
        <div>
          <p class="font-medium">{{ u.name }}</p>
          <p class="text-xs text-gray-500">{{ u.email }}</p>
        </div>
        <span v-if="selectedUser?.id === u.id">✔</span>
      </div>

      <div v-if="!users.length && userSearch" class="p-3 text-sm text-gray-400 text-center">
        User tidak ditemukan
      </div>
    </div>

    <!-- ACTION -->
    <div class="flex justify-end gap-2 mt-4">
      <button
        @click="showAddModal = false"
        class="px-4 py-2 text-gray-600"
      >
        Batal
      </button>
      <button
        @click="addParticipant"
        class="px-4 py-2 bg-emerald-600 text-white rounded-lg"
      >
        Tambah
      </button>
    </div>

  </div>
</div>



</template>

<style scoped>
.input {
  padding: 10px;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  outline: none;
}
.input:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 2px rgba(16,185,129,0.2);
}
</style>