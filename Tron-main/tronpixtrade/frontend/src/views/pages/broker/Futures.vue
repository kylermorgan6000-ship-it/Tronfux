<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { TrendingUp } from 'lucide-vue-next';
import api from '@/lib/api'

// ------------------ STATE ------------------
const search = ref('');
const hideSmall = ref(false);

// futures markets (from API)
const futuresMarkets = ref([])

const loadFutures = async () => {
  try {
  const res = await api.get('/markets', { params: { category: 'futures' } })
    futuresMarkets.value = res.data.data ?? res.data
  } catch (e) {
    console.warn('Futures markets API not available', e)
    futuresMarkets.value = []
  }
}

onMounted(() => { void loadFutures() })

// Filtered markets based on search and hideSmall
const filteredMarkets = computed(() => {
  return futuresMarkets.value
    .filter(m => m.pair.toLowerCase().includes(search.value.toLowerCase()))
    .filter(m => !hideSmall.value || m.volume > 1000); // Hide low-volume markets
});

// Open trade modal / navigate
const openTradeModal = (market: any) => {
  alert(`Open futures trade for ${market.pair} with ${market.leverage} leverage`);
};
</script>

<template>
  <div class="p-4 max-w-3xl mx-auto">
    <!-- SEARCH & FILTER -->
    <div class="flex items-center gap-2 mb-4">
      <div class="flex items-center gap-2 bg-white/5 px-3 py-2 rounded-lg flex-1">
        <TrendingUp class="w-4 h-4 text-white/40" />
        <input
          v-model="search"
          placeholder="Search futures market"
          class="bg-transparent outline-none text-[13px] w-full text-white/80"
        />
      </div>
      <button
        @click="hideSmall = !hideSmall"
        class="text-[12px] px-3 py-2 rounded-lg"
        :class="hideSmall ? 'bg-green-500/20 text-green-400' : 'bg-white/5 text-white/60'"
      >
        Hide Small
      </button>
    </div>

    <!-- TABLE HEADER -->
    <div class="grid grid-cols-6 text-[12px] text-white/50 mb-2 px-2">
      <span>Pair</span>
      <span>Price</span>
      <span>24h %</span>
      <span>Volume</span>
      <span>Leverage</span>
      <span></span>
    </div>

    <!-- MARKET LIST -->
    <div class="space-y-2">
      <div
        v-for="market in filteredMarkets"
        :key="market.pair"
        class="grid grid-cols-6 items-center py-3 px-2 rounded-lg hover:bg-white/5 cursor-pointer"
        @click="openTradeModal(market)"
      >
        <!-- Pair & Logo -->
        <div class="flex items-center gap-2">
          <img :src="market.logo" class="w-6 h-6 rounded-full" />
          <span>{{ market.pair }}</span>
        </div>

        <!-- Price -->
        <div>{{ market.price.toLocaleString() }}</div>

        <!-- 24h % -->
        <div :class="market.change24h >= 0 ? 'text-green-400' : 'text-red-400'">
          {{ market.change24h }}%
        </div>

        <!-- Volume -->
        <div>{{ market.volume.toLocaleString() }}</div>

        <!-- Leverage -->
        <div>{{ market.leverage }}</div>

        <!-- Trade Icon -->
        <div class="flex justify-end">
          <TrendingUp class="w-4 h-4 text-white/60" />
        </div>
      </div>

      <!-- NO RESULTS -->
      <div v-if="filteredMarkets.length === 0" class="text-white/50 text-center py-4">
        No matching futures markets
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Scrollbar styling for mobile */
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-thumb {
  background-color: rgba(0, 255, 119, 0.3);
  border-radius: 3px;
}
</style>