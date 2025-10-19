<template>
    <div class="overflow-hidden rounded-xl border-2 border-indigo-200 bg-white shadow-xl">
        <!-- Scroll container for small screens -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y-2 divide-indigo-100">
                <!-- Table Head -->
                <thead class="sticky top-0 z-10 bg-gradient-to-r from-indigo-500 to-purple-600">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold tracking-wider whitespace-nowrap text-white uppercase">#</th>
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            class="px-4 py-3 text-left text-xs font-bold tracking-wider whitespace-nowrap text-white uppercase"
                        >
                            {{ col.label }}
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody class="divide-y divide-gray-100 bg-white">
                    <tr
                        v-for="(row, index) in data"
                        :key="row.id || index"
                        class="transition-all duration-200 even:bg-gray-50/50 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50"
                    >
                        <!-- Auto Row Number -->
                        <td class="bg-indigo-50/50 px-4 py-3 text-sm font-bold whitespace-nowrap text-indigo-700">
                            {{ (pagination.current_page - 1) * pagination.per_page + (index + 1) }}
                        </td>

                        <td v-for="col in columns" :key="col.key" class="px-4 py-3 text-sm font-medium whitespace-nowrap text-gray-700">
                            <slot :name="col.key" :row="row">
                                {{ row[col.key] }}
                            </slot>
                        </td>
                    </tr>

                    <!-- Empty State -->
                    <tr v-if="data.length === 0">
                        <td :colspan="columns.length + 1" class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-3">
                                <span class="text-4xl">📋</span>
                                <span class="font-medium">No data found.</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div
            v-if="pagination"
            class="flex flex-col gap-4 border-t-2 border-indigo-100 bg-gradient-to-r from-gray-50 to-blue-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="text-center text-sm font-medium text-gray-600 sm:text-left">
                Showing
                <span class="font-bold text-indigo-700">
                    {{ (pagination.current_page - 1) * pagination.per_page + 1 }}
                </span>
                to
                <span class="font-bold text-indigo-700">
                    {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}
                </span>
                of
                <span class="font-bold text-indigo-700">{{ pagination.total }}</span>
                results
            </div>

            <div class="flex justify-center gap-3">
                <button
                    :disabled="pagination.current_page === 1"
                    @click="$emit('page-change', pagination.current_page - 1)"
                    class="rounded-lg border-2 border-blue-200 bg-white px-4 py-2 text-sm font-semibold text-blue-700 shadow-sm transition-all duration-200 hover:scale-105 hover:border-blue-300 hover:bg-blue-50 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50"
                >
                    ← Prev
                </button>

                <button
                    :disabled="pagination.current_page === pagination.last_page"
                    @click="$emit('page-change', pagination.current_page + 1)"
                    class="rounded-lg border-2 border-blue-200 bg-white px-4 py-2 text-sm font-semibold text-blue-700 shadow-sm transition-all duration-200 hover:scale-105 hover:border-blue-300 hover:bg-blue-50 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Next →
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    columns: Array,
    data: Array,
    pagination: {
        type: Object,
        default: null,
    },
});
defineEmits(['page-change']);
</script>
