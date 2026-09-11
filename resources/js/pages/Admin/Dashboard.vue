<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, onMounted, computed } from 'vue';
import StatCard from '@/components/StatCard.vue';

const props = defineProps<{
    events: Array<{ id: number; name: string; slug: string }>;
    selectedEventId: number;
    event: {
        id: number;
        name: string;
        description: string;
        venue: string;
        starts_at: string;
        ends_at: string;
        capacity: number;
    } | null;
}>();

const stats = ref({
    total_registered: 0,
    total_checked_in: 0,
    capacity: 0,
    hourly_data: [] as Array<{ hour: string; count: number }>,
});

const loadStats = async () => {
    if (!props.selectedEventId) return;
    try {
        const res = await fetch(`/api/events/${props.selectedEventId}/stats`);
        if (res.ok) {
            stats.value = await res.json();
        }
    } catch (e) {
        console.error('Failed to load stats', e);
    }
};

const changeEvent = (id: number) => {
    router.get('/dashboard', { event_id: id }, { preserveState: true });
};

// Set up 5s polling for live updates
onMounted(() => {
    loadStats();
    const interval = setInterval(loadStats, 5000);
    return () => clearInterval(interval);
});

// Custom SVG Chart calculation
const maxCount = computed(() => {
  if (stats.value.hourly_data.length === 0) return 1;
  return Math.max(...stats.value.hourly_data.map(d => d.count), 1);
});

const svgPoints = computed(() => {
  if (stats.value.hourly_data.length === 0) return '';
  const width = 800;
  const height = 200;
  const points = stats.value.hourly_data.map((d, index) => {
    const x = (index / (stats.value.hourly_data.length - 1 || 1)) * (width - 40) + 20;
    const y = height - ((d.count / maxCount.value) * (height - 40) + 20);
    return `${x},${y}`;
  });
  return points.join(' ');
});

const svgAreaPoints = computed(() => {
  if (stats.value.hourly_data.length === 0) return '';
  const width = 800;
  const height = 200;
  const pts = svgPoints.value;
  const firstX = 20;
  const lastX = width - 20;
  return `${firstX},${height - 10} ${pts} ${lastX},${height - 10}`;
});

const formatHourLabel = (hourString: string) => {
  try {
    const d = new Date(hourString);
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  } catch (e) {
    return hourString;
  }
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: '/dashboard' }]">
        <Head title="Live Admin Dashboard" />

        <div class="p-6 max-w-7xl mx-auto space-y-8 text-ink font-body">
            
            <!-- Selector Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 bg-paper border border-stub-line p-8 rounded-lg relative overflow-hidden">
                <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line"></div>
                <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line"></div>
                
                <div class="space-y-1">
                    <span class="font-mono text-xs uppercase text-stamp tracking-widest bg-stamp/10 px-3 py-1 rounded">
                        OPERATIONS CENTER
                    </span>
                    <h1 class="font-display text-4xl uppercase tracking-wider text-ink mt-2">LIVE CONTROL DASHBOARD</h1>
                    <p class="font-body text-xs text-muted">Select an event counter registry to monitor incoming traffic flow.</p>
                </div>
                <div class="w-full md:w-80">
                    <select 
                        :value="selectedEventId" 
                        @change="changeEvent(Number(($event.target as HTMLSelectElement).value))"
                        class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp"
                    >
                        <option v-for="e in events" :key="e.id" :value="e.id">
                            {{ e.name.toUpperCase() }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Stats Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8" v-if="event">
                <StatCard 
                    title="TOTAL REGISTERED" 
                    :value="stats.total_registered" 
                    :description="`Capacity: ${stats.capacity} attendees max`"
                />
                <StatCard 
                    title="CHECKED IN GUESTS" 
                    :value="stats.total_checked_in" 
                    :description="stats.total_checked_in > 0 ? 'Live QR counter active' : 'Waiting for first check-in'"
                    class="border-stamp/30"
                />
                <StatCard 
                    title="ATTENDANCE PERCENT" 
                    :value="`${stats.total_registered > 0 ? Math.round((stats.total_checked_in / stats.total_registered) * 100) : 0}%`" 
                    description="Proportion of registered arrivals"
                />
            </div>

            <!-- Chart Section -->
            <div class="bg-paper border border-stub-line p-8 rounded-lg space-y-6" v-if="event">
                <div class="flex items-center justify-between border-b border-dashed border-stub-line pb-4">
                    <div>
                        <h3 class="font-display text-2xl uppercase tracking-wide">Check-Ins Timeline</h3>
                        <p class="font-body text-xs text-muted">Hourly frequency of entry ticket validations.</p>
                    </div>
                    <span class="font-mono text-xs uppercase text-confirmed bg-confirmed/10 px-2.5 py-1 rounded">
                        Live Polling Active
                    </span>
                </div>

                <!-- SVG Area Chart -->
                <div v-if="stats.hourly_data.length > 0" class="w-full overflow-x-auto">
                    <div class="min-w-[800px] h-[250px] relative select-none">
                        <svg viewBox="0 0 800 200" class="w-full h-full">
                            <!-- Background Grid Lines -->
                            <line x1="20" y1="20" x2="780" y2="20" stroke="var(--stub-line)" stroke-dasharray="4,4" />
                            <line x1="20" y1="80" x2="780" y2="80" stroke="var(--stub-line)" stroke-dasharray="4,4" />
                            <line x1="20" y1="140" x2="780" y2="140" stroke="var(--stub-line)" stroke-dasharray="4,4" />
                            <line x1="20" y1="190" x2="780" y2="190" stroke="var(--ink)" stroke-width="1.5" />

                            <!-- Shaded Area -->
                            <polygon :points="svgAreaPoints" fill="var(--stamp)" opacity="0.08" />

                            <!-- Line Path -->
                            <polyline fill="none" stroke="var(--stamp)" stroke-width="2.5" :points="svgPoints" />

                            <!-- Data points -->
                            <circle 
                                v-for="(d, idx) in stats.hourly_data" 
                                :key="idx"
                                :cx="(idx / (stats.hourly_data.length - 1 || 1)) * 760 + 20"
                                :cy="200 - ((d.count / maxCount) * 160 + 20)"
                                r="4.5"
                                fill="var(--ink)"
                                stroke="var(--stamp)"
                                stroke-width="2.5"
                            />
                        </svg>
                        
                        <!-- Timeline Labels -->
                        <div class="flex justify-between px-4 mt-2 font-mono text-[9px] uppercase text-muted">
                            <span v-for="(d, idx) in stats.hourly_data" :key="idx">
                                {{ formatHourLabel(d.hour) }} ({{ d.count }})
                            </span>
                        </div>
                    </div>
                </div>

                <div v-else class="h-[200px] flex items-center justify-center border border-dashed border-stub-line rounded bg-paper/50">
                    <p class="font-mono text-xs text-muted">[ NO SCAN DATA LOGGED TODAY ]</p>
                </div>
            </div>

            <!-- Empty dashboard state -->
            <div v-else class="text-center py-24 bg-paper border border-dashed border-stub-line rounded-lg">
                <span class="text-4xl">🎟️</span>
                <h3 class="font-display text-2xl uppercase tracking-wider mt-4">NO REGISTRIES TRACKED</h3>
                <p class="font-body text-xs text-muted mt-2 max-w-sm mx-auto">Create an event registry and publish it to start monitoring attendee check-ins.</p>
            </div>
            
        </div>
    </AppLayout>
</template>
