<template>
  <aside class="w-64 h-screen bg-background text-white flex flex-col">
    <nav class="flex-1 p-4">
      <ul class="space-y-4">
        <li>
          <Link href="/dashboard" class="flex items-center gap-2 hover:bg-gray-700 p-2 rounded">
            <span>🏠</span>
            <span>Dashboard</span>
          </Link>
        </li>
        <li>
          <Link href="/contributions" class="flex items-center gap-2 hover:bg-gray-700 p-2 rounded">
            <span>💰</span>
            <span>Contributions</span>
          </Link>
        </li>
        <li>
          <Link href="/settings" class="flex items-center gap-2 hover:bg-gray-700 p-2 rounded">
            <span>⚙️</span>
            <span>Settings</span>
          </Link>
        </li>
      </ul>
    </nav>
  </aside>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
// Sidebar logic can be added here if needed
</script>

<style scoped>
/* Add custom styles here if needed */
</style>
