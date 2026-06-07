<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
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

const goToLogin = () => {
  router.visit(route('login'))
}


/* ================= HELPERS ================= */
const prevStep = () => {
  if (step.value > 1) step.value--
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
    if (e.response?.status === 404) {
      generalError.value = 'NIM / NIP tidak ditemukan.'
    } else if (e.response?.data?.errors) {
      errors.value = e.response.data.errors
    } else {
      generalError.value = 'Terjadi kesalahan, silakan coba lagi.'
    }
  } finally {
    loading.value = false
  }
}


/* ================= STEP 2 : SEND OTP ================= */
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
  } catch (e) {
    if (e.response?.data?.message) {
      generalError.value = e.response.data.message
    } else if (e.response?.data?.errors) {
      errors.value = e.response.data.errors
    } else {
      generalError.value = 'Gagal mengirim OTP.'
    }
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
    if (e.response?.data?.message) {
      generalError.value = e.response.data.message
    } else if (e.response?.data?.errors) {
      errors.value = e.response.data.errors
    } else {
      generalError.value = 'OTP tidak valid.'
    }
  } finally {
    loading.value = false
  }
}

</script>

<template>
  <GuestLayout>
    <Head title="Registrasi Akun" />

    <div class="mx-auto w-full max-w-xl rounded-2xl bg-white/90 backdrop-blur p-6 sm:p-8 shadow-lg">

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
        <div class="flex flex-col items-center flex-1">
          <div
            :class="[
              'h-8 w-8 sm:h-9 sm:w-9 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold',
              step >= 1 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'
            ]"
          >1</div>
          <span class="mt-2 text-[10px] sm:text-xs hidden sm:block">Cek NIM/NIP</span>
        </div>

        <div class="h-px flex-1 bg-gray-200 mx-2"></div>

        <div class="flex flex-col items-center flex-1">
          <div
            :class="[
              'h-8 w-8 sm:h-9 sm:w-9 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold',
              step >= 2 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'
            ]"
          >2</div>
          <span class="mt-2 text-[10px] sm:text-xs hidden sm:block">Email</span>
        </div>

        <div class="h-px flex-1 bg-gray-200 mx-2"></div>

        <div class="flex flex-col items-center flex-1">
          <div
            :class="[
              'h-8 w-8 sm:h-9 sm:w-9 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold',
              step >= 3 ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500'
            ]"
          >3</div>
          <span class="mt-2 text-[10px] sm:text-xs hidden sm:block">OTP</span>
        </div>
      </div>

      <!-- STEP CONTENT -->
      <div class="rounded-xl border border-emerald-100 bg-white p-5 sm:p-6">

        <!-- STEP 1 -->
        <form v-if="step === 1" @submit.prevent="checkNim" class="space-y-4">
          <div>
            <Label>NIM / NIP</Label>
            <Input v-model="nim" placeholder="Masukkan NIM/NIP" :disabled="existingEmail" />
            <InputError :message="errors.nim" />
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

            <Button class="w-full" :disabled="loading || existingEmail">
                {{ loading ? 'Mengecek...' : 'Cek User' }}
            </Button>
          </div>

          <div
                v-if="existingEmail"
                class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm space-y-3"
                >
                <div>
                    <p class="font-medium text-emerald-800">
                    Akun Anda sudah terdaftar
                    </p>
                    <p class="text-emerald-700 mt-1">
                    Email: <strong>{{ existingEmail }}</strong>
                    </p>
                </div>

                <Button
                    type="button"
                    class="w-full bg-emerald-600 hover:bg-emerald-700"
                    @click="goToLogin"
                >
                    Masuk ke Dashboard
                </Button>
            </div>

          
        </form>

        <!-- STEP 2 -->
        <form v-if="step === 2" @submit.prevent="sendOtp" class="space-y-4">
          <div>

            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                <span class="text-emerald-600">
                    Silakan masukkan <strong>email resmi UINSU anda</strong>.</span>
            </div>

            <Label>Email UINSU</Label>
            <Input v-model="email" type="email" placeholder="Masukkan email..." />
            <InputError :message="errors.email" />
          </div>

          <div class="flex flex-col sm:flex-row gap-3">
            <Button
              type="button"
              variant="outline"
              class="w-full sm:w-1/3"
              @click="prevStep"
            >
              ← Kembali
            </Button>

            <Button class="w-full sm:w-2/3" :disabled="loading">
              {{ loading ? 'Mengirim OTP...' : 'Kirim OTP' }}
            </Button>
          </div>
        </form>

        <!-- STEP 3 -->
        <form v-if="step === 3" @submit.prevent="verifyOtp" class="space-y-4">
          <div>
            <Label>Kode OTP</Label>
            <Input v-model="otp" placeholder="Masukkan OTP" />
            <InputError :message="errors.otp" />
          </div>

          <div class="flex flex-col sm:flex-row gap-3">
            <Button
              type="button"
              variant="outline"
              class="w-full sm:w-1/3"
              @click="prevStep"
            >
              ← Kembali
            </Button>

            <Button class="w-full sm:w-2/3" :disabled="loading">
              {{ loading ? 'Memverifikasi...' : 'Verifikasi & Masuk' }}
            </Button>
          </div>
        </form>

      </div>
    </div>
  </GuestLayout>
</template>
