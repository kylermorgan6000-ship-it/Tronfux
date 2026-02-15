<script setup lang="ts">
import { Shield, TrendingUp, Zap, BarChart2, Users, Coffee } from 'lucide-vue-next';

// Feature cards (object with title, description, icon)
const features = [
  { 
    title: 'Secure & Trusted', 
    description: 'Bank-level security ensures your funds and personal data are protected at all times. Our platform uses advanced encryption and multi-factor authentication for maximum safety.',
    icon: Shield 
  },
  { 
    title: 'Real-Time Market Data', 
    description: 'Get instant updates on Forex, crypto, and stock markets. Never miss a trade with live charts, price alerts, and advanced analytics tools.',
    icon: TrendingUp 
  },
  { 
    title: 'Instant Withdrawals', 
    description: 'Withdraw your funds instantly to your bank account or crypto wallet. Fast, reliable, and transparent, with zero hidden fees.',
    icon: Zap 
  },
  { 
    title: 'Advanced Analytics', 
    description: 'Powerful charts, technical indicators, and insights to optimize your trading strategies. Stay ahead with actionable market data.',
    icon: BarChart2 
  },
  { 
    title: 'Global Community', 
    description: 'Join a vibrant global network of traders. Share strategies, learn from experts, and grow together with thousands of active members worldwide.',
    icon: Users 
  },
  { 
    title: '24/7 Support', 
    description: 'Our dedicated support team is always available. Get help via live chat, email, or phone anytime you need assistance.',
    icon: Coffee 
  },
];

// Generate multiple floating orbs
const orbs = Array.from({ length: 12 }, (_, i) => ({
  id: i,
  top: Math.random() * 80 + '%',
  left: Math.random() * 90 + '%',
  size: 40 + Math.random() * 80, // 40px - 120px
  color: ['#0f0', '#0fa', '#0c0', '#0ff', '#0f6', '#0f9'][i % 6],
  class: `orb-${i % 2 === 0 ? 'up' : 'down'}`,
  duration: 12 + Math.random() * 10 + 's', // random 12s-22s
}));
</script>

<template>
  <section class="relative bg-black py-24 px-6 lg:px-20 text-white overflow-hidden">
    
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

    <!-- Section Heading -->
    <div class="max-w-6xl mx-auto text-center mb-16 relative z-10">
      <h2 class="text-[25px] md:text-4xl font-bold mb-4 text-white-400 whitespace-nowrap">Why Choose TronpixTrades</h2>
      <p class="text-gray-400 text-sm md:text-base">
        Professional trading made simple. Secure, fast, and transparent all with the tronpixtrades broker
      </p>
    </div>

    <!-- Feature Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 max-w-6xl mx-auto relative z-10">
      <div 
        v-for="(feature, i) in features" 
        :key="i"
        class="glass p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-transform duration-300 animate-fade-up"
        :style="{ animationDelay: `${i*0.2}s` }"
      >
        <component :is="feature.icon" class="w-10 h-10 text-green-400 mb-4"/>
        <h3 class="text-xl font-semibold mb-2 text-white-300">{{ feature.title }}</h3>
        <p class="text-gray-300 text-sm">{{ feature.description }}</p>
      </div>
    </div>
  </section>
</template>

<style scoped>
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

/* Glassmorphic card with black-green theme */
.glass {
  background: linear-gradient(135deg, rgba(0,255,0,0.05), rgba(0,255,0,0.15));
  background-color: hsl(0 0% 0% / 0.35); /* semi-transparent black */
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-radius: 1.5rem;
  border: 1px solid hsl(120 60% 50% / 0.2); /* subtle green border */
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.glass:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 0 40px rgba(0,255,0,0.3);
}
</style>