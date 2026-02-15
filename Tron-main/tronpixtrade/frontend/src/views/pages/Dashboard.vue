<template>
  <div class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-xl w-full text-center text-gray-200">
      <h1 class="text-2xl font-semibold mb-4">Dashboard</h1>
      <p class="mb-4">This is the top-level dashboard placeholder. If you are authenticated you'll be redirected to your role-specific dashboard.</p>
      <div class="space-x-2">
        <router-link to="/login" class="px-4 py-2 bg-green-600 rounded">Sign in</router-link>
        <router-link to="/register" class="px-4 py-2 bg-gray-700 rounded">Register</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
// Minimal placeholder to avoid blank page when component is missing
</script>

<style scoped>
/* minimal styling - uses tailwind if available */
</style>
