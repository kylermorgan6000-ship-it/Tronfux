<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue'

const props = defineProps()
const emits = defineEmits()

const transfer = ref({ from:'spot', to:'futures', asset:'USDT', amount:'' })
const selectedAsset = ref(props.assets[0] || null)

const switchDirection = () => {
  const temp = transfer.value.from
  transfer.value.from = transfer.value.to
  transfer.value.to = temp
}

const submitTransfer = () => emits('submit', { ...transfer.value })
</script>

<template>
  <div v-if="props.show" class="fixed inset-0 z-50 bg-black/60 flex items-end" @click.self="emits('close')">
    <div class="bg-[#0b0b0b] w-full rounded-t-2xl p-4 max-h-[70vh]">

      <div class="flex justify-between items-center mb-4">
        <p class="font-semibold text-[15px]">Internal Transfer</p>
        <button @click="emits('close')" class="text-white/60">X</button>
      </div>

      <!-- From / To -->
      <div class="bg-white/5 rounded-xl p-3 mb-4">
        <div class="flex justify-between items-center text-[13px] mb-2">
          <span>From</span>
          <span class="text-white/80 capitalize">{{ transfer.from }}</span>
        </div>

        <div class="flex justify-center my-2">
          <button class="p-2 rounded-full bg-white/10" @click="switchDirection">⇄</button>
        </div>

        <div class="flex justify-between items-center text-[13px] mb-2">
          <span>To</span>
          <span class="text-white/80 capitalize">{{ transfer.to }}</span>
        </div>

        <div class="mb-2">
          <p class="text-[12px] text-white/60 mb-1">Asset</p>
          <select v-model="transfer.asset" class="w-full bg-white/5 rounded-xl px-3 py-2 text-[13px]">
            <option v-for="a in props.assets" :key="a.symbol">{{ a.symbol }}</option>
          </select>
        </div>

        <div class="mb-4">
          <p class="text-[12px] text-white/60 mb-1">Amount</p>
          <input type="number" v-model="transfer.amount" placeholder="0.00" class="w-full bg-white/5 rounded-xl px-3 py-2"/>
        </div>
      </div>

      <button class="wallet-action primary w-full" @click="submitTransfer">Confirm Transfer</button>
    </div>
  </div>
</template>