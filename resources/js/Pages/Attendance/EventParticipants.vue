<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { toast } from 'vue3-toastify'
import { watch } from 'vue'

const page = usePage()

watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.success) toast.success(flash.success)
    if (flash?.error) toast.error(flash.error)
  },
  { immediate: true }
)

const props = defineProps({
  event: Object,
  participants: Array
})

const formatTime = (val) => {
  if (!val) return '-'
  return new Date(val).toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const goBack = () => {
  router.visit(`/events/${props.event.hashid}/attendance`)
}

const manualCheckin = (userId, name) => {
  Swal.fire({
    title: 'Check-in peserta?',
    text: `Peserta: ${name}`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, check-in',
    confirmButtonColor: '#6366f1'
  }).then((result) => {
    if (!result.isConfirmed) return

    router.post(`/events/${props.event.hashid}/checkin-manual`, {
      user_id: userId
    }, {
      preserveScroll: true
    })
  })
}

const exportData = () => {
  window.open(`/events/${props.event.hashid}/checkin-export`, '_blank')
}
</script>

<template>
  <Head title="Check-in Event" />

  <AuthenticatedLayout>

    <div class="max-w-4xl mx-auto px-4 py-6 space-y-6">

      <!-- HEADER -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
          <h2 class="text-2xl font-bold text-gray-800">
            Check-in Event
          </h2>
          <p class="text-sm text-gray-500">
            {{ event.title }}
          </p>
        </div>

        <div class="flex gap-2">
          <button 
            @click="exportData"
            class="text-sm px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition"
          >
            Export
          </button>

          <button 
            @click="goBack" 
            class="text-sm px-3 py-2 rounded-lg border hover:bg-gray-50"
          >
            ← Kembali
          </button>
        </div>
      </div>

      <!-- EMPTY -->
      <div 
        v-if="participants.length === 0"
        class="bg-white border rounded-2xl p-6 text-center text-gray-400"
      >
        Belum ada peserta
      </div>

      <!-- LIST -->
      <div class="bg-white border rounded-2xl divide-y">

        <div
          v-for="p in participants"
          :key="p.id"
          class="flex items-center justify-between px-4 py-3 hover:bg-gray-50"
        >
          
          <!-- LEFT -->
          <div class="flex items-center gap-3 min-w-0">
            
            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-semibold">
              {{ p.user.name.charAt(0).toUpperCase() }}
            </div>

            <div class="truncate">
              <p class="text-sm font-medium text-gray-800 truncate">
                {{ p.user.name }}
              </p>

              <p class="text-xs text-gray-500">
                <span v-if="p.is_checked_in">
                  {{ formatTime(p.checked_in_at) }}
                </span>
                <span v-else class="text-red-500">
                  Belum check-in
                </span>
              </p>
            </div>
          </div>

          <!-- RIGHT -->
          <div class="flex items-center gap-2">

            <span
              v-if="p.is_checked_in"
              class="text-[11px] font-medium bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full"
            >
              Hadir
            </span>

            <button
              v-else
              @click="manualCheckin(p.user.id, p.user.name)"
              class="text-[11px] px-2 py-1 rounded-md bg-indigo-500 text-white hover:bg-indigo-600"
            >
              Check-in
            </button>

          </div>

        </div>

      </div>

    </div>

  </AuthenticatedLayout>
</template>