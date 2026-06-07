<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Swal from 'sweetalert2'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const props = defineProps({
  events: Array,
  auth: Object
})

// ================= STATE =================
const search = ref('')
const statusFilter = ref('all')

// ================= FILTER =================
const filteredEvents = computed(() => {
  return props.events.filter(e => {
    const matchSearch = e.title.toLowerCase().includes(search.value.toLowerCase())
    const matchStatus = statusFilter.value === 'all' || e.status === statusFilter.value
    return matchSearch && matchStatus
  })
})

// ================= BADGE =================
const statusBadge = (status) => {
  switch (status) {
    case 'published':
      return 'bg-green-100 text-green-700'
    case 'draft':
      return 'bg-gray-100 text-gray-600'
    case 'finished':
      return 'bg-blue-100 text-blue-700'
    default:
      return 'bg-gray-100 text-gray-600'
  }
}

// ================= ACTION =================
// const deleteEvent = (id) => {
//   if (confirm('Yakin hapus event ini?')) {
//     router.delete(`/events/${id}`)
//   }
// }

const formatDateTime = (date) => {
  if (!date) return '-'

  return new Intl.DateTimeFormat('id-ID', {
    weekday: 'short',
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}

const deleteEvent = (id) => {
  Swal.fire({
    title: 'Hapus event?',
    text: 'Event akan dipindahkan ke trash',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#10b981',
    cancelButtonColor: '#ef4444',
    confirmButtonText: 'Ya, hapus',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {

      router.delete(`/events/${id}`, {
        preserveScroll: true,

        onSuccess: () => {
          toast.success('Event berhasil dihapus', {
            position: 'top-right',
            autoClose: 3000
          })
        },

        onError: () => {
          toast.error('Gagal menghapus event', {
            position: 'top-right',
            autoClose: 3000
          })
        }
      })

    }
  })
}

const isSuperAdmin = computed(() => {
  return props.auth?.user?.role === 'adminsuper'
})
</script>

<template>
  <Head title="Events" />

  <AuthenticatedLayout>

    <!-- HEADER -->
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-3xl font-extrabold">Events</h2>

        <a
          href="/events/create"
          class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg shadow"
        >
          + Buat Event
        </a>
      </div>
    </template>

    <div class="relative min-h-screen">

      <!-- BACKGROUND -->
      <div class="absolute inset-0 bg-repeat bg-[length:500px_500px]"
        style="background-image: url('/images/bg-4.jpg')"></div>
      <div class="absolute inset-0 bg-white/95"></div>

      <div class="relative z-10 py-6 max-w-7xl mx-auto px-4 sm:px-8 space-y-6">

        <!-- FILTER BAR -->
        <div class="bg-white rounded-xl shadow p-4 flex flex-col md:flex-row gap-4 md:items-center md:justify-between">

          <!-- SEARCH -->
          <input
            v-model="search"
            placeholder="Cari event..."
            class="input md:w-1/3"
          />

          <!-- FILTER -->
          <div class="flex gap-2">
            <button @click="statusFilter = 'all'" class="btn-filter" :class="{active: statusFilter==='all'}">All</button>
            <button @click="statusFilter = 'draft'" class="btn-filter" :class="{active: statusFilter==='draft'}">Draft</button>
            <button @click="statusFilter = 'published'" class="btn-filter" :class="{active: statusFilter==='published'}">Published</button>
            <button @click="statusFilter = 'finished'" class="btn-filter" :class="{active: statusFilter==='finished'}">Finished</button>
          </div>

        </div>

        <!-- LIST -->
        <div v-if="filteredEvents.length" class="grid md:grid-cols-2 lg:grid-cols-2 gap-6">
          <div
            v-for="event in filteredEvents"
            :key="event.hashid"
            class="bg-white rounded-2xl shadow hover:shadow-xl transition overflow-hidden flex flex-col"
          >

            <!-- HEADER -->
            <div class="p-5 border-b space-y-2">
              <div class="flex justify-between items-start">
                <h3 class="font-semibold text-lg leading-snug">
                  {{ event.title }}
                </h3>
                <span
                  class="text-xs px-2 py-1 rounded-full"
                  :class="statusBadge(event.status)"
                >
                  {{ event.status }}
                </span>
              </div>

              <p class="text-xs text-gray-400">
                ID: {{ event.hashid }}
              </p>
            </div>

            <!-- CONTENT -->
            <div class="p-5 space-y-2 text-sm text-gray-600 flex-1">
              <div class="flex items-center gap-2">
                <span>📍</span>
                <span>{{ event.venue?.name ?? 'Custom Location' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span>📅</span>
                <span>{{ formatDateTime(event.start_date) }}</span>
              </div>
            </div>

            <!-- FOOTER -->
            <div class="p-4 border-t space-y-3">

              <!-- PRIMARY -->
              <div class="grid grid-cols-3 gap-2">
                <Link
                  :href="`/events/${event.hashid}`"
                  class="flex-1 text-center bg-emerald-500 hover:bg-emerald-600 text-white text-sm py-2 rounded-lg transition"
                >
                  Detail
                </Link>

                <Link
                  :href="`/events/${event.hashid}/sessions`"
                  class="flex-1 text-center bg-indigo-500 hover:bg-indigo-600 text-white text-sm py-2 rounded-lg transition"
                >
                  Rundown
                </Link>
                
                <Link
                  :href="`/events/${event.hashid}/attendance`"
                  class="text-center bg-purple-500 hover:bg-purple-600 text-white text-sm py-2 rounded-lg transition"
                >
                  Attendance
                </Link>
              </div>

              <!-- SECONDARY -->
              <div class="flex justify-between items-center text-xs">
                <div class="flex gap-3">
                  <Link
                    :href="route('participant.index', event.hashid)"
                    class="text-emerald-600 hover:underline"
                  >
                    👥 Peserta
                  </Link>

                  <Link
                    v-if="isSuperAdmin"
                    :href="`/events/${event.hashid}/admins`"
                    class="text-indigo-600 hover:underline"
                  >
                    ⚙️ Admin
                  </Link>
                </div>

                <div class="flex gap-3">
                  <Link
                    :href="`/events/${event.hashid}/edit`"
                    class="text-yellow-500 hover:text-yellow-600 font-medium"
                  >
                    Edit
                  </Link>

                  <button
                    @click="deleteEvent(event.hashid)"
                    class="text-red-500 hover:text-red-700 font-medium"
                  >
                    Hapus
                  </button>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- EMPTY -->
        <div v-else class="text-center text-gray-500 py-20">
          Tidak ada event ditemukan 😢
        </div>

      </div>

    </div>

  </AuthenticatedLayout>
</template>