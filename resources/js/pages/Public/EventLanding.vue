<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { register } from '@/routes/events';
import TicketStub from '@/components/TicketStub.vue';

const props = defineProps<{
    event: {
        id: number;
        name: string;
        description: string;
        venue: string;
        starts_at: string;
        ends_at: string;
        ticket_template_path: string;
        ticket_types?: Array<{ name: string; capacity: number; price?: number }>;
        slug: string;
    };
}>();

const form = useForm({
    name: '',
    email: '',
    phone: '',
    ticket_type: props.event.ticket_types?.[0]?.name || 'General Admission',
});

const isSuccess = ref(false);
const registeredEmail = ref('');
const registeredPrice = ref(0);

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

const submit = () => {
    const selectedType = props.event.ticket_types?.find(t => t.name === form.ticket_type);
    registeredPrice.value = selectedType ? (selectedType.price || 0) : 0;
    
    form.post(register({ slug: props.event.slug }).url, {
        onSuccess: () => {
            registeredEmail.value = form.email;
            isSuccess.value = true;
            form.reset();
        },
    });
};

const hasDuplicateError = computed(() => {
    return form.errors.email && form.errors.email.includes('already registered');
});
</script>

<template>
    <Head :title="`${event.name} - Registration`" />

    <div class="min-h-screen bg-background text-ink font-body flex flex-col justify-between py-12 px-6 md:px-12">
        <div class="max-w-xl mx-auto w-full space-y-8">
            <!-- Back to Home -->
            <div class="text-left">
                <Link href="/" class="font-mono text-xs uppercase text-muted hover:text-ink transition-colors">
                    &larr; Back to counters
                </Link>
            </div>

            <!-- SUCCESS STATE: Ejecting Ticket Animation -->
            <div v-if="isSuccess" class="space-y-6">
                <div class="text-center space-y-2">
                    <span class="font-mono text-xs uppercase text-confirmed tracking-widest bg-confirmed/10 px-3 py-1 rounded">
                        CONFIRMED & ISSUED
                    </span>
                    <h2 class="font-display text-4xl uppercase tracking-wider text-ink mt-2">YOU ARE IN!</h2>
                    <p class="font-body text-sm text-muted">
                        Your registration is complete. We've ejected your ticket and sent it to your inbox.
                    </p>
                    <div class="mt-4 p-4 bg-paper border border-stub-line rounded flex justify-between items-center max-w-sm mx-auto">
                        <span class="font-mono text-xs uppercase tracking-widest text-muted">TICKET PRICE:</span>
                        <span class="font-display text-2xl text-ink">
                            {{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'LKR' }).format(registeredPrice) }}
                        </span>
                    </div>
                </div>

                <TicketStub :animate="true" class="w-full">
                  <template #main>
                    <div class="space-y-4">
                      <div>
                        <span class="text-[9px] uppercase font-mono text-muted tracking-wider">EVENT PASS</span>
                        <h4 class="font-display text-2xl uppercase tracking-wide leading-none text-ink mt-1">
                          {{ event.name }}
                        </h4>
                      </div>
                      <div>
                        <span class="text-[9px] uppercase font-mono text-muted tracking-wider">DELIVERED TO</span>
                        <p class="font-mono text-xs font-semibold text-ink truncate mt-0.5">{{ registeredEmail }}</p>
                      </div>
                      <div class="pt-2 border-t border-dashed border-stub-line flex items-center justify-between">
                        <div>
                          <span class="text-[9px] uppercase font-mono text-muted tracking-wider">STATUS</span>
                          <p class="font-mono text-xs font-semibold uppercase text-confirmed">TICKET SENT</p>
                        </div>
                        <span class="font-mono text-[9px] text-muted">CHECK INBOX</span>
                      </div>
                    </div>
                  </template>
                  <template #stub>
                    <div class="flex flex-col items-center justify-center space-y-2">
                      <!-- Checked in Ink Stamp confirm -->
                      <div class="w-16 h-16 border-4 border-dashed border-confirmed rounded-full flex items-center justify-center rotate-12 select-none">
                        <span class="font-display text-xs text-confirmed font-bold tracking-widest uppercase">OK</span>
                      </div>
                      <span class="font-mono text-[8px] uppercase tracking-wider text-muted mt-2">
                        SHOW QR AT DOOR
                      </span>
                    </div>
                  </template>
                </TicketStub>

                <div class="text-center pt-4">
                    <button @click="isSuccess = false" class="font-mono text-xs uppercase text-stamp hover:text-ink transition-colors underline decoration-dashed">
                        Register another person
                    </button>
                </div>
            </div>

            <!-- REGISTRATION STATE -->
            <div v-else class="space-y-8">
                <!-- Header Card -->
                <div class="bg-paper border border-stub-line p-8 rounded-lg relative overflow-hidden">
                    <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line"></div>
                    <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line"></div>

                    <div class="space-y-4">
                        <div class="font-mono text-xs text-muted mb-2 tracking-wider">
                            {{ formattedDate }} // {{ formattedTime }}
                        </div>
                        
                        <h1 class="font-display text-4xl uppercase tracking-wider text-ink">
                            {{ event.name }}
                        </h1>
                        
                        <p class="text-sm text-muted leading-relaxed">
                            {{ event.description || 'No description provided.' }}
                        </p>

                        <div class="pt-4 border-t border-dashed border-stub-line">
                            <span class="text-[9px] uppercase font-mono text-muted tracking-wider block">VENUE</span>
                            <span class="text-xs font-mono font-medium text-ink">{{ event.venue }}</span>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-paper border border-stub-line p-8 rounded-lg">
                    <h2 class="font-display text-2xl uppercase tracking-wider mb-6 text-ink">
                        TICKET APPLICATION
                    </h2>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">FULL NAME</label>
                            <input 
                                v-model="form.name" 
                                type="text" 
                                required
                                placeholder="JANE DOE"
                                class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink placeholder-muted/60 focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp transition"
                            />
                            <p v-if="form.errors.name" class="mt-2 font-mono text-xs text-stamp uppercase">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">EMAIL ADDRESS</label>
                            <input 
                                v-model="form.email" 
                                type="email" 
                                required
                                placeholder="JANE@EXAMPLE.COM"
                                class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink placeholder-muted/60 focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp transition"
                            />
                            <p v-if="form.errors.email" class="mt-2 font-mono text-xs text-stamp uppercase">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">PHONE NUMBER</label>
                            <input 
                                v-model="form.phone" 
                                type="tel" 
                                required
                                placeholder="+1 (555) 000-0000"
                                class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink placeholder-muted/60 focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp transition"
                            />
                            <p v-if="form.errors.phone" class="mt-2 font-mono text-xs text-stamp uppercase">{{ form.errors.phone }}</p>
                        </div>

                        <!-- Ticket Type Selector -->
                        <div v-if="event.ticket_types && event.ticket_types.length > 0">
                            <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">TICKET TYPE</label>
                            <select 
                                v-model="form.ticket_type"
                                required
                                class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp transition"
                            >
                                <option v-for="type in event.ticket_types" :key="type.name" :value="type.name">
                                    {{ type.name.toUpperCase() }} &mdash; {{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'LKR' }).format(type.price || 0) }}
                                </option>
                            </select>
                            <p v-if="form.errors.ticket_type" class="mt-2 font-mono text-xs text-stamp uppercase">{{ form.errors.ticket_type }}</p>
                        </div>

                        <!-- Duplicate Registration Helper -->
                        <div v-if="hasDuplicateError" class="p-4 bg-stamp/5 border border-stamp/20 rounded-[4px] space-y-3">
                            <p class="font-mono text-xs text-stamp uppercase">
                                You are already registered for this event.
                            </p>
                            <Link
                                :href="`/tickets/resend?email=${encodeURIComponent(form.email)}`"
                                class="inline-block bg-stamp hover:bg-ink text-paper font-mono text-xs uppercase px-4 py-2 rounded-[4px] tracking-wider transition-colors duration-150"
                            >
                                Resend My Ticket
                            </Link>
                        </div>

                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full py-4 px-6 rounded-[4px] font-mono text-xs uppercase tracking-widest bg-stamp hover:bg-ink text-paper transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <span v-if="form.processing" class="w-4 h-4 border-2 border-paper border-t-transparent rounded-full animate-spin"></span>
                            {{ form.processing ? 'ISSUING TICKET...' : 'APPLY FOR TICKET' }}
                        </button>
                    </form>
                </div>

                <!-- Footer link -->
                <div class="text-center font-mono text-[10px] uppercase">
                    <Link href="/tickets/resend" class="text-muted hover:text-ink transition-colors">
                        Lost your ticket? Go to Resend Desk &rarr;
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
