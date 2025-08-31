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

    <div class="grid min-h-screen grid-cols-1 md:grid-cols-2">
        <!-- Left Background Image Section -->
        <div class="hidden bg-cover bg-center md:block" style="background-image: url('/images/bg.jpg')">
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-emerald-500/80 to-teal-600/80 p-8">
                <div class="max-w-md text-center text-white">
                    <h2 class="mb-6 text-4xl font-bold drop-shadow-lg">Join Our Community</h2>
                    <p class="text-lg leading-relaxed drop-shadow">
                        Register to start tracking your housing contributions and access important information.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Registration Form Section -->
        <div class="flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-purple-50 p-6">
            <div class="w-full max-w-md space-y-8">
                <div class="text-center">
                    <h1 class="mb-2 text-3xl font-bold text-gray-800">Create Your Account</h1>
                    <p class="text-base text-gray-600">Enter your details below to join our community.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-5">
                        <div>
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

                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email Address</label>
                            <input
                                id="email"
                                type="email"
                                required
                                autocomplete="email"
                                v-model="form.email"
                                placeholder="email@example.com"
                                class="w-full rounded-xl border-2 border-blue-200 px-4 py-3 text-gray-700 placeholder-gray-400 transition-all outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">Password</label>
                            <input
                                id="password"
                                type="password"
                                required
                                autocomplete="new-password"
                                v-model="form.password"
                                placeholder="Create a strong password"
                                class="w-full rounded-xl border-2 border-purple-200 px-4 py-3 text-gray-700 placeholder-gray-400 transition-all outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100"
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-gray-700">Confirm Password</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
                                v-model="form.password_confirmation"
                                placeholder="Confirm your password"
                                class="w-full rounded-xl border-2 border-indigo-200 px-4 py-3 text-gray-700 placeholder-gray-400 transition-all outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                            />
                            <InputError :message="form.errors.password_confirmation" />
                        </div>
                    </div>

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

                <div class="text-center">
                    <span class="text-gray-600">Already have an account? </span>
                    <TextLink :href="route('login')" class="font-semibold text-blue-600 hover:text-blue-700"> Log in here </TextLink>
                </div>
            </div>
        </div>
    </div>
</template>
