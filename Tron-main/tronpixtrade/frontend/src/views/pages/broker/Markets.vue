<script setup lang="ts">
import { ref, computed, onMounted } from "vue"
import { TrendingUp } from "lucide-vue-next"

/* ------------------ STATE ------------------ */
const search = ref("")
const hideSmall = ref(false)

import api from '@/lib/api'

/* markets from backend */
const markets = ref([])

const loadMarkets = async () => {
  try {
    const res = await api.get('/markets')
    markets.value = res.data.data ?? res.data
  } catch (e) {
    // fallback to static list if endpoint missing
    markets.value = []
    console.warn('Markets API not available, showing empty list', e)
  }
}

onMounted(() => { void loadMarkets() })

/* ------------------ FILTER ------------------ */
const filteredMarkets = computed(() => {
  return markets.value.filter((m) => {
    const matches =
      m.symbol.toLowerCase().includes(search.value.toLowerCase()) ||
      m.name.toLowerCase().includes(search.value.toLowerCase())

    const passesSmall = !hideSmall.value || m.balance > 0

    return matches && passesSmall
  })
})
</script>

<template>
  <div class="p-4 max-w-md mx-auto text-white">

    <!-- SEARCH -->
    <div class="flex items-center gap-2 mb-4">
      <div
        class="flex items-center gap-2 bg-white/5 px-3 py-2 rounded-xl flex-1"
      >
        <TrendingUp class="w-4 h-4 text-white/40" />
        <input
          v-model="search"
          placeholder="Search coin..."
          class="bg-transparent outline-none text-[13px] w-full"
        />
      </div>

      <!-- Hide Small -->
      <button
        @click="hideSmall = !hideSmall"
        class="text-[12px] px-3 py-2 rounded-xl"
        :class="
          hideSmall
            ? 'bg-green-500/20 text-green-400'
            : 'bg-white/5 text-white/50'
        "
      >
        Hide
      </button>
    </div>

    <!-- MARKET LIST -->
    <div class="space-y-2">

      <router-link
        v-for="coin in filteredMarkets"
        :key="coin.symbol"
        :to="`/markets/${coin.symbol}`"
        class="flex items-center justify-between px-3 py-3 rounded-xl bg-white/5 hover:bg-white/10 transition"
      >
        <!-- LEFT -->
        <div class="flex items-center gap-3">
          <img
            :src="coin.logo"
            class="w-9 h-9 rounded-full"
          />

          <div>
            <p class="font-semibold text-sm">
              {{ coin.symbol }}
            </p>
            <p class="text-[12px] text-white/40">
              {{ coin.name }}
            </p>
          </div>
        </div>

        <!-- MIDDLE -->
        <div class="text-right">
          <p class="font-semibold text-sm">
            ${{ (coin?.price ?? 0).toLocaleString() }}
          </p>

          <p
            class="text-[12px]"
            :class="
              coin.change24h >= 0
                ? 'text-green-400'
                : 'text-red-400'
            "
          >
            {{ coin.change24h }}%
          </p>
        </div>

        <!-- RIGHT -->
        <div class="text-right text-[11px] text-white/50">
          <p>{{ coin.balance }} {{ coin.symbol }}</p>
          <p>
            ${{ (coin.balance * coin.price).toFixed(2) }}
          </p>
        </div>
      </router-link>

      <!-- EMPTY -->
      <div
        v-if="filteredMarkets.length === 0"
        class="text-center text-white/40 py-6"
      >
        No matching coins found.
      </div>
    </div>
  </div>
</template>