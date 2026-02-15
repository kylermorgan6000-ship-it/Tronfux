<script setup lang="ts">
import { ArrowRight, ShieldCheck, Zap, Smartphone } from 'lucide-vue-next'
import Button  from '@/components/ui/button'

// Generate plenty of stars (120+) across the hero
const stars = Array.from({ length: 120 }, (_, i) => ({
  id: i,
  size: Math.random() * 2 + 1,      // 1px - 3px
  left: Math.random() * 100 + '%',
  top: Math.random() * 100 + '%',
  duration: 10 + Math.random() * 15 + 's', // 10s - 25s
  delay: Math.random() * 10 + 's'
}));
</script>

<template>
  <!-- HERO -->
  <section class="relative overflow-hidden bg-black bg-gradient-to-b from-black via-black/80 to-black pt-28 pb-24 md:pt-36">

    <!-- Stars Layer -->
    <div class="pointer-events-none absolute inset-0 z-0">
      <div v-for="star in stars" :key="star.id"
           class="absolute bg-white rounded-full opacity-60 animate-star"
           :style="{
             width: star.size + 'px',
             height: star.size + 'px',
             top: star.top,
             left: star.left,
             animationDuration: star.duration,
             animationDelay: star.delay
           }"></div>
    </div>

    <div class="relative z-20 mx-auto max-w-5xl px-6">
      <div class="text-left md:text-left space-y-8">

        <!-- Headline -->
<h1 class="text-[27px] font-bold leading-tight md:text-5xl text-white">
  A Smarter Way to Trade
  <span class="block">
    with Tronpix
    <span class="text-green-400 font-extrabold drop-shadow-[0_0_10px_rgba(34,197,94,0.8)] animate-glow">
      Trades
    </span>
  </span>
</h1>

        <!-- Description -->
        <p class="mx-auto md:mx-0 max-w-xl text-base text-white/80">
          Trade forex, crypto, and global markets on a secure,
          lightning-fast trading platform built for clarity,
          speed, and control.
        </p>

        <!-- CTAs (HORIZONTAL) -->
   <div class="flex flex-nowrap justify-center md:justify-start gap-4">
          <!-- Start Trading: gradient black→green, white text -->
          <router-link to="/auth/register">
            <Button
              class="rounded-full px-8 py-6 gap-2 text-[15px] text-white bg-gradient-to-r from-black to-green-600 border-2  shadow-[0_0_12px_rgba(34,197,94,0.7)] hover:shadow-[0_0_18px_rgba(34,197,94,0.9)]"
            >
              Start Trading
              <ArrowRight class="h-5 w-5" />
            </Button>
          </router-link>

          <!-- View App Preview: outlined only -->
          <a href="/app-preview" class="inline-block">
            <Button
              variant="outline"
              class="rounded-full px-8 py-6 text-[14px] border-2 border-green-400 text-white shadow-[0_0_12px_rgba(34,197,94,0.7)] hover:shadow-[0_0_18px_rgba(34,197,94,0.9)] bg-transparent"
            >
              View App Preview
            </Button>
          </a>
        </div>


        <!-- TRUST INDICATORS -->
        <div class="pt-6 space-y-3 text-sm text-white">

          <!-- Row 1: Left & Right -->
          <div class="flex justify-between max-w-sm mx-auto md:mx-0">
            <div class="flex items-center gap-2">
              <Zap class="h-4 w-4 text-green-400" />
              <span>Fast execution</span>
            </div>

            <div class="flex items-center gap-2">
              <ShieldCheck class="h-4 w-4 text-green-400" />
              <span>Secure accounts</span>
            </div>
          </div>

          <!-- Row 2: Center -->
          <div class="flex justify-center">
            <div class="flex items-center gap-2">
              <Smartphone class="h-4 w-4 text-green-400" />
              <span>Mobile ready</span>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* ===== Stars animation ===== */
@keyframes star-up {
  0% { transform: translateY(100vh); opacity: 0; }
  10% { opacity: 1; }
  100% { transform: translateY(-10vh); opacity: 0; }
}

.animate-star {
  animation-name: star-up;
  animation-timing-function: linear;
  animation-iteration-count: infinite;
}

/* ===== Glowing header effect ===== */
@keyframes glow {
  0%, 100% { text-shadow: 0 0 8px rgba(34,197,94,0.8), 0 0 16px rgba(34,197,94,0.6); }
  50% { text-shadow: 0 0 12px rgba(34,197,94,1), 0 0 24px rgba(34,197,94,0.7); }
}
.animate-glow {
  animation: glow 2.5s ease-in-out infinite;
}
</style>