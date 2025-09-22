<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

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
                <p class="mt-2 text-sm text-gray-600">Join our community today</p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <!-- Name -->
                    <div class="sm:col-span-2">
                        <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">Full Name</label>
                        <input
                            id="name"
                            type="text"
                            required
                            autofocus
                            autocomplete="name"
                            v-model="form.name"
                            placeholder="Enter your full name"
                            class="w-full rounded-xl border-2 border-emerald-200 px-4 py-3 text-gray-700 placeholder-gray-400 transition-all outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <!-- Email -->
                    <div class="sm:col-span-2">
                        <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email Address</label>
                        <input
                            id="email"
                            type="email"
                            required
                            autocomplete="email"
                            v-model="form.email"
                            placeholder="email@example.com"
                            class="w-full rounded-xl border-2 border-emerald-200 px-4 py-3 text-gray-700 placeholder-gray-400 transition-all outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">Password</label>
                        <input
                            id="password"
                            type="password"
                            required
                            autocomplete="new-password"
                            v-model="form.password"
                            placeholder="Create a strong password"
                            class="w-full rounded-xl border-2 border-emerald-200 px-4 py-3 text-gray-700 placeholder-gray-400 transition-all outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-gray-700">Confirm Password</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            required
                            autocomplete="new-password"
                            v-model="form.password_confirmation"
                            placeholder="Confirm your password"
                            class="w-full rounded-xl border-2 border-emerald-200 px-4 py-3 text-gray-700 placeholder-gray-400 transition-all outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        />
                        <InputError :message="form.errors.password_confirmation" />
                    </div>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4 font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-emerald-700 hover:to-teal-700 hover:shadow-xl focus:ring-4 focus:ring-emerald-200 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100"
                >
                    <LoaderCircle v-if="form.processing" class="mr-2 inline h-5 w-5 animate-spin" />
                    <span v-if="form.processing">Creating account...</span>
                    <span v-else>Create Account</span>
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <span class="text-gray-600">Already have an account? </span>
                <TextLink :href="route('login')" class="font-semibold text-emerald-600 hover:text-emerald-700"> Log in here </TextLink>
            </div>
        </div>
    </div>
</template>
