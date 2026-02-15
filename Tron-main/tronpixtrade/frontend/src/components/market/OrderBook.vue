<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { getOrderBook } from '@/services/binance'

const props = defineProps()
const bids = ref([])
const asks = ref([])

onMounted(async () => {
  const data = await getOrderBook(props.symbol)
  bids.value = data.bids
  asks.value = data.asks
})
</script>

<template>
  <div class="grid grid-cols-2 gap-2 text-xs">
    <div>
      <p class="text-green-400 mb-1">Bids</p>
      <div v-for="b in bids" :key="b[0]">
        {{ b[0] }} — {{ b[1] }}
      </div>
    </div>

    <div>
      <p class="text-red-400 mb-1">Asks</p>
      <div v-for="a in asks" :key="a[0]">
        {{ a[0] }} — {{ a[1] }}
      </div>
    </div>
  </div>
</template>