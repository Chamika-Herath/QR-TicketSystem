<script setup lang="ts">
import AppSidebar from '@/components/AppSidebar.vue';
import { Toaster } from '@/components/ui/sonner';
import type { BreadcrumbItem } from '@/types';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { ref } from 'vue';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const isSidebarOpen = ref(false);
</script>

<template>
    <div class="flex min-h-screen bg-background text-ink font-body selection:bg-stamp selection:text-paper relative">
        
        <!-- Mobile Sidebar Overlay -->
        <div 
            v-if="isSidebarOpen" 
            class="fixed inset-0 bg-ink/20 backdrop-blur-sm z-40 md:hidden"
            @click="isSidebarOpen = false"
        ></div>

        <!-- Static/Mobile Sidebar -->
        <div :class="[
            'fixed inset-y-0 left-0 z-50 transform transition-transform duration-300 ease-in-out md:static md:translate-x-0',
            isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
        ]">
            <AppSidebar @close-sidebar="isSidebarOpen = false" />
        </div>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 bg-background w-full">
            <!-- Header bar / Breadcrumbs -->
            <header class="flex h-16 shrink-0 items-center border-b border-stub-line px-4 md:px-6 bg-paper gap-4 sticky top-0 z-30">
                <button 
                    @click="isSidebarOpen = true"
                    class="md:hidden p-2 -ml-2 text-ink hover:text-stamp hover:bg-stamp/10 rounded transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
                <div class="flex items-center gap-2 overflow-x-auto whitespace-nowrap scrollbar-hide">
                    <template v-if="breadcrumbs && breadcrumbs.length > 0">
                        <Breadcrumbs :breadcrumbs="breadcrumbs" />
                    </template>
                </div>
            </header>

            <!-- Page content slot -->
            <div class="flex-1 overflow-x-hidden">
                <slot />
            </div>
        </main>
        
        <Toaster />
    </div>
</template>
