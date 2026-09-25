<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';
import { logout } from '@/routes';

const page = usePage();
const emit = defineEmits(['close-sidebar']);

const mainNavItems = [
    {
        title: 'DASHBOARD',
        href: '/dashboard',
        icon: '📊',
    },
    {
        title: 'EVENTS',
        href: '/events',
        icon: '🎟️',
    },
    {
        title: 'SCANNER',
        href: '/scanner',
        icon: '📷',
    },
    {
        title: 'INCOME',
        href: '/income',
        icon: '💰',
    },
];

const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <aside class="w-64 bg-paper border-r border-stub-line flex flex-col justify-between h-screen sticky top-0 font-body select-none">
        
        <!-- Header / Logo -->
        <div class="p-6 border-b border-dashed border-stub-line">
            <Link href="/dashboard" class="flex flex-col gap-1">
                <span class="font-display text-2xl uppercase tracking-wider text-stamp">
                    TICKET // COUNTER
                </span>
                <span class="font-mono text-[9px] uppercase tracking-widest text-muted">
                    ENTRY OPERATIONS
                </span>
            </Link>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 py-8 px-4 space-y-2">
            <template v-for="item in mainNavItems" :key="item.title">
                <Link
                    v-if="!(page.props.auth?.user?.role === 'scanner' && (item.title === 'DASHBOARD' || item.title === 'INCOME'))"
                    :href="item.href"
                    @click="$emit('close-sidebar')"
                    class="flex items-center gap-3 px-4 py-3 rounded-[4px] font-mono text-xs uppercase tracking-wider hover:bg-stamp/10 hover:text-stamp transition-colors duration-150"
                    :class="[
                        $page.url.startsWith(item.href) ? 'bg-stamp text-paper hover:bg-stamp hover:text-paper' : 'text-ink'
                    ]"
                >
                    <span>{{ item.icon }}</span>
                    <span>{{ item.title }}</span>
                </Link>
            </template>

            <!-- Admin & Staff User Management link -->
            <Link
                v-if="page.props.auth?.user?.role === 'admin' || page.props.auth?.user?.role === 'staff'"
                href="/users"
                @click="$emit('close-sidebar')"
                class="flex items-center gap-3 px-4 py-3 rounded-[4px] font-mono text-xs uppercase tracking-wider hover:bg-stamp/10 hover:text-stamp transition-colors duration-150"
                :class="[
                    $page.url.startsWith('/users') ? 'bg-stamp text-paper hover:bg-stamp hover:text-paper' : 'text-ink'
                ]"
            >
                <span>👥</span>
                <span>USERS</span>
            </Link>
        </div>

        <!-- Footer / User profile & logout -->
        <div class="p-6 border-t border-dashed border-stub-line space-y-4">
            <div class="flex flex-col">
                <span class="font-display text-base uppercase text-ink truncate">
                    {{ page.props.auth?.user?.name }}
                </span>
                <span class="font-mono text-[9px] uppercase text-muted tracking-wider truncate">
                    ROLE: {{ page.props.auth?.user?.role }}
                </span>
            </div>

            <Link
                :href="logout()"
                @click="handleLogout"
                as="button"
                method="post"
                class="w-full text-left font-mono text-[10px] uppercase text-stamp hover:text-ink transition-colors focus:outline-none block pt-2 border-t border-stub-line"
            >
                [ LEAVE COUNTER &rarr; ]
            </Link>
        </div>

    </aside>
</template>
