<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { QrcodeStream } from 'vue-qrcode-reader'
import { ref } from 'vue'
import { toast } from 'vue3-toastify'
import axios from 'axios'
import { router } from '@inertiajs/vue3'

const loading = ref(false)
const scanned = ref(false)

const goBack = () => {
  router.visit('/dashboard')
}

const onDetect = async (results) => {
  if (loading.value || scanned.value) return

  try {
    if (!results?.length) return

    loading.value = true
    scanned.value = true

    const raw = results[0].rawValue
    const data = JSON.parse(raw)

    let endpoint = ''

    if (!data.type) {
      throw new Error('QR tidak memiliki tipe')
    }

    if (data.type === 'daily') {
      endpoint = '/scan'
    } else if (data.type === 'event_checkin') {
      endpoint = '/attendance/event-scan'
    } else {
      throw new Error('QR tidak dikenali')
    }

    await axios.post(endpoint, data)

    toast.success('Check-in berhasil')

    setTimeout(() => {
      router.visit(`/detail-event/${data.hashid}`)
    }, 1200)

  } catch (err) {
    scanned.value = false
    console.error(err)

    toast.error(
      err.response?.data?.message || err.message || 'Gagal scan QR'
    )
  } finally {
    loading.value = false
  }
}

const paintBoundingBox = (detectedCodes, ctx) => {
  for (const code of detectedCodes) {
    const { boundingBox } = code

    ctx.strokeStyle = '#10b981'
    ctx.lineWidth = 2
    ctx.strokeRect(
      boundingBox.x,
      boundingBox.y,
      boundingBox.width,
      boundingBox.height
    )
  }
}
</script>

<template>
  <AuthenticatedLayout>

    <div class="max-w-xl mx-auto py-8 px-4 space-y-6">

      <!-- HEADER -->
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-800">
          Scan QR Presensi
        </h2>

        <button
          @click="goBack"
          class="text-sm px-3 py-1.5 rounded-lg border hover:bg-gray-50"
        >
          ← Kembali
        </button>
      </div>

      <!-- INFO CARD -->
      <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 p-4 rounded-xl text-sm text-center">
        Arahkan kamera ke QR Code untuk melakukan presensi
      </div>

      <!-- CAMERA -->
      <div class="rounded-2xl overflow-hidden shadow-lg border bg-black relative">

        <QrcodeStream
          @detect="onDetect"
          :track="paintBoundingBox"
          facing-mode="environment"
        />

        <!-- OVERLAY LOADING -->
        <div
          v-if="loading"
          class="absolute inset-0 bg-black/60 flex items-center justify-center text-white text-sm font-medium"
        >
          Memproses QR...
        </div>

      </div>

      <!-- STATUS -->
      <div class="text-center text-xs text-gray-400">
        QR akan dipindai otomatis saat terdeteksi
      </div>

    </div>

  </AuthenticatedLayout>
</template>