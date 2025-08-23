<template>
    <Head title="Payments" />
    <AdminLayout >
        <div class="p-6 max-w-5xl mx-auto">
            <div class="mb-6 flex items-center justify-between flex-wrap gap-2">
                <h1 class="text-3xl font-bold tracking-tight">Payments</h1>
                <button
                    class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-black shadow hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition"
                    @click="$inertia.visit(route('payments.create'))">
                    + New Payment
                </button>
            </div>
            <div class="rounded-lg border bg-card shadow-sm overflow-x-auto">
                <table class="min-w-full divide-y divide-muted-foreground/10">
                    <thead class="bg-muted sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider whitespace-nowrap">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider whitespace-nowrap">Payer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider whitespace-nowrap">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider whitespace-nowrap">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="px-6 py-3 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-card divide-y divide-muted-foreground/10">
                        <tr v-for="payment in payments" :key="payment.id" class="hover:bg-muted/50 transition even:bg-muted/20">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-card-foreground">{{ payment.id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-card-foreground">{{ payment.payer }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-card-foreground">₱{{ payment.amount.toLocaleString(undefined, { minimumFractionDigits: 2 }) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-card-foreground">{{ payment.date }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span :class="[
                                    'inline-flex items-center px-2 py-1 rounded text-xs font-semibold',
                                    payment.status === 'Paid' ? 'bg-green-100 text-green-800' :
                                        payment.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                                            'bg-red-100 text-red-800'
                                ]">
                                    {{ payment.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <button class="inline-flex items-center gap-1 text-primary hover:underline font-medium px-2 py-1 rounded transition focus:outline-none focus:ring-2 focus:ring-primary/50">
                                    <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z' /></svg>
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr v-if="payments.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-muted-foreground">No payments found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/layouts/MainLayout.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';

// Mock payment data
const payments = ref([
    {
        id: 1,
        payer: 'John Doe',
        amount: 120.5,
        date: '2024-06-01',
        status: 'Paid'
    },
    {
        id: 2,
        payer: 'Jane Smith',
        amount: 75.0,
        date: '2024-06-03',
        status: 'Pending'
    },
    {
        id: 3,
        payer: 'Alice Johnson',
        amount: 200.0,
        date: '2024-06-05',
        status: 'Paid'
    },
    {
        id: 4,
        payer: 'Bob Brown',
        amount: 50.25,
        date: '2024-06-07',
        status: 'Failed'
    }
])
</script>
