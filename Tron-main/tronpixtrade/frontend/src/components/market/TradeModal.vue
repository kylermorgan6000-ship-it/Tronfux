<script setup lang="ts">
import { computed } from 'vue'
import { useTradeModal } from '@/composables/useTradeModal'
import { useFuturesEngine } from '@/composables/useFuturesEngine'

const { isOpen, activeAsset, closeTrade } = useTradeModal()
const futures = useFuturesEngine()

const markPrice = computed(() => {
  const v = futures.markPrice?.value ?? futures.markPrice ?? 0
  return Number.isFinite(v) ? v : 0
})

const tvSymbol = computed(() =>
  activeAsset.value?.symbol
    ? `${activeAsset.value.symbol}USDT`
    : ''
)
</script>

<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black z-50 p-4 overflow-y-auto"
  >
    <button
      @click="closeTrade"
      class="text-white mb-4 text-sm"
    >
      ← Back
    </button>

    <h2 class="text-xl font-bold mb-1">
      {{ activeAsset?.symbol }}
    </h2>

    <!-- MARK PRICE (SAFE) -->
    <p class="text-sm text-white/60 mb-3">
      Mark Price:
      <span class="text-white font-semibold">
        ${{ markPrice.toFixed(2) }}
      </span>
    </p>

    <!-- TradingView -->
    <iframe
      v-if="tvSymbol"
      class="w-full h-[320px] rounded-xl mb-4"
      :src="`https://s.tradingview.com/widgetembed/?symbol=${tvSymbol}&interval=15&theme=dark`"
    ></iframe>

    <!-- BUY / SELL -->
    <div class="grid grid-cols-2 gap-4">
      <button class="bg-green-500 py-2 rounded-lg font-semibold">
        Buy
      </button>
      <button class="bg-red-500 py-2 rounded-lg font-semibold">
        Sell
      </button>
    </div>
  </div>
</template>