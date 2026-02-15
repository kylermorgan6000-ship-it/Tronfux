<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Landmark } from 'lucide-vue-next'
import { submitBrokerTransfer, fetchWalletsList } from '@/lib/api'

const router = useRouter()

/* ---------------------------
STATE
--------------------------- */
const step = ref(1) // 1=form, 2=preview, 3=PIN, 4=success
const activeAccount = ref(0)
const loading = ref(false)
const pinLoading = ref(false)
const transactionPin = ref('')
const error = ref(null)

/* ---------------------------
ACCOUNTS
--------------------------- */
const accounts = ref([])

const loadAccounts = async () => {
  loading.value = true
  try {
    const wallets = await fetchWalletsList()
    const data = Array.isArray(wallets) ? wallets : (wallets?.data ?? wallets ?? [])
    accounts.value = data.map((w) => ({
      id: w.id,
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
    loading.value = false
  }
}

onMounted(() => { void loadAccounts() })

/* ---------------------------
FORM
--------------------------- */
const form = ref({
  amount: '',
  bankName: '',
  accountName: '',
  accountNumber: '',
  routingNumber: '',
  bankAddress: ''
})

/* ---------------------------
COMPUTED
--------------------------- */
const placeholder = { id: null, name: 'Wallet', balance: 0, currency: 'USD', status: 'Active', icon: Landmark, minDeposit: 0, maxDeposit: 0, lastLogin: null }
const sliderAccounts = computed(() => (accounts.value.length ? accounts.value : [placeholder]))
const activeIndex = computed(() => (accounts.value.length ? Number(activeAccount.value ?? 0) : 0))
const selectedAccount = computed(() => sliderAccounts.value[activeIndex.value] || null)
const canContinue = computed(() => (
  activeAccount.value !== null &&
  form.value.amount &&
  form.value.bankName &&
  form.value.accountName &&
  form.value.accountNumber &&
  form.value.routingNumber &&
  form.value.bankAddress
))

/* ---------------------------
HELPERS
--------------------------- */
const formatMoney = (val: number) => `$${val.toLocaleString()}`

/* ---------------------------
ACTIONS
--------------------------- */
const proceedToPreview = async () => {
  if (!canContinue.value) return
  loading.value = true
  await new Promise(r => setTimeout(r, 1200))
  loading.value = false
  step.value = 2
}

const proceedToPin = () => {
  step.value = 3
  error.value = null
}

const submitTransfer = async () => {
  if (!transactionPin.value) {
    error.value = 'Please enter your 4-digit PIN'
    return
  }
  if (!selectedAccount.value) {
    error.value = 'Account not selected'
    return
  }

  pinLoading.value = true
  error.value = null

  try {
    await submitBrokerTransfer({
      wallet_id: selectedAccount.value.id,
      amount: Number(form.value.amount),
      bank_name: form.value.bankName,
      account_name: form.value.accountName,
      account_number: form.value.accountNumber,
      routing_number: form.value.routingNumber,
      bank_address: form.value.bankAddress,
      transaction_pin: transactionPin.value
    })

    step.value = 4

    // Auto-redirect after 3 seconds
    setTimeout(() => {
      router.push('/broker/dashboard')
    }, 3000)
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Transfer failed. Please try again.'
    console.error('Transfer error:', err)
  } finally {
    pinLoading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-black text-white relative">

    <!-- PRELOADER -->
    <div v-if="loading" class="absolute inset-0 bg-black/80 backdrop-blur-md z-50 flex items-center justify-center">
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

    <!-- WITHDRAWABLE BALANCE HEADER -->
    <p class="px-4 mt-6 mb-2 text-xs text-white/60 font-medium">Amount Transferable</p>

    <!-- ACCOUNT CARDS SLIDER -->
    <div class="relative mb-6 px-4">
      <div class="overflow-hidden rounded-xl relative">

        <!-- Floating Orbs -->
        <div class="absolute w-16 h-16 bg-green-500/20 rounded-full blur-3xl animate-float top-2 left-10"></div>
        <div class="absolute w-24 h-24 bg-blue-500/10 rounded-full blur-3xl animate-float-slow bottom-2 right-8"></div>

        <div class="flex transition-transform duration-500" :style="{ transform: `translateX(-${Number(activeIndex) * 100}%)` }">
          <div
            v-for="(acc, i) in sliderAccounts"
            :key="i"
            class="relative min-w-full p-4 bg-gray-900/30 backdrop-blur-lg rounded-xl flex justify-between items-center overflow-hidden shadow-lg cursor-pointer"
            @click="activeAccount = i"
          >
            <!-- Shimmer overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-white/20 to-white/10 opacity-20 animate-shimmer rounded-xl"></div>

            <div class="flex items-center gap-4 relative z-10">
              <component :is="acc.icon" class="w-6 h-6 text-white/70" />
              <div>
                <p class="text-xs text-white/60">{{ acc.name }}</p>
                <p class="text-2xl font-bold">
                  {{ formatMoney(acc.balance) }}
                  <span class="text-sm text-white/50">{{ acc.currency }}</span>
                </p>
                <p class="text-[10px]" :class="acc.status === 'Active' ? 'text-green-400' : 'text-yellow-400'">{{ acc.status }}</p>
                <p class="text-[10px] text-white/50 mt-1">
                  Min Deposit: {{ acc.minDeposit }} · Max Deposit: {{ acc.maxDeposit }}
                </p>
                <p class="text-[10px] text-white/50">Last Login: {{ acc.lastLogin }}</p>
              </div>
            </div>

            <div class="flex flex-col items-end relative z-10">
              <ChevronRight class="w-5 h-5 text-white/50 mb-1" />
              <CheckCircle v-if="activeAccount === i" class="w-5 h-5 text-green-400" />
              <p class="text-[10px] text-white/50">Swipe →</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- STEP 1 FORM -->
    <div v-if="step === 1" class="space-y-4 mx-[10px]">

      <!-- AMOUNT -->
      <div class="bg-[#151a20] rounded-xl p-4">
        <label class="text-xs mb-1 block">Withdrawal Amount (USD)</label>
        <input v-model="form.amount" type="number" class="input" placeholder="Enter amount" />
      </div>

      <!-- BANK DETAILS -->
      <div class="bg-[#151a20] rounded-xl p-4 space-y-3">
        <h3 class="text-sm font-semibold mb-2">Wire Transfer Details</h3>

        <div>
          <label class="label">Bank Name</label>
          <div class="input-wrap">
            <Landmark class="icon" />
            <input v-model="form.bankName" class="input" />
          </div>
        </div>

        <div>
          <label class="label">Account Holder Name</label>
          <div class="input-wrap">
            <User class="icon" />
            <input v-model="form.accountName" class="input" />
          </div>
        </div>

        <div>
          <label class="label">Account Number</label>
          <div class="input-wrap">
            <CreditCard class="icon" />
            <input v-model="form.accountNumber" class="input" />
          </div>
        </div>

        <div>
          <label class="label">Routing Number</label>
          <div class="input-wrap">
            <Hash class="icon" />
            <input v-model="form.routingNumber" class="input" />
          </div>
        </div>

        <div>
          <label class="label">Bank Address</label>
          <div class="input-wrap">
            <MapPin class="icon" />
            <textarea v-model="form.bankAddress" class="input h-20"></textarea>
          </div>
        </div>
      </div>

      <button
        :disabled="!canContinue"
        @click="proceedToPreview"
        class="w-full bg-green-500 text-black p-3 rounded-xl font-semibold disabled:opacity-40"
      >
        Continue to Preview →
      </button>
    </div>

    <!-- STEP 2 PREVIEW -->
    <div v-if="step === 2" class="mx-[10px] bg-[#151a20] p-5 rounded-xl space-y-4 mb-4">
      <h3 class="font-semibold">Preview Transfer</h3>

      <div class="space-y-2 text-sm">
        <div class="row"><span>Account</span><span>{{ selectedAccount?.name }}</span></div>
        <div class="row"><span>Amount</span><span>${{ form.amount }}</span></div>
        <div class="row"><span>Bank</span><span>{{ form.bankName }}</span></div>
        <div class="row"><span>Account No.</span><span>****{{ (form.accountNumber || '').slice(-4) }}</span></div>
        <div class="row"><span>Routing</span><span>***{{ (form.routingNumber || '').slice(-3) }}</span></div>
      </div>

      <div class="flex gap-2">
        <button 
          @click="step = 1"
          class="flex-1 bg-gray-700 text-white p-3 rounded-xl font-semibold">
          Back
        </button>
        <button 
          @click="proceedToPin"
          class="flex-1 bg-green-500 text-black p-3 rounded-xl font-semibold">
          Proceed to Verification →
        </button>
      </div>
    </div>

    <!-- STEP 3 PIN VERIFICATION -->
    <div v-if="step === 3" class="mx-[10px] bg-[#151a20] p-5 rounded-xl space-y-4 mb-4">
      <h3 class="font-semibold">Enter Transaction PIN</h3>
      
      <div v-if="error" class="bg-red-900/20 border border-red-600 text-red-400 p-3 rounded-lg text-sm">
        {{ error }}
      </div>

      <p class="text-xs text-white/60">Enter your 4-digit transaction PIN to confirm</p>

      <input 
        v-model="transactionPin" 
        type="password" 
        maxlength="4"
        placeholder="••••"
        class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-center text-xl tracking-widest focus:border-green-500 outline-none"
      />

      <div class="flex gap-2">
        <button 
          @click="step = 2"
          :disabled="pinLoading"
          class="flex-1 bg-gray-700 text-white p-3 rounded-xl font-semibold disabled:opacity-50">
          Back
        </button>
        <button 
          @click="submitTransfer"
          :disabled="pinLoading || !transactionPin"
          class="flex-1 bg-green-500 text-black p-3 rounded-xl font-semibold disabled:opacity-50">
          {{ pinLoading ? 'Processing...' : 'Confirm Transfer' }}
        </button>
      </div>
    </div>

    <!-- STEP 4 SUCCESS -->
    <div v-if="step === 4" class="mx-[10px] bg-[#151a20] p-5 rounded-xl space-y-4 mb-4 flex flex-col items-center">
      <CheckCircle class="w-12 h-12 text-green-400 mb-4" />
      <h3 class="font-semibold text-lg">Transfer Submitted!</h3>
      <p class="text-xs text-white/60 text-center">Your transfer is being processed. Redirecting to dashboard...</p>
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
.label {
  font-size: 11px;
  color: rgba(255,255,255,0.6);
  margin-bottom: 4px;
  display: block;
}
.row {
  display: flex;
  justify-content: space-between;
  color: rgba(255,255,255,0.7);
}
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
/* ------------------------------- ACCOUNT CARDS ANIMATIONS ------------------------------- */
@keyframes shimmer {
  0% { transform: translateX(-100%) }
  100% { transform: translateX(100%) }
}
.animate-shimmer {
  animation: shimmer 2.5s linear infinite;
  background-size: 200% 100%;
}
@keyframes float {
  0%, 100% { transform: translateY(0) }
  50% { transform: translateY(-10px) }
}
.animate-float { animation: float 4s ease-in-out infinite }
.animate-float-slow { animation: float 7s ease-in-out infinite }
</style>