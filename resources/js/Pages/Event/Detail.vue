<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import { computed, ref } from 'vue'

const props = defineProps({
  event: Object,
  isRegistered: Boolean,
  myStatus: String,
  myParticipant: Object,
  sessions: Array,
  attendances: Object
})

// =======================
// FORMAT DATE
// =======================
const formatDate = (date) => {
  if (!date) return '-'
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(new Date(date))
}

const formatDateTime = (date) => {
  if (!date) return '-'
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}

const getAttendance = (date) => {
  const d = new Date(date)
  const normalized = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
  return props.attendances?.[normalized] || null
}
// =======================
// LOCATION
// =======================
const getLocation = () => {
  return props.event?.venue?.name || props.event?.custom_location || '-'
}

// =======================
// REGISTRATION CHECK
// =======================
const now = new Date()

const isRegistrationOpen = computed(() => {
  if (!props.event.registration_start || !props.event.registration_end) return false

  const start = new Date(props.event.registration_start)
  const end = new Date(props.event.registration_end)

  return now >= start && now <= end
})

// =======================
// APPLY EVENT
// =======================
const applyEvent = () => {
  router.post(`/apply-event/${props.event.hashid}`, {}, {
    onSuccess: () => {
      toast.success('Berhasil mendaftar, menunggu approval')
    }
  })
}
const statusConfig = (status) => {
  switch (status) {
    case 'pending':
      return {
        label: 'Menunggu Approval',
        class: 'bg-yellow-100 text-yellow-700'
      }
    case 'approved':
      return {
        label: 'Disetujui',
        class: 'bg-emerald-100 text-emerald-700'
      }
    case 'rejected':
      return {
        label: 'Ditolak',
        class: 'bg-red-100 text-red-700'
      }
    case 'checked_in':
      return {
        label: 'Sudah Check-in',
        class: 'bg-blue-100 text-blue-700'
      }
    default:
      return {
        label: status,
        class: 'bg-gray-100 text-gray-600'
      }
  }
}

