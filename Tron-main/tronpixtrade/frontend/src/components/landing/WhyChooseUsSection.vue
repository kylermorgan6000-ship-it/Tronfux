<script setup lang="ts">
import {
  ShieldCheck,
  Zap,
  LineChart,
  Globe,
  Wallet,
  Smartphone,
} from 'lucide-vue-next'

const trustPillars = [
  {
    icon: ShieldCheck,
    title: 'Regulated & Secure',
    desc: 'Industry-grade security, encryption, and compliance standards.',
  },
  {
    icon: Zap,
    title: 'Low-Latency Execution',
    desc: 'Ultra-fast order execution for precision trading.',
  },
  {
    icon: LineChart,
    title: 'Advanced Trading Tools',
    desc: 'Professional tools for technical and fundamental analysis.',
  },
  {
    icon: Globe,
    title: 'Global Market Access',
    desc: 'Trade forex, crypto, and global indices seamlessly.',
  },
  {
    icon: Wallet,
    title: 'Fast Deposits & Withdrawals',
    desc: 'Transparent, reliable, and instant transactions.',
  },
  {
    icon: Smartphone,
    title: 'Cross-Platform Trading',
    desc: 'Optimized for web and mobile devices.',
  },
]
</script>

<template>
  <section class="relative bg-black py-24 px-6 lg:px-20">
    <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-16 items-center">

      <!-- LEFT: Authority Copy -->
      <div>
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
          Infrastructure You Can Trust
        </h2>

        <p class="text-white/70 text-base leading-relaxed mb-8">
          TronpixTrades is engineered for traders who demand reliability,
          transparency, and institutional-grade performance — without
          compromise.
        </p>

        <!-- Optional trust stats -->
        <div class="grid grid-cols-2 gap-6 mt-10">
          <div>
            <p class="text-green-400 text-2xl font-bold">99.99%</p>
            <p class="text-white/60 text-sm">Platform uptime</p>
          </div>
          <div>
            <p class="text-green-400 text-2xl font-bold">&lt;20ms</p>
            <p class="text-white/60 text-sm">Order latency</p>
          </div>
        </div>
      </div>

      <!-- RIGHT: Trust Pillars -->
      <div class="space-y-8">
        <div
          v-for="(pillar, i) in trustPillars"
          :key="i"
          class="flex gap-5 items-start"
        >
          <!-- Icon -->
          <div class="flex-shrink-0">
            <component
              :is="pillar.icon"
              class="w-7 h-7 text-green-400 mt-1"
            />
          </div>

          <!-- Text -->
          <div>
            <h3 class="text-white font-semibold mb-1">
              {{ pillar.title }}
            </h3>
            <p class="text-white/65 text-sm leading-relaxed">
              {{ pillar.desc }}
            </p>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<style scoped>
/* ===== Glass Card ===== */
.glass-card {
  background: linear-gradient(
    135deg,
    rgba(0, 255, 0, 0.04),
    rgba(0, 255, 0, 0.1)
  );
  background-color: rgba(0, 0, 0, 0.45);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-radius: 1.25rem;
  border: 1px solid rgba(34, 197, 94, 0.25);
  padding: 2rem;
  transition: transform 0.35s ease, box-shadow 0.35s ease;
}

/* Hover: subtle lift (not flashy) */
.glass-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 50px rgba(0, 255, 0, 0.25);
}
</style>