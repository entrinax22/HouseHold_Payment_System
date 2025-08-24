<template>
  <div class="rounded-lg border bg-card shadow-sm overflow-x-auto">
    <table class="min-w-full divide-y divide-muted-foreground/10">
      <!-- Table Head -->
      <thead class="bg-muted sticky top-0 z-10">
        <tr>
          <!-- Index column -->
          <th class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider whitespace-nowrap">
            #
          </th>
          <th
            v-for="col in columns"
            :key="col.key"
            class="px-6 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider whitespace-nowrap"
          >
            {{ col.label }}
          </th>
        </tr>
      </thead>

      <!-- Table Body -->
      <tbody class="bg-card divide-y divide-muted-foreground/10">
        <tr
          v-for="(row, index) in data"
          :key="row.id || index"
          class="hover:bg-muted/50 transition even:bg-muted/20"
        >
          <!-- Auto Row Number -->
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-card-foreground">
            {{ (pagination.current_page - 1) * pagination.per_page + (index + 1) }}
          </td>

          <td
            v-for="col in columns"
            :key="col.key"
            class="px-6 py-4 whitespace-nowrap text-sm text-card-foreground"
          >
            <slot :name="col.key" :row="row">
              {{ row[col.key] }}
            </slot>
          </td>
        </tr>

        <!-- Empty State -->
        <tr v-if="data.length === 0">
          <td
            :colspan="columns.length + 1"
            class="px-6 py-8 text-center text-muted-foreground"
          >
            No data found.
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination Controls -->
    <div
      v-if="pagination"
      class="flex items-center justify-between px-6 py-4 border-t bg-card"
    >
      <div class="text-sm text-muted-foreground">
        Showing
        <span class="font-medium">
          {{ (pagination.current_page - 1) * pagination.per_page + 1 }}
        </span>
        to
        <span class="font-medium">
          {{
            Math.min(pagination.current_page * pagination.per_page, pagination.total)
          }}
        </span>
        of
        <span class="font-medium">{{ pagination.total }}</span>
        results
      </div>

      <div class="flex gap-2">
        <button
          :disabled="pagination.current_page === 1"
          @click="$emit('page-change', pagination.current_page - 1)"
          class="px-3 py-1 rounded border text-sm disabled:opacity-50"
        >
          Prev
        </button>

        <button
          :disabled="pagination.current_page === pagination.last_page"
          @click="$emit('page-change', pagination.current_page + 1)"
          class="px-3 py-1 rounded border text-sm disabled:opacity-50"
        >
          Next
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
defineEmits(["page-change"]);
</script>
