<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

// Example mock data
const chartData = [12000, 18000, 15000, 22000, 25000, 30000, 28000, 26000, 29000, 31000, 27000, 35000];
const chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

const contributions = [
    { payer: 'John Doe', amount: '₱1,500.00', method: 'Cash', date: '2024-08-20' },
    { payer: 'Jane Smith', amount: '₱2,000.00', method: 'PayPal', date: '2024-08-21' },
    { payer: 'Alice Johnson', amount: '₱1,200.00', method: 'Cash', date: '2024-08-22' },
    { payer: 'Bob Lee', amount: '₱3,000.00', method: 'PayPal', date: '2024-08-23' },
];
</script>

<template>
    <Head title="Contribution Dashboard" />

    <div class="flex min-h-screen">
        <div class="flex-1">
            <AdminLayout>
                <div class="flex flex-1 flex-col items-center space-y-6 bg-gradient-to-br from-blue-50/50 via-white to-purple-50/50 p-8 pt-6">
                    <!-- Header -->
                    <div class="mx-auto flex w-full max-w-7xl items-center justify-between">
                        <h2 class="text-4xl font-bold tracking-tight text-gray-800">Contribution Dashboard</h2>
                        <button
                            class="inline-flex items-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl focus:ring-4 focus:ring-blue-200 focus:outline-none"
                        >
                            📊 Download Report
                        </button>
                    </div>

                    <!-- Stats -->
                    <div class="mx-auto grid w-full max-w-7xl gap-6 md:grid-cols-3 lg:grid-cols-4">
                        <div
                            class="rounded-xl border-2 border-emerald-200 bg-gradient-to-br from-emerald-50 to-emerald-100 p-6 shadow-lg transition-all duration-200 hover:scale-105 hover:shadow-xl"
                        >
                            <div class="mb-1 text-sm font-semibold text-emerald-700">Total Contributions</div>
                            <div class="text-3xl font-bold text-emerald-800">₱125,450.00</div>
                            <div class="text-xs font-medium text-emerald-600">+12% from last month</div>
                        </div>
                        <div
                            class="rounded-xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-6 shadow-lg transition-all duration-200 hover:scale-105 hover:shadow-xl"
                        >
                            <div class="mb-1 text-sm font-semibold text-blue-700">Community Contributions</div>
                            <div class="text-3xl font-bold text-blue-800">₱65,000.00</div>
                            <div class="text-xs font-medium text-blue-600">+8 new entries</div>
                        </div>
                        <div
                            class="rounded-xl border-2 border-purple-200 bg-gradient-to-br from-purple-50 to-purple-100 p-6 shadow-lg transition-all duration-200 hover:scale-105 hover:shadow-xl"
                        >
                            <div class="mb-1 text-sm font-semibold text-purple-700">User Contributions</div>
                            <div class="text-3xl font-bold text-purple-800">₱60,450.00</div>
                            <div class="text-xs font-medium text-purple-600">-2 from last month</div>
                        </div>
                        <div
                            class="rounded-xl border-2 border-orange-200 bg-gradient-to-br from-orange-50 to-orange-100 p-6 shadow-lg transition-all duration-200 hover:scale-105 hover:shadow-xl"
                        >
                            <div class="mb-1 text-sm font-semibold text-orange-700">Pending Payments</div>
                            <div class="text-3xl font-bold text-orange-800">15</div>
                            <div class="text-xs font-medium text-orange-600">Awaiting processing</div>
                        </div>
                    </div>

                    <!-- Chart + Recent Contributions -->
                    <div class="mx-auto grid w-full max-w-7xl gap-6 md:grid-cols-2 lg:grid-cols-7">
                        <!-- Chart -->
                        <div class="col-span-5 rounded-xl border-2 border-teal-200 bg-gradient-to-br from-teal-50 to-cyan-50 p-6 shadow-lg">
                            <div class="mb-6 flex items-center justify-between">
                                <h3 class="text-xl font-bold text-teal-800">Monthly Contributions</h3>
                                <span class="rounded-full bg-teal-100 px-3 py-1 text-sm font-medium text-teal-600">Last 12 months</span>
                            </div>
                            <div class="relative flex h-72 w-full">
                                <!-- Y-axis indicators -->
                                <div class="mr-4 flex h-full flex-col justify-between py-1 text-xs font-medium text-teal-600">
                                    <span>{{ Math.max(...chartData) }}</span>
                                    <span>{{ Math.round(Math.max(...chartData) * 0.75) }}</span>
                                    <span>{{ Math.round(Math.max(...chartData) * 0.5) }}</span>
                                    <span>{{ Math.round(Math.max(...chartData) * 0.25) }}</span>
                                    <span>0</span>
                                </div>
                                <!-- Bars + X-axis -->
                                <div class="flex h-full w-full flex-1 flex-col">
                                    <div class="flex w-full flex-1 items-end gap-1">
                                        <div
                                            v-for="(value, idx) in chartData"
                                            :key="idx"
                                            class="min-w-0 flex-1 cursor-pointer rounded-t-lg bg-gradient-to-t from-teal-500 to-cyan-400 transition-all duration-300 hover:from-teal-600 hover:to-cyan-500"
                                            :style="{ height: (value / Math.max(...chartData)) * 100 + '%' }"
                                            :title="`₱${value.toLocaleString()}`"
                                        ></div>
                                    </div>
                                    <div class="mt-3 flex w-full text-xs font-medium text-teal-600">
                                        <div v-for="(label, idx) in chartLabels" :key="idx" class="min-w-0 flex-1 truncate text-center">
                                            {{ label }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Contributions -->
                        <div class="col-span-2 rounded-xl border-2 border-indigo-200 bg-gradient-to-br from-indigo-50 to-purple-50 p-6 shadow-lg">
                            <div class="mb-6 flex items-center justify-between">
                                <h3 class="text-xl font-bold text-indigo-800">Recent Contributions</h3>
                                <span class="rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-600">Latest</span>
                            </div>
                            <ul class="space-y-4">
                                <li
                                    v-for="(c, idx) in contributions"
                                    :key="idx"
                                    class="rounded-lg border border-indigo-100 bg-white/80 p-4 transition-all duration-200 hover:bg-white hover:shadow-md"
                                >
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-semibold text-gray-800">{{ c.payer }}</div>
                                            <div class="mt-1 text-xs text-gray-600">
                                                {{ c.date }} ·
                                                <span class="font-medium" :class="c.method === 'PayPal' ? 'text-blue-600' : 'text-emerald-600'">
                                                    {{ c.method }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-lg font-bold text-indigo-700">{{ c.amount }}</div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </AdminLayout>
        </div>
    </div>
</template>
