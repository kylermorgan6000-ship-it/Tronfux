<script setup lang="ts">
defineProps({
  price: String,
  amount: String,
  side: String
})
</script>

<template>
  <div
    class="flex justify-between px-2 py-0.5 text-xs"
    :class="side === 'BUY' ? 'text-green-400' : 'text-red-400'"
  >
    <span>{{ price }}</span>
    <span>{{ amount }}</span>
  </div>
</template>