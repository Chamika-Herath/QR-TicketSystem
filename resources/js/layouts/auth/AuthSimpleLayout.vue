<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-background text-ink font-body p-6 md:p-10 selection:bg-stamp selection:text-paper">
        <div class="w-full max-w-md bg-paper border border-stub-line p-8 rounded-lg relative overflow-hidden shadow-none">
            <!-- Cutout notches on the left and right sides of the card -->
            <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line z-20"></div>
            <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line z-20"></div>

            <div class="flex flex-col gap-6">
                <!-- Header -->
                <div class="flex flex-col items-center gap-4 text-center pb-4 border-b border-dashed border-stub-line">
                    <Link :href="home()" class="font-display text-3xl uppercase tracking-wider text-stamp hover:text-ink transition-colors">
                        TICKET // COUNTER
                    </Link>
                    <div class="space-y-1 mt-2">
                        <h1 class="font-display text-2xl uppercase tracking-wide text-ink">{{ title }}</h1>
                        <p class="text-xs text-muted leading-relaxed max-w-[280px] mx-auto">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <!-- Form Content Slot -->
                <slot />
            </div>
        </div>
        <div class="mt-6 text-center">
            <Link :href="home()" class="font-mono text-[10px] uppercase text-muted hover:text-ink transition-colors">
                &larr; Back to main counters
            </Link>
        </div>
    </div>
</template>
