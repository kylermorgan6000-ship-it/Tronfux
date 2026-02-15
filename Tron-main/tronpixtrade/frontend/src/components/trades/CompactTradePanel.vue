<script setup lang="ts">
import { ref, computed } from 'vue'
import { trade } from '@/stores/useTradeContext'
import { market } from '@/stores/useMarketContext'
import { BookOpen, Wallet, BarChart3 } from 'lucide-vue-next'

const showOrderBook = ref(false)

const estimatedTotal = computed(() => {
  if (!trade.amount) return 0
  const price =
    trade.type === 'MARKET'
      ? market.lastPrice
      : trade.price || 0
  return trade.amount * price
})

const feeRate = computed(() =>
  trade.type === 'LIMIT' ? 0.001 : 0.0015
)

const estimatedFee = computed(() =>
  estimatedTotal.value * feeRate.value
)
</script>

<template>
  <div class="relative space-y-2 text-[12px]">

    <!-- BUY / SELL -->
    <div class="flex rounded-[7px] overflow-hidden bg-[#151a21] h-9">
      <button
        class="flex-1 font-semibold"
        :class="trade.side === 'BUY'
          ? 'bg-green-500 text-black'
          : 'text-gray-400'"
        @click="trade.side = 'BUY'"
      >Buy</button>

      <button
        class="flex-1 font-semibold"
        :class="trade.side === 'SELL'
          ? 'bg-red-500 text-black'
          : 'text-gray-400'"
        @click="trade.side = 'SELL'"
      >Sell</button>
    </div>

    <!-- ORDER TYPE -->
    <div class="flex gap-2">
      <button
        class="flex-1 h-8 rounded-md text-[11px]"
        :class="trade.type === 'LIMIT'
          ? 'bg-[#1f2630] text-white'
          : 'bg-[#151a21] text-gray-400'"
        @click="trade.type = 'LIMIT'"
      >Limit</button>

      <button
        class="flex-1 h-8 rounded-md text-[11px]"
        :class="trade.type === 'MARKET'
          ? 'bg-[#1f2630] text-white'
          : 'bg-[#151a21] text-gray-400'"
        @click="trade.type = 'MARKET'"
      >Market</button>
    </div>

    <!-- PRICE -->
    <div
      v-if="trade.type === 'LIMIT'"
      class="bg-[#151a21] rounded-md px-2 py-1"
    >
      <div class="flex justify-between text-[11px] text-gray-400 mb-0.5">
        <span>Price</span>
        <span>USDT</span>
      </div>
      <input
        v-model.number="trade.price"
        class="w-full bg-transparent outline-none h-6 text-sm"
      />
    </div>

    <!-- AMOUNT -->
    <div class="bg-[#151a21] rounded-md px-2 py-1">
      <div class="flex justify-between text-[11px] text-gray-400 mb-0.5">
        <span>Amount</span>
        <span>{{ market.baseAsset }}</span>
      </div>
      <input
        v-model.number="trade.amount"
        class="w-full bg-transparent outline-none h-6 text-sm"
      />
    </div>
    
        <!-- PERCENT -->
    <div class="grid grid-cols-4 gap-1">
      <button
        v-for="p in [25,50,75,100]"
        :key="p"
        class="h-7 rounded-md bg-[#151a21] text-[11px] text-gray-400 hover:text-white"
        @click="trade.setAmountByPercent(p)"
      >
        {{ p }}%
      </button>
    </div>
    
    <!-- ESTIMATED TOTAL -->
    <div class="flex justify-between text-[11px] text-gray-400 px-1">
      <span>Estimated</span>
      <span>{{ estimatedTotal.toFixed(2) }} USDT</span>
    </div>


    <!-- FEES -->
    <div class="flex justify-between text-[11px] text-gray-400 px-1">
      <span>{{ trade.type === 'LIMIT' ? 'Maker Fee' : 'Taker Fee' }}</span>
      <span>{{ estimatedFee.toFixed(4) }} USDT</span>
    </div>

    <!-- LIMIT OPTIONS -->
    <div
      v-if="trade.type === 'LIMIT'"
      class="flex gap-4 text-[11px] text-gray-400 px-1"
    >
      <label class="flex items-center gap-1">
        <input class="bg-black" type="checkbox" v-model="trade.postOnly" />
        Post-only
      </label>

      <label class="flex items-center gap-1">
        <input class="bg-black" type="checkbox" v-model="trade.reduceOnly" />
        Reduce-only
      </label>
    </div>

    <!-- SUBMIT -->
    <button
      class="w-full h-10 rounded-[7px] font-semibold text-sm"
      :class="trade.side === 'BUY'
        ? 'bg-green-500 text-black'
        : 'bg-red-500 text-black'"
    >
      {{ trade.side }} {{ market.symbol.toUpperCase() }}
    </button>

    <!-- FLOATING ICONS -->
    <div
      class="fixed right-3 top-1/2 -translate-y-1/2
             flex flex-col gap-3 z-40"
    >
      <button class="floating-btn" @click="showOrderBook = true">
        <BookOpen class="w-5 h-5" />
      </button>

      <button class="floating-btn">
        <Wallet class="w-5 h-5" />
      </button>

      <button class="floating-btn">
        <BarChart3 class="w-5 h-5" />
      </button>
    </div>

  </div>
</template>

<style scoped>
.floating-btn {
  width: 40px;
  height: 40px;
  border-radius: 9999px;
  background: #151a21;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>