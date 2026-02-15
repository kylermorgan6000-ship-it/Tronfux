<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'

const props = defineProps()

const emits = defineEmits()
</script>

<template>
  <div v-if="props.show" class="fixed inset-0 z-50 bg-black/70 flex items-end" @click.self="emits('close')">
    <div class="bg-[#0b0b0b] w-full rounded-t-3xl p-5">
      <div class="flex justify-between items-center mb-4">
        <p class="font-semibold text-[15px]">Confirm Withdrawal</p>
        <button @click="emits('close')" class="text-white/60">X</button>
      </div>

      <div class="bg-white/5 rounded-2xl p-4 space-y-3 text-[13px]">
        <div class="flex justify-between">
          <span class="text-white/60">Amount</span>
          <span class="font-semibold">{{ props.withdrawAmount }} USDT</span>
        </div>
        <div class="flex justify-between">
          <span class="text-white/60">Fee</span>
          <span>{{ props.withdrawFee }} USDT</span>
        </div>
        <div class="flex justify-between">
          <span class="text-white/60">Net Receive</span>
          <span class="text-green-400 font-semibold">{{ props.netReceive.toFixed(2) }} USDT</span>
        </div>
        <div class="flex justify-between">
          <span class="text-white/60">Estimated Arrival</span>
          <span>5–15 minutes</span>
        </div>
      </div>

      <p class="text-[12px] text-yellow-400/80 mt-4">⚠️ Withdrawals cannot be reversed once submitted.</p>

      <button class="wallet-action primary w-full mt-5" @click="emits('confirm')">Confirm & Continue</button>
    </div>
  </div>
</template>