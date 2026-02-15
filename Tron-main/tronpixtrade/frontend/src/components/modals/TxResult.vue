<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'

const props = defineProps()

const emits = defineEmits()
</script>

<template>
  <div v-if="props.status !== 'idle'" class="fixed inset-0 z-[999] bg-black flex flex-col">
    <div class="flex justify-end p-4">
      <button class="text-[13px] text-white/60" @click="emits('close')">Close</button>
    </div>

    <div class="flex-1 flex flex-col items-center justify-center px-6 text-center">
      <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4"
           :class="props.status==='success'? 'bg-green-500/20': props.status==='error'? 'bg-red-500/20':'bg-white/10'">
        <Check v-if="props.status==='success'" class="w-10 h-10 text-green-400"/>
        <X v-else-if="props.status==='error'" class="w-10 h-10 text-red-400"/>
        <div v-else class="loader"></div>
      </div>

      <h2 class="text-[18px] font-semibold mb-2">{{ props.title }}</h2>
      <p class="text-[13px] text-white/60 mb-8">{{ props.message }}</p>

      <div v-if="props.status==='success' && props.txHash" class="bg-white/5 rounded-xl px-4 py-3 mb-6 w-full text-left">
        <p class="text-[12px] text-white/60 mb-1">Transaction Hash</p>
        <div class="flex justify-between items-center">
          <span class="text-[13px] font-mono text-green-400">{{ props.txHash }}</span>
          <a :href="`https://tronscan.org/#/transaction/${props.txHash}`" target="_blank" class="text-[12px] text-blue-400">View →</a>
        </div>
      </div>
    </div>
  </div>
</template>