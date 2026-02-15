<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import {
  ArrowLeft,
  Upload,
  Camera,
  CheckCircle2,
  XCircle,
  CreditCard,
  User,
  FileText,
  Loader2
} from 'lucide-vue-next'

import { Button } from '@/components/ui/button'
import { submitBrokerKyc } from '@/lib/api'

const router = useRouter()

/**
 * STATE
 */
const step = ref(1)
const showModal = ref(false)
const kycStatus = ref('success')
const isSubmitting = ref(false)

/**
 * This should come from backend / user profile
 */
const userCountry = ref('United States 🇺🇸')

const form = ref({
  documentType: 'national_id',
})

const uploads = ref({
  front: false,
  back: false,
  selfie: false,
})

/**
 * COMPUTED
 */
const progress = computed(() => `${(step.value / 3) * 100}%`)

const headerContent = computed(() => {
  if (step.value === 1) {
    return {
      title: 'Identity verification',
      subtitle: 'Choose a valid government-issued document',
    }
  }
  if (step.value === 2) {
    return {
      title: 'Document upload',
      subtitle: 'Upload clear photos of your document',
    }
  }
  return {
    title: 'Face verification',
    subtitle: 'Confirm your identity with a selfie',
  }
})

/**
 * ACTIONS
 */
const next = () => step.value < 3 && step.value++
const back = () => step.value > 1 && step.value--

const upload = (key: keyof typeof uploads.value) => {
  uploads.value[key] = true
}

