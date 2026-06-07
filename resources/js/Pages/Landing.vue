<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Card, CardContent } from '@/Components/ui/card'

import {
  CalendarCheck2,
  ListChecks,
  BarChart3
} from 'lucide-vue-next'

const props = defineProps({
  events: Array
})

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const getLocation = (event) => {
  if (event.venue) return event.venue.name
  if (event.custom_location) return event.custom_location
  return 'Lokasi belum ditentukan'
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-b from-white via-emerald-50/40 to-white">

    <Head title="AMIRA - Aplikasi Manajemen Informasi & Registrasi Acara" />

    <!-- NAVBAR -->
    <header class="sticky top-0 z-50 backdrop-blur-xl bg-white/70 border-b">
      <div class="max-w-6xl mx-auto flex justify-between items-center px-6 py-4">

        <div class="flex items-center gap-2 cursor-pointer">
          <img 
            src="/images/amira-2.png" 
            alt="AMIRA Logo"
            class="h-10 w-auto object-contain"
          />
        </div>

        <div class="flex gap-3">
          <!-- <Link href="/login">
            <Button variant="outline">Login</Button>
          </Link> -->
          <Link href="/login">
            <Button>Get Started</Button>
          </Link>
        </div>

      </div>
    </header>

    <!-- HERO SECTION -->
    <section class="relative overflow-hidden">

      <!-- 🌈 ANIMATED GRADIENT BACKGROUND -->
      <div class="absolute inset-0 bg-gradient-animate"></div>

      <!-- soft overlay noise/glow -->
      <div class="absolute inset-0 bg-black/5"></div>

      <!-- FLOATING BLUR ORBS -->
      <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-400/30 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute top-20 -right-32 w-96 h-96 bg-blue-400/30 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute bottom-[-100px] left-1/2 w-[500px] h-[500px] bg-purple-400/20 rounded-full blur-3xl animate-pulse"></div>

      <div class="relative max-w-6xl mx-auto px-6 py-24 grid md:grid-cols-2 gap-12 items-center">

        <!-- LEFT CONTENT -->
        <div class="text-white">

          <p class="text-white/80 text-sm tracking-wide">
            AMIRA Platform
          </p>

          <h1 class="text-4xl md:text-5xl font-bold leading-tight mt-3">
            Aplikasi Manajemen <br />
            Informasi & Registrasi Acara
          </h1>

          <p class="mt-5 text-white/80 text-lg">
             Kelola event, panitia, dan registrasi dalam satu sistem terintegrasi , ayo gunakan sekarang.
          </p>

          <div class="mt-8 flex gap-3">
            <a href="/events">
              <button class="px-6 py-3 rounded-2xl bg-white text-gray-900 font-semibold hover:scale-105 transition">
                Jelajahi Event
              </button>
            </a>

            <a href="/events/create">
              <button class="px-6 py-3 rounded-2xl border border-white/40 text-white hover:bg-white/10 transition">
                Buat Event
              </button>
            </a>
          </div>

        </div>

        <!-- RIGHT MOCKUP -->
        <div class="relative">

          <!-- glow behind card -->
          <div class="absolute inset-0 bg-white/20 blur-2xl rounded-3xl"></div>

          <!-- dashboard card -->
          <div class="relative rounded-3xl overflow-hidden border border-white/20 bg-white/10 backdrop-blur-xl shadow-2xl">

            <!-- fake top bar -->
            <div class="flex items-center gap-2 px-4 py-3 bg-white/10 border-b border-white/10">
              <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
              <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
              <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
            </div>

            <!-- mock content -->
            <img
              src="/images/hero-2.png"
              class="w-full object-cover"
            />

          </div>

        </div>

      </div>
    </section>

    <!-- FEATURES -->
    <section class="max-w-6xl mx-auto px-6 py-10">
      <h3 class="text-2xl font-bold text-center mb-10">
        Kenapa AMIRA?
      </h3>

      <div class="grid md:grid-cols-3 gap-6">

        <!-- 1. MANAGE EVENT -->
        <Card class="group rounded-3xl border bg-white/70 backdrop-blur hover:shadow-xl transition">
          <CardContent class="p-6 space-y-4">

            <!-- ICON -->
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center group-hover:scale-110 transition">
              <CalendarCheck2 class="w-6 h-6 text-emerald-600" />
            </div>

            <h4 class="font-semibold text-lg text-gray-900">
              Manajemen Event
            </h4>

            <p class="text-sm text-gray-600">
              Buat, edit, dan kelola event dengan workflow modern.
            </p>

          </CardContent>
        </Card>

        <!-- 2. RUNDOWN -->
        <Card class="group rounded-3xl border bg-white/70 backdrop-blur hover:shadow-xl transition">
          <CardContent class="p-6 space-y-4">

            <!-- ICON -->
            <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center group-hover:scale-110 transition">
              <ListChecks class="w-6 h-6 text-blue-600" />
            </div>

            <h4 class="font-semibold text-lg text-gray-900">
              Rundown Event
            </h4>

            <p class="text-sm text-gray-600">
              Susun jadwal acara terstruktur dari awal hingga selesai secara real-time.
            </p>

          </CardContent>
        </Card>

        <!-- 3. ANALYTICS -->
        <Card class="group rounded-3xl border bg-white/70 backdrop-blur hover:shadow-xl transition">
          <CardContent class="p-6 space-y-4">

            <!-- ICON -->
            <div class="w-12 h-12 rounded-2xl bg-purple-100 flex items-center justify-center group-hover:scale-110 transition">
              <BarChart3 class="w-6 h-6 text-purple-600" />
            </div>

            <h4 class="font-semibold text-lg text-gray-900">
              Analytics
            </h4>

            <p class="text-sm text-gray-600">
              Pantau jumlah peserta dan performa event secara real-time.
            </p>

          </CardContent>
        </Card>

      </div>
    </section>

    <!-- EVENT GRID (STRIPE STYLE) -->
    <section class="max-w-6xl mx-auto px-6 py-16">

      <div class="flex items-end justify-between mb-8">
        <h3 class="text-2xl font-bold text-gray-900">
          Event Terbaru
        </h3>

        <Link href="/events" class="text-emerald-600 text-sm hover:underline">
          Lihat semua →
        </Link>
      </div>

      <div v-if="!events || events.length === 0"
        class="text-center text-gray-500 py-16 border rounded-2xl bg-white/60">
        Belum ada event tersedia
      </div>

      <div v-else class="grid md:grid-cols-3 gap-6">

        <Card
          v-for="event in events"
          :key="event.id"
          class="group overflow-hidden rounded-3xl border bg-white shadow-sm hover:shadow-xl transition"
        >

          <!-- IMAGE -->
          <div class="h-44 relative overflow-hidden">

            <img
              v-if="event.banner"
              :src="`/storage/${event.banner}`"
              class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500"
            />

            <div v-else
              class="absolute inset-0 bg-gradient-to-r from-emerald-500 via-blue-500 to-purple-500"
            ></div>

            <div class="absolute inset-0 bg-black/30"></div>

            <div class="absolute bottom-3 left-4 text-white">
              <p class="text-xs opacity-80">AMIRA Event</p>
              <h3 class="text-lg font-semibold">
                {{ event.title }}
              </h3>
            </div>

          </div>

          <!-- BODY -->
          <CardContent class="p-5 space-y-2">

            <p class="text-sm text-gray-500">
              📅 {{ formatDate(event.start_date) }}
            </p>

            <p class="text-sm text-gray-500">
              📍 {{ getLocation(event) }}
            </p>

            <Link :href="`/events/${event.id}`">
              <Button class="mt-4 w-full rounded-2xl">
                Detail Event
              </Button>
            </Link>

          </CardContent>

        </Card>

      </div>

    </section>

    <!-- CTA -->
    <section class="bg-emerald-600 text-white py-16 mt-10">

      <div class="max-w-4xl mx-auto text-center px-6">
        <h3 class="text-3xl font-bold">
          Mulai Kelola Event dengan AMIRA
        </h3>

        <p class="mt-3 text-white/80">
          Satu platform untuk semua kebutuhan event Anda.
        </p>

        <Link href="/events/create">
          <Button class="mt-6 bg-white text-emerald-700 hover:bg-gray-100 rounded-2xl px-6">
            Buat Event Sekarang
          </Button>
        </Link>
      </div>

    </section>

    <!-- FOOTER -->
    <footer class="text-center text-xs text-gray-500 py-6">
      © AMIRA (Aplikasi Manajemen Informasi & Registrasi Acara) - 2026
    </footer>

  </div>
</template>

<style>
@keyframes gradientMove {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

.bg-gradient-animate {
  background: linear-gradient(
    -45deg,

    #059669,
    #059669,
    #10b981,
    #14b8a6,

    #0ea5e9,
    #6366f1,
    #a855f7,
    #d946ef 

  );

  background-size: 400% 400%;
  animation: gradientMove 16s ease infinite;
}
</style>
