<script setup lang="ts">
import { ref } from 'vue'
import MarketTabs from './MarketTabs.vue'

const marketType = ref<'spot' | 'futures' | 'forex'>('spot')
const category = ref<'favorites' | 'hot' | 'gainers' | 'losers' | 'new'>('favorites')
</script>

<template>
  <MarketTabs
    :marketType="marketType"
    :category="category"
    @changeMarket="marketType = $event"
    @changeCategory="category = $event"
  />

  <slot :marketType="marketType" :category="category" />
</template>