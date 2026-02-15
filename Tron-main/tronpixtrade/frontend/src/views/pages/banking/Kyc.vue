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
  FileText,
  ShieldCheck,
  Loader2
} from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { submitKyc } from '@/lib/api' // <- centralized API call

/* Initialize router */
const router = useRouter()

/* ---------------- STATE ---------------- */
const step = ref(1)
const showModal = ref(false)
const kycStatus = ref('success')
const userCountry = ref('United States 🇺🇸')
const isLoading = ref(false)

const form = ref({ documentType: '' })

const uploads = ref({ front: null, back: null, selfie: null })
const previews = ref({ front: '', back: '', selfie: '' })

/* ---------------- COMPUTED ---------------- */
const progress = computed(() => `${(step.value / 3) * 100}%`)

const headerContent = computed(() => {
  if (step.value === 1) return { title: 'Identity verification', subtitle: 'Choose a valid government-issued document' }
  if (step.value === 2) return { title: 'Document upload', subtitle: 'Upload clear photos of your document' }
  return { title: 'Face verification', subtitle: 'Confirm your identity with a selfie' }
})

/* ---------------- ACTIONS ---------------- */
const next = () => step.value < 3 && step.value++
const back = () => step.value > 1 && step.value--

const onFileChange = (e: Event, key: 'front' | 'back' | 'selfie') => {
  const input = e.target as HTMLInputElement
  if (!input.files || !input.files[0]) return
  uploads.value[key] = input.files[0]
  previews.value[key] = URL.createObjectURL(input.files[0])
}

const submitKycAction = async () => {
  if (!uploads.value.front || !uploads.value.back || !uploads.value.selfie) return

  const formData = new FormData()
  formData.append('documentType', form.value.documentType)
  formData.append('front', uploads.value.front)
  formData.append('back', uploads.value.back)
  formData.append('selfie', uploads.value.selfie)

  try {
    await submitKyc(formData)
    kycStatus.value = 'success'
    showModal.value = true
  } catch (err: any) {
    console.error(err)
    kycStatus.value = 'failed'
    alert(err.response?.data?.message || 'KYC submission failed')
  }
}
</script>

