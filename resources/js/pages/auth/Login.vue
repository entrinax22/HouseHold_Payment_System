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

    <div class="grid min-h-screen grid-cols-1 md:grid-cols-2">
        <!-- Left Background Image Section -->
        <div class="hidden bg-cover bg-center md:block" style="background-image: url('/images/bg.jpg')">
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-emerald-500/80 to-teal-600/80 p-8">
                <div class="max-w-md text-center text-white">
                    <h2 class="mb-6 text-4xl font-bold drop-shadow-lg">Welcome to Our Community</h2>
                    <p class="text-lg leading-relaxed drop-shadow">
                        Access your account to manage your housing contributions and view important updates.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Login Form Section -->
        <div class="flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-purple-50 p-6">
            <div class="w-full max-w-md space-y-8">
                <div class="text-center">
                    <h1 class="mb-2 text-3xl font-bold text-gray-800">Welcome Back!</h1>
                    <p class="text-base text-gray-600">Enter your credentials to access your account.</p>
                </div>

                <div v-if="status" class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-center text-sm font-medium text-emerald-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-5">
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
                                class="w-full rounded-xl border-2 border-blue-200 px-4 py-3 text-gray-700 placeholder-gray-400 transition-all outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                                <TextLink
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-sm font-medium text-blue-600 hover:text-blue-700"
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
                                class="w-full rounded-xl border-2 border-blue-200 px-4 py-3 text-gray-700 placeholder-gray-400 transition-all outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div class="flex items-center space-x-3">
                            <input
                                id="remember"
                                type="checkbox"
                                v-model="form.remember"
                                class="h-4 w-4 rounded border-2 border-blue-300 bg-gray-100 text-blue-600 focus:ring-2 focus:ring-blue-500"
                            />
                            <label for="remember" class="text-sm font-medium text-gray-700">Remember me</label>
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl focus:ring-4 focus:ring-blue-200 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100"
                    >
                        <LoaderCircle v-if="form.processing" class="mr-2 inline h-5 w-5 animate-spin" />
                        <span v-if="form.processing">Signing in...</span>
                        <span v-else>Log in</span>
                    </button>
                </form>

                <div class="text-center">
                    <span class="text-gray-600">Don't have an account? </span>
                    <TextLink :href="route('register')" class="font-semibold text-emerald-600 hover:text-emerald-700"> Sign up here </TextLink>
                </div>
            </div>
        </div>
    </div>
</template>
