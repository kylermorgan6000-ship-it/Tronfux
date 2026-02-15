<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue'
import { Button } from '@/components/ui/button'
</script>

<template>
  <header class="w-full flex items-center justify-between py-3 bg-black bg-opacity-80 backdrop-blur-md shadow-md fixed top-0 left-0 z-50">

    <!-- Logo + Brand Name -->
    <div class="flex items-center gap-2 whitespace-nowrap pl-2 pr-3">
      <!-- App Logo with glow -->
      <div class="flex items-center justify-center rounded-md">
        <AppLogo class="drop-shadow-[0_0_12px_hsl(160,100%,50%)] animate-glow-logo" />
      </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex items-center gap-3 pl-3 pr-2">
      <!-- Get Started (narrower) -->
       <router-link to="/auth/register">
      <Button
        class="rounded-full px-4 py-2 bg-gradient-to-r from-green-500 to-green-700 text-white shadow-[0_0_8px_rgba(34,197,94,0.7)] hover:shadow-[0_0_14px_rgba(34,197,94,0.9)] text-sm"
      >
        Get Started
      </Button>
      </router-link>

      <!-- Login (narrower outlined button) -->
      <router-link to="/auth/login">
      <Button
        variant="outline"
        class="rounded-full px-4 py-2 border-2 bg-black border-green-400 text-white hover:bg-green-400 hover:text-black transition-all text-sm"
      >
        Login
      </Button>
      </router-link>
    </div>

  </header>
</template>

<style scoped>
/* Glow animation for AppLogoIcon */
@keyframes glow-logo {
  0%, 100% { filter: drop-shadow(0 0 8px hsl(160,100%,50%)); }
  50% { filter: drop-shadow(0 0 14px hsl(160,100%,50%)); }
}
.animate-glow-logo {
  animation: glow-logo 2s ease-in-out infinite;
}
</style>