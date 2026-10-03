<script setup lang="ts">
import { Head, useForm, router, usePage, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';

const page = usePage();
const currentUserRole = page.props.auth?.user?.role || 'staff';

const props = defineProps<{
    users: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            role: string;
            creator: { name: string } | null;
            allowed_event_limit: number;
            allowed_ticket_limit: number;
        }>;
        links: Array<any>;
    };
    manager?: {
        id: number;
        name: string;
    } | null;
}>();

const isCreateOpen = ref(false);
const editingUser = ref<any>(null);
const showPassword = ref(false);

const form = useForm({
    name: '',
    email: '',
    role: currentUserRole === 'admin' && !props.manager ? 'staff' : 'scanner',
    allowed_event_limit: 5,
    allowed_ticket_limit: 100,
    password: '',
});

const editForm = useForm({
    name: '',
    email: '',
    role: currentUserRole === 'admin' ? 'staff' : 'scanner',
    allowed_event_limit: 5,
    allowed_ticket_limit: 100,
    password: '',
});

const submitCreate = () => {
    if (props.manager && currentUserRole === 'admin') {
        // If creating a scanner under a specific manager as admin, wait, we can't easily set created_by unless we pass it to the backend. 
        // Admin currently sets themselves as created_by. We'd have to alter store method.
        // For simplicity, if we are in manager view, maybe hide create operator or allow it.
        // Actually, let's let backend handle it or hide create button.
    }
    form.post('/users', {
        onSuccess: () => {
            isCreateOpen.value = false;
            form.reset();
        }
    });
};

const submitEdit = () => {
    editForm.put(`/users/${editingUser.value.id}`, {
        onSuccess: () => {
            editingUser.value = null;
            editForm.reset();
        }
    });
};

const editUser = (user: any) => {
    editingUser.value = user;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.role = user.role;
    editForm.allowed_event_limit = user.allowed_event_limit;
    editForm.allowed_ticket_limit = user.allowed_ticket_limit;
    editForm.password = '';
};

