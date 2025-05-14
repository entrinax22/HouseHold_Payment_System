<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

// Mock data for chart and sales
const chartData = [400, 550, 650, 590, 800, 810, 560, 700, 900, 750, 820, 600];
const chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const sales = [
    { name: 'John Doe', amount: '₱25,000.00', date: '2024-06-01' },
    { name: 'Jane Smith', amount: '₱18,000.00', date: '2024-06-02' },
    { name: 'Alice Johnson', amount: '₱32,000.00', date: '2024-06-03' },
    { name: 'Bob Lee', amount: '₱15,000.00', date: '2024-06-04' },
];

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>
<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-4 p-8 pt-6 flex flex-col items-center">
            <div class="flex items-center justify-between space-y-2 w-full max-w-7xl mx-auto">
                <h2 class="text-3xl font-bold tracking-tight">Dashboard</h2>
                <div class="flex items-center space-x-2">
                    <button
                        class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-black shadow transition-colors hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                        Download
                    </button>
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-4 w-full max-w-7xl mx-auto">
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
                    <div class="text-sm font-medium text-muted-foreground">Total Payments</div>
                    <div class="text-2xl font-bold">₱12,450.00</div>
                    <div class="text-xs text-muted-foreground">+8.5% from last month</div>
                </div>
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
                    <div class="text-sm font-medium text-muted-foreground">Active Households</div>
                    <div class="text-2xl font-bold">320</div>
                    <div class="text-xs text-muted-foreground">+12 new this month</div>
                </div>
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
                    <div class="text-sm font-medium text-muted-foreground">Pending Payments</div>
                    <div class="text-2xl font-bold">27</div>
                    <div class="text-xs text-muted-foreground">-5 from last month</div>
                </div>
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
                    <div class="text-sm font-medium text-muted-foreground">Total Members</div>
                    <div class="text-2xl font-bold">1,150</div>
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7 w-full max-w-7xl mx-auto">
                <div class="col-span-5 rounded-lg border bg-card p-4 shadow-sm">
                    <!-- Simple Chart Mockup with Y-axis indicators -->
                    <div class="mb-2 flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Overview</h3>
                        <span class="text-xs text-muted-foreground">Last 12 months</span>
                    </div>
                    <div class="relative h-72 flex w-full">
                        <!-- Y-axis indicators -->
                        <div class="flex flex-col justify-between h-full mr-2 py-1 text-xs text-muted-foreground">
                            <span>{{ Math.max(...chartData) }}</span>
                            <span>{{ Math.round(Math.max(...chartData) * 0.75) }}</span>
                            <span>{{ Math.round(Math.max(...chartData) * 0.5) }}</span>
                            <span>{{ Math.round(Math.max(...chartData) * 0.25) }}</span>
                            <span>0</span>
                        </div>
                        <!-- Chart bars and X-axis labels in a column flex -->
                        <div class="flex flex-col flex-1 h-full w-full">
                            <div class="flex items-end flex-1 w-full">
                                <div v-for="(value, idx) in chartData" :key="idx"
                                    class="bg-primary/70 rounded transition-all duration-300 mx-1 flex-1"
                                    :style="{
                                        height: (value / Math.max(...chartData) * 100) + '%',
                                        minWidth: '0'
                                    }"
                                    :title="value">
                                </div>
                            </div>
                            <div class="flex w-full mt-2">
                                <div v-for="(label, idx) in chartLabels" :key="idx" class="flex-1 text-center truncate min-w-0">
                                    {{ label }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-2 rounded-lg border bg-card p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Recent Sales</h3>
                        <span class="text-xs text-muted-foreground">Today</span>
                    </div>
                    <ul class="divide-y divide-muted-foreground/10">
                        <li v-for="(sale, idx) in sales" :key="idx" class="py-3 flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ sale.name }}</div>
                                <div class="text-xs text-muted-foreground">{{ sale.date }}</div>
                            </div>
                            <div class="font-semibold">{{ sale.amount }}</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>