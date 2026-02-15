<script setup>
import MarketRow from './MarketRow.vue'
import TradeModal from './TradeModal.vue'
import { useMarketData } from '@/composables/useMarketData'
import { useTradeModal } from '@/composables/useTradeModal'

const props = defineProps()

const { assets, isLoading } = useMarketData({
  marketType: props.marketType,
  category: props.category,
})

const { openTrade } = useTradeModal()
</script>

<template>
  <!-- TABLE HEADER -->
  <div class="grid grid-cols-[1.6fr_1fr_1fr] text-xs text-white/40 mb-2">
    <span>Pair</span>
    <span class="text-right">Price</span>
    <span class="text-right">24h %</span>
  </div>

  <div v-if="isLoading" class="text-xs text-white/40">
    Loading market…
  </div>

  <div v-else class="space-y-1">
    <MarketRow
      v-for="asset in assets"
      :key="asset.symbol"
      :asset="asset"
      @select="openTrade(asset)"
    />
  </div>

  <TradeModal />
</template>