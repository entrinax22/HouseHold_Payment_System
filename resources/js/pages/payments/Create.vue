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
                    <div class="mb-4">
                        <label class="mb-2 block text-sm font-semibold text-gray-700">Payment Method</label>
                        <p class="text-sm text-gray-600">Currently, PayPal is the only available payment method for secure online transactions.</p>
                    </div>
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
        icon: '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.0732 8.86357C20.2694 7.90255 20.0732 7.23876 19.68 6.67184C19.2867 6.10492 18.5011 5.73431 17.5187 5.73431H13.9804C13.784 5.73431 13.5878 5.92962 13.5878 6.12493L11.822 17.3048C11.822 17.5001 11.9191 17.6954 12.1153 17.6954H14.0776C14.2738 17.6954 14.47 17.5001 14.47 17.3048L15.1584 13.3046C15.1584 13.1093 15.3546 12.914 15.5508 12.914H16.7305C19.0955 12.914 20.7642 11.7816 21.2547 9.53736C21.3519 9.14676 21.3519 8.86357 21.2547 8.57038C20.9587 8.67304 20.5654 8.76569 20.0732 8.86357Z" fill="#002C8A"/><path d="M9.69124 9.53735C10.0845 7.19483 11.9477 5.73431 14.1749 5.73431H17.7132C18.6956 5.73431 19.4812 6.10492 19.8745 6.67184C20.2677 7.23876 20.4639 7.90255 20.2677 8.86357C19.777 11.1061 18.1084 12.2385 15.7434 12.2385H14.5637C14.3675 12.2385 14.1713 12.4338 14.1713 12.6291L13.483 16.6293C13.483 16.8246 13.2868 17.0199 13.0906 17.0199H11.1282C10.932 17.0199 10.8349 16.8246 10.8349 16.6293L9.06908 5.44934C9.06908 5.25404 9.16624 5.05873 9.36244 5.05873H11.3249C11.5211 5.05873 11.7173 5.25404 11.7173 5.44934L12.4057 9.44956C12.5028 9.44956 12.599 9.53735 12.6962 9.53735H9.69124Z" fill="#009BE1"/><path d="M4.39453 13.0215C4.39453 12.8262 4.59073 12.6309 4.78693 12.6309H6.74929C6.94549 12.6309 7.14169 12.8262 7.14169 13.0215L6.45329 17.0217C6.45329 17.217 6.25709 17.4123 6.06089 17.4123H4.09853C3.90233 17.4123 3.80517 17.217 3.80517 17.0217L2.03937 5.84175C2.03937 5.64644 2.13653 5.45114 2.33273 5.45114H4.29509C4.49129 5.45114 4.68749 5.64644 4.68749 5.84175L5.37589 9.84197C5.47305 9.84197 5.47305 9.92976 5.57021 9.92976H8.57521C8.96843 7.58724 10.8316 6.12672 13.0588 6.12672H13.5494C13.4522 6.22937 13.355 6.42468 13.355 6.51999L11.5892 17.6999C11.5892 17.8952 11.6864 18.0905 11.8826 18.0905H13.845C14.0412 18.0905 14.2374 17.8952 14.2374 17.6999L14.9258 13.6997C14.9258 13.5044 15.122 13.3091 15.3182 13.3091H16.4979C18.8629 13.3091 20.5316 12.1767 21.0221 9.93244C21.2183 9.15653 21.1212 8.49274 20.8252 7.92582C20.8252 8.02847 20.8252 8.13113 20.728 8.23379C20.2375 10.4781 18.5688 11.6105 16.2038 11.6105H15.0241C14.8279 11.6105 14.6317 11.8058 14.6317 12.0011L13.9433 16.0013C13.9433 16.1966 13.7471 16.3919 13.5509 16.3919H11.5886C11.3924 16.3919 11.2952 16.1966 11.2952 16.0013L9.52943 5.2213C9.52943 5.02599 9.62659 4.83069 9.82279 4.83069H11.7852C11.9814 4.83069 12.1776 5.02599 12.1776 5.2213L12.866 9.2215C12.866 9.2215 12.866 9.30929 12.9631 9.30929H9.95813C8.02041 9.22175 6.45329 10.7725 6.06089 12.6309H4.39453V13.0215Z" fill="#001F6B"/></svg>',
    },
];

const form = ref({
    contribution_id: contribution.contribution_id,
    payer: '',
    amount_paid: contribution.amount || 0,
    payment_date: new Date().toISOString().split('T')[0],
    payment_method: paymentMethods[0].value,
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
