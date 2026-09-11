<script setup lang="ts">
defineProps<{
  title: string;
  value: string | number;
  description?: string;
}>();
</script>

<template>
  <div class="relative bg-paper text-ink border border-stub-line p-6 rounded-lg flex flex-col justify-between">
    <div>
      <span class="text-xs uppercase font-mono tracking-wider text-muted">{{ title }}</span>
      <div class="font-mono text-4xl font-bold tracking-tight mt-2 mb-1">
        {{ value }}
      </div>
    </div>
    <div v-if="description" class="text-xs text-muted font-body mt-2 border-t border-dashed border-stub-line pt-2">
      {{ description }}
    </div>
  </div>
</template>
