<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import { toast } from 'vue3-toastify'

// 🔥 QUILL
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

const props = defineProps({
  event: Object,
  sessions: Array,
  days: Array
})

// ================= FORM =================
const form = reactive({})

// generate form per hari
props.days.forEach(day => {
  form[day.date] = {
    title: '',
    start_time: '',
    end_time: '',
    location: '',
    description: '',
    materials: []
  }
})

const handleFile = (event, date) => {
  const files = Array.from(event.target.files)
  form[date].materials = files
}

// ================= FILTER =================
const sessionsByDay = (date) => {
  return props.sessions.filter(s =>
    s.session_date === date
  )
}

// ================= FORMAT JAM =================
const formatTime = (val) => {
  if (!val) return '-'

  return new Date(val).toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit',
    timeZone: 'Asia/Jakarta'
  })
}

// ================= ADD =================
const addSession = (date) => {
  const f = form[date]

  const data = new FormData()

  data.append('title', f.title)
  data.append('session_date', date)
  data.append('start_time', f.start_time)
  data.append('end_time', f.end_time)
  data.append('location', f.location)
  data.append('description', f.description)

    f.materials.forEach((file) => {
    data.append('materials[]', file)
    })

    router.post(route('events.sessions.store', props.event.hashid), data, {
    forceFormData: true,
    preserveScroll: true,
    preserveState: false,

    onSuccess: () => {
      toast.success('Sesi ditambahkan')
    },

    onError: (errors) => {
      console.log(errors)

      Object.values(errors).forEach(msg => {
        toast.error(msg)
      })
    }
  })
}

// ================= DELETE =================
const deleteSession = (id) => {
  if (confirm('Hapus sesi ini?')) {
    router.delete(
      route('events.sessions.destroy', [props.event.hashid, id]),
      {
        onSuccess: () => toast.success('🗑️ Sesi dihapus')
      }
    )
  }
}
</script>

<template>
  <Head title="Rundown Event" />

  <AuthenticatedLayout>

    <!-- HEADER -->
    <template #header>
      <div>
        <h2 class="text-2xl font-bold">📋 Rundown Event</h2>
        <!-- <p class="text-sm text-gray-500">{{ event.title }}</p> -->
      </div>
    </template>

    <div class="p-6 max-w-5xl mx-auto space-y-6">

        <!-- EVENT INFO CARD -->
        <div class="mb-6 bg-gradient-to-r from-emerald-50 to-blue-50 border border-gray-100 rounded-2xl p-5 shadow-sm">

        <div class="flex justify-between items-start gap-4">

            <!-- LEFT -->
            <div class="flex-1">

            <h2 class="text-xl font-bold text-gray-800">
                {{ event.title }}
            </h2>

            <p class="text-sm text-gray-500 mt-1" v-html="event.description"></p>

            <div class="mt-3 flex flex-wrap gap-2 text-xs">

                <span class="px-2 py-1 bg-white border rounded-full text-gray-600">
                📅 {{ event.start_date_formatted }} → {{ event.end_date_formatted }}
                </span>

                <p class="text-xs text-gray-500">
                📍 {{ event.location_final }}
                </p>

                <span class="px-2 py-1 rounded-full text-white"
                    :class="event.public ? 'bg-emerald-500' : 'bg-gray-400'">
                {{ event.public ? 'Publik' : 'Private' }}
                </span>

            </div>

            </div>

            <!-- RIGHT (optional badge / status) -->
            <div class="text-right">

            <div class="text-xs text-gray-400">
                Status Event
            </div>

            <div class="text-sm font-semibold text-emerald-600">
                {{ event.status ?? 'ACTIVE' }}
            </div>

            </div>

        </div>
        </div>

      <!-- LOOP HARI -->
      <div
        v-for="day in days"
        :key="day.date"
        class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 space-y-4"
      >

        <!-- JUDUL HARI -->
        <div class="flex items-center justify-between border-b pb-2">
            <h3 class="font-bold text-lg text-gray-800">
                📅 {{ day.label }}
            </h3>
        </div>

        <!-- FORM -->
        <div class="grid md:grid-cols-2 gap-3 mb-3">

          <input v-model="form[day.date].title" placeholder="Judul sesi" class="input" />
          <input v-model="form[day.date].location" placeholder="Lokasi" class="input" />

          <input type="time" v-model="form[day.date].start_time" class="input" />
          <input type="time" v-model="form[day.date].end_time" class="input" />

        </div>

        <!-- QUILL -->
        <div class="mb-3">
          <QuillEditor
            v-model:content="form[day.date].description"
            contentType="html"
            theme="snow"
            style="min-height: 60px"
          />
        </div>

        <input
            type="file"
            multiple
            @change="handleFile($event, day.date)"
            class="input"
            />

        <button
          @click="addSession(day.date)"
          class="bg-emerald-500 text-white px-4 py-2 rounded-lg"
        >
          + Tambah Sesi
        </button>
        <hr/>

        <!-- LIST -->
        <div class="mt-5 space-y-3">

            <div
                v-for="s in sessionsByDay(day.date)"
                :key="s.id"
                class="relative bg-white border border-gray-100 shadow-sm hover:shadow-md transition rounded-xl p-4 pl-5"
            >

                <!-- timeline accent -->
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-400 rounded-l-xl"></div>

                <div class="flex justify-between gap-4">

                <!-- LEFT CONTENT -->
                <div class="flex-1">

                    <!-- TIME + TITLE -->
                    <div class="flex items-center gap-2 flex-wrap">

                    <span class="text-xs px-2 py-0.5 bg-gray-100 rounded-full text-gray-600">
                        ⏰ {{ formatTime(s.start_at) }} - {{ formatTime(s.end_at) }}
                    </span>

                    <h4 class="font-semibold text-gray-800">
                        {{ s.title }}
                    </h4>

                    </div>

                    <!-- LOCATION -->
                    <span class="text-gray-400 text-xs ml-1">
                    ({{ s.location || '-' }})
                    </span>

                    <!-- DESCRIPTION -->
                    <blockquote
                    v-if="s.description"
                    class="mt-2 text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100"
                    v-html="s.description"
                    />

                    <!-- MATERIALS -->
                    <div v-if="s.materials?.length" class="mt-3 flex flex-wrap gap-2">

                    <a
                        v-for="(file, i) in s.materials"
                        :key="i"
                        :href="`/storage/${file.path}`"
                        target="_blank"
                        class="flex items-center gap-1 px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs hover:bg-blue-100 transition"
                    >
                        📎 {{ file.name }}
                    </a>

                    </div>

                </div>

                <!-- ACTION -->
                <div class="flex flex-col gap-2">

                    <button
                    @click="deleteSession(s.id)"
                    class="text-xs text-red-500 hover:bg-red-50 px-2 py-1 rounded transition"
                    >
                    Hapus
                    </button>

                </div>

                </div>
            </div>

            <div v-if="!sessionsByDay(day.date).length"
                class="text-center text-gray-400 text-sm italic py-4">
                Belum ada sesi
            </div>

            </div>

      </div>

    </div>

  </AuthenticatedLayout>
</template>