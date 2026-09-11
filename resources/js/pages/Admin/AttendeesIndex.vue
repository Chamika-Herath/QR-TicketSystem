<script setup lang="ts">
import { Head, router, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps<{
    event: {
        id: number;
        name: string;
        slug: string;
    };
    attendees: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            phone: string;
            ticket: {
                id: number;
                token: string;
                ticket_type: string;
                seat_number?: string;
                status: string;
                check_in: {
                    id: number;
                    scanned_at: string;
                } | null;
            } | null;
        }>;
        links: Array<any>;
    };
    filters: {
        search?: string;
    };
}>();

const searchVal = ref(props.filters.search || '');

const handleSearch = () => {
    router.get(`/events/${props.event.id}/attendees`, { search: searchVal.value }, { preserveState: true });
};

const manualCheckin = (attendeeId: number) => {
    if (confirm('Are you sure you want to manually check-in this attendee?')) {
        router.post(`/events/${props.event.id}/attendees/${attendeeId}/manual-checkin`);
    }
};

const deleteAttendee = (attendeeId: number) => {
    if (confirm('Are you sure you want to delete this attendee from the registry? Their ticket will be permanently revoked and they will not be able to check-in.')) {
        router.delete(`/events/${props.event.id}/attendees/${attendeeId}`);
    }
};

const formatTime = (timeString: string) => {
  const d = new Date(timeString);
  return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }).toUpperCase();
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Events', href: '/events' }, { title: 'Attendees', href: `/events/${event.id}/attendees` }]">
        <Head :title="`${event.name} - Attendees Registry`" />

        <div class="p-6 max-w-7xl mx-auto space-y-6 text-ink font-body">
            
            <!-- Header bar with export option -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 bg-paper border border-stub-line p-8 rounded-lg relative overflow-hidden">
                <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line"></div>
                <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line"></div>

                <div>
                    <span class="font-mono text-xs uppercase text-stamp tracking-widest bg-stamp/10 px-3 py-1 rounded">
                        REGISTRY ARCHIVE
                    </span>
                    <h1 class="font-display text-4xl uppercase tracking-wider text-ink mt-2">{{ event.name.toUpperCase() }} Registry</h1>
                    <p class="font-body text-xs text-muted">Review registrations, manage manual entry validations, or export CSV sheets.</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative flex items-center">
                        <input 
                            v-model="searchVal"
                            @keyup.enter="handleSearch"
                            type="text" 
                            placeholder="SEARCH NAME / EMAIL..." 
                            class="bg-paper border border-stub-line rounded-[4px] px-4 py-2.5 text-xs font-mono uppercase text-ink placeholder-muted/60 focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp w-56" 
                        />
                    </div>
                    <button 
                        @click="handleSearch" 
                        class="bg-ink text-paper hover:bg-stamp hover:text-paper font-mono text-xs uppercase px-4 py-2.5 rounded-[4px] tracking-wider transition-colors duration-150"
                    >
                        Filter
                    </button>
                    <a 
                        :href="`/events/${event.id}/export`" 
                        class="bg-stamp hover:bg-ink text-paper font-mono text-xs uppercase px-4 py-2.5 rounded-[4px] tracking-wider transition-colors duration-150"
                    >
                        Export CSV
                    </a>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-paper border border-stub-line rounded-lg overflow-hidden">
                <div class="overflow-x-auto max-h-[600px] scrollbar-thin">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 z-10 bg-paper border-b border-stub-line">
                            <tr class="font-mono text-[10px] uppercase text-muted tracking-wider">
                                <th class="p-4 bg-paper">Attendee</th>
                                <th class="p-4 bg-paper">Contact Info</th>
                                <th class="p-4 bg-paper">Ticket Token</th>
                                <th class="p-4 bg-paper">Ticket Type</th>
                                <th class="p-4 bg-paper">Status</th>
                                <th class="p-4 bg-paper text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stub-line/60">
                            <tr v-for="attendee in attendees.data" :key="attendee.id" class="hover:bg-paper/40 transition">
                                <td class="p-4">
                                    <div class="font-semibold text-sm">{{ attendee.name.toUpperCase() }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-xs text-ink">{{ attendee.email.toUpperCase() }}</div>
                                    <div class="text-[10px] text-muted font-mono mt-0.5">{{ attendee.phone }}</div>
                                </td>
                                <td class="p-4 font-mono text-xs text-muted select-all">
                                    {{ attendee.ticket ? attendee.ticket.token.substring(0, 12) + '...' : '[ NO TOKEN ]' }}
                                </td>
                                <td class="p-4 font-mono text-xs">
                                    <span class="px-2 py-0.5 rounded-[4px] text-[10px] uppercase border bg-stamp/5 text-stamp border-stamp/20">
                                        {{ attendee.ticket ? attendee.ticket.ticket_type : 'N/A' }}
                                    </span>
                                    <div v-if="attendee.ticket?.seat_number" class="text-[9px] text-muted mt-1 font-bold">
                                        {{ attendee.ticket.seat_number }}
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span 
                                        v-if="attendee.ticket?.check_in" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-[4px] text-[10px] font-mono uppercase bg-confirmed/10 text-confirmed border border-confirmed/20"
                                    >
                                        Checked In ({{ formatTime(attendee.ticket.check_in.scanned_at) }})
                                    </span>
                                    <span 
                                        v-else 
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-[4px] text-[10px] font-mono uppercase bg-stamp/5 text-stamp border border-stamp/20"
                                    >
                                        Pending
                                    </span>
                                </td>
                                <td class="p-4 text-right flex items-center justify-end gap-3">
                                    <button 
                                        v-if="!attendee.ticket?.check_in"
                                        @click="manualCheckin(attendee.id)"
                                        class="font-mono text-[10px] uppercase border border-stub-line hover:border-ink hover:text-ink text-muted px-3 py-1.5 rounded-[4px] transition"
                                    >
                                        Stamp Check In
                                    </button>
                                    <span v-else class="font-mono text-[10px] text-muted mr-2">COMPLETED</span>
                                    
                                    <button 
                                        @click="deleteAttendee(attendee.id)"
                                        class="p-1.5 rounded-[4px] border border-stamp/20 hover:bg-stamp/10 text-stamp transition"
                                        title="Delete Registration"
                                    >
                                        🗑️
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="attendees.data.length === 0">
                                <td colspan="5" class="p-16 text-center text-muted font-mono text-xs uppercase">
                                    [ No attendee registrations found ]
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
