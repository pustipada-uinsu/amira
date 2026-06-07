<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()

const props = defineProps({
  event: Object
})

// format tanggal simple
const formatDate = (date) => {
  return new Date(date).toLocaleString('id-ID', {
    dateStyle: 'medium',
    timeStyle: 'short'
  })
}

const groupedSessions = computed(() => {
  if (!props.event.sessions) return {}

  return props.event.sessions.reduce((acc, session) => {
    const date = new Date(session.start_at).toLocaleDateString('id-ID', {
      weekday: 'long',
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    })

    if (!acc[date]) acc[date] = []
    acc[date].push(session)

    return acc
  }, {})
})
</script>

<template>
  <Head :title="event.title" />

  <AuthenticatedLayout>

    <div class="relative min-h-screen">

      <!-- HERO -->
      <div class="relative h-72 md:h-96">

        <!-- BANNER -->
        <img
          v-if="event.banner"
          :src="`/storage/${event.banner}`"
          class="w-full h-full object-cover"
        />

        <!-- FALLBACK -->
        <div
          v-else
          class="w-full h-full bg-gradient-to-r from-emerald-500 to-emerald-600"
        ></div>

        <!-- OVERLAY -->
        <div class="absolute inset-0 bg-black/40"></div>

        <!-- TEXT -->
        <div class="absolute bottom-0 p-6 text-white max-w-4xl">
          <h1 class="text-2xl md:text-4xl font-bold">
            {{ event.title }}
          </h1>

          <p class="mt-2 text-sm opacity-90">
            📅 {{ formatDate(event.start_date) }} - {{ formatDate(event.end_date) }}
          </p>
        </div>

      </div>

      <!-- CONTENT -->
      <div class="max-w-5xl mx-auto px-4 py-8 space-y-6">

        <!-- INFO CARD -->
        <div class="bg-white rounded-xl shadow p-6 grid md:grid-cols-3 gap-6">

          <div>
            <p class="text-sm text-gray-500">📍 Lokasi</p>
            <p class="font-medium">
              {{ event.venue?.name ?? event.custom_location }}
            </p>
          </div>

          <div>
            <p class="text-sm text-gray-500">📝 Registrasi</p>
            <p class="font-medium">
              {{ formatDate(event.registration_start) }} - 
              {{ formatDate(event.registration_end) }}
            </p>
          </div>

          <!-- <div class="flex items-center">
            <button class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-2 rounded-lg shadow">
              🎫 Daftar Sekarang
            </button>
          </div> -->

        </div>

        <!-- DESCRIPTION -->
        <div class="bg-white rounded-xl shadow p-6">
          <h3 class="font-semibold text-lg mb-4">
            📌 Deskripsi Event
          </h3>

          <div class="prose prose-emerald max-w-none"
              v-html="event.description">
          </div>
        </div>

        <!-- SESSION (OPTIONAL FUTURE) -->
        <div v-if="event.sessions?.length" class="bg-white rounded-xl shadow p-6">
          <h3 class="font-semibold text-lg mb-4">
            🗓️ Agenda Event
          </h3>
          <div class="space-y-6">
            <div
              v-for="(sessions, date) in groupedSessions"
              :key="date"
              class="border rounded-xl p-4 bg-gray-50"
            >
              <div class="font-semibold text-emerald-700 mb-3">
                📅 {{ date }}
              </div>
              <div class="space-y-3">
                <div
                  v-for="s in sessions"
                  :key="s.id"
                  class="bg-white p-4 rounded-lg border hover:shadow-sm transition"
                >
                  <div class="font-medium text-gray-800">
                    {{ s.title }}
                  </div>
                  <div class="text-xs text-gray-500 mt-1">
                    {{ formatDate(s.start_at) }} - {{ formatDate(s.end_at) }}
                  </div>
                  <div class="text-sm mt-2 text-gray-600"
                      v-html="s.description">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

  </AuthenticatedLayout>
</template>