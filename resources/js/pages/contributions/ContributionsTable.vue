<template>
    <Head title="Contributions Table" />
    <AdminLayout>
        <div class="mx-auto min-h-screen max-w-7xl bg-gradient-to-br from-blue-50/30 via-white to-purple-50/30 p-8">
            <!-- Header -->
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-4xl font-bold tracking-tight text-gray-800">Contributions Table</h1>

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
                    <template #status="{ row }">
                        <span
                            :class="[
                                'inline-flex items-center rounded-full px-3 py-1 text-xs font-bold shadow-sm',
                                row.status === 'active'
                                    ? 'border border-emerald-200 bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-800'
                                    : row.payment_status === 'disabled'
                                      ? 'border border-yellow-200 bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800'
                                      : 'border border-red-200 bg-gradient-to-r from-red-100 to-pink-100 text-red-800',
                            ]"
                        >
                            {{ row.status }}
                        </span>
                    </template>

                    <template #actions="{ row }">
                        <div class="flex items-center space-x-2">
                            <!-- Edit Button -->
                            <button
                                @click="edit(row.contribution_id)"
                                class="rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 px-3 py-2 text-xs font-semibold text-white shadow-md transition-all duration-200 hover:scale-105 hover:from-blue-600 hover:to-indigo-600 hover:shadow-lg"
                            >
                                ✏️ Edit
                            </button>

                            <!-- Delete Button -->
                            <button
                                @click="deleteContribution(row.contribution_id)"
                                class="rounded-lg bg-gradient-to-r from-red-500 to-pink-500 px-3 py-2 text-xs font-semibold text-white shadow-md transition-all duration-200 hover:scale-105 hover:from-red-600 hover:to-pink-600 hover:shadow-lg"
                            >
                                🗑️ Delete
                            </button>
                        </div>
                    </template>
                </BaseTable>
            </div>
        </div>

        <!-- ✅ Enhanced Edit Modal -->
        <div
            v-if="isEditModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md transition-all duration-300"
        >
            <div
                class="w-full max-w-2xl scale-100 transform rounded-3xl bg-gradient-to-br from-white via-indigo-50 to-blue-50 p-8 shadow-2xl transition-all duration-300 hover:scale-[1.01]"
            >
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between border-b border-indigo-100 pb-4">
                    <h2 class="flex items-center gap-2 text-3xl font-bold tracking-tight text-gray-800">✏️ Edit Contribution</h2>
                    <button
                        @click="closeEditModal"
                        class="rounded-full p-2 text-gray-500 transition-all duration-200 hover:bg-gray-200 hover:text-gray-700"
                        title="Close"
                    >
                        ✖
                    </button>
                </div>

                <form @submit.prevent="updateContribution" class="space-y-5">
                    <!-- User Selection -->
                    <div v-if="editContribution.contribution_type === 'personal'">
                        <label class="mb-1 block text-sm font-semibold text-gray-700">User</label>
                        <Multiselect
                            v-model="editContribution.user_id"
                            :options="users"
                            label="name"
                            track-by="user_id"
                            placeholder="Select a user"
                            class="w-full rounded-xl border-2 border-gray-200 transition-all duration-200 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Amount (₱)</label>
                        <input
                            type="number"
                            v-model="editContribution.amount"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-gray-700 transition-all duration-200 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Date</label>
                        <input
                            type="date"
                            v-model="editContribution.contribution_date"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-gray-700 transition-all duration-200 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Contribution Type</label>
                        <select
                            v-model="editContribution.contribution_type"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-gray-700 transition-all duration-200 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="community">Community</option>
                            <option value="personal">Personal</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Description</label>
                        <textarea
                            v-model="editContribution.description"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-gray-700 transition-all duration-200 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                            rows="3"
                            placeholder="Write a brief description..."
                        ></textarea>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Status</label>
                        <select
                            v-model="editContribution.status"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-gray-700 transition-all duration-200 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="active">Active</option>
                            <option value="disabled">Disabled</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-4 border-t border-indigo-100 pt-4">
                        <button
                            type="button"
                            @click="closeEditModal"
                            class="rounded-xl border-2 border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-gray-100 hover:text-gray-800"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl"
                        >
                            💾 Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import BaseTable from '@/components/BaseTable.vue';
import { notify } from '@/composables/useToast';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';

const contributions = ref([]);
const pagination = ref({});
const search = ref('');

const columns = [
    { key: 'user_name', label: 'User' },
    { key: 'amount', label: 'Amount' },
    { key: 'contribution_date', label: 'Date' },
    { key: 'contribution_type', label: 'Type' },
    { key: 'description', label: 'Description' },
    { key: 'status', label: 'Status' },
    { key: 'actions', label: 'Actions' },
];

const fetchContributions = async (page = 1) => {
    try {
        const route_url = route('contributions.list');
        const { data } = await axios.get(route_url, {
            params: {
                search: search.value,
                page: page,
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

const isEditModalOpen = ref(false);

const closeEditModal = () => {
    isEditModalOpen.value = false;
    editContribution.value = {};
};

const users = ref([]);
const editContribution = ref([]);

const edit = async (contribution_id) => {
    const route_url = route('contributions.edit', { contribution_id });
    try {
        const response = await axios.get(route_url);
        if (response.data.result === true) {
            users.value = response.data.data.users;

            const selectedUser = users.value.find((user) => user.user_id === response.data.data.contribution.user_id);

            editContribution.value = {
                ...response.data.data.contribution,
                user_id: selectedUser || null,
            };
            isEditModalOpen.value = true;
        }
    } catch (error) {
        console.error('Failed to edit contribution', error);
    }
};

const updateContribution = async () => {
    const route_url = route('contributions.update');
    const payload = { ...editContribution.value };

    if (payload.user_id && typeof payload.user_id === 'object') {
        payload.user_id = payload.user_id.user_id;
    }
    try {
        const response = await axios.post(route_url, payload);
        if (response.data.result === true) {
            closeEditModal();
            fetchContributions();
            notify('Contribution updated successfully!', 'success');
        } else {
            notify('Failed to update contribution.', 'error');
        }
    } catch (error) {
        console.error('Failed to update contribution', error);
    }
};

const deleteContribution = async (contribution_id) => {
    if (!confirm(`Are you sure you want to delete this contribution? This action cannot be undone.`)) {
        return;
    }

    const route_url = route('contributions.destroy', { contribution_id });
    try {
        const response = await axios.post(route_url);
        if (response.data.result === true) {
            fetchContributions();
            notify('Contribution deleted successfully!', 'success');
        } else {
            notify('Failed to delete contribution.', 'error');
        }
    } catch (error) {
        console.error('Failed to delete contribution', error);
    }
};
</script>
