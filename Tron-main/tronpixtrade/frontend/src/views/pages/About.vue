<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue'
import { Shield, Users, Zap, BarChart2, Coffee } from 'lucide-vue-next'

// Section content
const sections = [
  {
    title: 'Our Mission',
    description: 'To empower traders worldwide with a secure, fast, and transparent trading platform that delivers real-time market data and instant withdrawals.',
    icon: Shield
  },
  {
    title: 'Our Vision',
    description: 'To be the leading global trading platform, trusted by traders across all markets, providing cutting-edge tools and educational resources.',
    icon: BarChart2
  },
  {
    title: 'Community & Support',
    description: 'Join a vibrant global network of traders and access 24/7 support via live chat, email, or phone.',
    icon: Users
  },
  {
    title: 'Fast & Reliable',
    description: 'Experience instant deposits and withdrawals, real-time updates, and top-tier platform performance.',
    icon: Zap
  },
  {
    title: '24/7 Assistance',
    description: 'Our dedicated support team is always ready to assist you with any inquiries or challenges.',
    icon: Coffee
  }
]

// Floating orbs
const orbs = Array.from({ length: 10 }, (_, i) => ({
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
  <div class="relative bg-black text-white min-h-screen overflow-hidden">

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
        <a href="/" class="hover:text-green-400 transition-colors">Home</a>
        <a href="/about" class="text-green-400 font-semibold">About</a>
        <a href="/markets" class="hover:text-green-400 transition-colors">Markets</a>
        <a href="/login" class="hover:text-green-400 transition-colors">Login</a>
      </nav>
    </header>

    <!-- Hero -->
    <section class="text-center py-24 px-6 lg:px-20 relative z-10">
      <h1 class="text-4xl md:text-5xl font-bold mb-4">About TronpixTrades</h1>
      <p class="text-gray-400 max-w-2xl mx-auto text-sm md:text-base">
        We combine cutting-edge technology with expert market insights to provide the ultimate trading experience. Learn more about our mission, vision, and community.
      </p>
    </section>

    <!-- About Features -->
    <section class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 px-6 lg:px-20 relative z-10 pb-24">
      <div
        v-for="(section, i) in sections"
        :key="i"
        class="glass p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-transform duration-300 animate-fade-up"
        :style="{ animationDelay: `${i*0.2}s` }"
      >
        <component :is="section.icon" class="w-10 h-10 text-green-400 mb-4"/>
        <h3 class="text-xl font-semibold mb-2">{{ section.title }}</h3>
        <p class="text-gray-300 text-sm">{{ section.description }}</p>
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

/* Fade-up animation for cards */
@keyframes fade-up {
  0% { opacity: 0; transform: translateY(40px); }
  100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-up { opacity: 0; animation: fade-up 0.8s ease forwards; }

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