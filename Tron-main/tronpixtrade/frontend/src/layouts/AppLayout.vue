<template>
  <div class="min-h-screen bg-black text-white pb-20">
    <!-- Header with logo and navigation -->
    <header class="border-b border-green-400/20 py-3 px-4 flex items-center justify-between sticky top-0 z-40 bg-black/80 backdrop-blur-sm">
      <AppLogo />
      <nav class="flex gap-4">
        <router-link to="/dashboard" class="text-sm text-gray-400 hover:text-green-400">Dashboard</router-link>
        <router-link to="/settings" class="text-sm text-gray-400 hover:text-green-400">Settings</router-link>
      </nav>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 py-8">
      <!-- Breadcrumbs -->
      <div v-if="breadcrumbs && breadcrumbs.length" class="mb-6 flex gap-2 text-xs text-gray-400">
        <router-link to="/dashboard" class="hover:text-green-400">Home</router-link>
        <span>/</span>
        <span v-for="(crumb, idx) in breadcrumbs" :key="idx">
          <router-link v-if="crumb.path" :to="crumb.path" class="hover:text-green-400">
            {{ crumb.label }}
          </router-link>
          <span v-else>{{ crumb.label }}</span>
          <span v-if="idx < breadcrumbs.length - 1"> / </span>
        </span>
      </div>

      <!-- Page Content -->
      <slot />
    </main>

    <!-- Footer -->
    <footer class="border-t border-green-400/20 py-4 px-4 text-center text-xs text-gray-400 mt-12">
      <p>TronpixTrades © 2024. All rights reserved.</p>
    </footer>
  </div>
</template>

<script setup>
import AppLogo from '@/components/AppLogo.vue'

defineProps({
  breadcrumbs: {
    type: Array,
    default: () => []
  }
})
</script>

<style scoped>
header {
  position: sticky;
}
</style>
