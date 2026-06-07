<script setup>
import {
  Sidebar,
  SidebarContent,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarRail,
} from '@/Components/ui/sidebar'
import ApplicationLogo from '@/Components/ApplicationLogo2.vue'
import { usePage, Link } from '@inertiajs/vue3'

const props = defineProps({
  menu: { type: Array, default: () => [] },
  side: String,
  variant: String,
  collapsible: String,
  class: null,
})

const page = usePage()
const isActive = (url) => page.url === url || page.url.startsWith(url)
</script>

<template>
  <Sidebar v-bind="props">
    <SidebarHeader class="flex justify-center py-4 pt-2">
      <ApplicationLogo class="h-12 w-auto" />
    </SidebarHeader>

    <SidebarContent>
      <SidebarMenu>
        <SidebarMenuItem v-for="item in menu" :key="item.name">
          <SidebarMenuButton as-child :is-active="isActive(route(item.route))">
          <Link
              :href="route(item.route)"
              class="flex items-center gap-3 px-4 py-5 rounded transition-colors"
              :class="{
                'bg-indigo-50 text-indigo-600': isActive(route(item.route)),
                'hover:bg-indigo-100 hover:text-indigo-500': !isActive(route(item.route))
              }"
            >
              <component
                :is="item.icon"
                class="h-5 w-5"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
                fill="none"
              />
              <span>{{ item.name }}</span>
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarContent>

    <SidebarRail />
  </Sidebar>
</template>
