<script setup>
import axios from '@/lib/api'

const props = defineProps({ trade: {} })

async function submit() {
  await axios.post('/trade/execute', {
    symbol: props.trade.symbol,
    side: props.trade.side,
    type: props.trade.type,
    price: props.trade.price,
    quantity: props.trade.amount,
  })
}
</script>

<template>
  <button
    class="w-full py-3 rounded-lg font-bold"
    :class="trade.side === 'BUY' ? 'bg-green-500' : 'bg-red-500'"
    @click="submit"
  >
    {{ trade.side }} {{ trade.symbol.split('/')[0] }}
  </button>
</template>