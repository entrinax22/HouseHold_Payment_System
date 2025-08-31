<template>
    <Head title="Create Contribution" />
    <AdminLayout>
        <div class="mx-auto min-h-screen max-w-5xl bg-gradient-to-br from-blue-50/30 via-white to-purple-50/30 p-8">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-4xl font-bold tracking-tight text-gray-800">New Contribution</h1>
                <button
                    type="button"
                    @click="$inertia.visit(route('contributions.index'))"
                    class="inline-flex items-center rounded-xl border-2 border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-lg transition-all duration-200 hover:scale-105 hover:border-gray-400 hover:bg-gray-50 hover:shadow-xl focus:ring-4 focus:ring-gray-200 focus:outline-none"
                >
                    ← Back to List
                </button>
            </div>

            <!-- Form -->
            <form class="flex flex-col gap-8 rounded-2xl border-2 border-indigo-200 bg-white p-8 shadow-xl" @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <!-- Left column -->
                    <div class="flex flex-col gap-6">
                        <!-- Contribution Type (enum) -->
                        <div>
                            <label for="contribution_type" class="mb-2 block text-sm font-semibold text-gray-700"> Contribution Type </label>
                            <select
                                v-model="form.contribution_type"
                                id="contribution_type"
                                required
                                class="block w-full rounded-xl border-2 border-emerald-200 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 focus:outline-none"
                            >
                                <option value="">Select type</option>
                                <option value="community">Community</option>
                                <option value="personal">Personal</option>
                            </select>
                        </div>

                        <!-- User ID (only show if personal) -->
                        <div v-if="form.contribution_type === 'personal'">
                            <label for="user_id" class="mb-2 block text-sm font-semibold text-gray-700"> Contributor </label>
                            <select
                                v-model="form.user_id"
                                id="user_id"
                                required
                                class="block w-full rounded-xl border-2 border-blue-200 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none"
                            >
                                <option value="">Select User</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="mb-2 block text-sm font-semibold text-gray-700"> Amount (₱) </label>
                            <input
                                v-model.number="form.amount"
                                id="amount"
                                type="number"
                                min="0"
                                step="0.01"
                                required
                                class="block w-full rounded-xl border-2 border-purple-200 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-purple-500 focus:ring-4 focus:ring-purple-100 focus:outline-none"
                                placeholder="Enter amount"
                            />
                        </div>
                    </div>

                    <!-- Right column -->
                    <div class="flex flex-col gap-6">
                        <!-- Contribution Date -->
                        <div>
                            <label for="contribution_date" class="mb-2 block text-sm font-semibold text-gray-700"> Contribution Date </label>
                            <input
                                v-model="form.contribution_date"
                                id="contribution_date"
                                type="date"
                                required
                                class="block w-full rounded-xl border-2 border-indigo-200 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 focus:outline-none"
                            />
                        </div>

                        <!-- Status (enum) -->
                        <div>
                            <label for="status" class="mb-2 block text-sm font-semibold text-gray-700"> Status </label>
                            <select
                                v-model="form.status"
                                id="status"
                                required
                                class="block w-full rounded-xl border-2 border-teal-200 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-teal-500 focus:ring-4 focus:ring-teal-100 focus:outline-none"
                            >
                                <option value="">Select status</option>
                                <option value="active">Active</option>
                                <option value="disabled">Disabled</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="mb-2 block text-sm font-semibold text-gray-700"> Description / Notes </label>
                            <textarea
                                v-model="form.description"
                                id="description"
                                rows="4"
                                class="block w-full resize-none rounded-xl border-2 border-cyan-200 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 focus:outline-none"
                                placeholder="Add details about the contribution..."
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
                        💾 Save Contribution
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';

const form = useForm({
    user_id: '',
    amount: '',
    contribution_date: '',
    contribution_type: '',
    status: 'active',
    description: '',
});
const search = ref({});
const users = ref([]);

const fetchList = async () => {
    const route_url = route('admin.users.list');
    try {
        const response = await axios.get(route_url, { params: search.value });
        if (response.data.result === true) {
            users.value = response.data.data;
        }
    } catch (error) {
        console.error('Error fetching users:', error);
    }
};

onMounted(() => {
    fetchList();
});

watch(
    search,
    () => {
        fetchList();
    },
    { deep: true },
);

const submit = async () => {
    const route_url = route('contributions.store');
    try {
        const response = await axios.post(route_url, form);
        if (response.data.result === true) {
            console.log('success');
            // reset fields correctly
            form.user_id = '';
            form.amount = '';
            form.contribution_date = '';
            form.contribution_type = '';
            form.status = 'active';
            form.description = '';
        } else {
            console.log('Something went wrong:', response.data.message);
        }
    } catch (error) {
        if (error.response && error.response.data.errors) {
            console.log('Validation errors:', error.response.data.errors);
        } else {
            console.log('Unexpected error:', error);
        }
    }
};
</script>
