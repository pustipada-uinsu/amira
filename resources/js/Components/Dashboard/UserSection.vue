<script setup>
import { Card, CardContent } from '@/Components/ui/card'
import { Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  events: Array,
  my_events: Array,
  stats: Object,
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID')
}

const getLocation = (event) => {
  return event.venue?.name || event.custom_location || '-'
}

const filteredEvents = computed(() => {
  const myIds = props.my_events.map(e => e.event.id)
  return props.events
    .filter(e => !myIds.includes(e.id))
    .slice(0, 6)
})

import {
  Calendar,
  MapPin,
  Ticket,
  Rocket,
  ArrowRight
} from 'lucide-vue-next'

const showFlyer = ref(false)
const selectedFlyer = ref(null)

const openFlyer = (event) => {
  selectedFlyer.value = event.flyer
  showFlyer.value = true
}

const flyerUrl = computed(() => {
  return selectedFlyer.value
    ? `/storage/${selectedFlyer.value}`
    : null
})
</script>

<template>
  <div class="space-y-10">

    <!-- ===================== -->
    <!-- EVENT SAYA -->
    <!-- ===================== -->
    <Card class="rounded-3xl shadow-sm border">
      <CardContent class="p-6 space-y-6">

        <div class="flex items-center gap-2">
          <Ticket class="w-5 h-5 text-emerald-600" />
          <h2 class="text-lg font-semibold text-gray-800">
            Event Saya
          </h2>
        </div>

        <div v-if="my_events.length" class="grid md:grid-cols-2 gap-6">

          <div
            v-for="item in my_events"
            :key="item.id"
            class="group rounded-3xl overflow-hidden border bg-white shadow-sm 
                   hover:-translate-y-2 hover:shadow-2xl transition duration-300"
          >

            <!-- HEADER -->
            <div class="h-36 relative overflow-hidden">
              <img
                v-if="item.event.banner"
                :src="`/storage/${item.event.banner}`"
                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition"
              />

              <div class="absolute inset-0 bg-black/40"></div>

              <div
                v-if="!item.event.banner"
                class="absolute inset-0 bg-gradient-to-r from-emerald-500 via-blue-500 to-purple-500"
              ></div>

              <div class="absolute bottom-3 left-4 text-white">
                <p class="text-xs opacity-80">My Event</p>
                <h3 class="text-sm font-semibold leading-tight">
                  {{ item.event.title }}
                </h3>
              </div>
            </div>

            <!-- BODY -->
            <div class="p-5 space-y-3">

              <div class="flex items-center gap-2 text-sm text-gray-500">
                <Calendar class="w-4 h-4" />
                {{ formatDate(item.event.start_date) }}
              </div>

              <div class="flex items-center gap-2 text-sm text-gray-500">
                <MapPin class="w-4 h-4" />
                {{ getLocation(item.event) }}
              </div>

              <!-- STATUS + ACTION -->
              <div class="flex items-center justify-between pt-3">

                <span
                  :class="[
                    'text-xs px-3 py-1 rounded-full font-medium capitalize',
                    item.status === 'approved' && 'bg-emerald-100 text-emerald-700',
                    item.status === 'pending' && 'bg-yellow-100 text-yellow-700',
                    item.status === 'rejected' && 'bg-red-100 text-red-700',
                     item.status === 'checked_in' && 'bg-blue-100 text-blue-700'
                  ]"
                >
                  {{ item.status }}
                </span>

                <div class="flex gap-2">

                  <button
                    v-if="item.event.banner"
                    @click="openFlyer(item.event)"
                    class="flex items-center gap-1 text-xs px-3 py-1 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition shadow"
                  >
                    Lihat Flyer
                  </button>

                  <!-- LIHAT DETAIL -->
                  <Link :href="`/detail-event/${item.event.hashid}`">
                    <button class="flex items-center gap-1 text-sm text-emerald-600 font-medium hover:gap-2 transition">
                      Lihat Detail
                      <ArrowRight class="w-4 h-4" />
                    </button>
                  </Link>

                </div>

              </div>

            </div>
          </div>
        </div>

        <!-- EMPTY -->
        <div v-else class="text-sm text-gray-400 italic text-center py-6">
          Kamu belum mengikuti event
        </div>

      </CardContent>
    </Card>

    <!-- ===================== -->
    <!-- EVENT TERBARU -->
    <!-- ===================== -->
    <Card class="rounded-3xl shadow-sm border">
      <CardContent class="p-6 space-y-6">

        <div class="flex items-center gap-2">
          <Rocket class="w-5 h-5 text-blue-600" />
          <h2 class="text-lg font-semibold text-gray-800">
            Event Terbaru
          </h2>
        </div>

        <div class="grid md:grid-cols-2 gap-6">

          <div
            v-for="event in filteredEvents"
            :key="event.id"
            class="group rounded-3xl overflow-hidden border bg-white shadow-sm 
                   hover:-translate-y-2 hover:shadow-2xl transition duration-300"
          >

            <!-- HEADER -->
            <div class="h-36 relative overflow-hidden">
              <img
                v-if="event.banner"
                :src="`/storage/${event.banner}`"
                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition"
              />

              <div class="absolute inset-0 bg-black/40"></div>

              <div
                v-if="!event.banner"
                class="absolute inset-0 bg-gradient-to-r from-emerald-500 via-blue-500 to-purple-500"
              ></div>

              <div class="absolute bottom-3 left-4 text-white">
                <p class="text-xs opacity-80">Event</p>
                <h3 class="text-sm font-semibold leading-tight">
                  {{ event.title }}
                </h3>
              </div>
            </div>

            <!-- BODY -->
            <div class="p-5 space-y-3">

              <div class="flex items-center gap-2 text-sm text-gray-500">
                <Calendar class="w-4 h-4" />
                {{ formatDate(event.start_date) }}
              </div>

              <div class="flex items-center gap-2 text-sm text-gray-500">
                <MapPin class="w-4 h-4" />
                {{ getLocation(event) }}
              </div>

              <div class="flex gap-2 mt-3">
              <button
                v-if="event.banner"
                @click="openFlyer(event)"
                class="flex-1 text-xs rounded-xl bg-indigo-50 text-indigo-600 py-2 font-medium hover:bg-indigo-100 transition"
              >
                Lihat Flyer
              </button>
              <Link :href="`/detail-event/${event.hashid}`" class="flex-1">
                <button class="w-full flex items-center justify-center gap-2 
                              rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600
                              text-white py-2 text-sm font-semibold
                              shadow-md hover:shadow-lg hover:scale-[1.02]
                              transition">
                  Lihat Event
                  <ArrowRight class="w-4 h-4" />
                </button>
              </Link>
            </div>

            </div>
          </div>

        </div>

      </CardContent>
    </Card>

  </div>


  <Teleport to="body">
  <div
    v-if="showFlyer"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-md"
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
</Teleport>

</template>