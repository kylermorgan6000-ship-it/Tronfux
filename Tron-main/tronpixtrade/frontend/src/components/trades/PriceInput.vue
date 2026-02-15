<script setup lang="ts">
const model = defineModel()

function increase() {
  if (model.value === null) model.value = 0
  model.value = +(model.value + 0.01).toFixed(2)
}

function decrease() {
  if (!model.value) return
  model.value = Math.max(0, +(model.value - 0.01).toFixed(2))
}
</script>

<template>
  <div class="space-y-1">
    <label class="text-xs text-gray-400">
      Price
    </label>

    <div class="flex items-center bg-black border border-white/10 rounded-lg overflow-hidden">
      <input
        type="number"
        v-model="model"
        placeholder="0.00"
        class="flex-1 bg-transparent px-3 py-2 text-sm focus:outline-none"
      />

      <div class="flex flex-col border-l border-white/10">
        <button
          class="px-2 text-xs hover:bg-white/5"
          @click="increase"
        >
          +
        </button>
        <button
          class="px-2 text-xs hover:bg-white/5"
          @click="decrease"
        >
          −
        </button>
      </div>
    </div>
  </div>
</template>