<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  status: 'valid' | 'already_checked_in' | 'invalid' | 'requires_quantity' | null;
  attendeeName?: string;
  checkInTime?: string;
  message?: string;
  totalQuantity?: number;
  checkedInNow?: number;
  remainingQuantity?: number;
}>();

const classes = computed(() => {
  if (props.status === 'valid') {
    return 'bg-confirmed text-paper border-confirmed';
  } else if (props.status === 'already_checked_in') {
    return 'bg-[#D97706] text-paper border-[#D97706]'; // Amber warning color
  } else if (props.status === 'invalid') {
    return 'bg-stamp text-paper border-stamp';
  }
  return 'hidden';
});
</script>

<template>
  <Transition
    enter-active-class="transition duration-200 ease-out"
    enter-from-class="transform scale-95 opacity-0"
    enter-to-class="transform scale-100 opacity-100"
    leave-active-class="transition duration-150 ease-in"
    leave-from-class="transform scale-100 opacity-100"
    leave-to-class="transform scale-95 opacity-0"
  >
    <div
      v-if="status"
      class="border p-6 rounded-lg text-center shadow-none flex flex-col items-center justify-center min-h-[140px] z-30"
      :class="classes"
    >
      <div v-if="status === 'valid'" class="flex flex-col items-center w-full">
        <!-- SVG Checkmark -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        <h4 class="font-display text-2xl uppercase tracking-wider">TICKET VALID</h4>
        <p class="font-body text-base font-semibold mt-1">{{ attendeeName }}</p>
        
        <div v-if="totalQuantity && totalQuantity > 1" class="mt-3 bg-black/20 w-full py-2 px-4 rounded-md flex justify-between items-center text-xs font-mono">
            <span>Checked in: {{ checkedInNow }}</span>
            <span class="font-bold">Left: {{ remainingQuantity }} / {{ totalQuantity }}</span>
        </div>
      </div>

      <div v-else-if="status === 'already_checked_in'" class="flex flex-col items-center">
        <!-- SVG Warning -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <h4 class="font-display text-2xl uppercase tracking-wider">ALREADY CHECKED IN</h4>
        <p class="font-body text-base font-semibold mt-1">{{ attendeeName }}</p>
        <p class="font-mono text-xs mt-2 bg-black/25 px-2 py-1 rounded">
          Checked-in: {{ checkInTime }}
        </p>
      </div>

      <div v-else-if="status === 'invalid'" class="flex flex-col items-center">
        <!-- SVG Error -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
        <h4 class="font-display text-2xl uppercase tracking-wider">INVALID TICKET</h4>
        <p class="font-body text-sm mt-1 opacity-90">{{ message || 'Not a valid ticket for this event.' }}</p>
      </div>
    </div>
  </Transition>
</template>
