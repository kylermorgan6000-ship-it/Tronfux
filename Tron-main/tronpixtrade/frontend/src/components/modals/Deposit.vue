<script setup lang="ts">
import { ref, defineProps, defineEmits, computed, watch } from 'vue'

const props = defineProps()

const emits = defineEmits()

const network = ref((props.networks && props.networks[0]) || 'ERC20')
const timer = ref(600) // 10 minutes in seconds
const intervalId = ref(null)
const proofFile = ref(null)
const copied = ref(false)

const formattedTime = computed(() => {
  const m = Math.floor(timer.value / 60)
  const s = timer.value % 60
  return `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`
})

// Watch show prop to start/stop timer
watch(() => props.show, (val) => {
  if (val) startTimer()
  else stopTimer()
})

const startTimer = () => {
  timer.value = 600
  intervalId.value = setInterval(() => {
    if (timer.value > 0) timer.value--
    else stopTimer()
  }, 1000)
}

const stopTimer = () => {
  if (intervalId.value) clearInterval(intervalId.value)
}

const copyAddress = async () => {
  try {
    await navigator.clipboard.writeText(props.depositAddress)
    copied.value = true
    setTimeout(() => copied.value = false, 2000)
  } catch (e) {
    console.error('Copy failed', e)
  }
}

const handleFileUpload = (e) => {
  const target = e.target
  if (!target.files) return
  const file = target.files[0]
  if (!file) return
  const allowed = ['image/png', 'image/jpeg', 'application/pdf']
  if (!allowed.includes(file.type)) {
    alert('Only PNG, JPEG, or PDF files are allowed')
    return
  }
  proofFile.value = file
}

const submitVerify = () => {
  if (!proofFile.value) return
  emits('verify', proofFile.value)
}
</script>

<template>
  <div v-if="props.show" class="fixed inset-0 z-50 bg-black/70 flex items-center justify-center px-4" @click.self="emits('close')">
    <div class="bg-[#0b0b0b] w-full max-w-sm rounded-2xl p-5">

      <!-- Header -->
      <div class="flex justify-between items-center mb-4">
        <p class="font-semibold text-[16px]">Deposit USDT</p>
        <button class="text-white/60" @click="emits('close')">X</button>
      </div>

      <!-- Network -->
      <div class="mb-4">
        <p class="text-[12px] text-white/60 mb-1">Network</p>
        <select v-model="network" class="w-full bg-white/5 rounded-xl px-3 py-2 text-[13px]">
          <option v-for="n in props.networks" :key="n">{{ n }}</option>
        </select>
      </div>

      <!-- Deposit Address -->
      <div class="bg-white/5 rounded-xl p-3 text-[12px] mb-3 break-words flex justify-between items-center">
        <span>{{ props.depositAddress }}</span>
        <button @click="copyAddress" class="text-green-400 text-[12px]">
          {{ copied ? 'Copied!' : 'Copy' }}
        </button>
      </div>

      <p class="text-[11px] text-yellow-400 mb-3">
        Only send USDT via {{ network }} network. Sending other assets may result in permanent loss.
      </p>

      <!-- Timer -->
      <div v-if="timer > 0" class="mb-3 text-[12px] text-white/60">
        ⏳ Please complete your deposit within <span class="text-green-400 font-semibold">{{ formattedTime }}</span>
      </div>
      <div v-else class="mb-3 text-[12px] text-red-400 font-semibold">
        Time expired. Please start a new deposit.
      </div>

      <!-- Upload Proof -->
      <div class="mb-3">
        <label class="block text-[12px] text-white/60 mb-1">Upload Payment Proof</label>
        <input type="file" accept="image/png, image/jpeg, application/pdf" @change="handleFileUpload" class="w-full text-[12px] text-white/60 bg-white/5 rounded-xl px-3 py-2" />
        <p v-if="proofFile" class="text-[12px] text-green-400 mt-1">{{ proofFile.name }}</p>
      </div>

      <!-- Verify Button -->
      <button
        class="wallet-action primary w-full mt-4"
        :disabled="!proofFile || timer === 0"
        @click="submitVerify"
      >
        Verify Deposit
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