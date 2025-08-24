<template>
  <Head title="Payments" />
  <AdminLayout>
    <div class="p-6 max-w-6xl mx-auto">
      <!-- Header -->
      <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 class="text-3xl font-bold tracking-tight">Payments</h1>

        <div class="flex gap-2 w-full sm:w-auto">
          <!-- Search -->
          <input
            v-model="search"
            @keyup.enter="fetchPayments()"
            type="text"
            placeholder="Search payments..."
            class="w-full sm:w-64 rounded-md border px-3 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/50"
          />
        </div>
      </div>

      <!-- Table -->
      <div class="rounded-lg border bg-card shadow-sm overflow-x-auto">
        <BaseTable
          :columns="columns"
          :data="payments"
          :pagination="pagination"
          @page-change="fetchPayments"
        >  
          <!-- User Slot -->
          <template #user_name="{ row }">
            {{ row.user_name || 'Community' }}
          </template>

          <!-- Amount Slot -->
          <template #amount="{ row }">
            ₱{{ row.amount_paid.toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
          </template>

          <!-- Status Slot -->
          <template #payment_status="{ row }">
            <span
              :class="[
                'inline-flex items-center px-2 py-1 rounded text-xs font-semibold',
                row.payment_status === 'paid'
                  ? 'bg-green-100 text-green-800'
                  : row.payment_status === 'unpaid'
                  ? 'bg-yellow-100 text-yellow-800'
                  : 'bg-red-100 text-red-800',
              ]"
            >
              {{ row.payment_status }}
            </span>
          </template>

          <!-- Actions Slot -->
          <template #actions="{ row }">
            <div class="flex items-center space-x-2">
              <button
                @click="$emit('edit', row)"
                class="px-2 py-1 text-xs rounded bg-blue-500 text-white hover:bg-blue-600"
              >
                Edit
              </button>
              <button
                @click="$emit('delete', row)"
                class="px-2 py-1 text-xs rounded bg-red-500 text-white hover:bg-red-600"
              >
                Delete
              </button>
            </div>
          </template>
        </BaseTable>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { Head } from "@inertiajs/vue3";
import AdminLayout from "@/layouts/AdminLayout.vue";
import BaseTable from "@/components/BaseTable.vue";
import axios from "axios";

const payments = ref([]);
const pagination = ref({});
const search = ref("");

const columns = [
    { key: "user_name", label: "User" },
    { key: "amount_paid", label: "Amount" },
    { key: "contribution_date", label: "Date" },
    { key: "contribution_type", label: "Type" },
    { key: "payment_method", label: "Payment Method" },
    { key: "payment_status", label: "Status" },
    { key: "actions", label: "Actions" },
];

const fetchPayments = async (page = 1) => {
  try {
    const route_url = route("payments.list"); // make sure your route name matches
    const { data } = await axios.get(route_url, {
      params: {
        search: search.value,
        page,
      },
    });

    payments.value = data.data;
    pagination.value = data.pagination;
  } catch (error) {
    console.error("Failed to fetch payments", error);
  }
};

onMounted(() => {
  fetchPayments();
});

// Auto-fetch on search input
watch(search, () => {
  fetchPayments(1); // reset to page 1 on new search
});
</script>
