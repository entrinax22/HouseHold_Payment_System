<template>
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 h-screen bg-background text-white flex flex-col">
      <div class="p-6 text-2xl font-bold border-b border-gray-700">
        CPS Admin Panel
      </div>
      <nav class="flex-1 p-4 overflow-y-auto">
        <div class="mb-4">
          <div class="text-xs uppercase text-gray-400 mb-2">Platforms</div>
          <ul class="space-y-2">
            <li>
              <Link :href="route('dashboard')" class="flex items-center gap-2 hover:bg-gray-700 p-2 rounded">
                <span>👤</span>
                <span>Dashboard</span>
              </Link>
            </li>
            <li>
              <Link :href="route('home')" class="flex items-center gap-2 hover:bg-gray-700 p-2 rounded">
                <span>💸</span>
                <span>Home</span>
              </Link>
            </li>
          </ul>
        </div>
        <div class="mb-4">
          <div class="text-xs uppercase text-gray-400 mb-2">Tables</div>
          <ul class="space-y-2">
            <li>
              <Link href="#" class="flex items-center gap-2 hover:bg-gray-700 p-2 rounded">
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
              <Link href="#" class="flex items-center gap-2 hover:bg-gray-700 p-2 rounded">
                <span>📊</span>
                <span>Summary</span>
              </Link>
            </li>
            <li>
              <Link href="#" class="flex items-center gap-2 hover:bg-gray-700 p-2 rounded">
                <span>📅</span>
                <span>Monthly</span>
              </Link>
            </li>
          </ul>
        </div>
        
      </nav>
      <div class="mt-auto p-4 border-t border-gray-700 text-sm text-gray-400 flex flex-col gap-2">
        <div v-if="user" class="flex items-center gap-2 mb-2">
          <Link href="#" class="flex items-center gap-2 hover:bg-gray-700 p-2 rounded text-white">
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
      <Toast :message="toastMessage" :type="toastType" />
    </div>
  </div>
</template>

<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import Toast from '@/components/Toast.vue';
import { toastMessage, toastType } from '@/composables/useToast'
import { notify } from '@/composables/useToast';

const page = usePage();
const user = computed(() => page.props.auth.user);
const logout = () => {
  router.post('/logout');
};


watch(
  () => page.props.flash,
  (flash) => {
    if (flash.success) {
      notify(flash.success, 'success')
    }
    if (flash.error) {
      notify(flash.error, 'error')
    }
  },
  { immediate: true, deep: true }
)
</script>

<style scoped>
/* Add custom styles here if needed */
</style>
