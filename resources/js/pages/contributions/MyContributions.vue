<template>
    <Head title="My Contributions" />
    <MainLayout>
        <div class="mx-auto min-h-screen max-w-5xl bg-gradient-to-br from-blue-50/30 via-white to-purple-50/30 p-8">
            <!-- Page Title -->
            <h1 class="mb-10 text-4xl font-bold text-gray-800">My Contributions</h1>

            <!-- Timeline -->
            <div v-if="contributions.length > 0" class="relative border-l-2 border-indigo-200">
                <div v-for="contribution in contributions" :key="contribution.contribution_id" class="relative mb-12 ml-8">
                    <!-- Timeline Dot -->
                    <span
                        class="absolute -left-[22px] flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 shadow-md ring-4 ring-white"
                    ></span>

                    <!-- Card -->
                    <div class="rounded-2xl border-2 border-indigo-100 bg-white p-6 shadow-md transition-all duration-200 hover:shadow-lg">
                        <!-- Header row -->
                        <div class="mb-3 flex items-center justify-between">
                            <time class="text-sm font-semibold text-indigo-600">
                                {{ formatDate(contribution.contribution_date) }}
                            </time>
                            <span
                                :class="[
                                    'rounded-full px-3 py-1 text-xs font-bold shadow-sm',
                                    contribution.payment_status === 'paid'
                                        ? 'border border-emerald-300 bg-emerald-100 text-emerald-700'
                                        : 'border border-rose-300 bg-rose-100 text-rose-700',
                                ]"
                            >
                                {{ contribution.payment_status === 'paid' ? 'Paid' : 'Unpaid' }}
                            </span>
                        </div>

                        <!-- Amount -->
                        <div class="text-2xl font-extrabold text-gray-800">₱{{ contribution.amount }}</div>

                        <!-- Type -->
                        <div class="mt-1 text-sm text-gray-600">
                            Type:
                            <span class="font-semibold text-indigo-700 capitalize">
                                {{ contribution.contribution_type }}
                            </span>
                        </div>

                        <!-- Description -->
                        <p class="mt-3 text-sm text-gray-500 italic">
                            {{ contribution.description || 'No description provided.' }}
                        </p>

                        <!-- Pay Button -->
                        <div class="mt-6 flex justify-end">
                            <button
                                v-if="contribution.payment_status !== 'paid'"
                                @click="payContribution(contribution.contribution_id)"
                                class="inline-flex items-center rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-2 text-sm font-bold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-emerald-700 hover:to-teal-700 hover:shadow-xl focus:ring-4 focus:ring-emerald-200 focus:outline-none"
                            >
                                💳 Pay Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="py-16 text-center">
                <p class="text-lg font-medium text-gray-500">No contributions found.</p>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const contributions = ref([]);

const fetchList = async () => {
    const route_url = route('contributions.userList');
    try {
        const response = await axios.get(route_url);
        if (response.data.result === true) {
            contributions.value = response.data.data.sort((a, b) => {
                return new Date(b.contribution_date) - new Date(a.contribution_date);
            });
        } else {
            console.error('Error fetching contributions.');
        }
    } catch (error) {
        console.error('Error fetching contributions:', error);
    }
};

onMounted(() => {
    fetchList();
});

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const payContribution = (contribution_id) => {
    router.get(route('payments.create', { contribution_id: contribution_id }));
};
</script>
