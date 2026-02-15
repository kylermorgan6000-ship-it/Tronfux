<script setup lang="ts">
import { ref, computed, onMounted } from "vue"
import { TrendingUp } from "lucide-vue-next"

// ------------------ STATE ------------------
const search = ref("")
const hideSmall = ref(false)

import api from '@/lib/api'

const spotMarkets = ref([])

const loadSpotMarkets = async () => {
  try {
  const res = await api.get('/markets', { params: { category: 'spot' } })
    spotMarkets.value = res.data.data ?? res.data
  } catch (e) {
    console.warn('Spot markets API not available, showing empty list', e)
    spotMarkets.value = []
  }
}

onMounted(() => { void loadSpotMarkets() })

// ------------------ FILTER ------------------
const filteredMarkets = computed(() => {
  return spotMarkets.value
    .filter((m) =>
      m.pair.toLowerCase().includes(search.value.toLowerCase())
    )
    .filter((m) => !hideSmall.value || m.volume > 7000)
})
</script>

<template>
  <div class="min-h-screen bg-black text-white px-4 pt-5">

    <!-- PAGE HEADER -->
    <div class="mb-6">
      <h1 class="text-xl font-bold">Spot Markets</h1>
      <p class="text-sm text-white/40">
        Trade crypto instantly with real-time pricing
      </p>
    </div>

    <!-- SEARCH + FILTER -->
    <div class="flex items-center gap-2 mb-5">

      <!-- Search -->
      <div
        class="flex items-center gap-2 bg-white/5 px-3 py-2 rounded-xl flex-1"
      >
        <TrendingUp class="w-4 h-4 text-white/40" />

        <input
          v-model="search"
          placeholder="Search coin..."
          class="bg-transparent outline-none text-[13px] w-full text-white/80"
        />
      </div>

      <!-- Hide Small -->
      <button
        @click="hideSmall = !hideSmall"
        class="text-[12px] px-3 py-2 rounded-xl font-semibold transition"
        :class="
          hideSmall
            ? 'bg-green-500/20 text-green-400'
            : 'bg-white/5 text-white/60'
        "
      >
        Hide Small
      </button>
    </div>

    <!-- TABLE HEADER -->
    <div class="grid grid-cols-4 text-[11px] text-white/40 mb-2 px-2">
      <span>Pair</span>
      <span>Price</span>
      <span>24h</span>
      <span class="text-right">Volume</span>
    </div>

    <!-- MARKET LIST -->
    <div class="space-y-2">

      <router-link
        v-for="market in filteredMarkets"
        :key="market.symbol"
        :to="`/markets/${market.symbol}`"
        class="grid grid-cols-4 items-center py-3 px-3 rounded-xl bg-white/5 hover:bg-white/10 transition cursor-pointer"
      >
        <!-- Pair + Logo -->
        <div class="flex items-center gap-2">
          <img
            :src="market.logo"
            class="w-7 h-7 rounded-full"
          />

          <p class="font-semibold text-[13px]">
            {{ market.pair }}
          </p>
        </div>

        <!-- Price -->
        <div class="text-[13px] font-medium">
          {{ (market?.price ?? 0).toLocaleString() }}
        </div>

        <!-- Change -->
        <div
          class="text-[13px] font-semibold"
          :class="(market?.change24h ?? 0) >= 0 ? 'text-green-400' : 'text-red-400'"
        >
          {{ market?.change24h ?? 0 }}%
        </div>

        <!-- Volume -->
        <div class="text-right text-[12px] text-white/60">
          {{ (market?.volume ?? 0).toLocaleString() }}
        </div>
      </router-link>

      <!-- EMPTY -->
      <div
        v-if="filteredMarkets.length === 0"
        class="text-white/40 text-center py-6 text-sm"
      >
        No matching spot markets found.
      </div>
    </div>
  </div>
</template>

<style scoped>
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-thumb {
  background-color: rgba(0, 255, 119, 0.3);
  border-radius: 3px;
}
</style>