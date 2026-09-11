<script setup lang="ts">
import AppSidebar from '@/components/AppSidebar.vue';
import { Toaster } from '@/components/ui/sonner';
import type { BreadcrumbItem } from '@/types';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <div class="flex min-h-screen bg-background text-ink font-body selection:bg-stamp selection:text-paper">
        <!-- Static Sidebar (fixed width) -->
        <AppSidebar />

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 bg-background">
            <!-- Header bar / Breadcrumbs -->
            <header class="flex h-16 shrink-0 items-center justify-between border-b border-stub-line px-6 bg-paper">
                <div class="flex items-center gap-2">
                    <template v-if="breadcrumbs && breadcrumbs.length > 0">
                        <Breadcrumbs :breadcrumbs="breadcrumbs" />
                    </template>
                </div>
            </header>

            <!-- Page content slot -->
            <div class="flex-1 overflow-y-auto">
                <slot />
            </div>
        </main>
        
        <Toaster />
    </div>
</template>
