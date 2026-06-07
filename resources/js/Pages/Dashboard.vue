<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage, router } from '@inertiajs/vue3'

import SuperAdminSection from '@/Components/Dashboard/SuperAdminSection.vue'
import AdminSection from '@/Components/Dashboard/AdminSection.vue'
import UserSection from '@/Components/Dashboard/UserSection.vue'

const page = usePage()

const user = page.props.auth?.user ?? {}

const props = defineProps({
  role: String,
  events: Array,
  my_events: Array,
  stats: Object,
  greeting: Object,
})

const role = props.role ?? 'user'
const events = props.events ?? []
const myEvents = props.my_events ?? []
const stats = props.stats ?? {}

const goScan = () => {
  router.visit('/scan')
}

import { QrCode, ScanQrCode } from 'lucide-vue-next'
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>

    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">
          Dashboard
        </h2>
      </div>
    </template>

    <div class="relative min-h-screen overflow-hidden">

      <div
        class="absolute inset-0 bg-repeat bg-[length:450px_450px] opacity-50"
        style="background-image: url('/images/bg-6.png')"
      ></div>

      <div class="absolute inset-0 bg-white/70"></div>

      <!-- optional depth -->
      <div class="absolute inset-0 backdrop-blur-[2px]"></div>


      <!-- CONTENT -->
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-8 space-y-6">

        <!-- HERO -->
        <section class="relative overflow-hidden rounded-2xl shadow-lg">

          <div class="bg-gradient-animate p-6 md:p-10 text-white relative overflow-hidden">
  
            <!-- overlay biar lebih soft -->
            <div class="absolute inset-0 bg-black/10"></div>

            <!-- ilustrasi -->
            <img
              src="/images/logo-amira.png"
              class="absolute right-0 bottom-0 w-40 md:w-64 opacity-20"
            />

            <!-- content -->
            <div class="relative z-10">
              <h3 class="text-xl md:text-3xl font-bold">
                Selamat datang, {{ user.name }} 👋
              </h3>

              <p class="mt-2 text-white/80 text-sm md:text-base">
                {{ greeting?.text ?? 'AMIRA (Aplikasi Manajemen Informasi & Registrasi Acara)' }}
              </p>

              <p v-if="greeting?.author" class="text-xs mt-2 opacity-70">
                — {{ greeting.author }}
              </p>
            </div>

            <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>

            <span class="inline-block mt-3 px-3 py-1 bg-white/20 backdrop-blur rounded-full text-xs">
              Dashboard Overview
            </span>

          </div>
        </section>

          <!-- ===================== -->
          <!-- PRESENSI QR -->
          <!-- ===================== -->
          <section
            v-if="role === 'user'"
            class="bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 text-white rounded-2xl shadow-lg p-6 flex flex-col md:flex-row items-center justify-between gap-4"
          >
            <!-- LEFT -->
            <div>
              <h3 class="text-xl md:text-2xl font-bold">
               Presensi Kehadiran
              </h3>
              <p class="text-sm opacity-90 mt-1">
                Scan QR untuk melakukan check-in ke event yang sedang berlangsung
              </p>
            </div>
            <!-- RIGHT -->
            <div class="flex gap-3">
              <button
                @click="goScan"
                class="bg-white text-emerald-600 font-semibold px-5 py-3 rounded-xl shadow hover:bg-gray-100 transition flex items-center gap-2"
              >
                <ScanQrCode class="w-5 h-5" />
                <span>Scan Sekarang</span>
              </button>
            </div>
          </section>

        <!-- STATS -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-4">

          <div class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
            <p class="text-sm text-gray-500">Event Diikuti</p>
            <h2 class="text-3xl font-bold text-emerald-600">
              {{ stats.joined_events ?? 0 }}
            </h2>
          </div>

          <div class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
            <p class="text-sm text-gray-500">Event Tersedia</p>
            <h2 class="text-3xl font-bold text-blue-600">
              {{ events.length ?? 0 }}
            </h2>
          </div>

          <div class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition">
            <p class="text-sm text-gray-500">Status Akun</p>
            <h2 class="text-3xl font-bold text-gray-700">
              Aktif
            </h2>
          </div>

        </section>

        <!-- CONTENT -->
        <section class="bg-white rounded-2xl shadow-sm border p-6 space-y-6">

          <!-- ROLE SECTION -->
          <SuperAdminSection
            v-if="role === 'superadmin'"
            :events="events"
            :stats="stats"
          />

          <AdminSection
            v-else-if="role === 'admin'"
            :events="events"
            :stats="stats"
          />

          <UserSection
            v-else
            :events="events"
            :my_events="myEvents"
            :stats="stats"
          />

        </section>

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

<style>
@keyframes gradientMove {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.bg-gradient-animate {
  background: linear-gradient(
    -45deg,
    #059669,
    #10b981,
    #34d399,
    #14b8a6,
    #0ea5e9,
    #6366f1
  );
  background-size: 300% 300%;
  animation: gradientMove 12s ease infinite;
}
</style>