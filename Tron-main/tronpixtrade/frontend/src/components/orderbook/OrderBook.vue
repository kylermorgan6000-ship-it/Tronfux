<script setup lang="ts">
import OrderRow from './OrderRow.vue'

defineProps({
  bids: Array,
  asks: Array,
  lastPrice: Number
})
</script>

<template>
  <div class="bg-[#0b0f14] rounded-xl p-2">
    <div class="text-gray-400 text-xs mb-1">Order Book</div>

    <!-- ASKS -->
    <OrderRow
      v-for="a in asks"
      :key="a[0]"
      :price="a[0]"
      :amount="a[1]"
      side="SELL"
    />

    <!-- LAST PRICE -->
    <div class="text-center text-yellow-400 py-1 font-medium">
      {{ lastPrice.toFixed(2) }}
    </div>

    <!-- BIDS -->
    <OrderRow
      v-for="b in bids"
      :key="b[0]"
      :price="b[0]"
      :amount="b[1]"
      side="BUY"
    />
  </div>
</template>