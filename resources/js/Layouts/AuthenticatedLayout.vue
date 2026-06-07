<script setup>
import { usePage } from '@inertiajs/vue3'
import AppSidebar from '@/Components/AppSidebar.vue'
import { SidebarProvider, SidebarTrigger, SidebarInset } from '@/Components/ui/sidebar'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import Toaster from '@/Components/ui/toast/Toaster.vue'
import { Separator } from '@/Components/ui/separator'
import LoadingOverlay from '@/Components/LoadingOverlay.vue'
import { Home, Mail, Users, Settings, Calendar, Ticket, Hourglass, Megaphone, MapPinned, UserCog, UserRoundPlus, LogOut } from "lucide-vue-next"

const page = usePage()
const user = page.props.auth?.user || null

const role = user?.role || 'user'

const allMenu = [
  { name: "Home", route: "dashboard", icon: Home, roles: ['adminsuper','admin','user'] },
  { name: "Event", route: "events.index", icon: Ticket, roles: ['adminsuper','admin'] },
  // { name: "Event Admin", route: "ea.index", icon: UserRoundPlus, roles: ['adminsuper'] },
  // { name: "Participants", route: "participant.index", icon: Mail, roles: ['adminsuper','admin'] },
  { name: "Profile Users", route: "admin.profiles", icon: Users, roles: ['adminsuper'] },
  { name: "Manage Users", route: "users.index", icon: UserCog, roles: ['adminsuper'] },
  { name: "Venue", route: "venue.index", icon: MapPinned, roles: ['adminsuper','admin'] },
  // { name: "Session", route: "admin.sesi.index", icon: Hourglass, roles: ['superadmin','admin'] },
  // { name: "Pengumuman", route: "admin.announcements.index", icon: Megaphone, roles: ['superadmin','admin'] },
  { name: "Profile", route: "profile", icon: Users, roles: ['user'] },
  { name: "Keluar", route: "logout_", icon: LogOut, roles: ['adminsuper','admin','user'] },
]

const menu = user
  ? allMenu.filter(item => item.roles.includes(role))
  : []

</script>

<template>
  <LoadingOverlay />
  <SidebarProvider>
    <!-- Oper menu yang sudah difilter ke AppSidebar -->
    <AppSidebar :menu="menu" />

    <SidebarInset class="overflow-x-hidden">
      <!-- Header -->
      <header class="flex h-16 shrink-0 items-center gap-2 border-b px-3">
        <SidebarTrigger />

        <img src="/images/amira-2.png"
             alt="MyUINSU"
             class="h-8 w-auto sm:hidden"
             style="width:200px;height:auto"
        />

        <Separator orientation="vertical" class="mr-2 h-4" />
        <div class="flex-1"></div>

        <!-- User dropdown -->
        <Dropdown align="right" width="48" class="mr-5">
          <template #trigger>
            <button
              class="flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <img
                :src="user?.avatar ? `/storage/${user.avatar}` : '/images/default-avatar.jpg'"
                alt="avatar"
                class="w-8 h-8 rounded-full object-cover"
              />
              <span class="hidden sm:inline">{{ user?.name }}</span>
              <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.65a.75.75 0 01-1.08 0l-4.25-4.65a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
              </svg>
            </button>
          </template>

          <template #content>
            <DropdownLink href="/profile">Profile</DropdownLink>
            <DropdownLink href="/logout" method="post" as="button">Logout</DropdownLink>
          </template>
        </Dropdown>
      </header>

      <!-- Main content -->
      <main class="pb-16">

        <div v-if="$slots.header" class="bg-gray-100 border-b border-gray-300 px-6 py-4">
        <slot name="header" />
      </div>

        <slot />
      </main>

      <Toaster />
    </SidebarInset>
  </SidebarProvider>
</template>
