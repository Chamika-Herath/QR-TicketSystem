<script setup lang="ts">
import { computed } from 'vue';

defineProps<{
  animate?: boolean;
}>();
</script>

<template>
  <div
    class="relative bg-paper text-ink border border-stub-line font-body p-6 flex flex-col md:flex-row items-stretch rounded-lg shadow-none min-h-[200px] overflow-hidden"
    :class="[
      animate ? 'animate-ticket-print' : '',
    ]"
  >
    <!-- Circular cutout notches (Left and Right) at the center height of the card -->
    <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line z-20"></div>
    <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line z-20"></div>

    <!-- Main ticket details slot -->
    <div class="flex-1 pr-0 md:pr-8 flex flex-col justify-between z-10 py-1">
      <slot name="main" />
    </div>

    <!-- Perforation line with top/bottom notch markers -->
    <div class="relative flex items-center justify-center my-4 md:my-0 md:mx-4 z-10">
      <div class="w-full md:w-0 h-0 md:h-full border-t-2 md:border-l-2 border-dashed border-stub-line"></div>
    </div>

    <!-- Tear-off stub slot -->
    <div class="w-full md:w-56 pl-0 md:pl-8 flex flex-col items-center justify-center text-center z-10 py-1">
      <slot name="stub" />
    </div>
  </div>
</template>

<style scoped>
.animate-ticket-print {
  animation: ticketPrint 0.65s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  transform-origin: top center;
}

@keyframes ticketPrint {
  0% {
    opacity: 0;
    transform: translateY(-50px) rotate(-2deg);
  }
  60% {
    transform: translateY(4px) rotate(0.5deg);
  }
  100% {
    opacity: 1;
    transform: translateY(0) rotate(0deg);
  }
}
</style>
