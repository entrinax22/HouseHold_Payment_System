<template>
    <nav class="flex items-center justify-between border-b-2 border-green-200 bg-gradient-to-r from-green-50 to-emerald-100 px-8 py-4 shadow-sm">
        <div class="flex items-center gap-3">
            <!-- ✅ Logo -->
            <img src="/logo.png" alt="Logo" class="h-10 w-10 rounded-full shadow-md" />
            <a href="#" class="text-xl font-bold tracking-tight text-green-700 transition-colors hover:text-green-800"> Smart Homes Trinidad </a>
        </div>
        <div>
            <ul class="flex items-center gap-6">
                <li v-if="!user">
                    <Link href="/login" :class="linkClass('/login')">Login</Link>
                </li>
                <li v-if="!user">
                    <Link href="/register" :class="linkClass('/register')">Register</Link>
                </li>
                <li v-if="user">
                    <Link href="/" :class="linkClass('/')">HomePage</Link>
                </li>
                <li v-if="user">
                    <Link :href="route('contributions.userContributions')" :class="linkClass('/contributions/my-contributions')">
                        My Contributions
                    </Link>
                </li>

                <!-- User Dropdown -->
                <li v-if="user" class="relative">
                    <button
                        @click="toggleDropdown"
                        class="flex items-center gap-2 rounded-lg border border-green-200 bg-white/50 px-3 py-2 transition-all hover:border-green-300 hover:bg-white/80"
                    >
                        <span class="font-medium text-green-700">{{ user.name }}</span>
                        <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div
                        v-if="dropdownOpen"
                        class="absolute right-0 z-50 mt-2 w-48 overflow-hidden rounded-xl border-2 border-green-200 bg-white shadow-xl"
                    >
                        <ul class="py-2 text-sm">
                            <li v-if="user && user.role === 'admin'">
                                <Link
                                    :href="route('dashboard')"
                                    class="block px-4 py-3 text-gray-700 transition-colors hover:bg-green-50 hover:text-green-700"
                                >
                                    Dashboard
                                </Link>
                            </li>
                            <li>
                                <Link href="/profile" class="block px-4 py-3 text-gray-700 transition-colors hover:bg-green-50 hover:text-green-700">
                                    Profile
                                </Link>
                            </li>
                            <li>
                                <button
                                    @click="logout"
                                    class="w-full px-4 py-3 text-left text-gray-700 transition-colors hover:bg-red-50 hover:text-red-700"
                                >
                                    Logout
                                </button>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Keep the rest as is -->
    <div class="flex min-h-screen flex-col bg-gradient-to-br from-green-50 via-white to-emerald-50">
        <main class="flex-1">
            <slot />
            <Toast :message="toastMessage" :type="toastType" />
        </main>
        <footer class="border-t-2 border-green-200 bg-gradient-to-r from-green-50 to-emerald-50 py-6 text-center text-sm text-gray-600">
            &copy; {{ new Date().getFullYear() }} Neighborhood Household Payment System. All rights reserved.
        </footer>
    </div>
</template>

<script setup>
import Toast from '@/components/Toast.vue';
import { notify, toastMessage, toastType } from '@/composables/useToast';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const currentUrl = computed(() => page.url);
const dropdownOpen = ref(false);
const toggleDropdown = () => (dropdownOpen.value = !dropdownOpen.value);
const logout = () => {
    router.post('/logout');
};
const linkClass = (path) => {
    return currentUrl.value === path || (path !== '/' && currentUrl.value.startsWith(path))
        ? 'font-semibold text-blue-700 bg-white/70 px-3 py-2 rounded-lg border border-blue-200'
        : 'font-medium text-blue-600 hover:text-blue-700 hover:bg-white/50 px-3 py-2 rounded-lg transition-all';
};

watch(
    () => page.props.flash,
    (flash) => {
        if (flash.success) {
            notify(flash.success, 'success');
        }
        if (flash.error) {
            notify(flash.error, 'error');
        }
    },
    { immediate: true, deep: true },
);
</script>

<style scoped>
/* Add custom styles here if needed */
</style>
