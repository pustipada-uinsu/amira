<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

import { watch, nextTick, ref } from 'vue'

const page = usePage()
const user = page.props.auth?.user ?? {}

const props = defineProps({
  venues: Array
})

// ================= FORM =================
const form = useForm({
  title: '',
  description: '',
  public: true,
  start_date: '',
  end_date: '',
  registration_start: '',
  registration_end: '',
  venue_id: '',
  custom_location: '',
  banner: null,
  flyer: null,
})

const preview = ref(null)

const handleFile = (e) => {
  const file = e.target.files[0]
  form.banner = file

  if (file) {
    preview.value = URL.createObjectURL(file)
  }
}

// ================= HELPER =================
const inputClass = (field) => [
  'input w-full',
  form.errors[field] ? 'border-red-500 focus:ring-red-500' : ''
]

// ================= AUTO SCROLL =================
const scrollToFirstError = async (errors) => {
  await nextTick()

  const firstField = Object.keys(errors)[0]
  if (!firstField) return

  let el = document.querySelector(`[name="${firstField}"]`)

  // fallback untuk quill
  if (!el) {
    el = document.getElementById(firstField)
  }

  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'center' })
    el.focus?.()
  }
}

// ================= SUBMIT =================
const submit = () => {
  form.post(route('events.store'), {
    preserveScroll: true,
     forceFormData: true,

    onSuccess: () => {
      toast.success('🎉 Event berhasil dibuat!')
      form.reset()
    },

    onError: async (errors) => {
    //   toast.error('⚠️ Periksa kembali form kamu!')

      const firstError = Object.values(errors)[0]
      if (firstError) toast.error(firstError)

      await scrollToFirstError(errors)
    }
  })
}

// ================= FLASH =================
watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.success) toast.success(flash.success)
    if (flash?.error) toast.error(flash.error)
  },
  { deep: true }
)
const flyerPreview = ref(null)
const handleFlyer = (e) => {
  const file = e.target.files[0]
  form.flyer = file

  if (file) {
    flyerPreview.value = URL.createObjectURL(file)
  }
}
</script>

