<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { resend } from '@/routes/tickets';

defineProps<{
    events: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    event_id: '',
    email: '',
});

const successMessage = ref('');
const errorMessage = ref('');

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const emailParam = params.get('email');
    if (emailParam) {
        form.email = emailParam;
    }
});

const submit = () => {
    successMessage.value = '';
    errorMessage.value = '';
    form.post(resend().url, {
        onSuccess: () => {
            successMessage.value = 'Ticket email has been successfully re-queued. Please check your inbox shortly!';
            form.reset('email');
        },
        onError: (errors) => {
            errorMessage.value = errors.email || 'Failed to locate registration details.';
        }
    });
};
</script>

<template>
    <Head title="Ticket Resend Desk" />

    <div class="min-h-screen bg-background text-ink font-body flex flex-col justify-center py-12 px-6 md:px-12">
        <div class="max-w-md mx-auto w-full space-y-8">
            
            <div class="bg-paper border border-stub-line rounded-lg p-8 relative overflow-hidden">
                <!-- Decorative notch cutouts -->
                <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line"></div>
                <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line"></div>

                <div class="relative space-y-6">
                    <div class="text-center space-y-2">
                        <span class="font-mono text-xs uppercase text-stamp tracking-widest bg-stamp/10 px-3 py-1 rounded">
                            LOST AND FOUND DESK
                        </span>
                        <h1 class="font-display text-4xl uppercase tracking-wider text-ink">
                            RESEND TICKET
                        </h1>
                        <p class="font-body text-xs text-muted">
                            Enter your email and select the event to re-queue your digital ticket.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">SELECT EVENT</label>
                            <select 
                                v-model="form.event_id" 
                                required
                                class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp transition"
                            >
                                <option value="" disabled>CHOOSE AN EVENT</option>
                                <option v-for="event in events" :key="event.id" :value="event.id">
                                    {{ event.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.event_id" class="mt-2 font-mono text-xs text-stamp uppercase">{{ form.errors.event_id }}</p>
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

                        <!-- Alerts -->
                        <div v-if="successMessage" class="p-4 bg-confirmed/10 border border-confirmed/20 text-confirmed rounded-[4px] font-mono text-xs uppercase">
                            {{ successMessage }}
                        </div>

                        <div v-if="errorMessage" class="p-4 bg-stamp/10 border border-stamp/20 text-stamp rounded-[4px] font-mono text-xs uppercase">
                            {{ errorMessage }}
                        </div>

                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full py-4 px-6 rounded-[4px] font-mono text-xs uppercase tracking-widest bg-stamp hover:bg-ink text-paper transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <span v-if="form.processing" class="w-4 h-4 border-2 border-paper border-t-transparent rounded-full animate-spin"></span>
                            {{ form.processing ? 'LOCATING...' : 'RESEND MY TICKET' }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center font-mono text-[10px] uppercase">
                <Link href="/" class="text-muted hover:text-ink transition">
                    &larr; Return to main menu
                </Link>
            </div>

        </div>
    </div>
</template>
