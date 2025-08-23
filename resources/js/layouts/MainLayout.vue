<template>
  <nav class="border-b bg-background px-8 py-3 flex items-center justify-between">
    <div class="flex items-center">
      <a href="#" class="font-semibold text-lg tracking-tight text-primary">Household Contribution Payment System</a>
    </div>
    <div>
      <ul class="flex gap-6 items-center">
        <li v-if="!user">
          <Link href="/login" :class="linkClass('/login')">Login</Link>
        </li>
        <li v-if="!user">
          <Link href="/register" :class="linkClass('/register')">Register</Link>
        </li>
        <li v-if="user">
          <Link href="/" :class="linkClass('/')">HomePage</Link>
        </li>
        <li v-if="user">
          <Link :href="route('contributions.userContributions')" :class="linkClass('contributions/my-contributions')">My Contributions</Link>
        </li>
        <!-- User Dropdown -->
        <li v-if="user" class="relative">
          <button @click="toggleDropdown" class="flex items-center gap-2 hover:text-primary">
            <span class="text-gray-300">{{ user.name }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-48 bg-white dark:bg-popover border border-muted rounded-xl shadow-lg z-50">
            <ul class="py-2 text-sm text-muted-foreground">
              <li v-if="user && user.role === 'admin'">
                <Link :href="route('dashboard')" class="block px-4 py-2 hover:bg-muted/30">Dashboard</Link>
              </li>
              <li>
                <Link href="/profile" class="block px-4 py-2 hover:bg-muted/30">Profile</Link>
              </li>
              <li>
                <button @click="logout" class="w-full text-left px-4 py-2 hover:bg-muted/30">Logout</button>
              </li>
            </ul>
          </div>
        </li>
      </ul>
    </div>
  </nav>
  <div class="min-h-screen flex flex-col">
    <main class="flex-1">
      <slot />
    </main>
    <footer class="text-center py-6 text-sm text-muted-foreground border-t border-muted">
        &copy; {{ new Date().getFullYear() }} Neighborhood Household Payment System. All rights reserved.
    </footer>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, router, Link } from '@inertiajs/vue3';
const page = usePage();
const user = computed(() => page.props.auth.user);
const currentUrl = computed(() => page.url);
const dropdownOpen = ref(false);
const toggleDropdown = () => (dropdownOpen.value = !dropdownOpen.value);
const logout = () => {
  router.post('/logout');
};
const linkClass = (path) => {
  return currentUrl.value === path || (path !== '/' && currentUrl.value.startsWith(path))
    ? 'font-medium text-white'
    : 'font-medium text-muted-foreground hover:text-primary transition-colors';
};
</script>

<style scoped>
/* Add custom styles here if needed */
</style>
