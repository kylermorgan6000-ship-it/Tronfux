<script setup lang="ts">
import PairHeader from '@/components/trades/PairHeader.vue'
import TradingChart from '@/components/chart/TradingChart.vue'
import OrderBook from '@/components/orderbook/OrderBook.vue'
import RecentTrades from '@/components/trades/RecentTrades.vue'
import CompactTradePanel from '@/components/trades/CompactTradePanel.vue'

import { useMarketStream } from '@/composables/useMarketStream'

// 🔴 SINGLE SOURCE OF TRUTH (CRITICAL)
const market = useMarketStream('bnbusdt')
</script>

<template>
  <div class="grid grid-cols-12 grid-rows-[auto_1fr_auto] h-screen bg-black text-white gap-2 p-2">

    <!-- HEADER -->
    <div class="col-span-12">
      <PairHeader />
    </div>

    <!-- CHART (LEFT) -->
    <div class="col-span-8 rounded-xl bg-[#0b0f14] overflow-hidden">
      <TradingChart :last-price="market.lastPrice.value" />
    </div>

    <!-- RECENT TRADES (RIGHT) -->
    <div class="col-span-4 rounded-xl bg-[#0b0f14] overflow-hidden">
      <RecentTrades :trades="market.trades.value" />
    </div>

    <!-- TRADE PANEL (BOTTOM FULL WIDTH) -->
    <div class="col-span-12 rounded-xl bg-[#0b0f14] px-3 py-2">
      <CompactTradePanel />
    </div>

  </div>
</template>