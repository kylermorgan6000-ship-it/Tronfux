<script setup lang="ts">
import { ArrowLeft, Search, BookOpen, LifeBuoy, Users, Zap } from 'lucide-vue-next'
import { useRouter } from 'vue-router'
import { ref } from 'vue'

const router = useRouter()
const searchQuery = ref('')

const helpCategories = [
  {
    title: 'Getting Started',
    icon: Zap,
    items: [
      'How to create an account',
      'Account verification process',
      'Setting up your profile',
      'Installing the mobile app'
    ]
  },
  {
    title: 'Trading Basics',
    icon: BookOpen,
    items: [
      'How to place a trade',
      'Understanding market orders',
      'Risk management tips',
      'Trading hours and availability'
    ]
  },
  {
    title: 'Account & Security',
    icon: Users,
    items: [
      'Two-factor authentication',
      'Password reset',
      'Account recovery',
      'Security best practices'
    ]
  },
  {
    title: 'Deposits & Withdrawals',
    icon: LifeBuoy,
    items: [
      'Deposit methods',
      'Processing times',
      'Withdrawal requests',
      'Transaction history'
    ]
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
      <h1 class="font-semibold text-lg">Help Center</h1>
    </div>

    <!-- Content -->
    <div class="max-w-5xl mx-auto px-4 py-12">
      <!-- Search -->
      <div class="mb-12">
        <div class="relative">
          <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/40" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search for help..."
            class="w-full pl-12 pr-4 py-3 bg-white/5 border border-white/10 rounded-lg outline-none text-white placeholder:text-white/40"
          />
        </div>
      </div>

      <!-- Categories -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div v-for="category in helpCategories" :key="category.title" class="bg-white/5 rounded-lg p-6 border border-white/10 hover:border-white/20 transition">
          <div class="flex items-center gap-3 mb-4">
            <component :is="category.icon" class="w-6 h-6 text-green-400" />
            <h3 class="text-lg font-semibold">{{ category.title }}</h3>
          </div>
          <ul class="space-y-2">
            <li v-for="item in category.items" :key="item" class="text-white/70 text-sm hover:text-green-400 cursor-pointer transition">
              • {{ item }}
            </li>
          </ul>
        </div>
      </div>

      <!-- Contact Support -->
      <div class="mt-12 bg-gradient-to-r from-green-500/10 to-transparent border border-green-500/20 rounded-lg p-8 text-center">
        <h3 class="text-xl font-semibold mb-3">Can't find what you're looking for?</h3>
        <p class="text-white/60 mb-6">Our support team is ready to help 24/7</p>
        <button
          @click="router.push('/contacts')"
          class="px-6 py-2 bg-green-500 text-black font-semibold rounded-lg hover:bg-green-400 transition"
        >
          Contact Support
        </button>
      </div>
    </div>
  </div>
</template>
