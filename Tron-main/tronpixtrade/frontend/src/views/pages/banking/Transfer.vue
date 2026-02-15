<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import {
  ArrowLeft,
  Landmark,
  User,
  CreditCard,
  Hash,
  MapPin,
  ChevronRight,
  CheckCircle,
} from 'lucide-vue-next'
import api from '@/lib/api'

// --------------------------- STATE ---------------------------
const step = ref(1) // 1 = form, 2 = preview
const activeAccount = ref(0)

// loaders (IMPORTANT: unique names)
const pageLoading = ref(false)
const accountsLoading = ref(false)

// --------------------------- ACCOUNTS (from API) ---------------------------
const accounts = ref([])

const loadAccounts = async () => {
  accountsLoading.value = true
  try {
    const res = await api.get('/wallets')
    accounts.value = (res.data.data ?? res.data).map((w: any) => ({
      name: w.name ?? w.currency ?? 'Wallet',
      balance: w.balance ?? 0,
      currency: w.currency ?? 'USD',
      status: 'Active',
      icon: Landmark,
      minDeposit: w.minDeposit ?? 0,
      maxDeposit: w.maxDeposit ?? 0,
      lastLogin: w.updated_at ?? null,
    }))
  } catch (e) {
    console.error('Failed to load wallets', e)
    accounts.value = []
  } finally {
    accountsLoading.value = false
  }
}

onMounted(loadAccounts)

// --------------------------- FORM ---------------------------
const form = ref({
  amount: '',
  bankName: '',
  accountName: '',
  accountNumber: '',
  routingNumber: '',
  bankAddress: '',
})

// --------------------------- COMPUTED ---------------------------
// Slider placeholder to ensure UI always renders
const placeholder = {
  name: 'Wallet',
  balance: 0,
  currency: 'USD',
  status: 'Active',
  icon: Landmark,
  minDeposit: 0,
  maxDeposit: 0,
  lastLogin: null,
}

const sliderAccounts = computed(() => (accounts.value.length ? accounts.value : [placeholder]))
const activeIndex = computed(() => (accounts.value.length ? Number(activeAccount.value ?? 0) : 0))

const canContinue = computed(() =>
  activeAccount.value !== null &&
  Object.values(form.value).every(v => v)
)

const selectedAccount = computed(() =>
  sliderAccounts.value[activeIndex.value] || null
)

// --------------------------- HELPERS ---------------------------
const formatMoney = (val: number) => `$${val.toLocaleString()}`

// --------------------------- ACTIONS ---------------------------
const proceedToPreview = async () => {
  if (!canContinue.value) return
  pageLoading.value = true
  await new Promise(r => setTimeout(r, 1200))
  pageLoading.value = false
  step.value = 2
}
</script>

