<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

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
        <div class="flex-1 space-y-4 p-8 pt-6 flex flex-col items-center">
          <!-- Header -->
          <div class="flex items-center justify-between w-full max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold tracking-tight">Contribution Dashboard</h2>
            <button
              class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-black shadow transition-colors hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
              Download Report
            </button>
          </div>

          <!-- Stats -->
          <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-4 w-full max-w-7xl mx-auto">
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
              <div class="text-sm font-medium text-muted-foreground">Total Contributions</div>
              <div class="text-2xl font-bold">₱125,450.00</div>
              <div class="text-xs text-muted-foreground">+12% from last month</div>
            </div>
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
              <div class="text-sm font-medium text-muted-foreground">Community Contributions</div>
              <div class="text-2xl font-bold">₱65,000.00</div>
              <div class="text-xs text-muted-foreground">+8 new entries</div>
            </div>
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
              <div class="text-sm font-medium text-muted-foreground">User Contributions</div>
              <div class="text-2xl font-bold">₱60,450.00</div>
              <div class="text-xs text-muted-foreground">-2 from last month</div>
            </div>
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
              <div class="text-sm font-medium text-muted-foreground">Pending Payments</div>
              <div class="text-2xl font-bold">15</div>
            </div>
          </div>

          <!-- Chart + Recent Contributions -->
          <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7 w-full max-w-7xl mx-auto">
            <!-- Chart -->
            <div class="col-span-5 rounded-lg border bg-card p-4 shadow-sm">
              <div class="mb-2 flex items-center justify-between">
                <h3 class="text-lg font-semibold">Monthly Contributions</h3>
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
                <!-- Bars + X-axis -->
                <div class="flex flex-col flex-1 h-full w-full">
                  <div class="flex items-end flex-1 w-full">
                    <div v-for="(value, idx) in chartData" :key="idx"
                      class="bg-primary/70 rounded transition-all duration-300 mx-1 flex-1"
                      :style="{ height: (value / Math.max(...chartData) * 100) + '%', minWidth: '0' }"
                      :title="value.toString()">
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

            <!-- Recent Contributions -->
            <div class="col-span-2 rounded-lg border bg-card p-6 shadow-sm">
              <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold">Recent Contributions</h3>
                <span class="text-xs text-muted-foreground">Latest</span>
              </div>
              <ul class="divide-y divide-muted-foreground/10">
                <li v-for="(c, idx) in contributions" :key="idx" class="py-3 flex items-center justify-between">
                  <div>
                    <div class="font-medium">{{ c.payer }}</div>
                    <div class="text-xs text-muted-foreground">{{ c.date }} · {{ c.method }}</div>
                  </div>
                  <div class="font-semibold">{{ c.amount }}</div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </AdminLayout>
    </div>
  </div>
</template>
