<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { watch, ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { toast } from 'vue3-toastify'
import { UserRound, Sparkles, BadgeCheck } from 'lucide-vue-next'
import { Cropper, CircleStencil } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'

const showSizeChart = ref(false)

const props = defineProps({
  user: Object,
  profile: Object,
  institusi: Object
})

const form = useForm({
  nama_lengkap: props.profile?.nama_lengkap || props.user?.name || '',
  jenis_kelamin: props.profile?.jenis_kelamin || '',
  alamat: props.profile?.alamat || '',
  no_telp: props.profile?.no_telp || '',
  no_wa: props.profile?.no_wa || '',
  jabatan: props.profile?.jabatan || '',
  alamat_kantor: props.profile?.alamat_kantor || '',
  ukuran_baju: props.profile?.ukuran_baju || '',
  is_smoking: props.profile?.is_smoking ?? false,
  institusi_id: props.profile?.institusi_id || '',

  nama_bank: props.profile?.nama_bank || '',
  no_rekening: props.profile?.no_rekening || '',

  avatar: null
})

watch(() => form.same_as_phone, (val) => {
  if (val) {
    form.no_wa = form.no_telp
  }
})

watch(() => form.no_telp, (val) => {
  if (form.same_as_phone) {
    form.no_wa = val
  }
})

const submit = () => {
  form.post(route('profile.store'), {
    onSuccess: () => {
      toast.success('Profil berhasil disimpan')
    },
    onError: (errors) => {
      toast.error('Periksa kembali formulir kamu')
      console.log(errors)
    }
  })
}

//////////////////AVATAR//////////////////////

const cropResult = ref(null)
const onCropChange = (result) => {
  cropResult.value = result
}

const showAvatarModal = ref(false)
const image = ref(null)
const croppedImage = ref(null)

const confirmCrop = () => {
  if (!cropResult.value) return

  cropResult.value.canvas.toBlob((blob) => {
    const file = new File([blob], 'avatar.jpg', { type: 'image/jpeg' })
    form.avatar = file

    croppedImage.value = URL.createObjectURL(file)

    showAvatarModal.value = false
    image.value = null
    cropResult.value = null
  }, 'image/jpeg')
}

const onFileChange = (e) => {
  const file = e.target.files[0]
  if (!file) return

  const reader = new FileReader()
  reader.onload = (event) => {
    image.value = event.target.result
    showAvatarModal.value = true
  }
  reader.readAsDataURL(file)
}

const closeCropper = () => {
  showAvatarModal.value = false
  image.value = null
}

const avatarUrl = computed(() => {
  if (croppedImage.value) return croppedImage.value

  if (props.profile?.avatar_url) {
    return props.profile.avatar_url
  }

  if (props.profile?.avatar) {
    return `/storage/${props.profile.avatar}`
  }

  return null
})
</script>

<template>
<Head title="Lengkapi Profil" />

<AuthenticatedLayout>

<div class="max-w-3xl mx-auto p-6">

  <!-- HEADER -->
  <div class="mb-3">
    <div class="relative overflow-hidden rounded-2xl p-6 text-white bg-gradient-animate shadow-lg">
      <!-- overlay biar elegan -->
      <div class="absolute inset-0 bg-black/10"></div>
      <!-- content -->
      <div class="relative flex items-center justify-between">
        <!-- LEFT -->
        <div class="flex items-center gap-4">
          <!-- ICON -->
          <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
            <UserRound class="w-6 h-6 text-white" />
          </div>
          <!-- TEXT -->
          <div>
            <div class="flex items-center gap-2">
              <h1 class="text-lg font-bold">
                Lengkapi Profil Kamu
              </h1>
              <BadgeCheck class="w-4 h-4 text-white/90" />
            </div>
            <p class="text-sm text-white/80">
              Data digunakan untuk event & registrasi AMIRA
            </p>
          </div>
        </div>

        <!-- RIGHT -->
        <div class="hidden md:flex">
          <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur text-xs">
            Profile Setup
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- CARD -->
  <div class="bg-white rounded-2xl shadow-lg border p-6 space-y-5">

    <div class="flex items-center gap-5 mb-6">
      <!-- AVATAR -->
      <div class="relative group">

        <div class="w-20 h-20 rounded-full bg-gray-100 overflow-hidden border-2 border-emerald-500">
          <img
              v-if="avatarUrl"
              :src="avatarUrl"
              class="w-full h-full object-cover"
            />
          <div v-else class="w-full h-full flex items-center justify-center bg-emerald-50 text-emerald-600 font-bold text-lg">
            {{ form.nama_lengkap ? form.nama_lengkap.charAt(0).toUpperCase() : 'U' }}
          </div>
        </div>

        <!-- EDIT BUTTON -->
        <label class="absolute bottom-0 right-0 bg-emerald-600 text-white p-2 rounded-full cursor-pointer shadow-md hover:bg-emerald-700">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.414 2.586a2 2 0 010 2.828l-9.9 9.9L4 16l.686-3.514 9.9-9.9a2 2 0 012.828 0z"/>
          </svg>
          <input type="file" class="hidden" @change="onFileChange" />
        </label>

      </div>

      <div>
        <h3 class="font-semibold text-gray-900">
          Foto 
        </h3>
        <p class="text-xs text-gray-500">
          Klik ikon untuk upload & crop foto
        </p>
      </div>

    </div>

    <!-- Nama -->
    <div>
      <label class="text-sm font-medium">Nama Lengkap</label>
      <input v-model="form.nama_lengkap" class="input" />
      <p v-if="form.errors.nama_lengkap" class="text-red-500 text-xs">
        {{ form.errors.nama_lengkap }}
      </p>
    </div>

    <!-- Jenis Kelamin -->
    <div>
      <label class="text-sm font-medium">Jenis Kelamin</label>
      <select v-model="form.jenis_kelamin" class="input">
        <option value="">Pilih</option>
        <option value="L">Laki-laki</option>
        <option value="P">Perempuan</option>
      </select>
      <p v-if="form.errors.jenis_kelamin" class="text-red-500 text-xs">
        {{ form.errors.jenis_kelamin }}
        </p>
    </div>

    <!-- Alamat -->
    <div>
      <label class="text-sm font-medium">Alamat</label>
      <textarea v-model="form.alamat" class="input"></textarea>
      <p v-if="form.errors.alamat" class="text-red-500 text-xs">
        {{ form.errors.alamat }}
        </p>
    </div>

    <!-- TELP + WA -->
    <div class="grid md:grid-cols-2 gap-3">

      <div>
        <label class="text-sm font-medium">No Telepon</label>
        <input v-model="form.no_telp" class="input" />
        <p v-if="form.errors.no_telp" class="text-red-500 text-xs">
        {{ form.errors.no_telp }}
        </p>
      </div>

      <div>
        <label class="text-sm font-medium">No WhatsApp</label>
        <input v-model="form.no_wa" class="input" />
        <p v-if="form.errors.no_wa" class="text-red-500 text-xs">
        {{ form.errors.no_wa }}
        </p>
      </div>

    </div>

    <!-- CHECKBOX WA = TELP -->
    <label class="flex items-center gap-2 text-sm">
      <input type="checkbox" v-model="form.same_as_phone">
      Sama dengan No Telepon
    </label>

    <div>
      <label class="text-sm font-medium">Institusi</label>

      <select v-model="form.institusi_id" class="input">
        <option value="">Pilih Institusi</option>

        <template v-for="(items, zona) in institusi" :key="zona">
          <optgroup :label="zona">
            <option
              v-for="item in items"
              :key="item.id"
              :value="item.id"
            >
              {{ item.nama_institusi }}
            </option>
          </optgroup>
        </template>

      </select>

      <p v-if="form.errors.institusi_id" class="text-red-500 text-xs">
        {{ form.errors.institusi_id }}
      </p>
    </div>

    <!-- JABATAN -->
    <div>
      <label class="text-sm font-medium">Jabatan</label>
      <input v-model="form.jabatan" class="input" />
      <p v-if="form.errors.jabatan" class="text-red-500 text-xs">
        {{ form.errors.jabatan }}
        </p>
    </div>

    <!-- ALAMAT KANTOR -->
    <div>
      <label class="text-sm font-medium">Alamat Kantor</label>
      <textarea v-model="form.alamat_kantor" class="input"></textarea>
      <p v-if="form.errors.alamat_kantor" class="text-red-500 text-xs">
        {{ form.errors.alamat_kantor }}
        </p>
    </div>

    <!-- ===================== -->
    <!-- REKENING -->
    <!-- ===================== -->
    <div class="border-t pt-4 mt-4">

      <div class="flex items-center gap-2 mb-3">
        <span class="text-sm font-semibold text-gray-700">
          💳 Informasi Rekening
        </span>
        <span class="text-xs text-gray-400">
          (untuk keperluan administrasi)
        </span>
      </div>

      <div class="grid md:grid-cols-2 gap-3">

        <!-- NAMA BANK -->
        <div>
          <label class="text-sm font-medium">Nama Bank</label>
          <select v-model="form.nama_bank" class="input">
            <option value="">Pilih Bank</option>
            <option>BCA</option>
            <option>BRI</option>
            <option>BNI</option>
            <option>Mandiri</option>
            <option>BSI</option>
            <option>CIMB Niaga</option>
            <option>BTN</option>
            <option>Permata</option>
            <option>Danamon</option>
          </select>
          <p v-if="form.errors.nama_bank" class="text-red-500 text-xs">
            {{ form.errors.nama_bank }}
          </p>
        </div>

        <!-- NO REKENING -->
        <div>
          <label class="text-sm font-medium">Nomor Rekening</label>
          <input
            v-model="form.no_rekening"
            class="input"
            placeholder="Masukkan nomor rekening"
          />
          <p v-if="form.errors.no_rekening" class="text-red-500 text-xs">
            {{ form.errors.no_rekening }}
          </p>
        </div>

      </div>

      <!-- NOTE -->
      <p class="text-xs text-gray-400 mt-2">
        Data ini digunakan untuk kebutuhan reimbursement / administrasi event
      </p>

    </div>

    <!-- UKURAN BAJU -->
    <div>
    <label class="text-sm font-medium">Ukuran Baju</label>

    <select v-model="form.ukuran_baju" class="input">
        <option value="">Pilih</option>
        <option>M</option>
        <option>L</option>
        <option>XL</option>
        <option>XXL</option>
        <option>XXXL</option>
    </select>

    <p v-if="form.errors.ukuran_baju" class="text-red-500 text-xs">
        {{ form.errors.ukuran_baju }}
    </p>

    <!-- NOTE -->
    <div class="mt-2 text-xs text-gray-500">
        Lihat panduan ukuran : 
        <button
        type="button"
        class="text-emerald-600 underline hover:text-emerald-700"
        @click="showSizeChart = true"
        >
         klik disini
        </button>
    </div>
    </div>

    <!-- SMOKING -->
    <div>
        <label class="text-sm font-medium">Smooking Room?</label>
        <select v-model="form.is_smoking" class="input">
            <option :value="0">Tidak</option>
            <option :value="1">Ya</option>
        </select>
        <p v-if="form.errors.is_smoking" class="text-red-500 text-xs">
            {{ form.errors.is_smoking }}
        </p>
    </div>

    <!-- BUTTON -->
    <button
      @click="submit"
      class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-2 rounded-lg font-semibold"
      :disabled="form.processing"
    >
      Simpan Profil
    </button>

  </div>

</div>

</AuthenticatedLayout>


<!-- SIZE CHART MODAL -->
<div v-if="showSizeChart" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl shadow-lg max-w-2xl w-full p-4 relative">

    <button
      class="absolute top-2 right-2 text-gray-500 hover:text-red-500"
      @click="showSizeChart = false"
    >
      ✕
    </button>

    <h2 class="text-lg font-semibold mb-3">📏 Panduan Ukuran Baju</h2>

    <img
      src="/images/size-chart.jpg"
      alt="Size Chart"
      class="w-full rounded-lg border"
    />

  </div>
</div>



<!-- AVATAR CROPPER MODAL -->
<div
  v-if="showAvatarModal"
  class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4"
>
  <div class="bg-white rounded-2xl w-full max-w-md shadow-xl overflow-hidden">

    <!-- HEADER -->
    <div class="p-4 border-b flex items-center justify-between">
      <h2 class="text-sm font-semibold text-gray-800">
        Crop Foto Profil
      </h2>

      <button
        class="text-gray-400 hover:text-red-500 text-lg"
        @click="closeCropper"
      >
        ✕
      </button>
    </div>

    <!-- CROPPER -->
    <div class="p-4">
      <Cropper
        v-if="image"
        class="h-72 rounded-xl overflow-hidden"
        :src="image"
        :stencil-component="CircleStencil"
        @change="onCropChange"
      />
    </div>

    <!-- FOOTER -->
    <div class="p-4 border-t flex flex-col gap-2">

      <button
        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-xl font-medium"
        @click="confirmCrop"
      >
        Gunakan Foto Ini
      </button>

      <button
        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-xl"
        @click="closeCropper"
      >
        Batal
      </button>

    </div>

  </div>
</div>

</template>



<style>
.input {
  width: 100%;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  outline: none;
}

.input:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 2px rgba(16,185,129,0.2);
}

@keyframes gradientMove {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.bg-gradient-animate {
   background: linear-gradient(
    -45deg,
    #059669, /* emerald-600 (brand utama) */
    #10b981, /* emerald-500 */
    #34d399, /* emerald-400 */
    #14b8a6, /* teal-500 */
    #0ea5e9, /* sky-500 accent */
    #6366f1  /* indigo-500 soft accent */
  );
  background-size: 300% 300%;
  animation: gradientMove 12s ease infinite;
}
</style>

