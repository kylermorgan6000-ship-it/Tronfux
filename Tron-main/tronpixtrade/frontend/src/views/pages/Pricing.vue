<script setup lang="ts">
import { ref } from 'vue'
import { Check } from 'lucide-vue-next'

// Pricing plans
const plans = [
  {
    title: 'Basic',
    price: 19,
    period: 'month',
    features: [
      'Access to real-time market data',
      'Basic charting tools',
      'Email support'
    ]
  },
  {
    title: 'Pro',
    price: 49,
    period: 'month',
    features: [
      'Advanced charting & indicators',
      'Priority withdrawals',
      'Live chat support',
      'API access'
    ],
    highlight: true
  },
  {
    title: 'Enterprise',
    price: 99,
    period: 'month',
    features: [
      'Dedicated account manager',
      'Custom trading tools',
      'Team onboarding & training',
      'Full API & integrations'
    ]
  }
]
</script>

<template>
  <section class="relative bg-black text-white py-24 px-6 lg:px-20 overflow-hidden">
    
    <!-- Section Heading -->
    <div class="max-w-4xl mx-auto text-center mb-16">
      <h2 class="text-3xl md:text-4xl font-bold mb-4 text-white">Pricing Plans</h2>
      <p class="text-gray-400 text-sm md:text-base">
        Choose the plan that fits your trading goals. Transparent pricing with no hidden fees.
      </p>
    </div>

    <!-- Pricing Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto relative z-10">
      <div
        v-for="(plan, i) in plans"
        :key="i"
        :class="[
          'glass p-8 rounded-2xl shadow-lg transform transition-transform hover:scale-105 hover:shadow-2xl',
          plan.highlight ? 'border-2 border-green-400 scale-105 shadow-2xl' : ''
        ]"
      >
        <!-- Plan Title -->
        <h3 class="text-xl md:text-2xl font-semibold mb-2 text-white">{{ plan.title }}</h3>

        <!-- Price -->
        <p class="text-3xl md:text-4xl font-bold text-green-400 mb-4">
          ${{ plan.price }}<span class="text-sm text-white/70">/{{ plan.period }}</span>
        </p>

        <!-- Features -->
        <ul class="space-y-3 mb-6">
          <li v-for="(feat, idx) in plan.features" :key="idx" class="flex items-center gap-2 text-gray-300">
            <Check class="w-5 h-5 text-green-400 flex-shrink-0" /> {{ feat }}
          </li>
        </ul>

        <!-- CTA Button -->
        <button
          class="w-full py-3 rounded-xl bg-green-500 text-black font-semibold hover:shadow-[0_0_20px_rgba(34,197,94,0.5)] transition-all"
        >
          Choose Plan
        </button>
      </div>
    </div>

  </section>
</template>

<style scoped>
/* Glassmorphic Card */
.glass {
  background: linear-gradient(135deg, rgba(0,255,0,0.05), rgba(0,255,0,0.15));
  background-color: hsl(0 0% 0% / 0.35);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-radius: 1.5rem;
  border: 1px solid hsl(120 60% 50% / 0.2);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Highlighted Plan */
.border-green-400 {
  border-color: hsl(120 60% 50%);
}
</style>