<template>
    <div class="flex min-h-screen bg-gradient-to-br from-emerald-50 via-white to-cyan-50">
        <!-- Sidebar -->
        <aside class="flex h-screen w-64 flex-col bg-gradient-to-b from-emerald-500 to-teal-600 text-white shadow-xl">
            <div class="border-b border-emerald-400/30 bg-white/10 p-6 text-2xl font-bold">
                <span class="bg-gradient-to-r from-white to-emerald-100 bg-clip-text text-transparent"> CPS Admin Panel </span>
            </div>
            <nav class="flex-1 overflow-y-auto p-4">
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
                <div class="mb-6">
                    <div class="mb-3 text-xs font-semibold tracking-wider text-emerald-200 uppercase">Tables</div>
                    <ul class="space-y-2">
                        <li>
                            <Link
                                href="#"
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
                <div>
                    <div class="mb-3 text-xs font-semibold tracking-wider text-emerald-200 uppercase">Reports</div>
                    <ul class="space-y-2">
                        <li>
                            <Link
                                href="#"
                                class="flex items-center gap-3 rounded-lg p-3 transition-all duration-200 hover:scale-105 hover:bg-white/20"
                            >
                                <span class="text-lg">📊</span>
                                <span class="font-medium">Summary</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="#"
                                class="flex items-center gap-3 rounded-lg p-3 transition-all duration-200 hover:scale-105 hover:bg-white/20"
                            >
                                <span class="text-lg">📅</span>
                                <span class="font-medium">Monthly</span>
                            </Link>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="mt-auto border-t border-emerald-400/30 bg-white/10 p-4">
                <div v-if="user" class="mb-3 flex items-center gap-3 rounded-lg bg-white/20 p-3">
                    <span class="text-2xl">👨‍💼</span>
                    <span class="font-medium text-white">{{ user.name }}</span>
                </div>
                <button
                    @click="logout"
                    class="flex w-full items-center justify-center gap-3 rounded-lg bg-gradient-to-r from-red-500 to-pink-500 py-3 font-medium text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-red-600 hover:to-pink-600"
                >
                    <span class="text-lg">🚪</span>
                    <span>Logout</span>
                </button>
            </div>
        </aside>
        <!-- Main Content -->
        <div class="flex-1 bg-white/80 backdrop-blur-sm">
            <slot />
            <Toast :message="toastMessage" :type="toastType" />
        </div>
    </div>
</template>

<script setup>
import Toast from '@/components/Toast.vue';
import { notify, toastMessage, toastType } from '@/composables/useToast';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const logout = () => {
    router.post('/logout');
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
