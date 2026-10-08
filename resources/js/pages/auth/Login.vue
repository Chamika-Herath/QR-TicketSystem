<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'STAFF SIGN IN',
        description: 'Enter your credentials at the checkout desk to gain system access.',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const showPassword = ref(false);
</script>

<template>
    <Head title="Staff Sign In" />

    <div v-if="status" class="p-4 bg-confirmed/10 border border-confirmed/20 text-confirmed rounded-[4px] font-mono text-xs uppercase text-center">
        {{ status }}
    </div>

    <!-- Form component wrapping Laravel Wayfinder actions -->
    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="space-y-6 text-ink font-body"
    >
        <div>
            <label class="block font-mono text-[10px] uppercase tracking-wider text-muted mb-2">EMAIL ADDRESS</label>
            <input 
                id="email"
                type="email" 
                name="email"
                required
                autofocus
                placeholder="EMAIL@EXAMPLE.COM"
                class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 text-sm text-ink placeholder-muted/60 focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp transition"
            />
            <p v-if="errors.email" class="mt-2 font-mono text-xs text-stamp uppercase">{{ errors.email }}</p>
        </div>

        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="font-mono text-[10px] uppercase tracking-wider text-muted">PASSWORD</label>
                <Link
                    v-if="canResetPassword"
                    :href="request()"
                    class="font-mono text-[10px] uppercase text-stamp hover:text-ink transition-colors"
                >
                    FORGOT PASSWORD?
                </Link>
            </div>
            
            <div class="relative">
                <input 
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    required
                    placeholder="PASSWORD"
                    class="w-full bg-paper border border-stub-line rounded-[4px] px-4 py-3 pr-10 text-sm text-ink placeholder-muted/60 focus:outline-none focus:border-stamp focus:ring-1 focus:ring-stamp transition"
                />
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
            <p v-if="errors.password" class="mt-2 font-mono text-xs text-stamp uppercase">{{ errors.password }}</p>
        </div>

        <div class="flex items-center justify-between">
            <label for="remember" class="flex items-center gap-2 cursor-pointer select-none">
                <input 
                    id="remember" 
                    type="checkbox" 
                    name="remember" 
                    class="rounded-[4px] border-stub-line text-stamp focus:ring-stamp bg-paper h-4 w-4"
                />
                <span class="font-mono text-[10px] uppercase text-muted">REMEMBER ME</span>
            </label>
        </div>

        <button 
            type="submit" 
            :disabled="processing"
            class="w-full py-4 px-6 rounded-[4px] font-mono text-xs uppercase tracking-widest bg-stamp hover:bg-ink text-paper transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
        >
            <span v-if="processing" class="w-4 h-4 border-2 border-paper border-t-transparent rounded-full animate-spin"></span>
            {{ processing ? 'ACCESS DESK' : 'ACCESS DESK' }}
        </button>

    </Form>
</template>
