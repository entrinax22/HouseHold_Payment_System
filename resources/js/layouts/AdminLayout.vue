<template>
    <div class="flex min-h-screen bg-gradient-to-br from-emerald-50 via-white to-cyan-50">
        <!-- ✅ Mobile Toggle Button -->
        <button
            @click="toggleSidebar"
            class="fixed top-4 left-4 z-50 rounded-lg bg-emerald-600 p-2 text-white shadow-md focus:outline-none md:hidden"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path v-if="!isSidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- ✅ Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-40 flex h-screen w-64 flex-col overflow-hidden bg-gradient-to-b from-emerald-500 to-teal-600 text-white shadow-xl transition-transform duration-300 md:relative md:translate-x-0',
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
            ]"
        >
            <!-- Header -->
            <div class="flex-shrink-0 border-b border-emerald-400/30 bg-white/10 p-6">
                <span class="bg-gradient-to-r from-white to-emerald-100 bg-clip-text text-2xl font-bold text-transparent"> CPS Admin Panel </span>
            </div>

            <!-- Navigation - Scrollable Area -->
            <nav class="flex-1 overflow-y-auto p-4" style="scrollbar-width: thin; scrollbar-color: rgba(255, 255, 255, 0.3) transparent">
                <!-- Platforms -->
                <div class="mb-6">
                    <div class="mb-3 text-xs font-semibold tracking-wider text-emerald-200 uppercase">Platforms</div>
                    <ul class="space-y-2">
                        <li>
                            <Link
                                :href="route('dashboard')"
                                class="flex items-center gap-3 rounded-lg p-3 transition-all duration-200 hover:scale-105 hover:bg-white/20"
                            >
                                <span class="text-lg">👤</span>
                                <span class="font-medium">Dashboard</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('home')"
                                class="flex items-center gap-3 rounded-lg p-3 transition-all duration-200 hover:scale-105 hover:bg-white/20"
                            >
                                <span class="text-lg">🏠</span>
                                <span class="font-medium">Home</span>
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Tables -->
                <div class="mb-6">
                    <div class="mb-3 text-xs font-semibold tracking-wider text-emerald-200 uppercase">Tables</div>
                    <ul class="space-y-2">
                        <li>
                            <Link
                                :href="route('admin.users.index')"
                                class="flex items-center gap-3 rounded-lg p-3 transition-all duration-200 hover:scale-105 hover:bg-white/20"
                            >
                                <span class="text-lg">👥</span>
                                <span class="font-medium">Users</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('contributions.index')"
                                class="flex items-center gap-3 rounded-lg p-3 transition-all duration-200 hover:scale-105 hover:bg-white/20"
                            >
                                <span class="text-lg">💰</span>
                                <span class="font-medium">Contributions</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('payments.index')"
                                class="flex items-center gap-3 rounded-lg p-3 transition-all duration-200 hover:scale-105 hover:bg-white/20"
                            >
                                <span class="text-lg">🧾</span>
                                <span class="font-medium">Payments</span>
                            </Link>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Footer - Always visible at bottom -->
            <div class="flex-shrink-0 border-t border-emerald-400/30 bg-white/10 p-4">
                <div v-if="user" class="mb-3 flex items-center gap-3 rounded-lg bg-white/20 p-3">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-white/20">
                        <span class="text-xl">👨‍💼</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="truncate font-medium text-white">{{ user.name }}</div>
                        <div class="truncate text-xs text-emerald-100">{{ user.email }}</div>
                    </div>
                </div>
                <button
                    @click="logout"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-red-500 to-pink-500 py-3 font-medium text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-red-600 hover:to-pink-600"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <!-- ✅ Main Content -->
        <div class="flex min-h-screen flex-1 flex-col">
            <div class="flex-1 bg-white/80 p-6 md:p-8">
                <slot />
            </div>
            <Toast :message="toastMessage" :type="toastType" />
        </div>

        <!-- ✅ Overlay (for mobile) -->
        <div v-if="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 z-30 bg-black/40 backdrop-blur-sm md:hidden"></div>
    </div>
</template>

<script setup>
import Toast from '@/components/Toast.vue';
import { notify, toastMessage, toastType } from '@/composables/useToast';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const isSidebarOpen = ref(window.innerWidth >= 768); // ✅ show sidebar by default on desktop

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const logout = () => {
    router.post('/logout');
    isSidebarOpen.value = false;
};

watch(
    () => page.props.flash,
    (flash) => {
        if (flash.success) notify(flash.success, 'success');
        if (flash.error) notify(flash.error, 'error');
    },
    { immediate: true, deep: true },
);
</script>
