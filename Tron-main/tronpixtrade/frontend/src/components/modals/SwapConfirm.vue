<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'
import { X } from 'lucide-vue-next'

const props = defineProps()

const emit = defineEmits()

// Example fee & estimated arrival
const swapFee = 0.2
const estimatedTime = 'Instant'
</script>

<template>
  <div
    class="fixed inset-0 z-50 bg-black/70 flex items-end"
    @click.self="emit('close')"
  >
    <div class="bg-[#0b0b0b] w-full rounded-t-3xl p-5">

      <!-- Header -->
      <div class="flex justify-between items-center mb-4">
        <p class="font-semibold text-[15px]">Confirm Swap</p>
        <X class="w-5 h-5 text-white/60" @click="emit('close')" />
      </div>

      <!-- Summary -->
      <div class="bg-white/5 rounded-2xl p-4 space-y-3 text-[13px]">
        <div class="flex justify-between">
          <span class="text-white/60">From</span>
          <span class="font-semibold">{{ swap.amount }} {{ swap.from }}</span>
        </div>

        <div class="flex justify-between">
          <span class="text-white/60">To</span>
          <span class="font-semibold">{{ swap.to }}</span>
        </div>

        <div class="flex justify-between">
          <span class="text-white/60">Fee</span>
          <span>{{ swapFee }} {{ swap.from }}</span>
        </div>

        <div class="flex justify-between">
          <span class="text-white/60">Net Receive</span>
          <span class="text-green-400 font-semibold">
            {{ (swap.amount - swapFee).toFixed(4) }} {{ swap.to }}
          </span>
        </div>

        <div class="flex justify-between">
          <span class="text-white/60">Estimated Arrival</span>
          <span>{{ estimatedTime }}</span>
        </div>
      </div>

      <p class="text-[12px] text-yellow-400/80 mt-4">
        ⚠️ Swaps cannot be reversed once submitted.
      </p>

      <!-- Continue -->
      <button
        class="wallet-action primary w-full mt-5"
        @click="$emit('confirm')"
      >
        Confirm & Continue
      </button>
    </div>
  </div>
</template>

<style scoped>
.wallet-action {
  flex: 1;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  border-radius: 10px;
  background: rgba(255,255,255,0.06);
  font-size: 13px;
  font-weight: 500;
}

.wallet-action.primary {
  background: linear-gradient(135deg, #22c55e, #15803d);
  box-shadow: 0 0 12px rgba(34,197,94,.45);
} 
</style>