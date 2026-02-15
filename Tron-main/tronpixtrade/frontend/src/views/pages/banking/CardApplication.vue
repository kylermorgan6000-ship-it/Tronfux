<script setup lang="ts">
import { ref, computed } from 'vue'
import { ArrowLeft, CheckCircle, ShieldCheck } from 'lucide-vue-next'
import { useRouter } from 'vue-router'
import FooterLink from '@/components/banking/FooterLink.vue'
import { createCardApplication } from '@/lib/api'

/* Initialize router */
const router = useRouter()

const step = ref(1)
const selectedCard = ref<string | null>(null)
const showFees = ref(false)
const loading = ref(false)
const error = ref<string | null>(null)

const cardTypes = [
  {
    id: 'visa',
    name: 'Visa',
    image: 'https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png',
    description: 'Accepted worldwide for online and subscription payments.'
  },
  {
    id: 'mastercard',
    name: 'Mastercard',
    image: 'https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg',
    description: 'Flexible global payments with strong merchant coverage.'
  },
  {
    id: 'amex',
    name: 'American Express',
    image: 'https://upload.wikimedia.org/wikipedia/commons/3/30/American_Express_logo.svg',
    description: 'Premium card with enhanced security and higher limits.'
  }
]

const progressWidth = computed(() => `${step.value * 25}%`)

