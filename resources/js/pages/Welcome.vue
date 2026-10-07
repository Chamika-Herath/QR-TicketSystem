<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import TicketStub from '@/components/TicketStub.vue';
import EventCard from '@/components/EventCard.vue';
import EmptyState from '@/components/EmptyState.vue';

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

defineProps<{
  events: EventData[];
}>();
</script>

<template>
  <Head title="Real Ticket Counter" />

  <div class="min-h-screen bg-background text-ink flex flex-col selection:bg-stamp selection:text-paper">
    <!-- Navbar / Header -->
    <header class="border-b border-stub-line py-4 px-6 md:px-12 bg-paper/80 backdrop-blur-md sticky top-0 z-50">
      <div class="max-w-6xl mx-auto flex items-center justify-between">
        <Link href="/" class="flex items-center gap-2 group">
          <span class="font-display text-2xl uppercase tracking-wider text-stamp group-hover:text-ink transition-colors duration-150">
            TICKET // COUNTER
          </span>
        </Link>
        <nav class="flex items-center gap-6">
          <Link
            v-if="$page.props.auth?.user"
            href="/dashboard"
            class="font-mono text-xs uppercase text-ink hover:text-stamp tracking-wider transition-colors duration-150"
          >
            Dashboard
          </Link>
          <template v-else>
            <Link
              href="/login"
              class="font-mono text-xs uppercase text-ink hover:text-stamp tracking-wider transition-colors duration-150"
            >
              Sign In
            </Link>
          </template>
        </nav>
      </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-6 md:px-12 py-12 md:py-20 space-y-24">
      <!-- 1. Hero Section -->
      <section class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <div class="lg:col-span-6 space-y-6">
          <span class="font-mono text-xs uppercase text-stamp tracking-widest bg-stamp/10 px-3 py-1 rounded">
            THE PRINT-SHOP EXPERIENCE
          </span>
          <h1 class="font-display text-5xl md:text-6xl lg:text-7xl uppercase tracking-tight leading-none text-ink">
            TICKETS THAT <span class="text-stamp">SCAN</span>,<br />NOT SPREADSHEETS THAT STALL
          </h1>
          <p class="font-body text-base md:text-lg text-muted max-w-lg leading-relaxed">
            A vintage-inspired event registration and check-in desk. Attendees get clean, ink-confirmable ticket stubs. Staff check in guests with a single, one-handed mobile scanner.
          </p>
          <div class="flex flex-wrap gap-4 pt-2">
            <a
              href="#events"
              class="bg-stamp hover:bg-ink text-paper font-mono text-sm uppercase px-6 py-3.5 rounded-[4px] tracking-wider transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-stamp focus:ring-offset-2"
            >
              Browse Events
            </a>
            <Link
              href="/tickets/resend"
              class="border border-stub-line hover:border-ink text-ink font-mono text-sm uppercase px-6 py-3.5 rounded-[4px] tracking-wider transition-colors duration-150 focus:outline-none"
            >
              Resend Ticket
            </Link>
          </div>
        </div>

        <!-- Animated Ticket Stub Mockup -->
        <div class="lg:col-span-6 flex justify-center items-center">
          <TicketStub :animate="true" class="w-full max-w-[480px]">
            <template #main>
              <div class="space-y-4">
                <div>
                  <span class="text-[9px] uppercase font-mono text-muted tracking-wider">EVENT PRESENTS</span>
                  <h4 class="font-display text-3xl uppercase tracking-wide leading-none text-ink mt-1">
                    COUNTER LAUNCH CELEBRATION
                  </h4>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <span class="text-[9px] uppercase font-mono text-muted tracking-wider">VENUE</span>
                    <p class="font-mono text-xs font-semibold uppercase text-ink mt-0.5">THE OLD PRINT SHOP</p>
                  </div>
                  <div>
                    <span class="text-[9px] uppercase font-mono text-muted tracking-wider">DATE</span>
                    <p class="font-mono text-xs font-semibold uppercase text-ink mt-0.5">OCT 24, 2026</p>
                  </div>
                </div>
                <div class="pt-2 border-t border-dashed border-stub-line flex items-center justify-between">
                  <div>
                    <span class="text-[9px] uppercase font-mono text-muted tracking-wider">ADMIT</span>
                    <p class="font-mono text-xs font-semibold uppercase text-stamp">ONE GENERAL</p>
                  </div>
                  <span class="font-mono text-[10px] text-muted tracking-wider">N°. 00018-A</span>
                </div>
              </div>
            </template>
            <template #stub>
              <div class="flex flex-col items-center justify-center space-y-3 py-2">
                <!-- Inline SVG QR Code Mockup -->
                <svg width="90" height="90" viewBox="0 0 100 100" class="text-ink">
                  <!-- QR Code border/corners -->
                  <rect x="0" y="0" width="30" height="30" fill="none" stroke="currentColor" stroke-width="6" />
                  <rect x="7" y="7" width="16" height="16" fill="currentColor" />
                  <rect x="70" y="0" width="30" height="30" fill="none" stroke="currentColor" stroke-width="6" />
                  <rect x="77" y="7" width="16" height="16" fill="currentColor" />
                  <rect x="0" y="70" width="30" height="30" fill="none" stroke="currentColor" stroke-width="6" />
                  <rect x="7" y="77" width="16" height="16" fill="currentColor" />
                  <!-- Random blocks -->
                  <rect x="40" y="0" width="10" height="10" fill="currentColor" />
                  <rect x="50" y="10" width="10" height="20" fill="currentColor" />
                  <rect x="35" y="45" width="20" height="10" fill="currentColor" />
                  <rect x="10" y="40" width="15" height="15" fill="currentColor" />
                  <rect x="80" y="40" width="15" height="15" fill="currentColor" />
                  <rect x="45" y="75" width="20" height="20" fill="currentColor" />
                  <rect x="75" y="75" width="15" height="10" fill="currentColor" />
                </svg>
                <div class="text-[9px] uppercase font-mono text-muted tracking-wider mt-1">
                  VOID IF DETACHED
                </div>
              </div>
            </template>
          </TicketStub>
        </div>
      </section>

      <!-- Events Section -->
      <section id="events" class="border-t border-stub-line pt-20">
        <div class="mb-12">
          <h2 class="font-display text-4xl uppercase tracking-wider text-ink">UPCOMING EVENTS</h2>
          <p class="font-body text-sm text-muted mt-2">Find and register for open events.</p>
        </div>

        <div v-if="events.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <EventCard 
            v-for="event in events" 
            :key="event.id"
            :event="event"
          />
        </div>
        <div v-else class="py-12">
          <EmptyState 
            title="NO UPCOMING EVENTS"
            message="There are currently no public events scheduled. Please check back later."
          />
        </div>
      </section>

      <!-- 2. How It Works Section -->
      <section class="border-t border-stub-line pt-20">
        <div class="text-center max-w-xl mx-auto space-y-4 mb-16">
          <h2 class="font-display text-4xl uppercase tracking-wider">A SIMPLE, PRINTED WORKFLOW</h2>
          <p class="font-body text-sm text-muted">
            Designed for tactile confidence and functional speed at real-world entry desks.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Step 1 -->
          <div class="bg-paper border border-stub-line p-6 rounded-lg space-y-4 relative">
            <span class="font-display text-6xl text-stub-line absolute right-6 top-4 select-none">01</span>
            <div class="w-12 h-12 bg-stamp/10 text-stamp flex items-center justify-center rounded">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 stroke-[1.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </div>
            <h3 class="font-display text-xl uppercase tracking-wider text-ink mt-2">1. REGISTER ONLINE</h3>
            <p class="font-body text-xs text-muted leading-relaxed">
              Submit attendee details through the event checkout counter. Double-registration protections prevent errors.
            </p>
          </div>

          <!-- Step 2 -->
          <div class="bg-paper border border-stub-line p-6 rounded-lg space-y-4 relative">
            <span class="font-display text-6xl text-stub-line absolute right-6 top-4 select-none">02</span>
            <div class="w-12 h-12 bg-stamp/10 text-stamp flex items-center justify-center rounded">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 stroke-[1.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="font-display text-xl uppercase tracking-wider text-ink mt-2">2. CLAIM BY EMAIL</h3>
            <p class="font-body text-xs text-muted leading-relaxed">
              Your ticket stub is emailed instantly as a composited image containing your unique check-in QR code.
            </p>
          </div>

          <!-- Step 3 -->
          <div class="bg-paper border border-stub-line p-6 rounded-lg space-y-4 relative">
            <span class="font-display text-6xl text-stub-line absolute right-6 top-4 select-none">03</span>
            <div class="w-12 h-12 bg-stamp/10 text-stamp flex items-center justify-center rounded">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 stroke-[1.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
              </svg>
            </div>
            <h3 class="font-display text-xl uppercase tracking-wider text-ink mt-2">3. SCAN AT ENTRY</h3>
            <p class="font-body text-xs text-muted leading-relaxed">
              Show your QR code on arrival. Staff scan the ticket via mobile browser check-in for instant confirmation.
            </p>
          </div>
        </div>
      </section>



      <!-- 4. For Organizers -->
      <section class="border-t border-stub-line pt-20 text-center max-w-2xl mx-auto space-y-6">
        <h2 class="font-display text-3xl uppercase tracking-wider text-ink">ARE YOU AN ORGANIZER?</h2>
        <p class="font-body text-sm text-muted">
          Manage event details, monitor check-in statistics, export attendee reports, or scan tickets on-site.
        </p>
        <div class="flex justify-center pt-2">
          <Link
            href="/login"
            class="border border-stub-line hover:border-ink font-mono text-xs uppercase px-5 py-2.5 rounded-[4px] text-muted hover:text-ink transition-colors duration-150"
          >
            Access Dashboard Account &rarr;
          </Link>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-stub-line bg-paper py-8 px-6 mt-20 font-mono text-[10px] uppercase text-muted">
      <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
          &copy; 2026 Ticket Counter System. Made for speed & stability.
        </div>
        <div class="flex gap-6">
          <Link href="/login" class="hover:text-ink transition-colors duration-150">Admin Sign In</Link>
        </div>
      </div>
    </footer>
  </div>
</template>