<template>
  <Head title="Create Event" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">
        Create Event
      </h2>
    </template>

    <div class="relative min-h-screen">

      <!-- BACKGROUND -->
      <div class="absolute inset-0 bg-repeat bg-[length:500px_500px]"
        style="background-image: url('/images/bg-4.jpg')"></div>
      <div class="absolute inset-0 bg-white/95"></div>

      <div class="relative z-10 py-8 max-w-5xl mx-auto px-4 sm:px-8">

        <!-- HERO -->
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl p-6 shadow-lg mb-6">
          <h3 class="text-2xl font-bold">🎉 Buat Event Baru</h3>
          <p class="text-sm opacity-90 mt-1">
            Isi informasi event secara lengkap agar peserta lebih tertarik
          </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">

          <!-- INFORMASI -->
          <div class="bg-white rounded-xl shadow p-6 space-y-5">
            <h4 class="font-semibold border-b pb-2">📌 Informasi Utama</h4>

            <!-- BANNER -->
              <div>
                <label class="text-sm font-medium">Banner Event</label>

                <div class="mt-2">
                    
                    <!-- PREVIEW -->
                    <div v-if="preview" class="mb-3">
                    <img
                        :src="preview"
                        class="w-full h-48 object-cover rounded-xl shadow"
                    />
                    </div>

                    <!-- INPUT -->
                    <input
                    type="file"
                    accept="image/*"
                    @change="handleFile"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-500 file:text-white hover:file:bg-emerald-600"
                    />

                    <div v-if="form.errors.banner" class="text-red-500 text-sm">
                    {{ form.errors.banner }}
                    </div>

                </div>
              </div>

              <div class="text-xs text-gray-400 mt-1 space-y-1">
                <p>• Gunakan rasio 16:9 agar tidak terpotong</p>
                <p>• Rekomendasi ukuran: 1280×720</p>
                <p>• Format: JPG / PNG (max 2MB)</p>
              </div>

              <!-- FLYER -->
              <div>
                <label class="text-sm font-medium">Flyer Event</label>

                <div class="mt-2">

                  <!-- PREVIEW -->
                  <div v-if="flyerPreview" class="mb-3">
                    <img
                      :src="flyerPreview"
                      class="w-full h-64 object-contain rounded-xl shadow border"
                    />
                  </div>

                  <!-- INPUT -->
                  <input
                    type="file"
                    accept="image/*"
                    @change="handleFlyer"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-500 file:text-white hover:file:bg-purple-600"
                  />

                  <div v-if="form.errors.flyer" class="text-red-500 text-sm">
                    {{ form.errors.flyer }}
                  </div>

                </div>
              </div>

            <!-- TITLE -->
            <div>
              <label class="text-sm">Judul Event</label>
              <input
                name="title"
                v-model="form.title"
                :class="inputClass('title')"
              />
              <div v-if="form.errors.title" class="text-red-500 text-sm">
                {{ form.errors.title }}
              </div>
            </div>

            <!-- DESCRIPTION -->
            <div>
              <label class="text-sm">Deskripsi Event</label>

              <div
                id="description"
                class="mt-2 border rounded-xl overflow-hidden"
                :class="form.errors.description ? 'border-red-500' : 'border-gray-200'"
              >
                <QuillEditor
                  v-model:content="form.description"
                  contentType="html"
                  theme="snow"
                  style="min-height: 200px"
                />
              </div>

              <div v-if="form.errors.description" class="text-red-500 text-sm">
                {{ form.errors.description }}
              </div>
            </div>

            <!-- PUBLIC -->
            <div>
              <label class="text-sm">Tipe Event</label>
              <select
                name="public"
                v-model="form.public"
                :class="inputClass('public')"
              >
                <option :value="true">Public</option>
                <option :value="false">Private</option>
              </select>
            </div>
          </div>

          <!-- WAKTU -->
          <div class="bg-white rounded-xl shadow p-6 space-y-6">
            <h4 class="font-semibold border-b pb-2">🗓️ Waktu Event</h4>

            <!-- ================= EVENT TIME ================= -->
            <div>
                <p class="text-sm font-semibold text-gray-700 mb-3">
                ⏰ Waktu Pelaksanaan Event
                </p>

                <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm">Mulai Event</label>
                    <input
                    name="start_date"
                    type="datetime-local"
                    v-model="form.start_date"
                    :class="inputClass('start_date')"
                    />
                    <div v-if="form.errors.start_date" class="text-red-500 text-sm">
                    {{ form.errors.start_date }}
                    </div>
                </div>

                <div>
                    <label class="text-sm">Akhir Event</label>
                    <input
                    name="end_date"
                    type="datetime-local"
                    v-model="form.end_date"
                    :class="inputClass('end_date')"
                    />
                    <div v-if="form.errors.end_date" class="text-red-500 text-sm">
                    {{ form.errors.end_date }}
                    </div>
                </div>
                </div>
            </div>

            <!-- DIVIDER -->
            <div class="border-t pt-4"></div>

            <!-- ================= REGISTRATION ================= -->
            <div>
                <p class="text-sm font-semibold text-gray-700 mb-3">
                📝 Waktu Registrasi Peserta
                </p>

                <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm">Open Registrasi</label>
                    <input
                    name="registration_start"
                    type="datetime-local"
                    v-model="form.registration_start"
                    :class="inputClass('registration_start')"
                    />
                    <div v-if="form.errors.registration_start" class="text-red-500 text-sm">
                    {{ form.errors.registration_start }}
                    </div>
                </div>

                <div>
                    <label class="text-sm">Close Registrasi</label>
                    <input
                    name="registration_end"
                    type="datetime-local"
                    v-model="form.registration_end"
                    :class="inputClass('registration_end')"
                    />
                    <div v-if="form.errors.registration_end" class="text-red-500 text-sm">
                    {{ form.errors.registration_end }}
                    </div>
                </div>
                </div>
            </div>

            </div>

          <!-- LOKASI -->
          <div class="bg-white rounded-xl shadow p-6 space-y-5">
            <h4 class="font-semibold border-b pb-2">📍 Lokasi Event</h4>

            <div>
              <label class="text-sm">Venue</label>
              <select
                name="venue_id"
                v-model="form.venue_id"
                :class="inputClass('venue_id')"
              >
                <option value="">-- Pilih Venue --</option>
                <option v-for="v in props.venues" :key="v.id" :value="v.id">
                  {{ v.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="text-sm">Lokasi Custom</label>
              <input
                name="custom_location"
                v-model="form.custom_location"
                :class="inputClass('custom_location')"
              />
            </div>
          </div>

          <!-- ACTION -->
          <div class="flex justify-between">
            <a href="/events" class="text-gray-500">← Kembali</a>

            <button
              type="submit"
              :disabled="form.processing"
              class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2 rounded-lg shadow-lg"
            >
              🚀 Simpan Event
            </button>
          </div>

        </form>

      </div>
    </div>
  </AuthenticatedLayout>
</template>