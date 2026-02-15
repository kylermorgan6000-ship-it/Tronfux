<script setup lang="ts">
const props = defineProps({
  marketType: String,
  category: String
})

const emit = defineEmits(['changeMarket', 'changeCategory', 'changeLimit'])

const markets = ['spot', 'futures', 'forex']
const categories = ['favorites', 'hot', 'gainers', 'losers', 'new']

function selectCategory(c) {
  emit('changeCategory', c)

  // 🔒 limit logic (10 max for tabs)
  emit('changeLimit', 10)
}

function selectMarket(m) {
  emit('changeMarket', m)
}
</script>

<template>
  <!-- MARKET TYPE -->
  <div class="flex gap-4 mb-2 text-[14px]">
    <button
      v-for="m in markets"
      :key="m"
      @click="selectMarket(m)"
      class="font-medium transition"
      :class="
        marketType === m
          ? 'text-green-400'
          : 'text-white/70 hover:text-white'
      "
    >
      {{ m.charAt(0).toUpperCase() + m.slice(1) }}
    </button>
  </div>

  <!-- CATEGORY -->
  <div
    class="flex flex-wrap gap-x-4 gap-y-1 text-[14px] leading-tight max-h-[3.2em] overflow-hidden mb-2"
  >
    <button
      v-for="c in categories"
      :key="c"
      @click="selectCategory(c)"
      class="font-normal transition whitespace-nowrap"
      :class="
        category === c
          ? 'text-green-400'
          : 'text-white/60 hover:text-white'
      "
    >
      {{ c.charAt(0).toUpperCase() + c.slice(1) }}
    </button>
  </div>

  <!-- TABLE HEADER -->
  <div
    class="grid grid-cols-[1.6fr_1fr_1fr]
           text-[14px] text-white/50 mt-2"
  >
    <span>Pair</span>
    <span class="text-right">Price</span>
    <span class="text-right">24h %</span>
  </div>
</template>