<template>
    <Head title="Contributions Table" />
    <AdminLayout>
        <div class="mx-auto min-h-screen max-w-7xl bg-gradient-to-br from-blue-50/30 via-white to-purple-50/30 p-8">
            <!-- Header -->
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-4xl font-bold tracking-tight text-gray-800">Contributions</h1>

                <div class="flex w-full gap-3 sm:w-auto">
                    <!-- Search -->
                    <input
                        v-model="search"
                        @keyup.enter="fetchContributions()"
                        type="text"
                        placeholder="Search contributions..."
                        class="w-full rounded-xl border-2 border-blue-200 px-4 py-3 text-sm placeholder-gray-400 transition-all outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 sm:w-64"
                    />

                    <!-- New Contribution Button -->
                    <button
                        class="inline-flex items-center rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3 text-sm font-semibold whitespace-nowrap text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-emerald-700 hover:to-teal-700 hover:shadow-xl focus:ring-4 focus:ring-emerald-200 focus:outline-none"
                        @click="$inertia.visit(route('contributions.create'))"
                    >
                        ✨ New Contribution
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-xl border-2 border-indigo-200 bg-white shadow-xl">
                <BaseTable :columns="columns" :data="contributions" :pagination="pagination" @page-change="fetchContributions">
                    <!-- Custom Slot for amount -->
                    <template #amount="{ row }">
                        <span class="text-base font-bold text-emerald-700">
                            ₱{{ row.amount.toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                        </span>
                    </template>

                    <!-- Custom Slot for status -->
                    <template #payment_status="{ row }">
                        <span
                            :class="[
                                'inline-flex items-center rounded-full px-3 py-1 text-xs font-bold shadow-sm',
                                row.payment_status === 'paid'
                                    ? 'border border-emerald-200 bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-800'
                                    : row.payment_status === 'unpaid'
                                      ? 'border border-yellow-200 bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800'
                                      : 'border border-red-200 bg-gradient-to-r from-red-100 to-pink-100 text-red-800',
                            ]"
                        >
                            {{ row.payment_status }}
                        </span>
                    </template>

                    <template #actions="{ row }">
                        <div class="flex items-center space-x-2">
                            <!-- Edit Button -->
                            <button
                                @click="$emit('edit', row)"
                                class="rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 px-3 py-2 text-xs font-semibold text-white shadow-md transition-all duration-200 hover:scale-105 hover:from-blue-600 hover:to-indigo-600 hover:shadow-lg"
                            >
                                ✏️ Edit
                            </button>

                            <!-- Delete Button -->
                            <button
                                @click="$emit('delete', row)"
                                class="rounded-lg bg-gradient-to-r from-red-500 to-pink-500 px-3 py-2 text-xs font-semibold text-white shadow-md transition-all duration-200 hover:scale-105 hover:from-red-600 hover:to-pink-600 hover:shadow-lg"
                            >
                                🗑️ Delete
                            </button>
                        </div>
                    </template>
                </BaseTable>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import BaseTable from '@/components/BaseTable.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';

const contributions = ref([]);
const pagination = ref({});
const search = ref('');

const columns = [
    { key: 'user_name', label: 'User' },
    { key: 'amount', label: 'Amount' },
    { key: 'contribution_date', label: 'Date' },
    { key: 'contribution_type', label: 'Type' },
    { key: 'description', label: 'Description' },
    { key: 'payment_status', label: 'Status' },
    { key: 'actions', label: 'Actions' },
];

const fetchContributions = async (page = 1) => {
    try {
        const route_url = route('contributions.list');
        const { data } = await axios.get(route_url, {
            params: {
                search: search.value,
                page: page, // send page number
            },
        });

        contributions.value = data.data;
        pagination.value = data.pagination;
    } catch (error) {
        console.error('Failed to fetch contributions', error);
    }
};

onMounted(() => {
    fetchContributions();
});

watch(search, (newValue) => {
    fetchContributions(1);
});
</script>
