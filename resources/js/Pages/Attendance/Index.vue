<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import { toast } from 'vue3-toastify'

const page = usePage()

onMounted(() => {
  if (page.props.flash?.error) {
    toast.error(page.props.flash.error)
  }
})

const props = defineProps({
  event: Object,
  days: Array,
  event_checkin_count: Number
})

const openQR = (date) => {
  router.visit(`/events/${props.event.hashid}/attendance/qr-page?date=${date}`)
}
const viewParticipants = (date) => {
  router.visit(`/events/${props.event.hashid}/attendance/participants?date=${date}`)
}
const goBack = () => {
  router.visit('/events')
}
const formatIndoDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    weekday: 'long',  
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const openEventQR = () => {
  router.visit(`/events/${props.event.hashid}/checkin-qr`)
}
const viewEventParticipants = () => {
  router.visit(`/events/${props.event.hashid}/checkin-participants`)
}
</script>

<template>
  <Head title="Attendance" />

  <AuthenticatedLayout>

    <!-- HEADER -->
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-800">
            Presensi Event
          </h2>
          <p class="text-sm text-gray-500">
            Kelola kehadiran peserta
          </p>
        </div>

        <button
          @click="goBack"
          class="text-sm px-3 py-2 rounded-lg border hover:bg-gray-50 transition"
        >
          ← Kembali
        </button>
      </div>
    </template>

    <div class="max-w-5xl mx-auto px-4 py-6 space-y-6">

      <!-- HERO -->
      <div class="bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 text-white p-6 rounded-3xl shadow-lg">
        <h3 class="text-2xl font-bold">{{ event.title }}</h3>
        <p class="text-sm opacity-90 mt-1">
          Monitoring presensi harian peserta
        </p>
      </div>

      <!-- EVENT CHECK-IN CARD -->
      <div class="bg-white rounded-2xl border p-5 hover:shadow-md transition flex flex-col justify-between">

        <div class="space-y-3">

          <div>
            <p class="font-semibold text-gray-800 text-lg">
              Check-in Event
            </p>
            <p class="text-xs text-gray-400">
              Hanya sekali selama event berlangsung
            </p>
          </div>
          <!-- STATS -->
          <div class="flex items-center justify-between bg-emerald-50 rounded-xl px-4 py-3">
            <div>
              <p class="text-xs text-gray-500">
                Total Hadir
              </p>
              <p class="text-xl font-bold text-emerald-600">
                {{ event_checkin_count }}
              </p>
            </div>
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold">
              ✓
            </div>
          </div>

        </div>

        <!-- ACTION -->
        <div class="flex gap-2 mt-4">
          <button
            @click="openEventQR"
            class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-xl text-sm font-semibold transition"
          >
            QR
          </button>
          <button
            @click="viewEventParticipants"
            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-xl text-sm font-semibold transition"
          >
            Peserta
          </button>
        </div>

      </div>

      <!-- EMPTY -->
      <div
        v-if="days.length === 0"
        class="bg-white border rounded-2xl p-6 text-center text-gray-400"
      >
        Belum ada jadwal presensi
      </div>

      <!-- LIST -->
      <div class="grid md:grid-cols-2 gap-4">

        <div
          v-for="day in days"
          :key="day.date"
          class="bg-white rounded-2xl border p-5 hover:shadow-md transition flex flex-col justify-between"
        >

          <!-- TOP -->
          <div class="space-y-3">

            <!-- DATE -->
            <div>
              <p class="text-sm text-gray-500">
                Presensi Harian
              </p>
              <p class="font-semibold text-gray-800">
               {{ formatIndoDate(day.date) }}
              </p>
            </div>

            <!-- STATS -->
            <div class="flex items-center justify-between bg-emerald-50 rounded-xl px-4 py-3">
              <div>
                <p class="text-xs text-gray-500">
                  Total Hadir
                </p>
                <p class="text-xl font-bold text-emerald-600">
                  {{ day.attendance_count }}
                </p>
              </div>

              <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold">
                ✓
              </div>
            </div>

          </div>

          <!-- ACTION -->
          <div class="flex gap-2 mt-4">

            <button
              @click="openQR(day.date)"
              class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-xl text-sm font-semibold transition"
            >
              QR
            </button>

            <button
              @click="viewParticipants(day.date)"
              class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-xl text-sm font-semibold transition"
            >
              Peserta
            </button>

          </div>

        </div>

      </div>

    </div>

  </AuthenticatedLayout>
</template>