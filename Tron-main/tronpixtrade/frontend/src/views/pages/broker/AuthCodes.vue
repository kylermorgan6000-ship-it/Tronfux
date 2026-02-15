<script setup lang="ts">
import { ref, computed, nextTick, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import {
  ArrowLeft,
  ShieldCheck,
  KeyRound,
  Wallet,
  CheckCircle,
  XCircle,
  Loader2,
} from 'lucide-vue-next'
import { fetchUserSecuritySteps, verifySecurityStepCode } from '@/lib/api'

const router = useRouter()

// --------------------------- STATE ---------------------------
const loading = ref(false)
const showSuccess = ref(false)
const showError = ref(false)
const errorMessage = ref('')
const step = ref(0)

const CODE_LENGTH = 6
const otp = ref(Array(CODE_LENGTH).fill(''))
const otpRefs = ref([])

const securitySteps = ref([])

const currentStepData = computed(() => securitySteps.value[step.value] || { name: '', explanation: '', code: '', account: '' })
const otpString = computed(() => otp.value.join(''))
const canContinue = computed(() => otpString.value.length === CODE_LENGTH && !otp.value.includes(''))

// --------------------------- HELPERS ---------------------------
const resetOtp = () => {
  otp.value = Array(CODE_LENGTH).fill('')
  nextTick(() => otpRefs.value?.[0]?.focus())
}

const setOtpFromString = (val: string) => {
  const clean = val.replace(/\D/g, '').slice(0, CODE_LENGTH)
  otp.value = clean.split('')
  while (otp.value.length < CODE_LENGTH) otp.value.push('')
  nextTick(() =>
    otpRefs.value?.[Math.min(clean.length, CODE_LENGTH - 1)]?.focus()
  )
}

// --------------------------- OTP HANDLERS ---------------------------
const onOtpInput = (index, e) => {
  const input = e.target
  const value = input.value.replace(/\D/g, '')

  if (value.length > 1) {
    setOtpFromString(value)
    return
  }

  otp.value[index] = value

  if (value && index < CODE_LENGTH - 1) {
    otpRefs.value[index + 1]?.focus()
  }
}

const onOtpKeyDown = (index, e) => {
  if (e.key === 'Backspace') {
    if (otp.value[index]) {
      otp.value[index] = ''
      return
    }
    if (index > 0) {
      otpRefs.value[index - 1]?.focus()
      otp.value[index - 1] = ''
    }
  }
}

// --------------------------- AUTO SUBMIT ---------------------------
watch(canContinue, (ready) => {
  if (ready && !loading.value) {
    verifyCode()
  }
})

// --------------------------- ACTION ---------------------------
const fetchSteps = async () => {
  loading.value = true
  try {
    securitySteps.value = await fetchUserSecuritySteps()
  } catch (err) {
    showError.value = true
    errorMessage.value = 'Failed to load security steps'
  } finally {
    loading.value = false
  }
}

const verifyCode = async () => {
  if (!canContinue.value || loading.value) return

  loading.value = true
  showError.value = false
  showSuccess.value = false

  try {
    const currentStep = securitySteps.value[step.value]
    await verifySecurityStepCode(currentStep.id, otpString.value)
    showSuccess.value = true
    setTimeout(() => (showSuccess.value = false), 1000)

    if (step.value < securitySteps.value.length - 1) {
      step.value++
      resetOtp()
    } else {
      alert('All security checks completed successfully!')
      step.value = 0
      resetOtp()
      router.push('/broker/dashboard')
    }
  } catch (err) {
    loading.value = false
    showError.value = true
    errorMessage.value = err.response?.data?.message || 'Security code incorrect'
    resetOtp()
  } finally {
    loading.value = false
  }
}

// --------------------------- INIT ---------------------------
const goBack = () => window.history.back()

onMounted(fetchSteps)
</script>

<template>
  <div class="min-h-screen bg-black text-white px-4 pb-24">

    <!-- LOADER -->
    <div
      v-if="loading"
      class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center"
    >
      <Loader2 class="w-14 h-14 animate-spin text-green-400" />
    </div>

    <!-- HEADER -->
    <div class="sticky top-0 z-40 bg-black flex items-center gap-3 p-3">
      <button @click="goBack()" class="p-2 rounded-xl hover:bg-white/5">
        <ArrowLeft class="w-5 h-5 text-white/70" />
      </button>
      <h1 class="font-semibold text-lg">Security Verification</h1>
    </div>

    <!-- STEP PROGRESS -->
    <div class="flex items-center justify-between mt-4 mb-6 px-2">
      <div
        v-for="(_, i) in securitySteps"
        :key="i"
        class="flex-1 mx-1 h-1.5 rounded-full"
        :class="i <= step ? 'bg-green-400' : 'bg-white/10'"
      />
    </div>

    <!-- SECURITY CARD -->
    <div class="relative bg-gray-900/40 backdrop-blur-xl rounded-2xl p-5 border border-white/10">
      <div class="flex justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <ShieldCheck class="w-5 h-5 text-green-400" />
            <p class="font-semibold text-lg">{{ currentStepData.name }}</p>
          </div>
          <p class="text-sm text-white/60">
            {{ currentStepData.explanation }}
          </p>
          <p class="text-[11px] text-white/40 mt-2">
            Step {{ step + 1 }} of {{ securitySteps.length }} · Secure Flow
          </p>
        </div>

        <div
          class="h-12 w-12 rounded-2xl bg-green-500/10 border border-green-500/30 flex items-center justify-center"
        >
          <KeyRound class="w-6 h-6 text-green-400" />
        </div>
      </div>
    </div>

    <!-- OTP FLOATING SECTION -->
    <div class="mt-10">
      <p class="text-xs text-white/60 mb-4 text-center">
        Enter 6-digit security code
      </p>

      <div class="flex justify-center gap-3 mb-6 px-4">
        <div
          v-for="(_, i) in otp"
          :key="i"
          class="otp-wrapper"
          :class="{ filled: otp[i] }"
        >
          <input
            ref="otpRefs"
            maxlength="1"
            inputmode="numeric"
            class="otp-box"
            :value="otp[i]"
            @input="onOtpInput(i, $event)"
            @keydown="onOtpKeyDown(i, $event)"
          />
        </div>
      </div>

      <button
        :disabled="!canContinue"
        class="w-full p-3 rounded-xl font-semibold bg-green-500 text-black disabled:opacity-40"
      >
        Secure & Continue →
      </button>
    </div>

    <!-- ACCOUNT CONTEXT -->
    <div class="mt-6 bg-white/5 rounded-xl p-3 flex items-center gap-3">
      <Wallet class="w-5 h-5 text-white/60" />
      <div class="text-[11px]">
        <p class="text-white/70">Affected Area</p>
        <p class="font-semibold">{{ currentStepData.account }}</p>
      </div>
    </div>

    <!-- TOASTS -->
    <div
      v-if="showSuccess"
      class="fixed bottom-24 left-1/2 -translate-x-1/2 bg-green-500 text-black px-4 py-3 rounded-2xl flex gap-2"
    >
      <CheckCircle class="w-5 h-5" />
      Verification successful
    </div>

    <div
      v-if="showError"
      class="fixed bottom-24 left-1/2 -translate-x-1/2 bg-red-500 text-white px-4 py-3 rounded-2xl flex gap-2"
    >
      <XCircle class="w-5 h-5" />
      {{ errorMessage }}
    </div>
  </div>
</template>

<style scoped>
  .otp-wrapper {
    position: relative;
  }

  .otp-wrapper.filled .otp-box {
    animation: pin-pop 0.25s ease-out;
    border-color: rgba(34, 197, 94, 0.7);
    box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.15);
  }

  .otp-box {
    width: 48px;
    height: 56px;
    text-align: center;
    font-size: 20px;
    font-weight: 700;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.08);
    outline: none;
    transition: all 0.18s ease;
  }

  @media (max-width: 380px) {
    .otp-box {
      width: 44px;
      height: 54px;
    }
  }

  @keyframes pin-pop {
    0% {
      transform: scale(0.85);
      opacity: 0.5;
    }
    100% {
      transform: scale(1);
      opacity: 1;
    }
  }
</style>