<template>
  <!-- HEADER -->
  <div class="fixed top-0 left-0 w-full z-50 bg-[#0f1215]/95 backdrop-blur border-b border-white/5">
    <div class="px-4 py-3 space-y-2 max-w-md mx-auto">
      <div class="flex items-center gap-3">
        <button @click="router.push('/banking/dashboard')" class="text-white/60 hover:text-white">
          <ArrowLeft class="w-5 h-5" />
        </button>
        <div>
          <p class="text-sm font-medium">{{ headerContent.title }}</p>
          <p class="text-[11px] text-white/50">{{ headerContent.subtitle }}</p>
        </div>
      </div>

      <div class="h-1 w-full bg-white/10 rounded-full">
        <div class="h-full bg-green-500 rounded-full transition-all" :style="{ width: progress }" />
      </div>
    </div>
  </div>

  <!-- CONTENT -->
  <section class="min-h-screen bg-[#0f1215] pt-[104px] px-4 pb-24 max-w-md mx-auto space-y-8">

    <!-- STATS -->
    <div class="grid grid-cols-3 gap-3 text-center text-[11px]">
      <div class="bg-white/5 rounded-xl py-3">
        <p class="font-semibold">3</p>
        <p class="text-white/40">Steps</p>
      </div>
      <div class="bg-white/5 rounded-xl py-3">
        <p class="font-semibold">~5 min</p>
        <p class="text-white/40">Time</p>
      </div>
      <div class="bg-white/5 rounded-xl py-3">
        <p class="font-semibold text-green-400">98%</p>
        <p class="text-white/40">Success</p>
      </div>
    </div>

    <!-- COUNTRY -->
    <div class="flex items-center justify-between bg-white/5 rounded-xl p-4">
      <div>
        <p class="text-xs text-white/50">Country</p>
        <p class="text-sm font-medium">{{ userCountry }}</p>
      </div>
      <span class="text-xs text-green-400">Detected</span>
    </div>

    <!-- STEP 1 -->
    <div v-if="step === 1" class="space-y-5">
      <div
        v-for="(doc, key) in [
          { key: 'passport', label: 'Passport', icon: FileText, note: 'Recommended' },
          { key: 'national_id', label: 'National ID', icon: CreditCard },
          { key: 'driver_license', label: 'Driver’s License', icon: CreditCard },
        ]"
        :key="key"
        @click="form.documentType = doc.key"
        class="rounded-2xl p-5 bg-white/5 hover:bg-white/10 transition cursor-pointer"
        :class="form.documentType === doc.key && 'ring-2 ring-green-500/50'"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <component :is="doc.icon" class="w-6 h-6 text-green-400" />
            <p class="text-sm font-semibold">{{ doc.label }}</p>
          </div>
          <span v-if="doc.note" class="text-[10px] bg-green-500/20 text-green-400 px-2 rounded-full">
            {{ doc.note }}
          </span>
        </div>
        <p class="text-xs text-white/60 mt-2">
          Government-issued photo identification
        </p>
      </div>

      <Button class="w-full" :disabled="!form.documentType" @click="next">
        Continue
      </Button>
    </div>

    <!-- STEP 2 -->
    <div v-if="step === 2" class="space-y-6">

      <div class="rounded-xl bg-yellow-500/10 border border-yellow-500/20 p-4 text-xs text-yellow-300">
        • All corners visible<br />
        • No screenshots<br />
        • Document not expired
      </div>

      <div v-for="side in ['front','back']" :key="side"
        class="relative rounded-2xl border border-dashed border-white/20 p-4 overflow-hidden">
        <input
          type="file"
          accept="image/*"
          capture="environment"
          class="absolute inset-0 opacity-0 z-10"
          @change="e => onFileChange(e, side as 'front' | 'back')"
        />
        <img v-if="previews[side as 'front' | 'back']" :src="previews[side as 'front' | 'back']" class="h-40 w-full object-cover rounded-xl" />
        <div v-else class="flex flex-col items-center gap-3 py-6">
          <Upload class="w-8 h-8 text-white/40" />
          <p class="text-sm capitalize">{{ side }} of document</p>
          <p class="text-[11px] text-white/40">Tap to upload or snap</p>
        </div>
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
      <div class="flex items-center gap-2 text-xs text-white/60 bg-white/5 p-3 rounded-xl">
        <ShieldCheck class="w-4 h-4 text-green-400" />
        Encrypted & used only for verification
      </div>

      <div class="relative rounded-2xl bg-white/5 p-6 overflow-hidden">
        <input
          type="file"
          accept="image/*"
          capture="user"
          class="absolute inset-0 opacity-0 z-10"
          @change="e => onFileChange(e, 'selfie')"
        />
        <div v-if="previews.selfie" class="text-center space-y-4">
          <img :src="previews.selfie" class="w-32 h-32 rounded-full mx-auto object-cover ring-2 ring-green-400" />
          <p class="text-green-400 text-sm">Selfie captured ✓</p>
        </div>
        <div v-else class="flex flex-col items-center gap-4">
          <Camera class="w-12 h-12 text-white/50 animate-pulse" />
          <p class="text-sm">Take a selfie</p>
          <p class="text-[11px] text-white/40">Good lighting · No hats</p>
        </div>
      </div>

      <Button class="w-full" :disabled="!uploads.selfie || isLoading" @click="submitKycAction" :class="{ 'flex items-center justify-center gap-2': isLoading }">
        <Loader2 v-if="isLoading" class="w-4 h-4 animate-spin" />
        {{ isLoading ? 'Submitting...' : 'Submit verification' }}
      </Button>
    </div>
  </section>

  <!-- MODAL -->
  <div v-if="showModal" class="fixed inset-0 bg-black/70 flex items-center justify-center px-4 z-50">
    <div class="bg-[#0f1215] rounded-2xl p-6 w-full max-w-sm text-center space-y-4">
      <CheckCircle2 v-if="kycStatus === 'success'" class="w-14 h-14 text-green-400 mx-auto" />
      <XCircle v-else class="w-14 h-14 text-red-400 mx-auto" />
      <h3 class="text-lg font-semibold">
        {{ kycStatus === 'success' ? 'Verification submitted' : 'Verification failed' }}
      </h3>
      <p class="text-sm text-white/60">
        {{ kycStatus === 'success' ? "We're reviewing your information." : 'Please try again or contact support.' }}
      </p>
      <Button class="w-full" @click="kycStatus === 'success' ? router.push('/banking/dashboard') : (showModal = false)">
        {{ kycStatus === 'success' ? 'Done' : 'Try again' }}
      </Button>
    </div>
  </div>
</template>