const submitCardApplication = async () => {
  loading.value = true
  error.value = null

  try {
    const cardTypeMapping: Record<string, string> = {
      visa: 'debit',
      mastercard: 'debit',
      amex: 'credit',
    }

    await createCardApplication({
      bank_account_id: 1,
      card_name: selectedCard.value?.toUpperCase() || 'New Card',
      card_type: (selectedCard.value && cardTypeMapping[selectedCard.value]) || 'debit',
      daily_limit: 5000,
      monthly_limit: 50000,
    })

    // Move to success step
    step.value = 4

    // Auto-redirect after 3 seconds
    setTimeout(() => {
      router.push('/banking/cards')
    }, 3000)

  } catch (err: any) {
    error.value = err.response?.data?.message || err.message || 'An unknown error occurred'
    console.error('Error creating card:', err)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <!-- HEADER -->
  <div class="fixed top-0 left-0 w-full z-50 bg-[#0f1215] shadow-md">
    <div class="flex items-center gap-3 px-4 py-4">
      <button @click="router.push('/banking/cards')" class="text-white/70 hover:text-white">
        <ArrowLeft class="w-5 h-5" />
      </button>
      <h1 class="text-sm font-semibold text-white">Card Issuance</h1>
    </div>

    <!-- PROGRESS -->
    <div class="px-4 pb-3">
      <div class="flex justify-between text-[10px] text-white/40 mb-2">
        <span :class="step >= 1 && 'text-green-400'">Card</span>
        <span :class="step >= 2 && 'text-green-400'">Authorization</span>
        <span :class="step >= 3 && 'text-green-400'">Review</span>
        <span :class="step >= 4 && 'text-green-400'">Done</span>
      </div>
      <div class="h-[2px] bg-gray-800">
        <div class="h-full bg-green-500 transition-all" :style="{ width: progressWidth }" />
      </div>
    </div>
  </div>

  <section class="min-h-screen bg-[#0f1215] text-white mt-[100px] px-4 pb-28">

    <!-- ERROR STATE -->
    <div v-if="error" class="mb-4 bg-red-900/20 border border-red-600 text-red-400 p-3 rounded-lg text-sm">
      {{ error }}
    </div>

    <!-- CARD PREVIEW -->
    <div class="mb-8">
      <div
        class="relative w-full max-w-[320px] h-52 mx-auto rounded-2xl p-5
               bg-gradient-to-br from-gray-700 via-gray-900 to-black
               shadow-xl overflow-hidden"
      >
        <div class="absolute w-32 h-32 bg-green-500/20 blur-3xl -top-10 -left-10"></div>

        <div class="relative z-10 h-full flex flex-col justify-between">
          <div class="flex justify-between items-center">
            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/EMV_Chip.png" class="w-10" />
            <span class="text-xs font-semibold text-white/70">
              {{ selectedCard ? selectedCard.toUpperCase() : 'VIRTUAL CARD' }}
            </span>
          </div>

          <div class="text-lg tracking-widest font-semibold">
            •••• •••• •••• ••••
          </div>

          <div class="flex justify-between text-[11px] text-white/70">
            <span>YOUR NAME</span>
            <span>MM / YY</span>
          </div>
        </div>
      </div>

      <p class="text-center text-[11px] text-white/50 mt-2">
        Preview of your virtual card
      </p>
    </div>

    <!-- STEP 1: CARD TYPE -->
    <div v-if="step === 1" class="space-y-6">
      <div>
        <h2 class="text-lg font-semibold">Select Card Type</h2>
        <p class="text-sm text-white/60">
          Choose the card you want issued to your account.
        </p>
      </div>

      <div class="grid grid-cols-1 gap-4">
        <div
          v-for="card in cardTypes"
          :key="card.id"
          @click="selectedCard = card.id"
          class="rounded-2xl p-4 cursor-pointer transition border
                 flex items-center gap-4"
          :class="selectedCard === card.id
            ? 'border-green-500 bg-gray-800/60'
            : 'border-gray-700 bg-gray-900/40'"
        >
          <img :src="card.image" class="w-12 h-12 object-contain bg-white rounded-md p-1" />
          <div>
            <p class="font-semibold">{{ card.name }}</p>
            <p class="text-[11px] text-white/60">{{ card.description }}</p>
          </div>
        </div>
      </div>

      <button
        :disabled="!selectedCard"
        @click="showFees = true"
        class="w-full py-3 rounded-xl font-semibold transition
               disabled:opacity-40 bg-green-600 hover:bg-green-500"
      >
        Continue
      </button>
    </div>

    <!-- STEP 2: AUTHORIZATION -->
    <div v-if="step === 2" class="space-y-6">
      <div>
        <h2 class="text-lg font-semibold">Issuance Authorization</h2>
        <p class="text-sm text-white/60">
          We’ll issue this card using your verified identity.
          No additional KYC is required.
        </p>
      </div>

      <div class="rounded-xl bg-gray-900/40 p-4 space-y-3 text-sm">
        <p><strong>Card Type:</strong> {{ selectedCard?.toUpperCase() }}</p>
        <p><strong>Billing Currency:</strong> USD</p>
        <p><strong>Card Format:</strong> Virtual</p>
      </div>

      <div class="flex items-start gap-2 text-[11px] text-white/60">
        <ShieldCheck class="w-4 h-4 text-green-400 mt-[2px]" />
        <span>Card issuance is protected with bank-grade encryption.</span>
      </div>

      <button
        @click="step = 3"
        class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-500 transition font-semibold"
      >
        Review Issuance
      </button>
    </div>

    <!-- STEP 3: REVIEW -->
    <div v-if="step === 3" class="space-y-6">
      <h2 class="text-lg font-semibold">Final Review</h2>

      <div class="rounded-xl bg-gray-900/40 p-4 space-y-3 text-sm">
        <p><strong>Card:</strong> {{ selectedCard?.toUpperCase() }}</p>
        <p><strong>Region:</strong> United States</p>
        <p><strong>Security:</strong> Enabled</p>
      </div>

      <p class="text-[11px] text-white/50">
        By submitting, you authorize us to securely generate this card
        and bind it to your account.
      </p>

      <button
        @click="submitCardApplication"
        :disabled="loading"
        class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-500 transition font-semibold disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
      >
        <span v-if="loading" class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
        {{ loading ? 'Creating Card...' : 'Create Card' }}
      </button>
    </div>

    <!-- STEP 4: SUCCESS -->
    <div v-if="step === 4" class="flex flex-col items-center text-center mt-20">
      <CheckCircle class="w-14 h-14 text-green-400 mb-4" />
      <h2 class="text-lg font-semibold mb-2">Card Generation Started</h2>
      <p class="text-sm text-white/60 mb-6">
        Your card is being securely generated.
        You’ll see it appear once ready.
      </p>

      <button
        @click="router.push('/banking/cards')"
        class="w-full py-3 rounded-xl bg-gray-800 hover:bg-gray-700 transition"
      >
        Back to Cards
      </button>

      <p class="text-xs text-white/40 mt-4">
        Redirecting automatically in 3 seconds...
      </p>
    </div>

  </section>

  <!-- FEES BOTTOM SHEET -->
  <div v-if="showFees" class="fixed inset-0 z-50 pb-13 bg-black/60 flex items-end">
    <div class="w-full bg-[#0f1215] rounded-t-2xl p-5 space-y-4">
      <h3 class="font-semibold">Fees & Limits</h3>
      <ul class="text-[12px] text-white/60 space-y-2">
        <li>• One-time setup fee</li>
        <li>• Monthly maintenance may apply</li>
        <li>• USD billing currency</li>
      </ul>

      <button
        @click="showFees = false; step = 2"
        class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-500 transition font-semibold"
      >
        Accept & Continue
      </button>
    </div>
  </div>

  <FooterLink />
</template>