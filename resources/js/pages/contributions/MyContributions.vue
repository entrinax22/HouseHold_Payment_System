<template>
    <Head title="My Contributions" />
    <MainLayout>
        <div class="p-6 max-w-4xl mx-auto">
            <!-- Page Title -->
            <h1 class="text-2xl font-bold mb-8">My Contributions</h1>

            <!-- Timeline -->
            <div v-if="contributions.length > 0" class="relative border-l border-gray-200">
                <div 
                    v-for="contribution in contributions" 
                    :key="contribution.contribution_id"
                    class="mb-10 ml-6"
                >
                    <!-- Dot -->
                    <span 
                        class="absolute -left-3 flex items-center justify-center w-6 h-6 bg-primary rounded-full ring-8 ring-white"
                    >
                    </span>

                    <!-- Card -->
                    <div class="p-4 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition">
                        <!-- Header row -->
                        <div class="flex items-center justify-between mb-2">
                            <time class="text-sm font-medium text-gray-500">
                                {{ formatDate(contribution.contribution_date) }}
                            </time>
                            <span 
                                :class="[ 
                                    'px-3 py-1 text-xs rounded-full font-semibold',
                                    contribution.payment_status === 'paid' 
                                        ? 'bg-green-100 text-green-700' 
                                        : 'bg-red-100 text-red-700'
                                ]"
                            >
                                {{ contribution.payment_status === 'paid' ? 'Paid' : 'Unpaid' }}
                            </span>
                        </div>

                        <!-- Amount -->
                        <div class="text-xl font-bold text-gray-600">
                            ₱{{ contribution.amount }}
                        </div>

                        <!-- Type -->
                        <div class="text-sm text-gray-600 mt-1 capitalize">
                            Contribution Type: <span class="font-medium">{{ contribution.contribution_type }}</span>
                        </div>

                        <!-- Description -->
                        <p class="mt-2 text-sm text-gray-500">
                            {{ contribution.description || 'No description provided.' }}
                        </p>
                        <!-- Pay Button -->
                        <div class="mt-4 flex justify-end">
                            <button 
                                v-if="contribution.payment_status !== 'paid'" 
                                @click="payContribution(contribution.contribution_id)"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500 transition"
                            >
                                Pay Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center text-gray-500 py-12">
                No contributions found.
            </div>
        </div>
    </MainLayout>
</template>
<script setup>
import MainLayout from '@/layouts/MainLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const contributions = ref([]);

const fetchList = async () => {
    const route_url = route("contributions.userList");
    try {
        const response = await axios.get(route_url);
        if (response.data.result === true) {
            contributions.value = response.data.data.sort((a, b) => {
                return new Date(b.contribution_date) - new Date(a.contribution_date);
            });
        } else {
            console.error("Error fetching contributions.");
        }
    } catch (error) {
        console.error("Error fetching contributions:", error);
    }
};

onMounted(() => {
    fetchList();
});

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric"
    });
};

const payContribution = (contribution_id) => {
    router.get(route('payments.create', { contribution_id: contribution_id }));
};
</script>
