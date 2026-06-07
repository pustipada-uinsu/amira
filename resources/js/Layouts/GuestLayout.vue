<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import { Link } from '@inertiajs/vue3'
import LoadingOverlay from '@/Components/LoadingOverlay.vue'

defineProps({
  wide: {
    type: Boolean,
    default: false,
  },
})
</script>

<template>
  <LoadingOverlay />
  <div
    class="relative flex min-h-screen flex-col items-center justify-center
           overflow-hidden
           bg-gradient-to-br
           from-emerald-50 via-emerald-100 to-green-200
           pt-6 sm:pt-0"
  >
    <!-- PATTERN HALUS -->
    <div
      class="pointer-events-none absolute inset-0
             bg-[url('/images/bg-4.jpg')]
             bg-repeat
             bg-[length:500px_500px]
             opacity-5"
    ></div>

    <!-- GLOW DEKORATIF -->
    <div
      class="pointer-events-none absolute -top-32 -left-32
             h-96 w-96 rounded-full
             bg-emerald-300/0 blur-3xl"
    ></div>

    <div
      class="pointer-events-none absolute bottom-0 right-0
             h-[28rem] w-[28rem] rounded-full
             bg-emerald-200/40 blur-3xl"
    ></div>

    <!-- LOGO -->
    <div class="relative z-10">
      <Link href="/">
        <ApplicationLogo class="h-20 w-20 text-emerald-600" />
      </Link>
    </div>

    <!-- CONTENT -->
    <div
      :class="[
        'relative z-10 mt-6 w-full px-1',
        wide ? 'sm:max-w-7xl' : 'sm:max-w-2xl'
      ]"
    >
      <div
        class="rounded-2xl
               px-6 py-6 sm:px-8 sm:py-8"
      >
        <slot />
      </div>
    </div>
  </div>
</template>