const submitKycHandler = async () => {
  isSubmitting.value = true
  try {
    const payload = {
      document_type: form.value.documentType,
      document_front: uploads.value.front ? 'document_front_base64' : null,
      document_back: uploads.value.back ? 'document_back_base64' : null,
      selfie: uploads.value.selfie ? 'selfie_base64' : null,
      country: (userCountry.value || '').split(' ')[0],
      date_of_birth: '1990-01-01', // TODO: Get from user input
    }

    await submitBrokerKyc(payload)

    kycStatus.value = 'success'
    showModal.value = true
    setTimeout(() => router.push('/broker/dashboard'), 3000)
  } catch (err) {
    console.error('Broker KYC submission failed:', err)
    kycStatus.value = 'failed'
    showModal.value = true
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <!-- HEADER -->
  <div class="fixed top-0 left-0 w-full z-50 bg-[#0f1215]/95 backdrop-blur border-b border-white/5">
    <div class="px-4 py-3 space-y-2 max-w-md mx-auto">
      <div class="flex items-center gap-3">
        <button @click="router.push('/broker/dashboard')" class="text-white/60 hover:text-white">
          <ArrowLeft class="w-5 h-5" />
        </button>

        <div>
          <p class="text-sm font-medium text-white">
            {{ headerContent.title }}
          </p>
          <p class="text-[11px] text-white/50">
            {{ headerContent.subtitle }}
          </p>
        </div>
      </div>

      <div class="h-1 w-full bg-white/10 rounded-full">
        <div
          class="h-full bg-green-500 transition-all duration-300 rounded-full"
          :style="{ width: progress }"
        />
      </div>
    </div>
  </div>

  <!-- CONTENT -->
  <section class="min-h-screen bg-[#0f1215] pt-[104px] px-4 pb-24 max-w-md mx-auto space-y-8">

    <!-- COUNTRY (READ-ONLY) -->
    <div class="flex items-center justify-between bg-white/5 rounded-xl p-4">
      <div>
        <p class="text-xs text-white/50">Country</p>
        <p class="text-sm font-medium">{{ userCountry }}</p>
      </div>
      <span class="text-xs text-green-400">Detected</span>
    </div>

    <!-- STEP 1 -->
    <div v-if="step === 1" class="space-y-5">

      <!-- Passport -->
      <div
        class="rounded-2xl p-5 bg-white/5 hover:bg-white/10 transition cursor-pointer space-y-2"
        @click="form.documentType = 'passport'"
        :class="form.documentType === 'passport' && 'ring-2 ring-green-500/50'"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <FileText class="w-6 h-6 text-green-400" />
            <p class="text-sm font-semibold">Passport</p>
          </div>
          <span class="text-[10px] bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full">
            Recommended
          </span>
        </div>

        <p class="text-xs text-white/60">
          Accepted worldwide, highest verification success
        </p>

        <div class="flex gap-4 text-[11px] text-white/40">
          <span>⏱ ~1–3 mins</span>
          <span>✅ Very high success rate</span>
        </div>
      </div>

      <!-- National ID -->
      <div
        class="rounded-2xl p-5 bg-white/5 hover:bg-white/10 transition cursor-pointer space-y-2"
        @click="form.documentType = 'national_id'"
        :class="form.documentType === 'national_id' && 'ring-2 ring-green-500/50'"
      >
        <div class="flex items-center gap-3">
          <CreditCard class="w-6 h-6 text-white/60" />
          <p class="text-sm font-semibold">National ID Card</p>
        </div>

        <p class="text-xs text-white/60">
          Government-issued identity card
        </p>

        <div class="flex gap-4 text-[11px] text-white/40">
          <span>⏱ ~2–5 mins</span>
          <span>✅ High success rate</span>
        </div>
      </div>

      <!-- Driver License -->
      <div
        class="rounded-2xl p-5 bg-white/5 hover:bg-white/10 transition cursor-pointer space-y-2"
        @click="form.documentType = 'driver_license'"
        :class="form.documentType === 'driver_license' && 'ring-2 ring-green-500/50'"
      >
        <div class="flex items-center gap-3">
          <CreditCard class="w-6 h-6 text-white/60" />
          <p class="text-sm font-semibold">Driver’s License</p>
        </div>

        <p class="text-xs text-white/60">
          Valid driver’s license with photo
        </p>
      </div>

      <Button class="w-full" :disabled="!form.documentType" @click="next">
        Continue
      </Button>
    </div>

    <!-- STEP 2 -->
    <div v-if="step === 2" class="space-y-6">

      <!-- RULES -->
      <div class="rounded-xl bg-yellow-500/10 border border-yellow-500/20 p-4 text-xs text-yellow-300">
        Make sure:
        <ul class="mt-2 space-y-1">
          <li>• All corners are visible</li>
          <li>• No screenshots or photocopies</li>
          <li>• Document is not expired</li>
        </ul>
      </div>

      <!-- FRONT -->
      <div
        class="rounded-2xl border border-dashed border-white/20 p-6 flex flex-col items-center gap-3 hover:border-green-500/40 transition"
        @click="upload('front')"
      >
        <CheckCircle2
          v-if="uploads.front"
          class="w-8 h-8 text-green-400 animate-bounce"
        />
        <Upload v-else class="w-8 h-8 text-white/40" />
        <p class="text-sm">Front of document</p>
      </div>

      <!-- BACK -->
      <div
        class="rounded-2xl border border-dashed border-white/20 p-6 flex flex-col items-center gap-3 hover:border-green-500/40 transition"
        @click="upload('back')"
      >
        <CheckCircle2
          v-if="uploads.back"
          class="w-8 h-8 text-green-400 animate-bounce"
        />
        <Upload v-else class="w-8 h-8 text-white/40" />
        <p class="text-sm">Back of document</p>
      </div>

      <div class="flex gap-3">
        <Button variant="outline" class="flex-1" @click="back">Back</Button>
        <Button class="flex-1" :disabled="!uploads.front || !uploads.back" @click="next">
          Continue
        </Button>
      </div>
    </div>

    <!-- STEP 3 -->
    <div v-if="step === 3" class="space-y-6">

      <div class="rounded-xl bg-white/5 p-4 text-xs text-white/60">
        🔒 Your selfie is encrypted and used only for identity verification.
      </div>

      <div
        class="rounded-2xl bg-white/5 hover:bg-white/10 p-8 flex flex-col items-center gap-4"
        @click="upload('selfie')"
      >
        <CheckCircle2
          v-if="uploads.selfie"
          class="w-12 h-12 text-green-400 animate-bounce"
        />
        <Camera v-else class="w-12 h-12 text-white/50 animate-pulse" />
        <p class="text-sm">
          {{ uploads.selfie ? 'Selfie captured' : 'Take a selfie' }}
        </p>
      </div>

      <Button class="w-full" :disabled="!uploads.selfie || isSubmitting" @click="submitKycHandler" :class="{ 'flex items-center justify-center gap-2': isSubmitting }">
        <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
        {{ isSubmitting ? 'Submitting...' : 'Submit verification' }}
      </Button>
    </div>
  </section>

  <!-- RESULT MODAL -->
  <div
    v-if="showModal"
    class="fixed inset-0 bg-black/70 backdrop-blur flex items-center justify-center px-4 z-50"
  >
    <div class="bg-[#0f1215] rounded-2xl p-6 w-full max-w-sm text-center space-y-4 relative overflow-hidden">
      <div
        v-if="kycStatus === 'success'"
        class="absolute inset-0 bg-green-500/10 blur-2xl"
      />

      <CheckCircle2
        v-if="kycStatus === 'success'"
        class="w-14 h-14 text-green-400 mx-auto animate-[scaleIn_0.4s_ease-out]"
      />
      <XCircle
        v-else
        class="w-14 h-14 text-red-400 mx-auto"
      />

      <h3 class="text-lg font-semibold">
        {{ kycStatus === 'success' ? 'Verification submitted' : 'Verification failed' }}
      </h3>

      <p class="text-sm text-white/60">
        {{ kycStatus === 'success'
          ? 'Your verification is under review. This usually takes a few minutes.'
          : 'Please try again with clear information.'
        }}
      </p>

      <Button class="w-full" @click="router.push('/broker/dashboard')">
        Done
      </Button>
    </div>
  </div>
</template>