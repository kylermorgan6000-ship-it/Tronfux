<script setup lang="ts">
import { computed } from 'vue'
import { useTradeModal } from '@/composables/useTradeModal'

const props = defineProps()

const { openTrade } = useTradeModal()

const changeValue = computed(() => {
  const val =
    props.asset.change24h ??
    props.asset.change ??
    0

  return Number.isFinite(val) ? val : 0
})
</script>

<template>
  <div
    @click="openTrade(asset)"
    class="grid grid-cols-[1.6fr_1fr_1fr]
           items-center px-2 py-2
           cursor-pointer transition
           hover:bg-white/5"
  >
    <!-- PAIR -->
    <div class="flex items-center gap-2 min-w-0">
      <img
        v-if="asset.logo"
        :src="asset.logo"
        class="w-6 h-6 rounded-full"
      />
      <div class="min-w-0">
        <p class="text-[14px] font-medium truncate">
          {{ asset.symbol }}
        </p>
        <p class="text-[11px] text-white/40 truncate">
          {{ asset.name }}
        </p>
      </div>
    </div>

    <!-- PRICE -->
    <p class="text-right text-[14px]">
      ${{ asset.price.toLocaleString() }}
    </p>

    <!-- CHANGE -->
    <p
      class="text-right text-[14px]"
      :class="changeValue >= 0 ? 'text-green-400' : 'text-red-400'"
    >
      {{ changeValue > 0 ? '+' : '' }}{{ changeValue.toFixed(2) }}%
    </p>
  </div>
</template>