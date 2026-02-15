<script setup lang="ts">
const modelValue = defineModel()
</script>

<template>
  <div class="flex rounded-lg overflow-hidden text-xs">
    <button
      class="flex-1 py-2"
      :class="modelValue === 'BUY'
        ? 'bg-green-500 text-black'
        : 'bg-[#0b0f14] text-gray-400'"
      @click="modelValue = 'BUY'"
    >
      Buy
    </button>

    <button
      class="flex-1 py-2"
      :class="modelValue === 'SELL'
        ? 'bg-red-500 text-black'
        : 'bg-[#0b0f14] text-gray-400'"
      @click="modelValue = 'SELL'"
    >
      Sell
    </button>
  </div>
</template>