const deleteUser = (id: number) => {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        router.delete(`/users/${id}`);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Users', href: '/users' }]">
        <Head title="User Management" />

        <div class="p-6 max-w-7xl mx-auto space-y-6 text-ink font-body">
            
            <!-- Header bar -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 bg-paper border border-stub-line p-8 rounded-lg relative overflow-hidden">
                <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line"></div>
                <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-background rounded-full border border-stub-line"></div>
                
                <div>
                    <div class="flex items-center gap-4">
                        <Link 
                            v-if="manager" 
                            href="/users" 
                            class="font-mono text-[10px] uppercase text-stamp hover:text-ink transition border border-stub-line px-2 py-1 rounded"
                        >
                            &larr; Back
                        </Link>
                        <span class="font-mono text-xs uppercase text-stamp tracking-widest bg-stamp/10 px-3 py-1 rounded">
                            ADMIN DESK
                        </span>
                    </div>
                    <h1 class="font-display text-4xl uppercase tracking-wider text-ink mt-2">
                        <template v-if="$page.props.auth.user.role === 'admin' && !manager">USER MANAGEMENT</template>
                        <template v-else-if="manager">SCANNERS FOR {{ manager.name }}</template>
                        <template v-else>SCANNER MANAGEMENT</template>
                    </h1>
                    <p class="font-body text-xs text-muted">
                        <template v-if="$page.props.auth.user.role === 'admin' && !manager">Create operator accounts and allocate event creation limits.</template>
                        <template v-else>Manage ticket scanning staff.</template>
                    </p>
                </div>
                <button 
                    v-if="!manager"
                    @click="isCreateOpen = !isCreateOpen; editingUser = null"
                    class="bg-stamp hover:bg-ink text-paper font-mono text-xs uppercase px-5 py-3 rounded-[4px] tracking-wider transition-colors duration-150"
                >
                    Create Operator
                </button>
            </div>

            <!-- Create Form Drawer -->
            <div v-if="isCreateOpen" class="bg-paper border border-stub-line p-8 rounded-lg space-y-6">
                <h2 class="font-display text-2xl uppercase tracking-wider text-ink">New Operator Profile</h2>
                <form @submit.prevent="submitCreate" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">FULL NAME</label>
                        <input v-model="form.name" type="text" required class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" placeholder="STAFF OPERATOR" />
                        <p v-if="form.errors.name" class="font-mono text-xs text-stamp uppercase mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">EMAIL ADDRESS</label>
                        <input v-model="form.email" type="email" required class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" placeholder="OPERATOR@EXAMPLE.COM" />
                        <p v-if="form.errors.email" class="font-mono text-xs text-stamp uppercase mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">PASSWORD</label>
                        <div class="relative">
                            <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 pr-10 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" placeholder="MINIMUM 8 CHARACTERS" />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-muted hover:text-ink focus:outline-none"
                            >
                                <span class="font-mono text-[10px] uppercase tracking-tighter">
                                    {{ showPassword ? 'HIDE' : 'SHOW' }}
                                </span>
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="font-mono text-xs text-stamp uppercase mt-1">{{ form.errors.password }}</p>
                    </div>

                    <div v-if="$page.props.auth.user.role === 'admin'">
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">ROLE</label>
                        <select v-model="form.role" required class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp">
                            <option value="scanner">TICKET SCANNER</option>
                            <option v-if="$page.props.auth.user.role === 'admin'" value="staff">STAFF OPERATOR</option>
                            <option v-if="$page.props.auth.user.role === 'admin'" value="admin">ADMINISTRATOR</option>
                        </select>
                        <p v-if="form.errors.role" class="font-mono text-xs text-stamp uppercase mt-1">{{ form.errors.role }}</p>
                    </div>

                    <div v-if="$page.props.auth.user.role === 'admin'">
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">EVENT CREATION LIMIT</label>
                        <input v-model="form.allowed_event_limit" type="number" min="0" required class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" />
                        <p v-if="form.errors.allowed_event_limit" class="font-mono text-xs text-stamp uppercase mt-1">{{ form.errors.allowed_event_limit }}</p>
                    </div>

                    <div v-if="$page.props.auth.user.role === 'admin'">
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">TICKET ISSUANCE LIMIT</label>
                        <input v-model="form.allowed_ticket_limit" type="number" min="0" required class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" />
                        <p v-if="form.errors.allowed_ticket_limit" class="font-mono text-xs text-stamp uppercase mt-1">{{ form.errors.allowed_ticket_limit }}</p>
                    </div>

                    <div class="md:col-span-2 flex justify-end gap-4 border-t border-dashed border-stub-line pt-6">
                        <button type="button" @click="isCreateOpen = false" class="font-mono text-xs uppercase border border-stub-line hover:border-ink px-5 py-3 rounded-[4px] text-muted hover:text-ink transition">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="bg-stamp hover:bg-ink text-paper font-mono text-xs uppercase px-5 py-3 rounded-[4px] tracking-wider transition disabled:opacity-50">Create Profile</button>
                    </div>
                </form>
            </div>

            <!-- Edit Form Drawer -->
            <div v-if="editingUser" class="bg-paper border border-stub-line p-8 rounded-lg space-y-6">
                <h2 class="font-display text-2xl uppercase tracking-wider text-ink">Edit Operator Profile</h2>
                <form @submit.prevent="submitEdit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">FULL NAME</label>
                        <input v-model="editForm.name" type="text" required class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" />
                        <p v-if="editForm.errors.name" class="font-mono text-xs text-stamp uppercase mt-1">{{ editForm.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">EMAIL ADDRESS</label>
                        <input v-model="editForm.email" type="email" required class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" />
                        <p v-if="editForm.errors.email" class="font-mono text-xs text-stamp uppercase mt-1">{{ editForm.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">PASSWORD (LEAVE BLANK TO KEEP UNCHANGED)</label>
                        <div class="relative">
                            <input v-model="editForm.password" :type="showPassword ? 'text' : 'password'" class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 pr-10 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" placeholder="NEW PASSWORD" />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-muted hover:text-ink focus:outline-none"
                            >
                                <span class="font-mono text-[10px] uppercase tracking-tighter">
                                    {{ showPassword ? 'HIDE' : 'SHOW' }}
                                </span>
                            </button>
                        </div>
                        <p v-if="editForm.errors.password" class="font-mono text-xs text-stamp uppercase mt-1">{{ editForm.errors.password }}</p>
                    </div>

                    <div v-if="$page.props.auth.user.role === 'admin'">
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">ROLE</label>
                        <select v-model="editForm.role" required class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp">
                            <option value="scanner">TICKET SCANNER</option>
                            <option v-if="$page.props.auth.user.role === 'admin'" value="staff">STAFF OPERATOR</option>
                            <option v-if="$page.props.auth.user.role === 'admin'" value="admin">ADMINISTRATOR</option>
                        </select>
                        <p v-if="editForm.errors.role" class="font-mono text-xs text-stamp uppercase mt-1">{{ editForm.errors.role }}</p>
                    </div>

                    <div v-if="$page.props.auth.user.role === 'admin'">
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">EVENT CREATION LIMIT</label>
                        <input v-model="editForm.allowed_event_limit" type="number" min="0" required class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" />
                        <p v-if="editForm.errors.allowed_event_limit" class="font-mono text-xs text-stamp uppercase mt-1">{{ editForm.errors.allowed_event_limit }}</p>
                    </div>

                    <div v-if="$page.props.auth.user.role === 'admin'">
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">TICKET ISSUANCE LIMIT</label>
                        <input v-model="editForm.allowed_ticket_limit" type="number" min="0" required class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp" />
                        <p v-if="editForm.errors.allowed_ticket_limit" class="font-mono text-xs text-stamp uppercase mt-1">{{ editForm.errors.allowed_ticket_limit }}</p>
                    </div>

                    <div class="md:col-span-2 flex justify-end gap-4 border-t border-dashed border-stub-line pt-6">
                        <button type="button" @click="editingUser = null" class="font-mono text-xs uppercase border border-stub-line hover:border-ink px-5 py-3 rounded-[4px] text-muted hover:text-ink transition">Cancel</button>
                        <button type="submit" :disabled="editForm.processing" class="bg-stamp hover:bg-ink text-paper font-mono text-xs uppercase px-5 py-3 rounded-[4px] tracking-wider transition disabled:opacity-50">Save Changes</button>
                    </div>
                </form>
            </div>

            <!-- Operators Table -->
            <div class="bg-paper border border-stub-line rounded-lg overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap md:whitespace-normal">
                    <thead>
                        <tr class="font-mono text-[10px] uppercase text-muted tracking-wider border-b border-stub-line">
                            <th class="p-4">Name</th>
                            <th class="p-4">Email</th>
                            <th class="p-4" v-if="$page.props.auth.user.role === 'admin'">Role</th>
                            <th class="p-4" v-if="$page.props.auth.user.role === 'admin'">Manager</th>
                            <th class="p-4" v-if="$page.props.auth.user.role === 'admin'">Allowed Events</th>
                            <th class="p-4" v-if="$page.props.auth.user.role === 'admin'">Ticket Limit</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stub-line/60">
                        <tr v-for="user in users.data" :key="user.id" class="hover:bg-paper/40 transition">
                            <td class="p-4 font-semibold text-sm">{{ user.name.toUpperCase() }}</td>
                            <td class="p-4 text-xs text-ink">{{ user.email.toUpperCase() }}</td>
                            <td class="p-4" v-if="$page.props.auth.user.role === 'admin'">
                                <span :class="[
                                    'px-2.5 py-0.5 rounded-[4px] font-mono text-[9px] uppercase tracking-wider border',
                                    user.role === 'admin' ? 'bg-confirmed/10 text-confirmed border-confirmed/20' : 'bg-stamp/10 text-stamp border-stamp/20'
                                ]">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="p-4 text-xs text-muted" v-if="$page.props.auth.user.role === 'admin'">
                                {{ user.creator ? user.creator.name.toUpperCase() : 'SYSTEM ADMIN' }}
                            </td>
                            <td class="p-4 font-mono text-xs" v-if="$page.props.auth.user.role === 'admin'">
                                {{ user.role === 'admin' ? 'UNLIMITED' : user.allowed_event_limit }}
                            </td>
                            <td class="p-4 font-mono text-xs" v-if="$page.props.auth.user.role === 'admin'">
                                {{ user.role === 'admin' ? 'UNLIMITED' : user.allowed_ticket_limit }}
                            </td>
                            <td class="p-4 text-right flex items-center justify-end gap-3">
                                <Link 
                                    v-if="user.role === 'staff' && $page.props.auth.user.role === 'admin' && !manager"
                                    :href="`/users?manager_id=${user.id}`"
                                    class="font-mono text-[10px] uppercase border border-stub-line hover:border-ink hover:text-ink text-muted px-3 py-1.5 rounded-[4px] transition"
                                >
                                    Scanners
                                </Link>
                                <button 
                                    @click="editUser(user)"
                                    class="font-mono text-[10px] uppercase border border-stub-line hover:border-ink hover:text-ink text-muted px-3 py-1.5 rounded-[4px] transition"
                                >
                                    Edit
                                </button>
                                <button 
                                    v-if="$page.props.auth.user.id !== user.id"
                                    @click="deleteUser(user.id)"
                                    class="p-1.5 rounded-[4px] border border-stamp/25 hover:bg-stamp/10 text-stamp transition"
                                >
                                    🗑️
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </AppLayout>
</template>