<template>
  <div class="min-h-screen bg-black text-white relative">

    <!-- PRELOADER -->
    <div
      v-if="pageLoading"
      class="absolute inset-0 bg-black/80 backdrop-blur-md z-50 flex items-center justify-center"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 100 100"
        class="w-24 h-24 animate-spin"
      >
        <defs>
          <filter id="greenGlow" x="-50%" y="-50%" width="200%" height="200%">
            <feGaussianBlur stdDeviation="3" result="blur" />
            <feMerge>
              <feMergeNode in="blur" />
              <feMergeNode in="SourceGraphic" />
            </feMerge>
          </filter>
        </defs>
        <path
          d="M50 5 L95 50 L50 95 L5 50 Z"
          fill="none"
          stroke="#00ff88"
          stroke-width="6"
          stroke-linejoin="round"
          filter="url(#greenGlow)"
        />
        <path
          d="M30 32 H70 V40 H56 V72 H44 V40 H30 Z"
          fill="#00ff88"
          filter="url(#greenGlow)"
        />
      </svg>
    </div>

    <!-- HEADER -->
    <div class="sticky top-0 z-40 bg-black shadow-md px-4 py-3 flex items-center gap-3">
      <ArrowLeft class="w-5 h-5 text-white/70" />
      <h1 class="font-semibold text-lg">Transfer</h1>
    </div>

    <!-- INTRO -->
    <div class="px-4 mt-4">
      <p class="text-sm font-medium">Secure Wire Transfer</p>
      <p class="text-xs text-white/50 mt-1">
        Choose an account, enter your bank details, and review before submitting.
      </p>
    </div>

    <!-- BALANCE HEADER -->
    <p class="px-4 mt-6 mb-2 text-xs text-white/60 font-medium">
      Amount Transferable
    </p>

    <!-- ACCOUNT CARDS -->
    <div class="relative mb-6 px-4">
      <div class="overflow-hidden rounded-xl relative">

        <div class="flex transition-transform duration-500" :style="{ transform: `translateX(-${Number(activeIndex) * 100}%)` }">
          <div
            v-for="(acc, i) in sliderAccounts"
            :key="i"
            class="relative min-w-full p-4 bg-gray-900/30 backdrop-blur-lg rounded-xl flex justify-between items-center cursor-pointer"
            @click="activeAccount = i"
          >
            <div class="flex items-center gap-4">
              <component :is="acc.icon" class="w-6 h-6 text-white/70" />
              <div>
                <p class="text-xs text-white/60">{{ acc.name }}</p>
                <p class="text-2xl font-bold">
                  {{ formatMoney(acc.balance) }}
                  <span class="text-sm text-white/50">{{ acc.currency }}</span>
                </p>
              </div>
            </div>

            <CheckCircle
              v-if="activeAccount === i"
              class="w-5 h-5 text-green-400"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- STEP 1 -->
    <div v-if="step === 1" class="space-y-4 mx-[10px]">

      <div class="bg-[#151a20] rounded-xl p-4">
        <label class="text-xs mb-1 block">Transfer Amount</label>
        <input v-model="form.amount" type="number" class="input" />
      </div>

      <div class="bg-[#151a20] rounded-xl p-4 space-y-3">
        <h3 class="text-sm font-semibold">Wire Transfer Details</h3>

        <div>
          <label class="label">Bank Name</label>
          <input v-model="form.bankName" class="input" />
        </div>

        <div>
          <label class="label">Account Holder</label>
          <input v-model="form.accountName" class="input" />
        </div>

        <div>
          <label class="label">Account Number</label>
          <input v-model="form.accountNumber" class="input" />
        </div>

        <div>
          <label class="label">Routing Number</label>
          <input v-model="form.routingNumber" class="input" />
        </div>

        <div>
          <label class="label">Bank Address</label>
          <textarea v-model="form.bankAddress" class="input h-20"></textarea>
        </div>
      </div>

      <button
        :disabled="!canContinue"
        @click="proceedToPreview"
        class="w-full bg-green-500 text-black p-3 rounded-xl font-semibold disabled:opacity-40"
      >
        Continue →
      </button>
    </div>

    <!-- STEP 2 -->
    <div v-if="step === 2" class="mx-[10px] bg-[#151a20] p-5 rounded-xl space-y-4">
      <h3 class="font-semibold">Preview Transfer</h3>

      <div class="row"><span>Account</span><span>{{ selectedAccount?.name }}</span></div>
      <div class="row"><span>Amount</span><span>${{ form.amount }}</span></div>
      <div class="row"><span>Bank</span><span>{{ form.bankName }}</span></div>

      <button class="w-full bg-green-500 text-black p-3 rounded-xl font-semibold">
        Proceed →
      </button>
    </div>

  </div>
</template>

<style scoped>
.input {
  width: 100%;
  padding: 12px;
  border-radius: 12px;
  background: rgba(255,255,255,0.05);
  outline: none;
}
.label {
  font-size: 11px;
  color: rgba(255,255,255,0.6);
}
.row {
  display: flex;
  justify-content: space-between;
  color: rgba(255,255,255,0.7);
}
</style>