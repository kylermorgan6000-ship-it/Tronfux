<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue'

const props = defineProps()
const emits = defineEmits()

const pin = ref('')

const submitPin = () => {
  emits('submit', pin.value)
  pin.value = ''
}
</script>

<template>
  <div v-if="props.show" class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center px-6" @click.self="emits('close')">
    <div class="bg-[#111] w-full max-w-sm rounded-2xl p-6">
      <h2 class="text-[16px] font-semibold mb-2">Enter Transaction PIN</h2>
      <p class="text-[13px] text-white/60 mb-5">For security, confirm this withdrawal using your 4-digit PIN.</p>

      <input v-model="pin" maxlength="4" type="password" placeholder="••••"
             class="w-full text-center text-[20px] tracking-[8px] bg-white/5 rounded-xl py-3 mb-4" />

      <button class="wallet-action primary w-full" :disabled="pin.length !== 4" @click="submitPin">Verify & Submit</button>
      <button class="w-full mt-3 text-[13px] text-white/50" @click="emits('close')">Cancel</button>
    </div>
  </div>
</template>