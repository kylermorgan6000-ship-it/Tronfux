<template>
  <div class="min-h-screen bg-black text-white flex flex-col">
    <!-- Header -->
    <header class="border-b border-green-400/20 py-4 px-4">
      <div class="max-w-md mx-auto">
        <AppLogo />
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 py-8">
      <div class="w-full max-w-md">
        <!-- Title & Description -->
        <div class="space-y-2 mb-8">
          <h1 class="text-2xl font-bold text-white">{{ title }}</h1>
          <p v-if="description" class="text-gray-400 text-sm">{{ description }}</p>
        </div>

        <!-- Form Content -->
        <slot />

        <!-- Footer Links -->
        <div v-if="showFooterLinks" class="mt-8 pt-6 border-t border-green-400/20 text-center text-xs text-gray-400">
          <p>TronpixTrades © 2024. All rights reserved.</p>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer v-if="showFooter" class="border-t border-green-400/20 py-4 px-4 text-center text-xs text-gray-400">
      <p>Secure | Fast | Reliable</p>
    </footer>
  </div>
</template>

<script setup>
import AppLogo from '@/components/AppLogo.vue'

defineProps({
  title: {
    type: String,
    default: 'Auth'
  },
  description: {
    type: String,
    default: ''
  },
  showFooter: {
    type: Boolean,
    default: true
  },
  showFooterLinks: {
    type: Boolean,
    default: true
  }
})
</script>

<style scoped>
::-webkit-scrollbar {
  width: 4px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: rgb(34 197 94 / 0.3);
  border-radius: 2px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgb(34 197 94 / 0.5);
}
</style>
