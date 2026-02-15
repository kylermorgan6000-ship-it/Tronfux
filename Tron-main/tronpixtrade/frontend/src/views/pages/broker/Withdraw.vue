<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
import {
  ArrowLeft,
  Landmark,
  User,
  CreditCard,
  Hash,
  MapPin,
  ChevronRight,
  CheckCircle,
  Loader2
} from 'lucide-vue-next'
import { fetchWalletsList, submitWithdrawalRequest } from '@/lib/api'

// --------------------------- STATE ---------------------------
const step = ref(1) // 1 = form, 2 = preview
const activeAccount = ref(0)

// loaders
const pageLoading = ref(false)      // full-screen overlay
const accountsLoading = ref(false)  // wallets fetch
const withdrawalPin = ref('')       // PIN verification

// --------------------------- ACCOUNTS ---------------------------
const accounts = ref([])

const loadAccounts = async () => {
  accountsLoading.value = true
  try {
    const data = await fetchWalletsList()
    const wallets = Array.isArray(data) ? data : (data?.data ?? [])

    accounts.value = wallets.map((w) => ({
      id: w.id ?? null,
      name: w.name ?? w.currency ?? 'Wallet',
      balance: Number(w.balance ?? 0),
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

onMounted(() => {
  void loadAccounts()
})

// --------------------------- FORM ---------------------------
const form = ref({
  amount: '',
  bankName: '',
  accountName: '',
  accountNumber: '',
  routingNumber: '',
  bankAddress: ''
})

// --------------------------- COMPUTED ---------------------------
// Slider placeholder
const placeholder = {
  id: null,
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
const activeIndex = computed(() => Math.max(0, Math.min(Number(activeAccount.value ?? 0), sliderAccounts.value.length - 1)))

const canContinue = computed(() => {
  return (
    activeAccount.value !== null &&
    Number(form.value.amount) > 0 &&
    form.value.bankName &&
    form.value.accountName &&
    form.value.accountNumber &&
    form.value.routingNumber &&
    form.value.bankAddress
  )
})

const selectedAccount = computed(() => sliderAccounts.value?.[activeIndex.value] || placeholder)

// --------------------------- HELPERS ---------------------------
const formatMoney = (val: number) =>
  `$${val.toLocaleString(undefined, { minimumFractionDigits: 2 })}`

// --------------------------- ACTIONS ---------------------------
const proceedToPreview = async () => {
  if (!canContinue.value) return
  pageLoading.value = true
  await new Promise(r => setTimeout(r, 1200))
  pageLoading.value = false
  step.value = 2
}

const submitWithdrawal = async () => {
  if (!selectedAccount.value?.id) return
  pageLoading.value = true
  try {
    await submitWithdrawalRequest({
      wallet_id: selectedAccount.value.id,
      amount: Number(form.value.amount),
      currency: selectedAccount.value.currency,
      withdrawal_method: 'bank',
      destination: form.value.accountNumber
    })
    alert('Withdrawal request submitted! Redirecting to auth codes...')
    step.value = 1
    form.value = { amount: '', bankName: '', accountName: '', accountNumber: '', routingNumber: '', bankAddress: '' }
    await loadAccounts()
    router.push('/broker/auth-codes')
  } catch (err) {
    alert(err?.response?.data?.error || 'Withdrawal failed')
  } finally {
    pageLoading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-black text-white relative">

    <!-- FULL PAGE LOADER -->
    <div
      v-if="pageLoading"
      class="absolute inset-0 bg-black/80 backdrop-blur-md z-50 flex items-center justify-center"
    >
      <svg viewBox="0 0 100 100" class="w-24 h-24 animate-spin">
        <path
          d="M50 5 L95 50 L50 95 L5 50 Z"
          fill="none"
          stroke="#00ff88"
          stroke-width="6"
        />
        <path
          d="M30 32 H70 V40 H56 V72 H44 V40 H30 Z"
          fill="#00ff88"
        />
      </svg>
    </div>

    <!-- HEADER -->
    <div class="sticky top-0 z-40 bg-black shadow-md px-4 py-3 flex items-center gap-3">
      <ArrowLeft class="w-5 h-5 text-white/70" />
      <h1 class="font-semibold text-lg">Withdraw</h1>
    </div>

    <!-- INTRO -->
    <div class="px-4 mt-4">
      <p class="text-sm font-medium">Secure Wire Transfer</p>
      <p class="text-xs text-white/50 mt-1">
        Choose an account, enter your bank details, and review before submitting.
      </p>
    </div>

    <!-- ACCOUNT SLIDER -->
    <p class="px-4 mt-6 mb-2 text-xs text-white/60 font-medium">
      Withdrawable Balance
    </p>

    <div class="relative mb-6 px-4">
      <div class="overflow-hidden rounded-xl">
        <div class="flex transition-transform duration-500" :style="{ transform: `translateX(-${Number(activeIndex) * 100}%)` }">
          <div
            v-for="(acc, i) in sliderAccounts"
            :key="i"
            class="min-w-full p-4 bg-gray-900/30 rounded-xl flex justify-between items-center cursor-pointer"
            @click="activeAccount = i"
          >
            <div class="flex gap-4">
              <component :is="acc.icon" class="w-6 h-6" />
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
        <label class="text-xs">Withdrawal Amount</label>
        <input v-model="form.amount" type="number" class="input" />
      </div>

      <div class="bg-[#151a20] rounded-xl p-4 space-y-3">
        <h3 class="text-sm font-semibold">Wire Transfer Details</h3>

        <div class="input-wrap">
          <Landmark class="icon" />
          <input v-model="form.bankName" class="input" placeholder="Bank Name" />
        </div>

        <div class="input-wrap">
          <User class="icon" />
          <input v-model="form.accountName" class="input" placeholder="Account Holder" />
        </div>

        <div class="input-wrap">
          <CreditCard class="icon" />
          <input v-model="form.accountNumber" class="input" placeholder="Account Number" />
        </div>

        <div class="input-wrap">
          <Hash class="icon" />
          <input v-model="form.routingNumber" class="input" placeholder="Routing Number" />
        </div>

        <div class="input-wrap">
          <MapPin class="icon" />
          <textarea v-model="form.bankAddress" class="input h-20" placeholder="Bank Address" />
        </div>
      </div>

      <button
        :disabled="!canContinue"
        @click="proceedToPreview"
        class="w-full bg-green-500 text-black p-3 rounded-xl font-semibold disabled:opacity-40 flex items-center justify-center gap-2"
      >
        <Loader2 v-if="pageLoading" class="w-4 h-4 animate-spin" />
        Continue to Preview →
      </button>
    </div>

    <!-- STEP 2: PIN VERIFICATION -->
    <div v-if="step === 2" class="mx-[10px] bg-[#151a20] p-5 rounded-xl space-y-4">
      <h3 class="font-semibold">Enter Withdrawal PIN</h3>
      <p class="text-sm text-white/60">For security, enter your 4-digit withdrawal PIN</p>

      <input
        v-model="withdrawalPin"
        type="password"
        placeholder="Enter PIN"
        maxlength="6"
        class="w-full p-3 rounded-xl bg-transparent border border-white/10 text-center text-2xl tracking-widest font-bold"
      />

      <button 
        :disabled="!withdrawalPin || withdrawalPin.length < 4"
        @click="step = 3"
        class="w-full bg-green-500 text-black p-3 rounded-xl font-semibold disabled:opacity-40"
      >
        Verify PIN →
      </button>

      <button
        @click="step = 1; withdrawalPin = ''"
        class="w-full bg-white/5 text-white p-3 rounded-xl font-semibold"
      >
        Back
      </button>
    </div>

    <!-- STEP 3 -->
    <div v-if="step === 3" class="mx-[10px] bg-[#151a20] p-5 rounded-xl space-y-4">
      <h3 class="font-semibold">Preview Withdrawal</h3>

      <div class="space-y-2 text-sm">
        <div class="row"><span>Account</span><span>{{ selectedAccount?.name ?? 'N/A' }}</span></div>
        <div class="row"><span>Amount</span><span>${{ form.amount }}</span></div>
        <div class="row"><span>Bank</span><span>{{ form.bankName }}</span></div>
        <div class="row">
          <span>Account No.</span>
          <span>****{{ (form.accountNumber || '').slice(-4) }}</span>
        </div>
      </div>

      <button class="w-full bg-green-500 text-black p-3 rounded-xl font-semibold flex items-center justify-center gap-2" @click="submitWithdrawal" :disabled="pageLoading">
        <Loader2 v-if="pageLoading" class="w-4 h-4 animate-spin" />
        {{ pageLoading ? 'Processing' : 'Submit Withdrawal' }} →
      </button>

      <button
        @click="step = 2; withdrawalPin = ''"
        class="w-full bg-white/5 text-white p-3 rounded-xl font-semibold"
      >
        Back
      </button>
    </div>
  </div>
</template>

<style scoped>
.input {
  width: 100%;
  padding: 12px 12px 12px 38px;
  border-radius: 12px;
  background: rgba(255,255,255,0.05);
  outline: none;
}
.input-wrap {
  position: relative;
}
.icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  opacity: 0.6;
}
.row {
  display: flex;
  justify-content: space-between;
  color: rgba(255,255,255,0.7);
}
</style>