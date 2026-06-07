<script setup>
import { Button } from '@/Components/ui/button'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import LoadingOverlay from '@/Components/LoadingOverlay.vue'

const page = usePage()

const redirectGoogle = () => {
  document.dispatchEvent(new Event('loading:start'))
  setTimeout(() => {
    window.location.href = route('auth.google')
  }, 100)
}
</script>

<template>
  <LoadingOverlay />
  <!-- <GuestLayout wide> -->
    <Head title="Login - AMIRA" />

    <div class="min-h-screen grid md:grid-cols-2">

      <!-- LEFT (LANDING STYLE) -->
      <div class="relative flex items-center justify-center overflow-hidden">

        <!-- gradient AMIRA -->
        <div class="absolute inset-0 bg-gradient-animate"></div>
        <div class="absolute inset-0 bg-black/10"></div>

        <!-- floating orbs -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-400/30 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-20 -right-32 w-96 h-96 bg-blue-400/30 rounded-full blur-3xl animate-pulse"></div>

        <!-- content -->
        <div class="relative text-white text-center px-8">

          <p class="text-white/80 text-sm tracking-wide">
            AMIRA Platform
          </p>

          <h1 class="text-4xl md:text-5xl font-bold leading-tight mt-3">
            Aplikasi Manajemen  <br />
            Informasi & Registrasi Acara
          </h1>

          <p class="mt-5 text-white/80 text-lg max-w-md mx-auto">
            Kelola event, panitia, dan registrasi dalam satu sistem terintegrasi , ayo gunakan sekarang.
          </p>

          <div class="mt-8">
            <img src="/images/hero-2.png" class="w-full max-w-md mx-auto rounded-2xl shadow-2xl" />
          </div>

        </div>

      </div>

      <!-- RIGHT (LOGIN SIMPLE) -->
      <div class="relative flex items-center justify-center bg-white px-10">

        <!-- subtle background glow -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(16,185,129,0.08),transparent_60%)]"></div>

        <div class="relative w-full max-w-md">

          <div class="flex flex-col items-center mb-6 text-center">
            <img
              src="/images/logo-amira.png"
              alt="AMIRA"
              class="object-contain mb-2"
              style="filter: drop-shadow(0 4px 10px rgba(0,0,0,0.1)); width: 180px; height: 180px;"
            />
          </div>

          <!-- small accent line -->
          <div class="w-14 h-1 bg-emerald-500 rounded-full mb-6"></div>

          <!-- ERROR -->
          <div
            v-if="page.props.errors?.email || page.props.flash?.error"
            class="mb-5 rounded-xl bg-red-50 p-3 text-sm text-red-700 border border-red-200"
          >
            {{ page.props.errors?.email || page.props.flash?.error }}
          </div>

          <!-- TITLE -->
          <h2 class="text-2xl font-bold text-gray-900">
            Selamat Datang 👋
          </h2>

          <p class="text-sm text-gray-500 mt-2">
            Masuk ke sistem AMIRA menggunakan akun Google anda
          </p>

          <!-- LOGIN CARD FEEL (light, not box) -->
          <div class="mt-8 p-5 rounded-2xl border border-gray-100 bg-white shadow-sm hover:shadow-md transition">

            <p class="text-xs text-gray-400 mb-3">
              AMIRA - Aplikasi Manajemen Informasi & Registrasi Acara - Universitas Islam Negeri Sumatera Utara
            </p>

            <!-- BUTTON (GREEN AMIRA) -->
            <Button
              type="button"
              class="w-full flex items-center justify-center gap-3 text-base py-5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white"
              @click="redirectGoogle"
            >
              <svg class="h-5 w-5" viewBox="0 0 533.5 544.3">
                <path fill="currentColor" d="M533.5 278.4c0-17.4-1.6-34.1-4.6-50.4H272.1v95.4h147c-6.3 34.3-25 63.4-53.4 83v68h86.4c50.6-46.6 81.4-115.3 81.4-196z"/>
                <path fill="currentColor" d="M272.1 544.3c72.4 0 133.1-23.9 177.5-64.9l-86.4-68c-24 16.1-54.7 25.6-91.1 25.6-69.9 0-129.1-47.2-150.4-110.7h-89.5v69.5c44.2 87.3 135.6 148.5 239.9 148.5z"/>
                <path fill="currentColor" d="M121.7 326.3c-10.9-32.8-10.9-68.2 0-101l-89.5-69.5c-39.1 77.9-39.1 162.6 0 240.5z"/>
                <path fill="currentColor" d="M272.1 107.7c38.6 0 73.2 13.3 100.5 39.5l75.4-75.4C405.2 24.6 344.5 0 272.1 0 167.8 0 76.3 61.2 32.1 148.5l89.6 69.5c21.3-63.5 80.5-110.7 150.4-110.7z"/>
              </svg>

              Masuk dengan Google
            </Button>

          </div>

          <!-- footer note -->
          <p class="text-xs text-gray-400 mt-6">
            AMIRA © 2026 • PUSTIPADA UIN Sumatera Utara
          </p>

        </div>
      </div>

    </div>
  <!-- </GuestLayout> -->
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

  background-size: 400% 400%;
  animation: gradientMove 16s ease infinite;
}
</style>