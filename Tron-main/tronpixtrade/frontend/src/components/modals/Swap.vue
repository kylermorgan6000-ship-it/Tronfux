<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue'

const props = defineProps()
const emits = defineEmits()

const swap = ref({ from:'USDT', to:'BTC', amount:'' })
const switchSwap = () => {
  const temp = swap.value.from
  swap.value.from = swap.value.to
  swap.value.to = temp
}

const confirmSwap = () => emits('confirm', { ...swap.value })
</script>

<template>
  <div v-if="props.show" class="fixed inset-0 z-50 bg-black/60 flex items-end" @click.self="emits('close')">
    <div class="bg-[#0b0b0b] w-full rounded-t-2xl p-4 max-h-[70vh]">

      <div class="flex justify-between items-center mb-4">
        <p class="font-semibold text-[15px]">Swap Assets</p>
        <button @click="emits('close')" class="text-white/60">X</button>
      </div>

      <div class="bg-white/5 rounded-xl p-3 mb-4">
        <div class="flex justify-between items-center mb-2">
          <span>From</span>
          <select v-model="swap.from" class="bg-white/5 px-2 py-1 rounded text-[13px]">
            <option v-for="a in props.assets" :key="a.symbol">{{ a.symbol }}</option>
          </select>
        </div>

        <div class="flex justify-center my-2">
          <button class="p-2 rounded-full bg-white/10" @click="switchSwap">⇄</button>
        </div>

        <div class="flex justify-between items-center mb-2">
          <span>To</span>
          <select v-model="swap.to" class="bg-white/5 px-2 py-1 rounded text-[13px]">
            <option v-for="a in props.assets" :key="a.symbol">{{ a.symbol }}</option>
          </select>
        </div>

        <div class="mb-4">
          <p class="text-[12px] text-white/60 mb-1">Amount</p>
          <input type="number" v-model="swap.amount" placeholder="0.00" class="w-full bg-white/5 rounded-xl px-3 py-2"/>
        </div>
      </div>

      <button class="wallet-action primary w-full" @click="confirmSwap">Confirm Swap</button>
    </div>
  </div>
</template>