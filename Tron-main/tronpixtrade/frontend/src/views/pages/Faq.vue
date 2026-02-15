<script setup lang="ts">
import { ref } from 'vue'
import AppLogo from '@/components/AppLogo.vue'
import { ChevronDown, ChevronUp } from 'lucide-vue-next'

// FAQ items
const faqs = ref([
  {
    question: 'What is TronpixTrades?',
    answer: 'TronpixTrades is a professional trading platform for forex, crypto, and commodities. We provide secure, fast, and transparent trading tools for both beginners and professionals.',
    open: false
  },
  {
    question: 'How do I create an account?',
    answer: 'Click on the Get Started or Register button on the landing page, fill in your personal details, and verify your email. Once registered, you can start trading immediately.',
    open: false
  },
  {
    question: 'What payment methods are supported?',
    answer: 'We support bank transfers, Paystack, PayPal, PalmPay, and Opay. Withdrawals are processed instantly to your preferred account or wallet.',
    open: false
  },
  {
    question: 'Is my account safe?',
    answer: 'Yes! We use bank-level security, advanced encryption, and multi-factor authentication to protect your account and funds at all times.',
    open: false
  },
  {
    question: 'Can I trade on mobile?',
    answer: 'Absolutely. TronpixTrades supports mobile browsers and our dedicated mobile app, so you can trade on the go.',
    open: false
  },
])

// Toggle FAQ
const toggle = (index) => {
  faqs.value[index].open = !faqs.value[index].open
}

// Floating orbs
const orbs = Array.from({ length: 12 }, (_, i) => ({
  id: i,
  top: Math.random() * 80 + '%',
  left: Math.random() * 90 + '%',
  size: 40 + Math.random() * 80,
  color: ['#0f0', '#0fa', '#0c0', '#0ff', '#0f6'][i % 5],
  class: `orb-${i % 2 === 0 ? 'up' : 'down'}`,
  duration: 12 + Math.random() * 10 + 's',
}))
</script>

<template>
  <div class="relative bg-black min-h-screen text-white overflow-hidden">

    <!-- Floating Orbs -->
    <div class="absolute inset-0 pointer-events-none">
      <div 
        v-for="orb in orbs"
        :key="orb.id"
        :class="`absolute rounded-full opacity-20 ${orb.class}`"
        :style="{
          width: orb.size + 'px',
          height: orb.size + 'px',
          top: orb.top,
          left: orb.left,
          backgroundColor: orb.color,
          animationDuration: orb.duration
        }"
      ></div>
    </div>

    <!-- Header -->
    <header class="sticky top-0 z-50 bg-black/80 backdrop-blur-md shadow-md px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <AppLogo class="w-8 h-8 drop-shadow-[0_0_12px_hsl(160,100%,50%)] animate-glow-logo"/>
        <span class="font-bold text-lg">TronpixTrades</span>
      </div>
      <nav class="flex gap-4">
        <router-link to="/" class="hover:text-green-400 transition-colors">Home</router-link>
        <router-link to="/about" class="hover:text-green-400 transition-colors">About</router-link>
        <router-link to="/contact" class="hover:text-green-400 transition-colors">Contact</router-link>
        <router-link to="/faq" class="text-green-400 font-semibold">FAQ</router-link>
        <router-link to="/login" class="hover:text-green-400 transition-colors">Login</router-link>
      </nav>
    </header>

    <!-- Hero / Intro -->
    <section class="text-center py-24 px-6 lg:px-20 relative z-10">
      <h1 class="text-4xl md:text-5xl font-bold mb-4">Frequently Asked Questions</h1>
      <p class="text-gray-400 max-w-2xl mx-auto text-sm md:text-base">
        Find answers to the most common questions about TronpixTrades. If you still have doubts, contact us anytime.
      </p>
    </section>

    <!-- FAQ Accordion -->
    <section class="max-w-4xl mx-auto px-6 lg:px-20 relative z-10 space-y-4 pb-24">
      <div 
        v-for="(item, i) in faqs" 
        :key="i"
        class="glass p-4 rounded-2xl shadow-lg cursor-pointer transition-all hover:shadow-2xl"
        @click="toggle(i)"
      >
        <div class="flex justify-between items-center">
          <h3 class="font-semibold text-white">{{ item.question }}</h3>
          <span>
            <ChevronUp v-if="item.open" class="w-5 h-5 text-green-400"/>
            <ChevronDown v-else class="w-5 h-5 text-green-400"/>
          </span>
        </div>
        <p 
          v-show="item.open" 
          class="text-gray-300 mt-2 text-sm transition-all duration-300"
        >
          {{ item.answer }}
        </p>
      </div>
    </section>

  </div>
</template>

<style scoped>
/* Glow animation for AppLogo */
@keyframes glow-logo {
  0%, 100% { filter: drop-shadow(0 0 8px hsl(160,100%,50%)); }
  50% { filter: drop-shadow(0 0 14px hsl(160,100%,50%)); }
}
.animate-glow-logo { animation: glow-logo 2s ease-in-out infinite; }

/* Orb float animations */
@keyframes orb-up {
  0% { transform: translateY(0) translateX(0); opacity: 0.15; }
  50% { transform: translateY(-120px) translateX(30px); opacity: 0.3; }
  100% { transform: translateY(0) translateX(0); opacity: 0.15; }
}
@keyframes orb-down {
  0% { transform: translateY(0) translateX(0); opacity: 0.15; }
  50% { transform: translateY(120px) translateX(-30px); opacity: 0.3; }
  100% { transform: translateY(0) translateX(0); opacity: 0.15; }
}
.orb-up { animation: orb-up linear infinite alternate; }
.orb-down { animation: orb-down linear infinite alternate; }

/* Glass card style */
.glass {
  background: linear-gradient(135deg, rgba(0,255,0,0.05), rgba(0,255,0,0.15));
  background-color: hsl(0 0% 0% / 0.35);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-radius: 1.5rem;
  border: 1px solid hsl(120 60% 50% / 0.2);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.glass:hover { transform: translateY(-4px) scale(1.02); box-shadow: 0 0 40px rgba(0,255,0,0.3); }
</style>