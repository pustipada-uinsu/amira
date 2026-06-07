<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Swal from 'sweetalert2'
import { toast } from 'vue3-toastify'

const props = defineProps({
  event: Object,
  admins: Array,
  users: Array
})

const selectedUser = ref('')
const selectedRole = ref('admin')

const addAdmin = () => {
  router.post(`/events/${props.event.hashid}/admins`, {
    user_id: selectedUser.value,
    role: selectedRole.value
  }, {
    onSuccess: () => {
      toast.success('Admin ditambahkan')
      selectedUser.value = ''
    }
  })
}

const deleteAdmin = (userId) => {
  Swal.fire({
    title: 'Hapus admin?',
    icon: 'warning',
    showCancelButton: true
  }).then((res) => {
    if (res.isConfirmed) {
      router.delete(`/events/${props.event.hashid}/admins/${userId}`)
    }
  })
}

const updateRole = (userId, role) => {
  router.put(`/events/${props.event.hashid}/admins/${userId}`, {
    role
  })
}
</script>

<template>
  <Head title="Manage Admin Event" />

  <AuthenticatedLayout>

    <template #header>
      <h2 class="text-2xl font-bold">
        Admin Event - {{ event.title }}
      </h2>
    </template>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

      <!-- TAMBAH ADMIN -->
      <div class="bg-white p-5 rounded-2xl shadow flex flex-col md:flex-row gap-3 items-stretch md:items-center">
            <select v-model="selectedUser"
                class="flex-1 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                <option value="">Pilih User</option>
                <option v-for="u in users" :key="u.id" :value="u.id">
                {{ u.name }} ({{ u.email }})
                </option>
            </select>
            <select v-model="selectedRole"
                class="w-32 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="admin">Admin</option>
                <option value="owner">Owner</option>
            </select>
            <button
                @click="addAdmin"
                class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2 rounded-lg shadow transition">
                Tambah
            </button>
        </div>

      <!-- LIST ADMIN -->
      <div class="bg-white rounded-2xl shadow overflow-hidden">
            <div class="p-4">
                <h3 class="font-semibold text-gray-700 mb-3">
                Daftar Admin
                </h3>

                <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead class="bg-gray-100 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-center">Role</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr
                        v-for="admin in admins"
                        :key="admin.id"
                        class="border-t hover:bg-gray-50 transition"
                    >

                        <td class="px-4 py-3 font-medium text-gray-800">
                        {{ admin.name }}
                        </td>

                        <td class="px-4 py-3 text-gray-500">
                        {{ admin.email }}
                        </td>

                        <td class="px-4 py-3 text-center">
                        <select
                            :value="admin.pivot.role"
                            @change="e => updateRole(admin.id, e.target.value)"
                            class="border rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500"
                        >
                            <option value="admin">Admin</option>
                            <option value="owner">Owner</option>
                        </select>
                        </td>

                        <td class="px-4 py-3 text-center">
                        <button
                            @click="deleteAdmin(admin.id)"
                            class="text-red-500 hover:text-red-700 font-medium transition"
                        >
                            Hapus
                        </button>
                        </td>

                    </tr>
                    </tbody>

                </table>
                </div>

            </div>
        </div>

    </div>

  </AuthenticatedLayout>
</template>