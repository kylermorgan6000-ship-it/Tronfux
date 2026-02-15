<script setup lang="ts">
import { market } from '@/stores/useMarketContext'
</script>

<template>
  <div class="flex items-center gap-3 px-3 py-2 bg-[#0b0f14] rounded-xl">
    <span class="font-semibold">{{ market.symbol }}</span>
    <span class="text-green-400 text-sm">
      {{ market.lastPrice.toFixed(2) }}
    </span>
  </div>
</template>