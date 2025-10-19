<template>
    <nav class="flex items-center justify-between border-b-2 border-green-200 bg-gradient-to-r from-green-50 to-emerald-100 px-6 py-4 shadow-sm">
        <!-- Left: Logo and Title -->
        <div class="flex items-center gap-3">
            <img src="/logo.png" alt="Logo" class="h-10 w-10 rounded-full shadow-md" />
            <a href="#" class="text-lg font-bold tracking-tight text-green-700 hover:text-green-800 md:text-xl"> Smart Homes Trinidad </a>
        </div>

        <!-- Mobile Menu Button -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="block rounded-lg p-2 text-green-700 hover:bg-green-100 focus:outline-none md:hidden">
            <svg
                v-if="!mobileMenuOpen"
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Desktop Menu -->
        <ul class="hidden items-center gap-6 md:flex">
            <li v-if="!user"><Link href="/login" :class="linkClass('/login')">Login</Link></li>
            <li v-if="!user"><Link href="/register" :class="linkClass('/register')">Register</Link></li>

            <li v-if="user"><Link href="/" :class="linkClass('/')">HomePage</Link></li>
            <li v-if="user">
                <Link :href="route('contributions.userContributions')" :class="linkClass('/contributions/my-contributions')"> My Contributions </Link>
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
                            <Link :href="route('dashboard')" class="block px-4 py-3 hover:bg-green-50 hover:text-green-700"> Dashboard </Link>
                        </li>
                        <li>
                            <Link :href="route('users.profile')" class="block px-4 py-3 hover:bg-green-50 hover:text-green-700"> Profile </Link>
                        </li>
                        <li>
                            <button @click="logout" class="w-full px-4 py-3 text-left hover:bg-red-50 hover:text-red-700">Logout</button>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>

        <!-- ✅ Mobile Dropdown Menu -->
        <transition name="fade">
            <div v-if="mobileMenuOpen" class="absolute top-16 left-0 z-40 w-full border-t border-green-200 bg-white/90 backdrop-blur-sm md:hidden">
                <ul class="flex flex-col space-y-2 p-4 text-center">
                    <li v-if="!user"><Link href="/login" :class="mobileLinkClass('/login')">Login</Link></li>
                    <li v-if="!user"><Link href="/register" :class="mobileLinkClass('/register')">Register</Link></li>

                    <li v-if="user"><Link href="/" :class="mobileLinkClass('/')">HomePage</Link></li>
                    <li v-if="user">
                        <Link :href="route('contributions.userContributions')" :class="mobileLinkClass('/contributions/my-contributions')">
                            My Contributions
                        </Link>
                    </li>
                    <li v-if="user"><Link :href="route('users.profile')" :class="mobileLinkClass('/profile')">Profile</Link></li>
                    <li v-if="user && user.role === 'admin'">
                        <Link :href="route('dashboard')" :class="mobileLinkClass('/dashboard')">Dashboard</Link>
                    </li>
                    <li v-if="user">
                        <button @click="logout" class="w-full rounded-lg bg-red-100 px-4 py-2 text-red-700 hover:bg-red-200">Logout</button>
                    </li>
                </ul>
            </div>
        </transition>
    </nav>

    <!-- ✅ Main layout -->
    <div class="flex min-h-screen flex-col bg-gradient-to-br from-green-50 via-white to-emerald-50">
        <main class="flex-1">
            <slot />
        </main>

        <footer class="border-t-2 border-green-200 bg-gradient-to-r from-green-50 to-emerald-50 py-6 text-center text-sm text-gray-600">
            &copy; {{ new Date().getFullYear() }} Neighborhood Household Payment System. All rights reserved.
        </footer>
    </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const currentUrl = computed(() => page.url);

const dropdownOpen = ref(false);
const mobileMenuOpen = ref(false);

const toggleDropdown = () => (dropdownOpen.value = !dropdownOpen.value);

const logout = () => {
    router.post('/logout');
    mobileMenuOpen.value = false;
};

const linkClass = (path) => {
    return currentUrl.value === path || (path !== '/' && currentUrl.value.startsWith(path))
        ? 'font-semibold text-blue-700 bg-white/70 px-3 py-2 rounded-lg border border-blue-200'
        : 'font-medium text-blue-600 hover:text-blue-700 hover:bg-white/50 px-3 py-2 rounded-lg transition-all';
};

const mobileLinkClass = (path) => {
    return currentUrl.value === path
        ? 'block w-full rounded-lg bg-green-100 px-4 py-2 font-semibold text-green-700'
        : 'block w-full rounded-lg px-4 py-2 font-medium text-gray-700 hover:bg-green-50 hover:text-green-700';
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
