<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import QRCode from 'qrcode'

const goBack = () => {
  router.visit(`/events/${props.event.hashid}/attendance`)
}

const reloadQR = async () => {
  clearInterval(interval) 
  await generateQR()
  startTimer()
}

const props = defineProps({
  event: Object,
  date: String
})

const qrImage = ref(null)
const countdown = ref(0)
const maxTime = ref(0)
let interval = null

const formatDate = (val) => {
  return new Date(val).toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const isLoading = ref(false)

const generateQR = async () => {
  try {
    isLoading.value = true

    const res = await axios.get(`/events/${props.event.hashid}/attendance/qr?date=${props.date}`)

    const payload = JSON.stringify({
      type: 'daily', 
      event_id: res.data.event_id,
      hashid: res.data.hashid,
      date: res.data.date,
      token: res.data.token
    })

    qrImage.value = await QRCode.toDataURL(payload)

    countdown.value = res.data.expired_in
    maxTime.value = res.data.expired_in

  } catch (e) {
    qrImage.value = null
  } finally {
    isLoading.value = false
  }
}

const startTimer = () => {
  interval = setInterval(() => {
    countdown.value--

    if (countdown.value <= 0) {
      generateQR()
    }
  }, 1000)
}

onMounted(async () => {
  await generateQR()
  startTimer()
})

onUnmounted(() => {
  clearInterval(interval)
})
</script>

<template>
  <Head title="QR Presensi" />

  <AuthenticatedLayout>

    <template #header>
      <div>
        <h2 class="text-2xl font-bold text-gray-800">
          QR Presensi
        </h2>
        <p class="text-sm text-gray-500">
          {{ event.title }}
        </p>
      </div>
    </template>

    <div class="max-w-3xl mx-auto px-4 py-8 space-y-6 text-center">

      <!-- INFO -->
      <div>
        <p class="text-sm text-gray-500">Tanggal</p>
        <p class="font-semibold text-gray-800">
          {{ formatDate(date) }}
        </p>
      </div>

      <!-- QR CARD -->
      <div class="bg-white p-6 md:p-8 rounded-3xl shadow-lg space-y-6">

        <!-- QR -->
        <div class="flex justify-center">
          <div class="p-4 bg-gray-50 rounded-2xl flex items-center justify-center w-72 h-72">

            <img 
              v-if="qrImage && !isLoading" 
              :src="qrImage" 
              class="w-full h-full object-contain"
            />

            <p v-else class="text-sm text-gray-400">
              Memuat QR...
            </p>

          </div>
        </div>

        <!-- COUNTDOWN -->
        <div class="space-y-2">
          <!-- <p class="text-sm text-gray-500">
            Berlaku dalam
          </p>
          <p class="text-3xl font-bold text-emerald-600">
            {{ countdown }} detik
          </p> -->

          <!-- PROGRESS BAR -->
          <!-- <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
            <div
              class="bg-emerald-500 h-2 rounded-full transition-all"
              :style="{ width: (countdown / maxTime * 100) + '%' }"
            ></div>
          </div> -->
        </div>

      </div>

      <!-- ACTION BUTTONS -->
        <div class="flex justify-center gap-3">

          <button
            @click="reloadQR"
            class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition"
          >
            Reload QR
          </button>

          <button
            @click="goBack"
            class="px-4 py-2 rounded-xl border text-sm font-semibold hover:bg-gray-50 transition"
          >
            ← Kembali
          </button>

        </div>

      <!-- INFO -->
      <div class="text-sm text-gray-500 space-y-1">
        <p>Scan QR ini untuk melakukan presensi</p>
        <p>QR akan otomatis diperbarui setiap beberapa detik</p>
      </div>

    </div>

  </AuthenticatedLayout>
</template>