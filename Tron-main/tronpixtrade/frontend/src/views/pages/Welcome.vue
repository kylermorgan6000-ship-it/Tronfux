<script setup lang="ts">
import { onMounted, ref } from 'vue'

import HeaderSection from '@/components/landing/HeaderSection.vue'
import HeroSection from '@/components/landing/HeroSection.vue'
import FeaturesSection from '@/components/landing/FeaturesSection.vue'
import MarketPricesSection from '@/components/landing/MarketPricesSection.vue'
import AppPreviewSection from '@/components/landing/AppPreviewSection.vue'
import WhyChooseUsSection from '@/components/landing/WhyChooseUsSection.vue'
import FaqSection from '@/components/landing/FaqSection.vue'
import CTASection from '@/components/landing/CTASection.vue'
import FooterSection from '@/components/landing/FooterSection.vue'

const preloaderHidden = ref(false)

onMounted(() => {
  setTimeout(() => {
    preloaderHidden.value = true
  }, 3000) // small delay for smooth fade
})
</script>

<template>
  <!-- Preloader -->
  <div
    v-if="!preloaderHidden"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-background"
  >
    <svg
      class="w-16 h-16 text-green-500 preloader-icon"
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 40 40"
      fill="none"
    >
      <path d="M20 2L38 20L20 38L2 20L20 2Z" stroke="currentColor" stroke-width="2.4" stroke-linejoin="round"/>
      <path d="M12 13h16v3h-6v12h-4V16h-6v-3z" fill="currentColor"/>
    </svg>
  </div>

  <!-- Main landing content -->
  <main
    class="bg-background text-foreground transition-opacity duration-700"
    :class="{ 'opacity-0': !preloaderHidden, 'opacity-100': preloaderHidden }"
  >
    <HeaderSection />
    <HeroSection />
    <FeaturesSection />
    <MarketPricesSection />
    <AppPreviewSection />
    <WhyChooseUsSection /> 
    <FaqSection />
    <CTASection />
    <FooterSection />
  </main>
</template>

<style scoped>
/* ===== Preloader Glow + Bounce ===== */
.preloader-icon {
  animation: bounce-glow 1.5s ease-in-out infinite, glow-pulse 2s ease-in-out infinite alternate;
}

/* Smooth bounce */
@keyframes bounce-glow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-16px); }
}

/* Glow pulse */
@keyframes glow-pulse {
  0% { filter: drop-shadow(0 0 8px rgba(34,197,94,0.7)); }
  50% { filter: drop-shadow(0 0 16px rgba(34,197,94,1)); }
  100% { filter: drop-shadow(0 0 8px rgba(34,197,94,0.7)); }
}
</style>