<script setup lang="ts">
import { ArrowLeft, Calendar, User, ArrowRight } from 'lucide-vue-next'
import { useRouter } from 'vue-router'

const router = useRouter()

const blogPosts = [
  {
    id: 1,
    title: 'Getting Started with Forex Trading',
    excerpt: 'Learn the basics of forex trading and how to execute your first trade on our platform.',
    author: 'Sarah Johnson',
    date: '2025-03-15',
    category: 'Guides',
    image: 'bg-gradient-to-br from-blue-500 to-blue-600'
  },
  {
    id: 2,
    title: 'Market Analysis: Trends to Watch in 2025',
    excerpt: 'Our expert analysis of upcoming market trends and how they may impact your trading strategy.',
    author: 'Michael Chen',
    date: '2025-03-12',
    category: 'Analysis',
    image: 'bg-gradient-to-br from-purple-500 to-purple-600'
  },
  {
    id: 3,
    title: 'Risk Management Best Practices',
    excerpt: 'Discover essential risk management strategies to protect your trading capital.',
    author: 'Emma Wilson',
    date: '2025-03-10',
    category: 'Guides',
    image: 'bg-gradient-to-br from-orange-500 to-orange-600'
  },
  {
    id: 4,
    title: 'Cryptocurrency Trading 101',
    excerpt: 'A complete beginner\'s guide to trading cryptocurrencies on TronpixTrades.',
    author: 'David Lee',
    date: '2025-03-08',
    category: 'Education',
    image: 'bg-gradient-to-br from-yellow-500 to-yellow-600'
  },
  {
    id: 5,
    title: 'Technical Analysis Tools Every Trader Should Know',
    excerpt: 'Master the essential technical analysis indicators used by professional traders.',
    author: 'Lisa Brown',
    date: '2025-03-05',
    category: 'Tools',
    image: 'bg-gradient-to-br from-green-500 to-green-600'
  },
  {
    id: 6,
    title: 'Building Your Trading Strategy',
    excerpt: 'Step-by-step guide to developing and testing your personal trading strategy.',
    author: 'James Mitchell',
    date: '2025-03-01',
    category: 'Guides',
    image: 'bg-gradient-to-br from-pink-500 to-pink-600'
  }
]

const categories = ['All', 'Guides', 'Analysis', 'Education', 'Tools']
</script>

<template>
  <div class="min-h-screen bg-black text-white">
    <!-- Header -->
    <div class="sticky top-0 z-40 bg-black shadow-sm px-4 py-4 flex items-center gap-3 border-b border-white/10">
      <button @click="router.back()" class="text-white/70 hover:text-white">
        <ArrowLeft class="w-5 h-5" />
      </button>
      <h1 class="font-semibold text-lg">Blog</h1>
    </div>

    <!-- Content -->
    <div class="max-w-6xl mx-auto px-4 py-12">
      <!-- Hero Section -->
      <div class="mb-12 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Trading Insights & Updates</h2>
        <p class="text-white/60 text-lg">Stay informed with expert analysis and trading tips</p>
      </div>

      <!-- Categories -->
      <div class="flex overflow-x-auto gap-3 mb-12 pb-4">
        <button
          v-for="category in categories"
          :key="category"
          class="px-4 py-2 whitespace-nowrap rounded-lg border border-white/20 hover:border-green-500 hover:text-green-400 transition text-white/70"
        >
          {{ category }}
        </button>
      </div>

      <!-- Blog Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="post in blogPosts"
          :key="post.id"
          class="bg-white/5 rounded-lg overflow-hidden hover:border-green-500/30 border border-white/10 hover:bg-white/10 transition cursor-pointer group"
        >
          <!-- Featured Image -->
          <div :class="[post.image, 'h-40 relative overflow-hidden']">
            <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition"></div>
            <span class="absolute top-3 right-3 px-3 py-1 bg-green-500 text-black text-xs font-semibold rounded-full">
              {{ post.category }}
            </span>
          </div>

          <!-- Content -->
          <div class="p-5">
            <h3 class="font-semibold text-white mb-2 group-hover:text-green-400 transition line-clamp-2">
              {{ post.title }}
            </h3>
            <p class="text-white/60 text-sm mb-4 line-clamp-2">
              {{ post.excerpt }}
            </p>

            <!-- Meta Information -->
            <div class="flex items-center justify-between text-xs text-white/50 border-t border-white/10 pt-3">
              <div class="flex items-center gap-3">
                <span class="flex items-center gap-1">
                  <User class="w-3 h-3" />
                  {{ post.author }}
                </span>
              </div>
              <span class="flex items-center gap-1">
                <Calendar class="w-3 h-3" />
                {{ new Date(post.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}
              </span>
            </div>

            <!-- Read More Link -->
            <div class="mt-3 flex items-center gap-2 text-green-400 text-sm group-hover:gap-3 transition">
              Read More
              <ArrowRight class="w-4 h-4" />
            </div>
          </div>
        </div>
      </div>

      <!-- Subscribe Section -->
      <div class="mt-16 bg-gradient-to-r from-green-500/10 to-transparent border border-green-500/20 rounded-lg p-8 text-center">
        <h3 class="text-xl font-semibold mb-3">Subscribe to Our Newsletter</h3>
        <p class="text-white/60 mb-6">Get the latest trading insights and updates delivered to your inbox</p>
        <div class="flex gap-2 max-w-md mx-auto">
          <input
            type="email"
            placeholder="Enter your email"
            class="flex-1 px-4 py-2 bg-white/10 border border-white/20 rounded-lg outline-none text-white placeholder:text-white/40 focus:border-green-500"
          />
          <button class="px-6 py-2 bg-green-500 text-black font-semibold rounded-lg hover:bg-green-400 transition">
            Subscribe
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
