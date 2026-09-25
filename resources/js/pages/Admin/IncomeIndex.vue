<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    incomeData: Array<{
        id: number;
        name: string;
        status: string;
        capacity: number;
        attendees_count: number;
        total_income: number;
        breakdown: Array<{ ticket_type: string; count: number; total_revenue: number }>;
    }>;
    totalOverallIncome: number;
}>();
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Income', href: '/income' }]">
        <Head title="Income Reports" />

        <div class="p-6 max-w-7xl mx-auto space-y-6 text-ink font-body">
            
            <!-- Header bar -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 bg-paper border border-stub-line p-8 rounded-lg relative overflow-hidden">
                <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line"></div>
                <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line"></div>
                
                <div>
                    <span class="font-mono text-xs uppercase text-stamp tracking-widest bg-stamp/10 px-3 py-1 rounded">
                        FINANCE DESK
                    </span>
                    <h1 class="font-display text-4xl uppercase tracking-wider text-ink mt-2">INCOME REPORTS</h1>
                    <p class="font-body text-xs text-muted">View revenue generated across your events.</p>
                </div>

                <div class="text-right">
                    <p class="font-mono text-xs uppercase tracking-wider text-muted mb-1">TOTAL OVERALL REVENUE</p>
                    <p class="font-display text-4xl text-confirmed tracking-wider">
                        {{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'LKR' }).format(totalOverallIncome) }}
                    </p>
                </div>
            </div>

            <!-- List Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div 
                    v-for="event in incomeData" 
                    :key="event.id" 
                    class="bg-paper border border-stub-line rounded-lg p-6 flex flex-col relative overflow-hidden group hover:border-stamp transition-colors duration-200"
                >
                    <!-- Small ticket cutouts -->
                    <div class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-background rounded-full border border-stub-line"></div>
                    <div class="absolute -right-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-background rounded-full border border-stub-line"></div>

                    <div class="flex justify-between items-start mb-4">
                        <span :class="[
                            'px-2.5 py-0.5 rounded-[4px] font-mono text-[9px] uppercase tracking-wider border',
                            event.status === 'published' ? 'bg-confirmed/10 text-confirmed border-confirmed/20' : 'bg-stamp/10 text-stamp border-stamp/20'
                        ]">
                            {{ event.status }}
                        </span>
                        <span class="font-mono text-[9px] text-muted">CAPACITY: {{ event.capacity }}</span>
                    </div>

                    <h3 class="font-display text-2xl uppercase tracking-wide text-ink group-hover:text-stamp transition-colors flex-1">{{ event.name }}</h3>
                    
                    <div class="mt-4 space-y-2 border-t border-dashed border-stub-line pt-4 font-mono text-[10px] text-ink/80">
                        <div class="flex justify-between mb-2">
                            <span>👥 TICKETS ISSUED:</span>
                            <span class="font-medium text-ink">{{ event.attendees_count }} / {{ event.capacity }}</span>
                        </div>
                        
                        <!-- Breakdown -->
                        <div v-if="event.breakdown && event.breakdown.length > 0" class="space-y-1 mb-2 bg-background p-2 border border-dashed border-stub-line rounded">
                            <div v-for="cat in event.breakdown" :key="cat.ticket_type" class="flex justify-between items-center text-[9px] uppercase tracking-wider">
                                <span>{{ cat.ticket_type }} ({{ cat.count }})</span>
                                <span class="font-medium">{{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'LKR' }).format(cat.total_revenue) }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-2 border-t border-stub-line">
                            <span class="text-xs uppercase font-bold text-stamp tracking-widest">REVENUE:</span>
                            <span class="font-display text-2xl text-ink">
                                {{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'LKR' }).format(event.total_income || 0) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div v-if="incomeData.length === 0" class="text-center py-12 bg-paper border border-dashed border-stub-line rounded-lg">
                <p class="font-mono text-sm text-muted uppercase tracking-widest">No events found.</p>
            </div>

        </div>
    </AppLayout>
</template>
