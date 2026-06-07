<script setup>
import { cn } from "@/lib/utils";

const props = defineProps({
  class: { type: null, required: false },
});
</script>

<template>
  <div class="relative flex min-h-0 flex-1">
    <!-- Menu scrollable -->
    <div
      data-sidebar="content"
      :class="cn(
        'relative flex min-h-0 flex-1 flex-col gap-0 overflow-auto group-data-[collapsible=icon]:overflow-hidden z-10',
        props.class,
      )"
    >
      <slot />
    </div>

    <!-- Background tetap di bawah sidebar -->
    <div class="absolute bottom-0 left-0 w-full pointer-events-none z-0">
      <img
        src="/images/side-bg-amira.png"
        class="w-full object-contain opacity-30"
        alt="Sidebar background"
      />
    </div>
  </div>
</template>
