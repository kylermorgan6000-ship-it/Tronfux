<script setup>
const model = defineModel()

const pairs = [
  'BTC/USDT',
  'ETH/USDT',
  'BNB/USDT',
  'SOL/USDT',
]
</script>

<template>
  <div class="space-y-1">
    <label class="text-xs text-gray-400">
      Trading Pair
    </label>

    <select
      v-model="model"
      class="w-full bg-black border border-white/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-yellow-400"
    >
      <option
        v-for="pair in pairs"
        :key="pair"
        :value="pair"
      >
        {{ pair }}
      </option>
    </select>
  </div>
</template>