const handleDownload = (status, sessionId, index) => {
  if (status !== 'checked_in') {
    toast.warning('Materi hanya bisa diunduh setelah check-in')
    return
  }

  window.open(
    route('materials.download', { id: sessionId, index }),
    '_blank'
  )
}
const groupedSessions = computed(() => {
  const groups = {}

  props.sessions.forEach(s => {
    if (!groups[s.session_date]) {
      groups[s.session_date] = []
    }
    groups[s.session_date].push(s)
  })

  return groups
})
const formatTimeOnly = (val) => {
  if (!val) return '-'

  return new Date(val).toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const showFlyer = ref(false)

const flyerUrl = computed(() => {
  return props.event?.flyer
    ? `/storage/${props.event.flyer}`
    : null
})

const dayColors = [
  {
    header: 'bg-emerald-100 text-emerald-700',
    line: 'bg-emerald-400'
  },
  {
    header: 'bg-blue-100 text-blue-700',
    line: 'bg-blue-400'
  },
  {
    header: 'bg-purple-100 text-purple-700',
    line: 'bg-purple-400'
  }
]
</script>

<template>
  <Head :title="event.title" />

  <AuthenticatedLayout>
    <div class="max-w-6xl mx-auto px-4 py-6 space-y-6">

      <!-- ===================== -->
      <!-- HEADER -->
      <!-- ===================== -->
      <div class="flex justify-between items-center">
        <h1 class="text-lg font-semibold text-gray-700">
          Detail Event
        </h1>

        <button
          @click="router.visit('/dashboard')"
          class="text-sm text-gray-500 hover:text-gray-700"
        >
          ← Kembali ke Dashboard
        </button>
      </div>

      <!-- ===================== -->
      <!-- HERO -->
      <!-- ===================== -->
      <div class="rounded-3xl overflow-hidden shadow-lg relative h-72">

        <img
          v-if="event.banner"
          :src="`/storage/${event.banner}`"
          class="absolute inset-0 w-full h-full object-cover"
        />

        <div
          v-else
          class="absolute inset-0 bg-gradient-to-r from-emerald-500 via-blue-500 to-purple-500"
        ></div>

        <div class="absolute inset-0 bg-black/50"></div>

        <div class="absolute bottom-6 left-6 text-white max-w-xl">
          <p class="text-xs opacity-80 mb-1">EVENT</p>
          <h1 class="text-3xl font-bold leading-tight">
            {{ event.title }}
          </h1>

            <div v-if="myParticipant">
              <div v-if="myParticipant.checked_in_at">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/90 backdrop-blur">
                  ✓ Sudah Check-in Event
                </span>
              </div>
              <div v-else-if="myStatus === 'approved'">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-500/90 backdrop-blur">
                   Belum Check-in Event
                </span>
              </div>
            </div>

        </div>
      </div>

      <!-- FLYER CTA -->
        <div v-if="flyerUrl" class="mb-4">
          <button
            @click="showFlyer = true"
            class="w-full md:w-auto px-4 py-2 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow"
          >
            Lihat Flyer Event
          </button>

          <p class="text-xs text-gray-400 mt-2">
            Klik untuk melihat flyer secara full
          </p>
        </div>

      <!-- ===================== -->
      <!-- GRID -->
      <!-- ===================== -->
      <div class="grid lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 flex flex-col gap-6">

          <!-- ===================== -->
          <!-- DESKRIPSI -->
          <!-- ===================== -->
          <div class="bg-white rounded-2xl shadow border p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-5">
            Deskripsi Event
          </h2>
            <blockquote class="border-l-4 border-emerald-500 pl-4 text-gray-600">
              <div v-html="event.description"></div>
            </blockquote>
          </div>

          <!-- ===================== -->
          <!-- RUNDOWN -->
          <!-- ===================== -->
          <div
            v-if="['approved','checked_in'].includes(myStatus)"
            class="bg-white rounded-2xl shadow border p-6 space-y-4 lg:col-span-2"
          >
            <h2 class="text-xl font-bold text-gray-800">
              Rundown Event
            </h2>
            <div v-if="sessions.length" class="space-y-6">

            <div
              v-for="(daySessions, date, index) in groupedSessions"
              :key="date"
              class="space-y-3"
            >

              <!-- HEADER HARI -->
              <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex items-center justify-center">
                  <span
                    class="absolute inline-flex h-4 w-4 rounded-full opacity-75 animate-ping"
                    :class="dayColors[index % dayColors.length].line"
                  ></span>
                  <span
                    class="relative inline-flex h-4 w-4 rounded-full"
                    :class="dayColors[index % dayColors.length].line"
                  ></span>
                </div>

                <div
                  class="px-3 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap"
                  :class="dayColors[index % dayColors.length].header"
                >
                  {{ formatDate(date) }}
                </div>
                <!-- STATUS -->
                <div>
                  <span
                    v-if="getAttendance(date)"
                    class="flex items-center gap-1.5 text-xs md:text-sm px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200"
                  >
                    <span class="text-emerald-500">●</span>
                    Hadir
                  </span>
                  <span
                    v-else
                    class="flex items-center gap-1.5 text-xs md:text-sm px-3 py-1.5 rounded-full bg-red-50 text-red-600 border border-red-200"
                  >
                    <span class="text-red-500">●</span>
                    Belum Presensi
                  </span>
                </div>
                <!-- LINE -->
                <div class="hidden md:block flex-1 h-px bg-gray-200"></div>

              </div>

              <!-- LIST SESSION -->
              <div
                v-for="s in daySessions"
                :key="s.id"
                class="relative border rounded-xl p-4 pl-5 bg-white shadow-sm hover:shadow-md transition"
              >

                <!-- timeline accent -->
                <div
                  class="absolute left-0 top-0 bottom-0 w-1 rounded-l-xl"
                  :class="dayColors[index % dayColors.length].line"
                ></div>
                <!-- HEADER (TIME + TITLE) -->
                <div class="flex items-center justify-between flex-wrap gap-2">

                  <h4 class="font-semibold text-gray-800">
                    {{ s.title }}
                  </h4>

                  <span class="text-xs px-2 py-1 bg-gray-100 rounded-full text-gray-600">
                    ⏰ {{ formatTimeOnly(s.start_at) }} - {{ formatTimeOnly(s.end_at) }}
                  </span>

                </div>

                <!-- DESCRIPTION -->
                <p
                  v-if="s.description"
                  class="text-sm text-gray-600 mt-2"
                  v-html="s.description"
                ></p>

                <!-- MATERIALS -->
                <div v-if="s.materials?.length" class="flex flex-wrap gap-2 mt-3">
                  <button
                    v-for="(file, i) in s.materials"
                    :key="file.id"
                    @click="handleDownload(myStatus, s.id, i)"
                    class="flex items-center gap-1 px-3 py-1 rounded-full text-xs transition"
                    :class="myStatus === 'checked_in'
                      ? 'bg-blue-50 text-blue-600 hover:bg-blue-100'
                      : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                  >
                    📎 {{ file.name }}
                  </button>
                </div>

                <!-- LOCK INFO -->
                <p
                  v-if="s.materials?.length && myStatus !== 'checked_in'"
                  class="text-xs text-gray-400 italic mt-1"
                >
                  🔒 Materi tersedia setelah kamu check-in
                </p>

              </div>

            </div>
          </div>

            <div v-else class="text-sm text-gray-400 italic">
              Belum ada rundown
            </div>

            <!-- STATUS INFO (di luar rundown) -->
            <div v-if="isRegistered && myStatus === 'pending'"
              class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl p-4 text-sm">
              ⏳ Pendaftaran kamu sedang direview. Rundown akan tersedia setelah disetujui.
            </div>

            <div v-if="myStatus === 'rejected'"
              class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm">
              ❌ Pendaftaran kamu ditolak.
            </div>
          </div>
          
        </div>

        <!-- ===================== -->
        <!-- SIDEBAR -->
        <!-- ===================== -->
        <div class="space-y-4 self-start">

          <!-- INFO -->
          <div class="bg-white rounded-2xl shadow border p-5 space-y-4">
            <h3 class="font-semibold text-gray-800">
              Informasi Event
            </h3>
            <div class="space-y-3 text-sm">
              <!-- TANGGAL -->
              <div class="flex items-start gap-3">
                <span>📅</span>
                <div>
                  <p class="text-gray-500">Tanggal Event</p>
                  <p class="font-medium">
                    {{ formatDate(event.start_date) }} - {{ formatDate(event.end_date) }}
                  </p>
                </div>
              </div>
              <!-- LOKASI -->
              <div class="flex items-start gap-3">
                <span>📍</span>
                <div>
                  <p class="text-gray-500">Lokasi</p>
                  <p class="font-medium">
                    {{ getLocation() }}
                  </p>
                </div>
              </div>

              <!-- PERIODE PENDAFTARAN -->
              <div class="flex items-start gap-3">
                <span>📝</span>
                <div>
                  <p class="text-gray-500">Periode Pendaftaran</p>
                  <p class="font-medium">
                    {{ formatDateTime(event.registration_start) }}
                    -
                    {{ formatDateTime(event.registration_end) }}
                  </p>

                  <!-- STATUS OPEN/CLOSE -->
                  <span
                    class="inline-block mt-1 text-xs font-medium"
                    :class="isRegistrationOpen ? 'text-emerald-600' : 'text-red-500'"
                  >
                    {{ isRegistrationOpen ? 'Pendaftaran Dibuka' : 'Pendaftaran Ditutup' }}
                  </span>
                </div>
              </div>

              <!-- DATA USER -->
              <div v-if="isRegistered" class="flex items-start gap-3">
                <span>🕒</span>
                <div>
                  <p class="text-gray-500">Waktu Kamu Daftar</p>
                  <p class="font-medium">
                    {{ formatDateTime(myParticipant?.registered_at) }}
                  </p>

                  <span
                    class="inline-block mt-2 px-2 py-1 rounded-full text-xs font-medium"
                    :class="statusConfig(myStatus).class"
                  >
                    {{ statusConfig(myStatus).label }}
                  </span>
                </div>
              </div>

            </div>

          </div>

          <!-- ACTION -->
          <div class="bg-white rounded-2xl shadow border p-5 space-y-4">

            <!-- DAFTAR -->
            <button
              v-if="!isRegistered && isRegistrationOpen"
              @click="applyEvent"
              class="w-full bg-emerald-500 text-white py-3 rounded-xl font-semibold hover:bg-emerald-600 transition shadow"
            >
              🚀 Daftar Sekarang
            </button>

            <!-- PENDAFTARAN TUTUP -->
            <div
              v-else-if="!isRegistered && !isRegistrationOpen"
              class="w-full text-center py-3 rounded-xl text-sm font-semibold bg-gray-100 text-gray-500"
            >
              Pendaftaran Ditutup
            </div>

            <!-- SUDAH DAFTAR -->
            <div
              v-else
              class="w-full text-center py-3 rounded-xl text-sm font-semibold"
              :class="{
                'bg-yellow-100 text-yellow-700': myStatus === 'pending',
                'bg-emerald-100 text-emerald-700': myStatus === 'approved',
                'bg-red-100 text-red-700': myStatus === 'rejected',
                'bg-blue-100 text-blue-700': myStatus === 'checked_in'
              }"
            >
              Kamu sudah mendaftar
            </div>

            <p class="text-xs text-gray-400 text-center">
              Pastikan data profil kamu sudah lengkap
            </p>

          </div>

        </div>

      </div>
    </div>
  </AuthenticatedLayout>

  <!-- FLYER MODAL -->
<div
  v-if="showFlyer"
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-md"
  @click.self="showFlyer = false"
>
  <div class="relative max-w-4xl w-full mx-4">
    <button
      @click="showFlyer = false"
      class="absolute -top-10 right-0 text-white text-sm bg-white/10 px-3 py-1 rounded-lg hover:bg-white/20"
    >
      ✕ Tutup
    </button>
    <img
      :src="flyerUrl"
      class="w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl border border-white/10"
    />
  </div>

</div>
</template>