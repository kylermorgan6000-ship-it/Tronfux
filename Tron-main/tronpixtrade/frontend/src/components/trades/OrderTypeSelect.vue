<script setup lang="ts">
const model = defineModel()

const orderTypes: { label: string; value: OrderType }[] = [
  { label: 'Market', value: 'MARKET' },
  { label: 'Limit', value: 'LIMIT' },
  { label: 'Stop', value: 'STOP' },
]
</script>

<template>
  <div class="space-y-1">
    <label class="text-xs text-gray-400">
      Order Type
    </label>

    <div class="flex rounded-lg overflow-hidden bg-black">
      <button
        v-for="t in orderTypes"
        :key="t.value"
        @click="model = t.value"
        class="flex-1 py-2 text-xs font-medium transition"
        :class="
          model === t.value
            ? 'bg-[#1e2329] text-yellow-400'
            : 'text-gray-400 hover:bg-white/5'
        "
      >
        {{ t.label }}
      </button>
    </div>
  </div>
</template>