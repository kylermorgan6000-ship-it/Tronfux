<script setup lang="ts">
import { ArrowLeft, Shield, TrendingUp, Users } from 'lucide-vue-next'
import { useRouter } from 'vue-router'

const router = useRouter()

const partnerPrograms = [
  {
    title: 'Affiliate Program',
    description: 'Earn commissions by referring new traders to our platform',
    features: ['Up to 30% commission', 'Real-time tracking', 'Marketing materials provided'],
    icon: TrendingUp
  },
  {
    title: 'White Label Solutions',
    description: 'Launch your own branded trading platform powered by our technology',
    features: ['Custom branding', 'Full API access', 'Dedicated support team'],
    icon: Shield
  },
  {
    title: 'Strategic Partnerships',
    description: 'Collaborate with us for mutual growth and market expansion',
    features: ['Co-marketing opportunities', 'Revenue sharing', 'Integration support'],
    icon: Users
  }
]
</script>

<template>
  <div class="min-h-screen bg-black text-white">
    <!-- Header -->
    <div class="sticky top-0 z-40 bg-black shadow-sm px-4 py-4 flex items-center gap-3 border-b border-white/10">
      <button @click="router.back()" class="text-white/70 hover:text-white">
        <ArrowLeft class="w-5 h-5" />
      </button>
      <h1 class="font-semibold text-lg">Partners</h1>
    </div>

    <!-- Content -->
    <div class="max-w-5xl mx-auto px-4 py-12">
      <!-- Hero Section -->
      <div class="mb-12 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Partner With Us</h2>
        <p class="text-white/60 text-lg">Grow your business with TronpixTrades</p>
      </div>

      <!-- Partner Programs -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div
          v-for="program in partnerPrograms"
          :key="program.title"
          class="bg-white/5 rounded-lg p-6 border border-white/10 hover:border-green-500/30 transition"
        >
          <component :is="program.icon" class="w-8 h-8 text-green-400 mb-4" />
          <h3 class="text-xl font-semibold mb-2">{{ program.title }}</h3>
          <p class="text-white/60 text-sm mb-4">{{ program.description }}</p>
          <ul class="space-y-2">
            <li v-for="feature in program.features" :key="feature" class="text-white/70 text-sm flex items-center gap-2">
              <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
              {{ feature }}
            </li>
          </ul>
          <button class="mt-6 w-full py-2 bg-green-500 text-black font-semibold rounded-lg hover:bg-green-400 transition">
            Learn More
          </button>
        </div>
      </div>

      <!-- Benefits Section -->
      <div class="mb-12">
        <h3 class="text-2xl font-bold mb-6">Why Partner With Us?</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="bg-white/5 rounded-lg p-6 border border-white/10">
            <h4 class="font-semibold mb-3">Proven Track Record</h4>
            <p class="text-white/60 text-sm">Join thousands of successful partners generating revenue with our platform</p>
          </div>
          <div class="bg-white/5 rounded-lg p-6 border border-white/10">
            <h4 class="font-semibold mb-3">Comprehensive Support</h4>
            <p class="text-white/60 text-sm">Dedicated account managers and 24/7 technical support for all partners</p>
          </div>
          <div class="bg-white/5 rounded-lg p-6 border border-white/10">
            <h4 class="font-semibold mb-3">Competitive Commissions</h4>
            <p class="text-white/60 text-sm">Industry-leading commission rates and performance-based incentives</p>
          </div>
          <div class="bg-white/5 rounded-lg p-6 border border-white/10">
            <h4 class="font-semibold mb-3">Marketing Resources</h4>
            <p class="text-white/60 text-sm">Access to promotional materials, guides, and co-marketing opportunities</p>
          </div>
        </div>
      </div>

      <!-- Requirements -->
      <div class="mb-12 bg-white/5 rounded-lg p-8 border border-white/10">
        <h3 class="text-xl font-semibold mb-4">Partnership Requirements</h3>
        <ul class="space-y-3">
          <li class="flex items-start gap-3">
            <span class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-black flex-shrink-0 mt-1 text-sm font-semibold">1</span>
            <p class="text-white/80">Maintain professional business standards and regulatory compliance</p>
          </li>
          <li class="flex items-start gap-3">
            <span class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-black flex-shrink-0 mt-1 text-sm font-semibold">2</span>
            <p class="text-white/80">Use approved marketing materials and comply with brand guidelines</p>
          </li>
          <li class="flex items-start gap-3">
            <span class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-black flex-shrink-0 mt-1 text-sm font-semibold">3</span>
            <p class="text-white/80">Maintain transparent reporting and payment processing</p>
          </li>
          <li class="flex items-start gap-3">
            <span class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-black flex-shrink-0 mt-1 text-sm font-semibold">4</span>
            <p class="text-white/80">Regular communication and adherence to partnership agreement</p>
          </li>
        </ul>
      </div>

      <!-- CTA -->
      <div class="bg-gradient-to-r from-green-500/10 to-transparent border border-green-500/20 rounded-lg p-8 text-center">
        <h3 class="text-xl font-semibold mb-3">Ready to Get Started?</h3>
        <p class="text-white/60 mb-6">Contact our partnerships team to discuss opportunities</p>
        <a
          href="mailto:partnerships@tronpixtrades.com"
          class="inline-block px-6 py-2 bg-green-500 text-black font-semibold rounded-lg hover:bg-green-400 transition"
        >
          Apply Now
        </a>
      </div>
    </div>
  </div>
</template>
