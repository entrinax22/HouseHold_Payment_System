<template>
    <Head title="New Payment" />
    <AppLayout>
        <div class="p-6 max-w-4xl mx-auto">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-3xl font-bold tracking-tight">Pay Household Bill</h1>
            </div>
            <form class="bg-card rounded-xl border shadow-lg p-10 flex flex-col gap-8" @submit.prevent="submitPayment">
                <!-- Payment Method Card Group -->
                <div>
                    <label class="block text-sm font-medium text-muted-foreground mb-2">Payment Method</label>
                    <div class="grid grid-cols-5 gap-4 mb-2">
                        <button
                            v-for="method in paymentMethods"
                            :key="method.value"
                            type="button"
                            :class="[
                                'flex flex-col items-center justify-center rounded-lg border px-4 py-4 text-sm font-medium transition focus:outline-none',
                                form.method === method.value ? 'bg-primary text-black border-primary ring-2 ring-primary' : 'bg-background text-card-foreground border-muted-foreground/20 hover:border-primary'
                            ]"
                            @click="form.method = method.value"
                        >
                            <span v-if="method.icon" v-html="method.icon" class="mb-1"></span>
                            <span>{{ method.label }}</span>
                        </button>
                    </div>
                    <p v-if="!form.method" class="text-xs text-red-500 mt-1">Please select a payment method.</p>
                </div>
                <div class="flex flex-row gap-8 flex-wrap">
                    <div class="flex-1 min-w-[260px] flex flex-col gap-4">
                        <div>
                            <label for="payer" class="block text-sm font-medium text-muted-foreground mb-1">Payer Name</label>
                            <input v-model="form.payer" id="payer" type="text" required
                                class="block w-full rounded-md border border-muted-foreground/20 bg-background px-4 py-2 text-sm shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition" placeholder="Enter payer name" />
                        </div>
                        <div>
                            <label for="amount" class="block text-sm font-medium text-muted-foreground mb-1">Amount (₱)</label>
                            <input v-model.number="form.amount" id="amount" type="number" min="0" step="0.01" required
                                class="block w-full rounded-md border border-muted-foreground/20 bg-background px-4 py-2 text-sm shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition" placeholder="Enter amount" />
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-muted-foreground mb-1">Household Address</label>
                            <input v-model="form.address" id="address" type="text"
                                class="block w-full rounded-md border border-muted-foreground/20 bg-background px-4 py-2 text-sm shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition" placeholder="Enter household address" />
                        </div>
                    </div>
                    <div class="flex-1 min-w-[260px] flex flex-col gap-4">
                        <div>
                            <label for="date" class="block text-sm font-medium text-muted-foreground mb-1">Payment Date</label>
                            <div class="flex gap-2">
                                <select v-model="selectedYear" class="rounded-md border border-muted-foreground/20 bg-background px-2 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition w-24">
                                    <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                                </select>
                                <select v-model="selectedMonth" class="rounded-md border border-muted-foreground/20 bg-background px-2 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition w-28">
                                    <option v-for="(month, idx) in months" :key="month" :value="idx+1">{{ month }}</option>
                                </select>
                                <select v-model="selectedDay" class="rounded-md border border-muted-foreground/20 bg-background px-2 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition w-20">
                                    <option v-for="day in daysInSelectedMonth" :key="day" :value="day">{{ day }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col">
                            <label for="details" class="block text-sm font-medium text-muted-foreground mb-1">Payment Details / Notes</label>
                            <textarea v-model="form.details" id="details" rows="5"
                                class="block w-full rounded-md border border-muted-foreground/20 bg-background px-4 py-2 text-sm shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition" placeholder="Add any details or notes (e.g. for which month, remarks, etc.)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center rounded-md bg-primary px-8 py-3 text-base font-semibold text-black shadow hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition">
                        Proceed to Payment
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const paymentMethods = [
    { value: 'GCash', label: 'GCash', icon: '<svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="10" /></svg>' },
    { value: 'Bank Transfer', label: 'Bank Transfer', icon: '<svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="10" width="18" height="8" rx="2"/><path d="M16 10V6a4 4 0 00-8 0v4"/></svg>' },
    { value: 'Credit Card', label: 'Credit Card', icon: '<svg class="h-5 w-5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>' },
    { value: 'PayMaya', label: 'PayMaya', icon: '<svg class="h-5 w-5 text-teal-500" fill="currentColor" viewBox="0 0 20 20"><ellipse cx="10" cy="10" rx="10" ry="7" /></svg>' },
    { value: 'Cash', label: 'Cash', icon: '<svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><rect x="3" y="7" width="14" height="6" rx="2"/></svg>' },
];

const form = ref({
    payer: '',
    amount: '',
    date: '',
    method: '',
    reference: '',
    details: '',
});

// Date select logic
const today = new Date();
const currentYear = today.getFullYear();
const years = Array.from({ length: 10 }, (_, i) => currentYear - i);
const months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];
const selectedYear = ref(currentYear);
const selectedMonth = ref(today.getMonth() + 1);
const selectedDay = ref(today.getDate());

const daysInSelectedMonth = computed(() => {
    return new Date(selectedYear.value, selectedMonth.value, 0).getDate();
});

watch([selectedYear, selectedMonth], () => {
    // Reset day if out of range
    if (selectedDay.value > daysInSelectedMonth.value) {
        selectedDay.value = daysInSelectedMonth.value;
    }
});

watch([selectedYear, selectedMonth, selectedDay], () => {
    const m = String(selectedMonth.value).padStart(2, '0');
    const d = String(selectedDay.value).padStart(2, '0');
    form.value.date = `${selectedYear.value}-${m}-${d}`;
});
</script>
