<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
  trade: Object
})

const baseQuote = computed(() => {
  const symbol = props.trade.symbol.value
  return symbol.includes('USDT')
    ? [symbol.replace('USDT', ''), 'USDT']
    : symbol.split('/')
})

function submit() {
  if (!props.trade.amount.value) return

  const payload = {
    symbol: props.trade.symbol.value,
    side: props.trade.side.value,
    type: props.trade.type.value,
    price: props.trade.price.value,
    amount: props.trade.amount.value,
  }

  console.log('Submitting order:', payload)
  // axios.post('/api/order', payload)
}
</script>

<template>
  <button
    class="w-full py-2 rounded-lg text-sm font-semibold
           bg-green-600 hover:bg-green-700
           disabled:opacity-50"
    :disabled="!trade.amount.value"
    @click="submit"
  >
    Place Order
  </button>
</template>