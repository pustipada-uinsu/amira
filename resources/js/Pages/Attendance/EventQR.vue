<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import QRCode from 'qrcode'

const props = defineProps({
  event: Object
})

const qrImage = ref(null)
const countdown = ref(0)
const maxTime = ref(0)
const isLoading = ref(false)

let interval = null

const goBack = () => {
  router.visit(`/events/${props.event.hashid}/attendance`)
}

const generateQR = async () => {
  try {
    isLoading.value = true

    const res = await axios.get(`/events/${props.event.hashid}/generate-checkin-qr`)

    const payload = JSON.stringify({
      type: 'event_checkin', 
      event_id: res.data.event_id,
      hashid: res.data.hashid,
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

const reloadQR = async () => {
  clearInterval(interval)
  await generateQR()
  startTimer()
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
  <Head title="QR Check-in Event" />

  <AuthenticatedLayout>

    <template #header>
      <div>
        <h2 class="text-2xl font-bold text-gray-800">
          QR Check-in Event
        </h2>
        <p class="text-sm text-gray-500">
          {{ event.title }}
        </p>
      </div>
    </template>

    <div class="max-w-3xl mx-auto px-4 py-8 space-y-6 text-center">

      <!-- INFO -->
      <div class="bg-indigo-50 border border-indigo-100 text-indigo-700 p-4 rounded-xl">
        Scan QR ini untuk melakukan check-in event
      </div>

      <!-- QR -->
      <div class="bg-white p-6 md:p-8 rounded-3xl shadow-lg space-y-6">

        <div class="flex justify-center">
          <div class="p-4 bg-gray-50 rounded-2xl w-72 h-72 flex items-center justify-center">

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

      </div>

      <!-- ACTION -->
      <div class="flex justify-center gap-3">

        <button
          @click="reloadQR"
          class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700"
        >
          Reload QR
        </button>

        <button
          @click="goBack"
          class="px-4 py-2 rounded-xl border text-sm font-semibold hover:bg-gray-50"
        >
          ← Kembali
        </button>

      </div>

      <!-- INFO -->
      <div class="text-sm text-gray-500">
        QR akan otomatis diperbarui secara berkala
      </div>

    </div>

  </AuthenticatedLayout>
</template>