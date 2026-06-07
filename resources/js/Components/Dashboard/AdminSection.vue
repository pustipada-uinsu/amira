<script setup>
import { Card, CardContent } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  events: Array,
  stats: Object,
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID')
}

const getLocation = (event) => {
  return event.venue?.name || event.custom_location || '-'
}
</script>

<template>
  <div>

    <!-- STATS -->
    <div class="grid md:grid-cols-2 gap-6 mb-8">
      <Card>
        <CardContent class="p-5">
          <p class="text-sm text-gray-500">Event Saya</p>
          <h2 class="text-2xl font-bold">{{ stats.my_events }}</h2>
        </CardContent>
      </Card>

      <Card>
        <CardContent class="p-5">
          <p class="text-sm text-gray-500">Total Peserta</p>
          <h2 class="text-2xl font-bold">{{ stats.total_participants }}</h2>
        </CardContent>
      </Card>
    </div>

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Event Saya</h2>

      <Link href="/events/create">
        <Button>Buat Event</Button>
      </Link>
    </div>

    <!-- LIST -->
    <div v-if="events.length === 0" class="text-gray-500">
      Kamu belum punya event
    </div>

    <div v-else class="grid md:grid-cols-3 gap-6">
      <Card v-for="event in events" :key="event.id">
        <CardContent class="p-5">
          <h3 class="font-semibold">{{ event.title }}</h3>

          <p class="text-sm text-gray-500">
            📅 {{ formatDate(event.start_date) }}
          </p>

          <p class="text-sm text-gray-500">
            📍 {{ getLocation(event) }}
          </p>

          <Link :href="`/events/${event.id}`">
            <Button class="mt-3 w-full">Kelola</Button>
          </Link>
        </CardContent>
      </Card>
    </div>

  </div>
</template>