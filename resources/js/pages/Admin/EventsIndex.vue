<script setup lang="ts">
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';

defineProps<{
    events: Array<{
        id: number;
        name: string;
        slug: string;
        description: string;
        venue: string;
        starts_at: string;
        ends_at: string;
        capacity: number;
        status: string;
        attendees_count: number;
    }>;
}>();

const isCreateOpen = ref(false);
const form = useForm({
    name: '',
    description: '',
    venue: '',
    starts_at: '',
    ends_at: '',
    ticket_types: [{ name: 'General Admission', capacity: 100 }],
});

const addTicketType = () => {
    form.ticket_types.push({ name: '', capacity: 50 });
};

const removeTicketType = (index: number) => {
    if (form.ticket_types.length > 1) {
        form.ticket_types.splice(index, 1);
    }
};

const submit = () => {
    form.post('/events', {
        onSuccess: () => {
            isCreateOpen.value = false;
            form.reset();
        }
    });
};

const deleteEvent = (id: number) => {
    if (confirm('Are you sure you want to delete this event? This will remove all associated attendee registrations and tickets.')) {
        router.delete(`/events/${id}`);
    }
};

const formatTime = (timeString: string) => {
  const d = new Date(timeString);
  return d.toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).toUpperCase();
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Events', href: '/events' }]">
        <Head title="Events Management" />

        <div class="p-6 max-w-7xl mx-auto space-y-6 text-ink font-body">
            
            <!-- Header bar -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 bg-paper border border-stub-line p-8 rounded-lg relative overflow-hidden">
                <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line"></div>
                <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line"></div>
                
                <div>
                    <span class="font-mono text-xs uppercase text-stamp tracking-widest bg-stamp/10 px-3 py-1 rounded">
                        COUNTER REGISTRIES
                    </span>
                    <h1 class="font-display text-4xl uppercase tracking-wider text-ink mt-2">EVENTS DATABASE</h1>
                    <p class="font-body text-xs text-muted">Create registries and manage live check-in tickets.</p>
                </div>
                <button 
                    @click="isCreateOpen = !isCreateOpen"
                    class="bg-stamp hover:bg-ink text-paper font-mono text-xs uppercase px-5 py-3 rounded-[4px] tracking-wider transition-colors duration-150"
                >
                    Create Event Counter
                </button>
            </div>

            <!-- Create Modal/Drawer Layout -->
            <div v-if="isCreateOpen" class="bg-paper border border-stub-line p-8 rounded-lg space-y-6">
                <h2 class="font-display text-2xl uppercase tracking-wider text-ink">New Event Registry</h2>
                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">Event Name</label>
                        <input v-model="form.name" type="text" required class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink placeholder-muted/60 focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" placeholder="PRINTING COOPERATIVE LAUNCH" />
                        <p v-if="form.errors.name" class="font-mono text-xs text-stamp uppercase mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">Venue Location</label>
                        <input v-model="form.venue" type="text" required class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink placeholder-muted/60 focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" placeholder="THE MAIN FLOOR Counter 2" />
                        <p v-if="form.errors.venue" class="font-mono text-xs text-stamp uppercase mt-1">{{ form.errors.venue }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">Event Description</label>
                        <textarea v-model="form.description" class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink placeholder-muted/60 focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" placeholder="Details of the event checkout counter..."></textarea>
                    </div>

                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">Start Time</label>
                        <input v-model="form.starts_at" type="datetime-local" required class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" />
                        <p v-if="form.errors.starts_at" class="font-mono text-xs text-stamp uppercase mt-1">{{ form.errors.starts_at }}</p>
                    </div>

                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">End Time</label>
                        <input v-model="form.ends_at" type="datetime-local" required class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" />
                        <p v-if="form.errors.ends_at" class="font-mono text-xs text-stamp uppercase mt-1">{{ form.errors.ends_at }}</p>
                    </div>

                    <!-- Ticket Categories & Seat Capacities List Builder -->
                    <div class="md:col-span-2 space-y-4 border-t border-dashed border-stub-line pt-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-display text-lg uppercase tracking-wider text-ink">Ticket Categories & Capacities</h3>
                                <p class="text-[11px] text-muted font-body">Define the seat capacities for individual ticket tiers. Overall capacity is computed automatically.</p>
                            </div>
                            <button 
                                type="button" 
                                @click="addTicketType"
                                class="border border-stamp hover:bg-stamp hover:text-paper text-stamp font-mono text-[10px] uppercase px-3 py-1.5 rounded-[4px] tracking-wider transition-colors"
                            >
                                + Add Category
                            </button>
                        </div>

                        <div class="space-y-3">
                            <div 
                                v-for="(type, idx) in form.ticket_types" 
                                :key="idx" 
                                class="flex items-start gap-4 bg-paper/60 p-4 border border-stub-line/60 rounded-[4px]"
                            >
                                <div class="flex-1">
                                    <label class="block font-mono text-[9px] uppercase tracking-wider text-muted mb-1">Category Name</label>
                                    <input 
                                        v-model="type.name" 
                                        type="text" 
                                        required 
                                        class="w-full bg-paper border border-stub-line rounded-[4px] px-3 py-2 text-xs text-ink placeholder-muted/60 focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" 
                                        placeholder="e.g. VIP" 
                                    />
                                    <p v-if="form.errors[`ticket_types.${idx}.name`]" class="font-mono text-[10px] text-stamp uppercase mt-1">{{ form.errors[`ticket_types.${idx}.name`] }}</p>
                                </div>
                                <div class="w-32">
                                    <label class="block font-mono text-[9px] uppercase tracking-wider text-muted mb-1">Seat Capacity</label>
                                    <input 
                                        v-model.number="type.capacity" 
                                        type="number" 
                                        required 
                                        class="w-full bg-paper border border-stub-line rounded-[4px] px-3 py-2 text-xs text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" 
                                        placeholder="100" 
                                    />
                                    <p v-if="form.errors[`ticket_types.${idx}.capacity`]" class="font-mono text-[10px] text-stamp uppercase mt-1">{{ form.errors[`ticket_types.${idx}.capacity`] }}</p>
                                </div>
                                <button 
                                    type="button" 
                                    @click="removeTicketType(idx)"
                                    :disabled="form.ticket_types.length <= 1"
                                    class="mt-6.5 p-2 rounded-[4px] border border-stamp/20 hover:bg-stamp/10 text-stamp transition disabled:opacity-30 disabled:cursor-not-allowed"
                                >
                                    🗑️
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2 flex justify-end gap-4">
                        <button type="button" @click="isCreateOpen = false" class="font-mono text-xs uppercase border border-stub-line hover:border-ink px-5 py-3 rounded-[4px] text-muted hover:text-ink transition">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="bg-stamp hover:bg-ink text-paper font-mono text-xs uppercase px-5 py-3 rounded-[4px] tracking-wider transition disabled:opacity-50">Save Registry</button>
                    </div>
                </form>
            </div>

            <!-- List Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div 
                    v-for="event in events" 
                    :key="event.id" 
                    class="bg-paper border border-stub-line rounded-lg p-6 flex flex-col justify-between relative overflow-hidden group hover:border-stamp transition-colors duration-200"
                >
                    <!-- Small ticket cutouts -->
                    <div class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-background rounded-full border border-stub-line"></div>
                    <div class="absolute -right-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-background rounded-full border border-stub-line"></div>

                    <div>
                        <div class="flex justify-between items-start">
                            <span :class="[
                                'px-2.5 py-0.5 rounded-[4px] font-mono text-[9px] uppercase tracking-wider border',
                                event.status === 'published' ? 'bg-confirmed/10 text-confirmed border-confirmed/20' : 'bg-stamp/10 text-stamp border-stamp/20'
                            ]">
                                {{ event.status }}
                            </span>
                            <span class="font-mono text-[9px] text-muted">CAPACITY: {{ event.capacity }}</span>
                        </div>

                        <h3 class="font-display text-2xl uppercase tracking-wide mt-4 text-ink group-hover:text-stamp transition-colors">{{ event.name }}</h3>
                        <p class="text-xs text-muted mt-2 line-clamp-2 leading-relaxed">{{ event.description || 'No description provided.' }}</p>
                        
                        <div class="mt-4 space-y-2 border-t border-dashed border-stub-line pt-4 font-mono text-[10px] text-ink/80">
                            <div>📍 VENUE: <span class="font-medium text-ink">{{ event.venue }}</span></div>
                            <div>📅 STARTS: <span class="font-medium text-ink">{{ formatTime(event.starts_at) }}</span></div>
                            <div>👥 STUBS: <span class="font-medium text-stamp font-bold">{{ event.attendees_count }} REGISTERED</span></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-6 pt-4 border-t border-dashed border-stub-line">
                        <Link :href="`/events/${event.id}/attendees`" class="flex-1 text-center py-2 rounded-[4px] font-mono text-[10px] uppercase border border-stub-line hover:border-ink hover:text-ink text-muted transition">
                            Registry
                        </Link>
                        <a :href="`/events/${event.slug}`" target="_blank" class="flex-1 text-center py-2 rounded-[4px] font-mono text-[10px] uppercase bg-stamp hover:bg-ink text-paper transition">
                            Form
                        </a>
                        <button @click="deleteEvent(event.id)" class="p-2 rounded-[4px] border border-stamp/20 hover:bg-stamp/10 text-stamp transition">
                            🗑️
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
