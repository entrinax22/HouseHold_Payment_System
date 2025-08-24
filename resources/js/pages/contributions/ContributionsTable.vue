<template>
  <Head title="Contributions Table" />
  <AdminLayout>
    <div class="p-6 max-w-5xl mx-auto">
      <!-- Header -->
      <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 class="text-3xl font-bold tracking-tight">Contribution</h1>

        <div class="flex gap-2 w-full sm:w-auto">
          <!-- Search -->
          <input
            v-model="search"
            @keyup.enter="fetchContributions()"
            type="text"
            placeholder="Search contributions..."
            class="w-full sm:w-64 rounded-md border px-3 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/50"
          />

          <!-- New Contribution Button -->
          <button
            class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-black shadow hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition"
            @click="$inertia.visit(route('contributions.create'))"
          >
            + New Contribution
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="rounded-lg border bg-card shadow-sm overflow-x-auto">
        <BaseTable
          :columns="columns"
          :data="contributions"
          :pagination="pagination"
          @page-change="fetchContributions"
        >  
          <!-- Custom Slot for amount -->
          <template #amount="{ row }">
            ₱{{ row.amount.toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
          </template>

          <!-- Custom Slot for status -->
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

          <template #actions="{ row }">
            <div class="flex items-center space-x-2">
                <!-- Edit Button -->
                <button
                @click="$emit('edit', row)"
                class="px-2 py-1 text-xs rounded bg-blue-500 text-white hover:bg-blue-600"
                >
                Edit
                </button>

                <!-- Delete Button -->
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

const contributions = ref([]);
const pagination = ref({});
const search = ref("");

const columns = [
  { key: "user_name", label: "User" }, 
  { key: "amount", label: "Amount" },
  { key: "contribution_date", label: "Date" },
  { key: "contribution_type", label: "Type" },
  { key: "description", label: "Description" },
  { key: "payment_status", label: "Status" },
  { key: "actions", label: "Actions" },
];

const fetchContributions = async (page = 1) => {
  try {
    const route_url = route("contributions.list");
    const { data } = await axios.get(route_url, {
      params: {
        search: search.value,
        page: page, // send page number
      },
    });

    contributions.value = data.data;
    pagination.value = data.pagination;
  } catch (error) {
    console.error("Failed to fetch contributions", error);
  }
};

onMounted(() => {
  fetchContributions();
});

watch(search, (newValue)=>{
    fetchContributions(1);
})
</script>
