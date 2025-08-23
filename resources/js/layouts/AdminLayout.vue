<template>
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 h-screen bg-background text-white flex flex-col">
      <div class="p-6 text-2xl font-bold border-b border-gray-700">
        CPS Admin Panel
      </div>
      <nav class="flex-1 p-4 overflow-y-auto">
        <div class="mb-4">
          <div class="text-xs uppercase text-gray-400 mb-2">Tables</div>
          <ul class="space-y-2">
            <li>
              <Link href="/admin/users" class="flex items-center gap-2 hover:bg-gray-700 p-2 rounded">
                <span>👤</span>
                <span>Users</span>
              </Link>
            </li>
            <li>
              <Link :href="route('contributions.index')" class="flex items-center gap-2 hover:bg-gray-700 p-2 rounded">
                <span>💸</span>
                <span>Contributions</span>
              </Link>
            </li>
            <li>
              <Link :href="route('payments.index')" class="flex items-center gap-2 hover:bg-gray-700 p-2 rounded">
                <span>🧾</span>
                <span>Payments</span>
              </Link>
            </li>
          </ul>
        </div>
        <div>
          <div class="text-xs uppercase text-gray-400 mb-2">Reports</div>
          <ul class="space-y-2">
            <li>
              <Link href="/admin/reports/summary" class="flex items-center gap-2 hover:bg-gray-700 p-2 rounded">
                <span>📊</span>
                <span>Summary</span>
              </Link>
            </li>
            <li>
              <Link href="/admin/reports/monthly" class="flex items-center gap-2 hover:bg-gray-700 p-2 rounded">
                <span>📅</span>
                <span>Monthly</span>
              </Link>
            </li>
          </ul>
        </div>
        
      </nav>
      <div class="mt-auto p-4 border-t border-gray-700 text-sm text-gray-400 flex flex-col gap-2">
        <div v-if="user" class="flex items-center gap-2 mb-2">
          <Link href="/profile" class="flex items-center gap-2 hover:bg-gray-700 p-2 rounded text-white">
            <span>🙍‍♂️</span>
            <span>{{ user.name }}</span>
          </Link>
        </div>
        <button @click="logout" class="w-full flex items-center gap-2 justify-center bg-red-600 hover:bg-red-700 text-white py-2 rounded">
          <span>🚪</span>
          <span>Logout</span>
        </button>
      </div>
    </aside>
    <!-- Main Content -->
    <div class="flex-1">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
const page = usePage();
const user = computed(() => page.props.auth.user);
const logout = () => {
  router.post('/logout');
};
</script>

<style scoped>
/* Add custom styles here if needed */
</style>
