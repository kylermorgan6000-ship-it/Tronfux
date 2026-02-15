<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
  amount: [Number, null],
  price: [Number, null],
  feeRate: Number
})

const orderValue = computed(() => {
  if (!props.amount || !props.price) return 0
  return props.amount * props.price
})

const estimatedFee = computed(() => {
  return orderValue.value * props.feeRate
})
</script>

<template>
  <div class="text-xs text-gray-400 space-y-1">
    <div class="flex justify-between">
      <span>Order Value</span>
      <span>{{ orderValue.toFixed(2) }} USDT</span>
    </div>

    <div class="flex justify-between">
      <span>Estimated Fee</span>
      <span>{{ estimatedFee.toFixed(4) }} USDT</span>
    </div>
  </div>
</template>