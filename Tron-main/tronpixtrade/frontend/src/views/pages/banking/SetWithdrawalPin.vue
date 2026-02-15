<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { ArrowLeft, Lock, CheckCircle, AlertCircle } from 'lucide-vue-next'
import { createWithdrawalPin, hasWithdrawalPin } from '@/lib/api'

const router = useRouter()
const pin = ref('')
const confirmPin = ref('')
const loading = ref(false)
const error = ref('')
const success = ref(false)
const pinExists = ref(false)

const pinErrors = ref({
  weakPin: false,
  mismatch: false,
  length: false,
})

const validatePin = () => {
  pinErrors.value = {
    weakPin: false,
    mismatch: false,
    length: false,
  }

  if (pin.value.length < 4) {
    pinErrors.value.length = true
    return false
  }

  if (!/^\d{4,}$/.test(pin.value)) {
    pinErrors.value.weakPin = true
    return false
  }

  if (pin.value !== confirmPin.value) {
    pinErrors.value.mismatch = true
    return false
  }

  return true
}

const submitPin = async () => {
  if (!validatePin()) return

  loading.value = true
  error.value = ''

  try {
    await createWithdrawalPin(pin.value)
    success.value = true
    setTimeout(() => {
      router.push('/banking/dashboard')
    }, 2000)
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to create PIN'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-[#0f1215] text-white">
    <!-- HEADER -->
    <div class="sticky top-0 z-40 bg-black shadow-sm px-4 py-3 flex items-center gap-3">
      <button @click="router.back()" class="text-white/70 hover:text-white">
        <ArrowLeft class="w-5 h-5" />
      </button>
      <h1 class="font-semibold text-lg">Create Withdrawal PIN</h1>
    </div>

    <div class="max-w-md mx-auto px-4 py-6 space-y-6">
      <!-- SUCCESS STATE -->
      <div v-if="success" class="text-center py-12 space-y-4">
        <div class="w-16 h-16 rounded-full bg-green-500/20 mx-auto flex items-center justify-center">
          <CheckCircle class="w-8 h-8 text-green-400" />
        </div>
        <h2 class="text-xl font-semibold">PIN Created Successfully</h2>
        <p class="text-sm text-white/60">Your withdrawal PIN has been set. You'll be redirected shortly.</p>
      </div>

      <!-- FORM STATE -->
      <div v-else class="space-y-6 pt-4">
        <div class="bg-white/5 rounded-xl p-4 border border-white/10">
          <p class="text-sm text-white/70 mb-3 flex items-center gap-2">
            <Lock class="w-4 h-4 text-green-400" />
            PIN Requirements
          </p>
          <ul class="text-xs text-white/50 space-y-1">
            <li>• Minimum 4 characters</li>
            <li>• Only numbers allowed (0-9)</li>
            <li>• Keep it secure and memorable</li>
            <li>• You'll need it for withdrawals</li>
          </ul>
        </div>

        <!-- PIN INPUT -->
        <div class="space-y-2">
          <label class="block text-sm font-medium">Withdrawal PIN</label>
          <input
            v-model="pin"
            type="password"
            placeholder="Enter 4+ digit PIN"
            maxlength="6"
            class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 outline-none"
          />
          <p v-if="pinErrors.length" class="text-xs text-red-400 flex items-center gap-1">
            <AlertCircle class="w-3 h-3" />
            PIN must be at least 4 digits
          </p>
          <p v-if="pinErrors.weakPin" class="text-xs text-red-400 flex items-center gap-1">
            <AlertCircle class="w-3 h-3" />
            PIN must contain only numbers
          </p>
        </div>

        <!-- CONFIRM PIN INPUT -->
        <div class="space-y-2">
          <label class="block text-sm font-medium">Confirm PIN</label>
          <input
            v-model="confirmPin"
            type="password"
            placeholder="Confirm your PIN"
            maxlength="6"
            class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 outline-none"
          />
          <p v-if="pinErrors.mismatch" class="text-xs text-red-400 flex items-center gap-1">
            <AlertCircle class="w-3 h-3" />
            PINs do not match
          </p>
        </div>

        <!-- ERROR MESSAGE -->
        <div v-if="error" class="p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
          {{ error }}
        </div>

        <!-- SUBMIT BUTTON -->
        <button
          :disabled="loading || !pin || !confirmPin"
          @click="submitPin"
          class="w-full py-3 rounded-lg bg-green-500 text-black font-semibold disabled:opacity-40 hover:bg-green-400 transition flex items-center justify-center gap-2"
        >
          <span v-if="!loading">Create PIN</span>
          <svg v-else class="w-4 h-4 animate-spin" viewBox="0 0 50 50">
            <circle cx="25" cy="25" r="20" fill="none" stroke="currentColor" stroke-width="5" />
            <circle cx="25" cy="5" r="3" fill="currentColor" />
          </svg>
        </button>

        <p class="text-xs text-white/40 text-center">
          You can update your PIN anytime from Settings
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
  -webkit-box-shadow: 0 0 0 1000px rgba(255, 255, 255, 0.05) inset !important;
  -webkit-text-fill-color: white !important;
}
</style>
