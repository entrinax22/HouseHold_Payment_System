<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <!-- Background with gradient overlay -->
    <div
        class="relative flex min-h-screen items-center justify-center bg-cover bg-center"
        style="background-image: url('/system_images/bg_homes.png')"
    >
        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-700/80 via-green-700/70 to-teal-800/80"></div>

        <!-- Card -->
        <div class="relative z-10 w-full max-w-md rounded-2xl bg-white/95 p-8 shadow-2xl backdrop-blur-md">
            <!-- Branding -->
            <div class="mb-8 flex flex-col items-center text-center">
                <img src="/logo.png" alt="logo" class="mb-4 h-24 w-24 object-contain" />
                <h1 class="text-3xl font-extrabold text-emerald-700">Smart Homes Trinidad</h1>
                <p class="mt-2 text-sm text-gray-600">Securely sign in to continue</p>
            </div>

            <!-- Status -->
            <div v-if="status" class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-center text-sm font-medium text-emerald-600">
                {{ status }}
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-5">
                    <!-- Email -->
                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email address</label>
                        <input
                            id="email"
                            type="email"
                            required
                            autofocus
                            autocomplete="email"
                            v-model="form.email"
                            placeholder="email@example.com"
                            class="w-full rounded-xl border-2 border-emerald-200 px-4 py-3 text-gray-700 placeholder-gray-400 transition-all outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                            <TextLink
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-sm font-medium text-emerald-600 hover:text-emerald-700"
                            >
                                Forgot password?
                            </TextLink>
                        </div>
                        <input
                            id="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            v-model="form.password"
                            placeholder="Enter your password"
                            class="w-full rounded-xl border-2 border-emerald-200 px-4 py-3 text-gray-700 placeholder-gray-400 transition-all outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center space-x-3">
                        <input
                            id="remember"
                            type="checkbox"
                            v-model="form.remember"
                            class="h-4 w-4 rounded border-2 border-emerald-300 bg-gray-100 text-emerald-600 focus:ring-2 focus:ring-emerald-500"
                        />
                        <label for="remember" class="text-sm font-medium text-gray-700">Remember me</label>
                    </div>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4 font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-emerald-700 hover:to-teal-700 hover:shadow-xl focus:ring-4 focus:ring-emerald-200 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100"
                >
                    <LoaderCircle v-if="form.processing" class="mr-2 inline h-5 w-5 animate-spin" />
                    <span v-if="form.processing">Signing in...</span>
                    <span v-else>Log in</span>
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <span class="text-gray-600">Don't have an account? </span>
                <TextLink :href="route('register')" class="font-semibold text-emerald-600 hover:text-emerald-700"> Sign up here </TextLink>
            </div>
        </div>
    </div>
</template>
