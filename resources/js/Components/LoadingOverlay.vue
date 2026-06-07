<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const loading = ref(false)

const start = () => loading.value = true
const finish = () => loading.value = false

onMounted(() => {
  document.addEventListener('loading:start', start)
  document.addEventListener('loading:finish', finish)
})

onUnmounted(() => {
  document.removeEventListener('loading:start', start)
  document.removeEventListener('loading:finish', finish)
})
</script>

<template>
  <div
    v-if="loading"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-white/70 backdrop-blur-sm"
  >
    <div class="relative flex items-center justify-center">

        <div class="absolute w-20 h-20 rounded-full bg-emerald-400/20 animate-ping"></div>
        <img
            src="/images/logo-amira.png"
            class="w-16 h-16 object-contain animate-logo relative z-10"
        />

    </div>
  </div>
</template>

<style>
@keyframes spinSlow {
  0% { transform: rotate(0deg) scale(1); opacity: 0.8; }
  50% { transform: rotate(180deg) scale(1.1); opacity: 1; }
  100% { transform: rotate(360deg) scale(1); opacity: 0.8; }
}

.animate-spin-slow {
  animation: spinSlow 1.2s linear infinite;
}

@keyframes logoFloat {
  0%, 100% {
    transform: translateY(0px) scale(1);
  }
  50% {
    transform: translateY(-8px) scale(1.05);
  }
}

@keyframes logoGlow {
  0%, 100% {
    filter: drop-shadow(0 0 0px rgba(16,185,129,0.0));
  }
  50% {
    filter: drop-shadow(0 0 16px rgba(16,185,129,0.5));
  }
}

@keyframes logoShine {
  0% {
    filter: brightness(1);
  }
  50% {
    filter: brightness(1.3);
  }
  100% {
    filter: brightness(1);
  }
}

.animate-logo {
  animation:
    logoFloat 3s ease-in-out infinite,
    logoGlow 2.5s ease-in-out infinite,
    logoShine 2s ease-in-out infinite;
}
</style>