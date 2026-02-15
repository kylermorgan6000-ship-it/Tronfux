<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Tooltip,
  Filler,
} from 'chart.js'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Tooltip,
  Filler
)

/* UI STATE */
const timeframes = ['1D', '7D', '1M', '3M', '1Y', 'YTD']
const activeTimeframe = ref('1D')

/* MOCK DATA */
const prices = ref([])
const labels = ref([])

/* PRICE HEADER */
const symbol = 'BTCUSDT'
const currentPrice = computed(() => 37262.9)
const changePercent = computed(() => 2.16)

/* NAVIGATION */
function goToTrade(side) {
  router.push({
    name: 'broker-trade-view',
    query: {
      symbol,
      side,
    },
  })
}

/* CHART CONFIG */
const chartData = computed(() => ({
  labels: labels.value,
  datasets: [
    {
      data: prices.value,
      borderColor: '#facc15',
      borderWidth: 2,
      tension: 0.4,
      fill: true,
      backgroundColor: (ctx) => {
        const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300)
        gradient.addColorStop(0, 'rgba(250, 204, 21, 0.35)')
        gradient.addColorStop(1, 'rgba(250, 204, 21, 0)')
        return gradient
      },
      pointRadius: 0,
    },
  ],
}))

async function loadMarketData() {
  try {
    const interval = '1h'
    const limit = 50
    const res = await fetch(`https://api.binance.com/api/v3/klines?symbol=${symbol}&interval=${interval}&limit=${limit}`)
    const data = await res.json()
    // kline format: [ openTime, open, high, low, close, ... ]
    labels.value = data.map((d) => new Date(d[0]).toLocaleString())
    prices.value = data.map((d) => Number(d[4]))
  } catch (err) {
    console.error('Failed to load market data, falling back to mock', err)
    labels.value = Array.from({ length: 50 }, (_, i) => i.toString())
    prices.value = labels.value.map((_, i) => 36400 + i * 15 + Math.random() * 80)
  }
}

onMounted(loadMarketData)
</script>

<template>
  <section class="bg-[#0b0e11] min-h-screen text-white px-4 pt-6 pb-24">

    <!-- HEADER -->
    <div class="flex items-center gap-3 mb-4">
      <div class="w-8 h-8 rounded-full bg-yellow-500 flex items-center justify-center font-bold">
        ₿
      </div>

      <div>
        <p class="text-sm text-white/60">Bitcoin Price</p>
        <p class="text-xl font-bold">
          ${{ currentPrice.toLocaleString() }}
          <span class="text-green-400 text-sm ml-2">
            +{{ changePercent }}%
          </span>
        </p>
      </div>
    </div>

    <!-- TIMEFRAMES -->
    <div class="flex gap-4 text-xs text-white/50 mb-4">
      <button
        v-for="t in timeframes"
        :key="t"
        @click="activeTimeframe = t"
        :class="[
          'pb-1',
          activeTimeframe === t && 'text-yellow-400 border-b border-yellow-400'
        ]"
      >
        {{ t }}
      </button>
    </div>

    <!-- CHART -->
    <div class="h-[320px]">
      <Line :data="chartData" :options="chartOptions" />
    </div>

<!-- BUY / SELL BAR -->
<div
  class="fixed bottom-0 left-0 right-0 z-40
         bg-[#0b0e11]/95 backdrop-blur
         border-t border-white/10 px-4 py-3"
>
  <div class="flex gap-3 max-w-md mx-auto">

    <button
      @click="goToTrade('buy')"
      class="flex-1 py-3 rounded-xl
             bg-green-500 text-black font-semibold"
    >
      Buy
    </button>

    <button
      @click="goToTrade('sell')"
      class="flex-1 py-3 rounded-xl
             bg-red-500 text-white font-semibold"
    >
      Sell
    </button>

  </div>
</div>
  

  </section>
</template>
