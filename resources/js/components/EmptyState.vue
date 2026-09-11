<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
  message: string;
  ctaText?: string;
  ctaLink?: string;
}>();
</script>

<template>
  <div class="relative bg-paper text-ink border border-dashed border-stub-line p-10 rounded-lg text-center flex flex-col items-center justify-center min-h-[220px]">
    <!-- Custom Ticket-styled icon -->
    <div class="mb-4 text-muted">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 stroke-[1.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
      </svg>
    </div>

    <p class="font-mono text-sm tracking-wide text-muted max-w-md mb-6 leading-relaxed">
      {{ message }}
    </p>

    <Link
      v-if="ctaText && ctaLink"
      :href="ctaLink"
      class="bg-stamp hover:bg-ink text-paper font-mono text-xs uppercase px-5 py-2.5 rounded-[4px] tracking-wider transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-stamp focus:ring-offset-2"
    >
      {{ ctaText }}
    </Link>
  </div>
</template>
