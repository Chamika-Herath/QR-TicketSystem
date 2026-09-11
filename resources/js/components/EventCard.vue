<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface EventData {
  id: number;
  name: string;
  slug: string;
  starts_at: string | Date;
  ends_at?: string | Date;
  venue: string;
  description?: string;
  capacity: number;
}

const props = defineProps<{
  event: EventData;
}>();

const formattedDate = computed(() => {
  const date = new Date(props.event.starts_at);
  return date.toLocaleDateString('en-US', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  }).toUpperCase();
});

const formattedTime = computed(() => {
  const date = new Date(props.event.starts_at);
  return date.toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
  });
});
</script>

<template>
  <div class="relative bg-paper text-ink border border-stub-line font-body p-6 rounded-lg flex flex-col justify-between hover:border-stamp transition-colors duration-250 min-h-[220px]">
    <!-- Ticket cutout notches for mini-ticket feel -->
    <div class="absolute -left-1.5 top-1/2 -translate-y-1/2 w-3 h-3 bg-background rounded-full border border-stub-line"></div>
    <div class="absolute -right-1.5 top-1/2 -translate-y-1/2 w-3 h-3 bg-background rounded-full border border-stub-line"></div>

    <div>
      <!-- Monospace date block -->
      <div class="font-mono text-xs text-muted mb-2 tracking-wider">
        {{ formattedDate }} // {{ formattedTime }}
      </div>
      
      <!-- Display font for event title -->
      <h3 class="font-display text-2xl uppercase tracking-wide leading-tight mb-2">
        {{ event.name }}
      </h3>
      
      <!-- Humanist body description -->
      <p class="text-sm text-muted line-clamp-2 mb-4 leading-relaxed">
        {{ event.description || 'No description provided.' }}
      </p>
    </div>

    <div class="pt-4 border-t border-dashed border-stub-line flex items-center justify-between">
      <div class="flex flex-col">
        <span class="text-[10px] uppercase font-mono text-muted">VENUE</span>
        <span class="text-xs font-mono font-medium truncate max-w-[150px]">{{ event.venue }}</span>
      </div>

      <Link
        :href="`/events/${event.slug}`"
        class="bg-stamp hover:bg-ink text-paper font-mono text-xs uppercase px-4 py-2 rounded-[4px] tracking-wider transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-stamp focus:ring-offset-2"
      >
        Get Ticket
      </Link>
    </div>
  </div>
</template>
