<template>
    <Head title="Pay Contribution" />
    <MainLayout>
        <div class="mx-auto min-h-screen max-w-5xl bg-gradient-to-br from-blue-50/30 via-white to-purple-50/30 p-8">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-4xl font-bold tracking-tight text-gray-800">Pay Contribution</h1>
                <button
                    type="button"
                    @click="$inertia.visit(route('contributions.userContributions'))"
                    class="inline-flex items-center rounded-xl border-2 border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-lg transition-all duration-200 hover:scale-105 hover:border-gray-400 hover:bg-gray-50 hover:shadow-xl focus:ring-4 focus:ring-gray-200 focus:outline-none"
                >
                    ← Back to Contributions
                </button>
            </div>

            <!-- Form -->
            <form class="flex flex-col gap-8 rounded-2xl border-2 border-indigo-200 bg-white p-8 shadow-xl" @submit.prevent="submitPayment">
                <!-- Payment Method -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Payment Method</label>
                    <div class="grid grid-cols-2 gap-6 md:grid-cols-3">
                        <button
                            v-for="method in paymentMethods"
                            :key="method.value"
                            type="button"
                            :class="[
                                'flex flex-col items-center justify-center rounded-xl border-2 px-6 py-6 text-sm font-semibold shadow-sm transition-all hover:scale-105 focus:outline-none',
                                form.payment_method === method.value
                                    ? 'border-emerald-500 bg-emerald-50 text-emerald-700 ring-2 ring-emerald-200'
                                    : 'border-gray-200 bg-white text-gray-600 hover:border-emerald-400',
                            ]"
                            @click="form.payment_method = method.value"
                        >
                            <span v-if="method.icon" v-html="method.icon" class="mb-2 text-xl"></span>
                            <span>{{ method.label }}</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <!-- Left column -->
                    <div class="flex flex-col gap-6">
                        <div>
                            <label for="payer" class="mb-2 block text-sm font-semibold text-gray-700">Payer Name</label>
                            <input
                                v-model="form.payer"
                                id="payer"
                                type="text"
                                required
                                class="block w-full rounded-xl border-2 border-purple-200 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-purple-500 focus:ring-4 focus:ring-purple-100 focus:outline-none"
                                placeholder="Enter payer name"
                            />
                        </div>

                        <div>
                            <label for="amount" class="mb-2 block text-sm font-semibold text-gray-700">Amount (₱)</label>
                            <input
                                v-model.number="form.amount_paid"
                                id="amount"
                                type="number"
                                min="0"
                                step="0.01"
                                readonly
                                class="block w-full rounded-xl border-2 border-emerald-200 bg-gray-50 px-4 py-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 focus:outline-none"
                            />
                        </div>

                        <div>
                            <label for="address" class="mb-2 block text-sm font-semibold text-gray-700">Household Address</label>
                            <input
                                v-model="form.address"
                                id="address"
                                type="text"
                                class="block w-full rounded-xl border-2 border-blue-200 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none"
                                placeholder="Enter household address"
                            />
                        </div>
                    </div>

                    <!-- Right column -->
                    <div class="flex flex-col gap-6">
                        <div>
                            <label for="date" class="mb-2 block text-sm font-semibold text-gray-700">Payment Date</label>
                            <div class="flex gap-2">
                                <select
                                    v-model="selectedYear"
                                    class="rounded-xl border-2 border-indigo-200 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 focus:outline-none"
                                >
                                    <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                                </select>
                                <select
                                    v-model="selectedMonth"
                                    class="rounded-xl border-2 border-indigo-200 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 focus:outline-none"
                                >
                                    <option v-for="(month, idx) in months" :key="month" :value="idx + 1">{{ month }}</option>
                                </select>
                                <select
                                    v-model="selectedDay"
                                    class="rounded-xl border-2 border-indigo-200 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 focus:outline-none"
                                >
                                    <option v-for="day in daysInSelectedMonth" :key="day" :value="day">{{ day }}</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="details" class="mb-2 block text-sm font-semibold text-gray-700">Payment Details / Notes</label>
                            <textarea
                                readonly
                                v-model="form.details"
                                id="details"
                                rows="5"
                                class="block w-full resize-none rounded-xl border-2 border-cyan-200 bg-gray-50 px-4 py-3 text-sm shadow-sm focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 focus:outline-none"
                                placeholder="Notes about this payment"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end border-t border-gray-200 pt-4">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-4 text-base font-bold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-emerald-700 hover:to-teal-700 hover:shadow-xl focus:ring-4 focus:ring-emerald-200 focus:outline-none"
                    >
                        💳 Proceed to Payment
                    </button>
                </div>
            </form>
        </div>
    </MainLayout>
</template>

<script setup>
import { notify } from '@/composables/useToast';
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref, watch } from 'vue';

const page = usePage();
const { contribution } = page.props;

const paymentMethods = [
    {
        value: 'Paypal',
        label: 'Paypal',
        icon: '<svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="10" /></svg>',
    },
    {
        value: 'Cash',
        label: 'Cash',
        icon: '<svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><rect x="3" y="7" width="14" height="6" rx="2"/></svg>',
    },
];

const form = ref({
    contribution_id: contribution.contribution_id,
    payer: '',
    amount_paid: contribution.amount || 0,
    payment_date: new Date().toISOString().split('T')[0],
    payment_method: paymentMethods[1].value,
    reference: '',
    details: contribution.description || '',
});

// --- Date logic ---
const today = new Date();
const currentYear = today.getFullYear();
const years = Array.from({ length: 10 }, (_, i) => currentYear - i);
const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
const selectedYear = ref(currentYear);
const selectedMonth = ref(today.getMonth() + 1);
const selectedDay = ref(today.getDate());

const daysInSelectedMonth = computed(() => {
    return new Date(selectedYear.value, selectedMonth.value, 0).getDate();
});

watch([selectedYear, selectedMonth], () => {
    if (selectedDay.value > daysInSelectedMonth.value) {
        selectedDay.value = daysInSelectedMonth.value;
    }
});

watch([selectedYear, selectedMonth, selectedDay], () => {
    const m = String(selectedMonth.value).padStart(2, '0');
    const d = String(selectedDay.value).padStart(2, '0');
    form.value.payment_date = `${selectedYear.value}-${m}-${d}`;
});

// --- Submit logic ---
const submitPayment = async () => {
    try {
        if (form.value.payment_method === 'Paypal') {
            // request PayPal order from backend
            const response = await axios.post(route('paypal.payment'), form.value);

            if (response.data?.approval_url) {
                // Redirect to PayPal approval page
                window.location.href = response.data.approval_url;
            } else {
                console.error('PayPal approval link not found', response.data);
            }
        } else {
            // Cash payment -> direct save
            const response = await axios.post(route('payments.store'), form.value);
            if (response.data.result === true) {
                console.log('Cash payment successful', response.data.message);
                notify('Cash payment successful!', 'success');

                setTimeout(() => {
                    window.location.href = route('home');
                }, 3000);
            }
        }
    } catch (error) {
        console.error('Error submitting request', error.response?.data || error);
    }
};
</script>
