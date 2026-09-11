<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, onMounted, onUnmounted } from 'vue';
import { Html5QrcodeScanner } from 'html5-qrcode';
import ScanResultBanner from '@/components/ScanResultBanner.vue';

const props = defineProps<{
    todayCount: number;
}>();

interface Attendee {
    name: string;
    email: string;
    event_name: string;
}

const localCount = ref(props.todayCount);
const manualToken = ref('');
const isSubmittingManual = ref(false);
const manualError = ref('');

const scanResult = ref<{
    status: 'valid' | 'already_checked_in' | 'invalid' | null;
    attendeeName?: string;
    checkInTime?: string;
    message?: string;
}>({
    status: null,
});

let html5QrcodeScanner: Html5QrcodeScanner | null = null;
const isScanning = ref(true);
const showManualInput = ref(false);

const processCheckIn = async (token: string) => {
    try {
        const res = await fetch(`/api/checkin/${token}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
            }
        });

        const data = await res.json();
        
        if (res.ok) {
            scanResult.value = {
                status: 'valid',
                attendeeName: data.attendee?.name || 'Attendee',
            };
            localCount.value++;
            manualToken.value = '';
        } else if (res.status === 409) {
            scanResult.value = {
                status: 'already_checked_in',
                attendeeName: data.attendee?.name || 'Attendee',
                checkInTime: data.scanned_at || 'Just now',
            };
        } else {
            scanResult.value = {
                status: 'invalid',
                message: data.message || 'Not a valid ticket for this event.',
            };
        }
    } catch (e) {
        scanResult.value = {
            status: 'invalid',
            message: 'Network verification failed.',
        };
    }

    // Auto reset result after 2.5 seconds and resume scanning
    setTimeout(() => {
        scanResult.value = { status: null };
        isScanning.value = true;
    }, 2500);
};

const onScanSuccess = async (decodedText: string) => {
    if (!isScanning.value) return;
    isScanning.value = false;
    await processCheckIn(decodedText);
};

const onScanFailure = (error: any) => {
    // Quietly log scanner failures as QR code scanning reads frame-by-frame
};

const submitManual = async () => {
    if (!manualToken.value.trim()) return;
    isSubmittingManual.value = true;
    manualError.value = '';
    await processCheckIn(manualToken.value.trim());
    isSubmittingManual.value = false;
};

onMounted(() => {
    html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader",
        { fps: 10, qrbox: { width: 220, height: 220 } },
        /* verbose= */ false
    );
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
});

onUnmounted(() => {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear().catch(err => console.error("Error clearing scanner", err));
    }
});
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Scanner', href: '/scanner' }]">
        <Head title="Staff QR Check-In Scanner" />

        <div class="min-h-[calc(100vh-64px)] bg-background text-ink font-body p-6 max-w-xl mx-auto space-y-6 flex flex-col justify-between relative">
            
            <!-- Floating Live Counter -->
            <div class="absolute top-4 right-4 bg-ink text-paper font-mono text-[10px] uppercase px-3 py-1.5 rounded-[4px] tracking-wider z-20 shadow-none border border-stub-line">
                Checked in today: <span class="text-stamp font-bold">{{ localCount }}</span>
            </div>

            <!-- Top info card -->
            <div class="text-left space-y-2 pt-6">
                <span class="font-mono text-xs uppercase text-stamp tracking-widest bg-stamp/10 px-3 py-1 rounded">
                    COUNTER ENTRY SCANNER
                </span>
                <h1 class="font-display text-4xl uppercase tracking-wider text-ink mt-2">LIVE ENTRY SCAN</h1>
                <p class="font-body text-xs text-muted">
                    Align the ticket's QR code within the bounding box below. Camera permissions are required to operate.
                </p>
            </div>

            <!-- Scan Outcome Overlay (over top portion) -->
            <div class="relative min-h-[140px] flex items-center justify-center">
                <div v-if="!scanResult.status" class="text-center font-mono text-xs text-muted/60 animate-pulse border border-dashed border-stub-line w-full py-8 rounded-lg">
                    [ WAITING FOR CODE FRAME ]
                </div>
                <ScanResultBanner
                    v-else
                    :status="scanResult.status"
                    :attendeeName="scanResult.attendeeName"
                    :checkInTime="scanResult.checkInTime"
                    :message="scanResult.message"
                    class="w-full"
                />
            </div>

            <!-- QR Reader Bounding Box -->
            <div class="w-full bg-paper border border-stub-line rounded-lg p-4 flex flex-col items-center justify-center overflow-hidden">
                <div id="qr-reader" class="w-full rounded-md overflow-hidden bg-black/5"></div>
            </div>

            <!-- Manual Token Fallback Collapsible -->
            <div class="border-t border-stub-line pt-6">
                <button 
                    @click="showManualInput = !showManualInput"
                    class="w-full flex items-center justify-between font-mono text-xs uppercase text-muted hover:text-ink transition-colors focus:outline-none"
                >
                    <span>[ {{ showManualInput ? 'HIDE' : 'SHOW' }} MANUAL ENTRY FALLBACK ]</span>
                    <span>{{ showManualInput ? '&uarr;' : '&darr;' }}</span>
                </button>

                <div v-if="showManualInput" class="mt-4 space-y-3">
                    <div class="flex gap-2">
                        <input 
                            v-model="manualToken"
                            type="text"
                            placeholder="ENTER TICKET REF TOKEN"
                            class="flex-1 bg-paper border border-stub-line rounded-[4px] px-3 py-2 text-xs font-mono uppercase text-ink placeholder-muted/60 focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp"
                        />
                        <button 
                            @click="submitManual"
                            :disabled="isSubmittingManual"
                            class="bg-stamp hover:bg-ink text-paper font-mono text-xs uppercase px-4 py-2 rounded-[4px] tracking-wider transition disabled:opacity-50"
                        >
                            VERIFY
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<style>
/* Clean custom styles for html5-qrcode */
#qr-reader {
    border: none !important;
    background: transparent !important;
}
#qr-reader__dashboard_section_csr button, 
#qr-reader__dashboard_section_swaplink,
#qr-reader button {
    background-color: var(--stamp) !important;
    color: var(--paper) !important;
    border: none !important;
    padding: 8px 16px !important;
    border-radius: 4px !important;
    font-family: 'JetBrains Mono', 'IBM Plex Mono', monospace !important;
    font-size: 10px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
    font-weight: 500 !important;
    cursor: pointer !important;
    margin: 5px !important;
    transition: background-color 150ms ease !important;
}
#qr-reader__dashboard_section_csr button:hover,
#qr-reader button:hover {
    background-color: var(--ink) !important;
}
#qr-reader__status_span {
    color: var(--muted) !important;
    font-family: 'JetBrains Mono', 'IBM Plex Mono', monospace !important;
    font-size: 10px !important;
}
</style>
