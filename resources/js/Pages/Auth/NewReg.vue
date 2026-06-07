<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, onUnmounted } from 'vue'
import axios from 'axios'

import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import InputError from '@/Components/InputError.vue'

/* ================= STATE ================= */
const step = ref(1)
const loading = ref(false)

const nim = ref('')
const email = ref('')
const otp = ref('')

const userId = ref(null)
const existingEmail = ref(null)
const errors = ref({})
const generalError = ref(null)

/* OTP RESEND */
const resendCooldown = ref(0)
let timer = null

const startCooldown = (seconds = 60) => {
  resendCooldown.value = seconds
  timer = setInterval(() => {
    resendCooldown.value--
    if (resendCooldown.value <= 0) {
      clearInterval(timer)
    }
  }, 1000)
}

onUnmounted(() => {
  if (timer) clearInterval(timer)
})

const goToLogin = () => {
  router.visit(route('login'))
}

/* ================= STEP 1 : CEK NIM ================= */
const checkNim = async () => {
  loading.value = true
  errors.value = {}
  generalError.value = null
  existingEmail.value = null

  try {
    const res = await axios.post(route('register.check-user'), {
      nim: nim.value,
    })

    if (res.data.status === 'has_email') {
      existingEmail.value = res.data.email
    }

    if (res.data.status === 'no_email') {
      userId.value = res.data.user_id
      step.value = 2
    }
  } catch (e) {
    generalError.value =
      e.response?.status === 404
        ? 'NIM / NIP tidak ditemukan.'
        : 'Terjadi kesalahan, silakan coba lagi.'
  } finally {
    loading.value = false
  }
}

/* ================= STEP 2 : SEND / RESEND OTP ================= */
const sendOtp = async () => {
  loading.value = true
  errors.value = {}
  generalError.value = null

  try {
    await axios.post(route('register.send-otp'), {
      user_id: userId.value,
      email: email.value,
    })

    step.value = 3
    startCooldown(60)
  } catch (e) {
    generalError.value = e.response?.data?.message || 'Gagal mengirim OTP.'
  } finally {
    loading.value = false
  }
}

/* ================= STEP 3 : VERIFY OTP ================= */
const verifyOtp = async () => {
  loading.value = true
  errors.value = {}
  generalError.value = null

  try {
    const res = await axios.post(route('register.verify-otp'), {
      user_id: userId.value,
      otp: otp.value,
    })

    router.visit(res.data.redirect)
  } catch (e) {
    generalError.value = e.response?.data?.message || 'OTP tidak valid.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <GuestLayout>
    <Head title="Registrasi Akun" />

    <div class="mx-auto w-full max-w-xl rounded-2xl bg-white/90 backdrop-blur p-4 sm:p-8 shadow-lg">

      <!-- HEADER -->
      <div class="text-center mb-6">
        <div
          v-if="generalError"
          class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
        >
          {{ generalError }}
        </div>

        <h1 class="text-xl sm:text-2xl font-bold text-emerald-700">
          Registrasi Akun
        </h1>
        <p class="text-xs sm:text-sm text-muted-foreground mt-1">
          Gunakan NIM/NIP & email resmi UINSU
        </p>
      </div>

      <!-- STEPPER -->
      <div class="flex items-center justify-between mb-8">
        <template v-for="n in 3" :key="n">
          <div class="flex flex-col items-center flex-1">
            <div
              :class="[
                'h-8 w-8 rounded-full flex items-center justify-center text-sm font-semibold',
                step >= n ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'
              ]"
            >
              {{ n }}
            </div>
          </div>
          <div v-if="n < 3" class="h-px flex-1 bg-gray-200 mx-2"></div>
        </template>
      </div>

      <!-- CONTENT -->
      <div class="rounded-xl border border-emerald-100 bg-white p-6">

        <!-- STEP 1 -->
        <form v-if="step === 1" @submit.prevent="checkNim" class="space-y-4">
          <div>
            <Label>NIM / NIP</Label>
            <Input v-model="nim" placeholder="Masukkan NIM/NIP" />
          </div>

          <div class="flex flex-col sm:flex-row gap-3">
            <Button
              type="button"
              variant="outline"
              class="w-full sm:w-1/3"
              @click="goToLogin"
            >
              ← Kembali
            </Button>

            <Button class="w-full" :disabled="loading">
              {{ loading ? 'Mengecek...' : 'Cek User' }}
            </Button>

            <!-- <Button class="w-full" :disabled="loading || existingEmail">
                {{ loading ? 'Mengecek...' : 'Cek User' }}
            </Button> -->
          </div>



          <div
            v-if="existingEmail"
            class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm">
            <p class="font-medium text-emerald-800">Akun sudah terdaftar</p>
            <p>Email: <strong>{{ existingEmail }}</strong></p>
            <Button class="w-full mt-3" @click="goToLogin">
              Masuk ke Dashboard
            </Button>
          </div>
        </form>

        <!-- STEP 2 -->
        <form v-if="step === 2" @submit.prevent="sendOtp" class="space-y-4">
          <div>
            <Label>Email UINSU</Label>
            <Input v-model="email" type="email" placeholder="email@uinsu.ac.id" />
          </div>

          <Button class="w-full" :disabled="loading">
            {{ loading ? 'Mengirim OTP...' : 'Kirim OTP' }}
          </Button>
        </form>

        <!-- STEP 3 (LOCKED) -->
        <form v-if="step === 3" @submit.prevent="verifyOtp" class="space-y-4">
          <div class="text-sm text-emerald-700 bg-emerald-50 border p-3 rounded-lg">
            OTP dikirim ke <strong>{{ email }}</strong>
          </div>

          <div>
            <Label>Kode OTP</Label>
            <Input v-model="otp" placeholder="6 digit OTP" />
          </div>

          <Button class="w-full" :disabled="loading">
            {{ loading ? 'Memverifikasi...' : 'Verifikasi & Masuk' }}
          </Button>

          <!-- RESEND -->
          <button
            type="button"
            class="w-full text-sm text-muted-foreground hover:text-emerald-600"
            @click="sendOtp"
            :disabled="resendCooldown > 0 || loading"
          >
            Kirim ulang OTP
            <span v-if="resendCooldown > 0">
              ({{ resendCooldown }}s)
            </span>
          </button>
        </form>

        <button id="google-login-btn"></button>

      </div>
    </div>
  </GuestLayout>
</template>
