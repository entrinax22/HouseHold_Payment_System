<template>
    <Head title="Payments" />
    <AdminLayout>
        <div class="mx-auto min-h-screen max-w-7xl bg-gradient-to-br from-blue-50/30 via-white to-purple-50/30 p-8">
            <!-- Header -->
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-4xl font-bold tracking-tight text-gray-800">Payments Table</h1>

                <div class="flex w-full gap-3 sm:w-auto">
                    <!-- Search -->
                    <input
                        v-model="search"
                        @keyup.enter="fetchPayments()"
                        type="text"
                        placeholder="Search payments..."
                        class="w-full rounded-xl border-2 border-blue-200 px-4 py-3 text-sm placeholder-gray-400 transition-all outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 sm:w-64"
                    />
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-xl border-2 border-indigo-200 bg-white shadow-xl">
                <BaseTable :columns="columns" :data="payments" :pagination="pagination" @page-change="fetchPayments">
                    <!-- User Slot -->
                    <template #user_name="{ row }">
                        <span class="font-semibold text-gray-800">
                            {{ row.user_name || 'Community' }}
                        </span>
                    </template>

                    <!-- Amount Slot -->
                    <template #amount="{ row }">
                        <span class="text-base font-bold text-emerald-700">
                            ₱{{ row.amount_paid.toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                        </span>
                    </template>

                    <!-- Status Slot -->
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

                    <!-- Actions Slot -->
                    <template #actions="{ row }">
                        <div class="flex items-center space-x-2">
                            <button
                                @click="editPayment(row.payment_id)"
                                class="rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 px-3 py-2 text-xs font-semibold text-white shadow-md transition-all duration-200 hover:scale-105 hover:from-blue-600 hover:to-indigo-600 hover:shadow-lg"
                            >
                                Edit
                            </button>
                            <button
                                @click="deletePayment(row.payment_id)"
                                class="rounded-lg bg-gradient-to-r from-red-500 to-pink-500 px-3 py-2 text-xs font-semibold text-white shadow-md transition-all duration-200 hover:scale-105 hover:from-red-600 hover:to-pink-600 hover:shadow-lg"
                            >
                                Delete
                            </button>
                        </div>
                    </template>
                </BaseTable>
            </div>
        </div>

        <!-- Edit Payment Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="w-full max-w-2xl rounded-2xl border-2 border-indigo-200 bg-white p-8 shadow-2xl">
                <h2 class="mb-6 text-2xl font-bold text-gray-800">Edit Payment</h2>

                <form @submit.prevent="updatePayment" class="space-y-6">
                    <!-- User -->
                    <div>
                        <label for="user_id" class="mb-2 block text-sm font-semibold text-gray-700">User</label>
                        <Multiselect
                            v-model="editForm.user_id"
                            :options="users"
                            :searchable="true"
                            :close-on-select="true"
                            :show-labels="false"
                            placeholder="Select User"
                            label="name"
                            track-by="id"
                            class="w-full"
                        />
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="amount_paid" class="mb-2 block text-sm font-semibold text-gray-700">Amount (₱)</label>
                        <input
                            v-model="editForm.amount_paid"
                            id="amount_paid"
                            type="number"
                            min="0"
                            step="0.01"
                            required
                            class="w-full rounded-xl border-2 border-purple-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-100"
                        />
                    </div>

                    <!-- Payment Status -->
                    <div>
                        <label for="payment_status" class="mb-2 block text-sm font-semibold text-gray-700">Payment Status</label>
                        <select
                            v-model="editForm.payment_status"
                            id="payment_status"
                            required
                            class="w-full rounded-xl border-2 border-emerald-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        >
                            <option value="">Select Status</option>
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label for="payment_method" class="mb-2 block text-sm font-semibold text-gray-700">Payment Method</label>
                        <input
                            v-model="editForm.payment_method"
                            id="payment_method"
                            type="text"
                            required
                            class="w-full rounded-xl border-2 border-teal-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-teal-500 focus:ring-4 focus:ring-teal-100"
                        />
                    </div>

                    <!-- Payment Date -->
                    <div>
                        <label for="payment_date" class="mb-2 block text-sm font-semibold text-gray-700">Payment Date</label>
                        <input
                            v-model="editForm.payment_date"
                            id="payment_date"
                            type="date"
                            class="w-full rounded-xl border-2 border-indigo-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        />
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">Description / Note</label>
                        <textarea
                            v-model="editForm.description"
                            id="description"
                            rows="3"
                            class="w-full resize-none rounded-xl border-2 border-cyan-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                        ></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-4 border-t border-gray-200 pt-4">
                        <button
                            type="button"
                            @click="closeModal"
                            class="rounded-xl border-2 border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-base font-bold text-white shadow-lg transition-all hover:scale-105 hover:from-emerald-700 hover:to-teal-700"
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
import 'vue-multiselect/dist/vue-multiselect.min.css';

const payments = ref([]);
const pagination = ref({});
const search = ref('');

const columns = [
    { key: 'user_name', label: 'User' },
    { key: 'amount_paid', label: 'Amount' },
    { key: 'contribution_date', label: 'Date' },
    { key: 'contribution_type', label: 'Type' },
    { key: 'payment_method', label: 'Payment Method' },
    { key: 'payment_status', label: 'Status' },
    { key: 'actions', label: 'Actions' },
];

const fetchPayments = async (page = 1) => {
    try {
        const route_url = route('payments.list');
        const { data } = await axios.get(route_url, {
            params: {
                search: search.value,
                page,
            },
        });

        payments.value = data.data;
        pagination.value = data.pagination;
    } catch (error) {
        console.error('Failed to fetch payments', error);
    }
};

onMounted(() => {
    fetchPayments();
});

// Auto-fetch on search input
watch(search, () => {
    fetchPayments(1); // reset to page 1 on new search
});

const showEditModal = ref(false);
const users = ref([]);
const editForm = ref({
    payment_id: '',
    user_id: '',
    amount_paid: '',
    payment_status: '',
    payment_method: '',
    payment_date: '',
    description: '',
});

const closeModal = () => {
    showEditModal.value = false;
};

const editPayment = async (payment_id) => {
    const route_url = route('admin.payments.edit', { payment_id });
    try {
        const response = await axios.get(route_url);

        if (response.data.result === true) {
            users.value = response.data.users;
            const user = users.value.find((u) => u.id === response.data.data.user_id);

            editForm.value = {
                payment_id: payment_id,
                user_id: user || null,
                amount_paid: response.data.data.amount_paid,
                payment_status: response.data.data.payment_status,
                payment_method: response.data.data.payment_method,
                payment_date: response.data.data.payment_date ? response.data.data.payment_date.split('T')[0] : '',
                description: response.data.data.description,
            };
            showEditModal.value = true;
        }
    } catch (error) {
        console.log('Error fetching payment data:', error);
    }
};

const updatePayment = async () => {
    const route_url = route('admin.payments.update');
    const payload = {
        payment_id: editForm.value.payment_id,
        user_id: editForm.value.user_id?.id || null,
        amount_paid: editForm.value.amount_paid,
        payment_status: editForm.value.payment_status,
        payment_method: editForm.value.payment_method,
        payment_date: editForm.value.payment_date,
        description: editForm.value.description,
    };
    try {
        const response = await axios.post(route_url, payload);
        if (response.data.result === true) {
            notify('Payment updated successfully!', 'success');
            editForm.value = {
                payment_id: '',
                user_id: '',
                amount_paid: '',
                payment_status: '',
                payment_method: '',
                payment_date: '',
                description: '',
            };
            showEditModal.value = false;
        }
    } catch (error) {
        console.log('Error updating payment data:', error);
    }
};

const deletePayment = async (payment_id) => {
    if (!confirm(`Are you sure you want to delete this payment? This action cannot be undone.`)) {
        return;
    }

    const route_url = route('admin.payments.destroy', { payment_id });
    try {
        const response = await axios.post(route_url);
        if (response.data.result === true) {
            fetchPayments();
            notify('Payment deleted successfully!', 'success');
        } else {
            notify('Failed to delete payment.', 'error');
        }
    } catch (error) {
        console.error('Failed to delete payment', error);
    }
};
</script>
