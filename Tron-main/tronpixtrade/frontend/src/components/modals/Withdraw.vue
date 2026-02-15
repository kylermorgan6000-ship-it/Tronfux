<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps({ show: Boolean, withdrawFee: Number, withdrawMin: Number })
const emits = defineEmits(['close', 'continue'])

const amount = ref('')

const netReceive = computed(() => {
  const amt = Number(amount.value || 0)
  return amt > props.withdrawFee ? amt - props.withdrawFee : 0
})

const continueWithdraw = () => emits('continue', Number(amount.value))
</script>

<template>
  <div v-if="props.show" class="fixed inset-0 z-50 bg-black/60 flex items-end" @click.self="emits('close')">
    <div class="bg-[#0b0b0b] w-full rounded-t-2xl p-4 max-h-[70vh]">
      <div class="flex justify-between items-center mb-4">
        <p class="font-semibold text-[15px]">Withdraw USDT</p>
        <button @click="emits('close')" class="text-white/60">X</button>
      </div>

      <div class="mb-4">
        <p class="text-[12px] text-white/60 mb-1">Amount</p>
        <input v-model="amount" type="number" placeholder="0.00" class="w-full bg-white/5 rounded-xl px-3 py-2" />
      </div>

      <div class="text-[12px] text-white/60 space-y-1 mb-4">
        <p>Fee: <span class="text-white">{{ props.withdrawFee }} USDT</span></p>
        <p>Minimum: <span class="text-white">{{ props.withdrawMin }} USDT</span></p>
        <p>You will receive: <span class="text-green-400">{{ netReceive.toFixed(2) }} USDT</span></p>
      </div>

      <button class="wallet-action primary w-full" :disabled="Number(amount) < props.withdrawMin" @click="continueWithdraw">Continue</button>
    </div>
  </div>
</template>