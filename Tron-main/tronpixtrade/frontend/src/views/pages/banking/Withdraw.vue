<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Landmark } from 'lucide-vue-next'
import { useBankAccount } from '@/composables/useBankAccount'
import { verifyWithdrawalPin, submitWithdrawalRequest } from '@/lib/api'

// --------------------------- STATE ---------------------------
const router = useRouter()
const step = ref(1) // 1 = form, 2 = preview, 3 = PIN
const activeAccount = ref(0)
const loading = ref(false)
const pinLoading = ref(false)
const transactionPin = ref('')

const form = ref({
  amount: '',
  bankName: '',
  accountName: '',
  accountNumber: '',
  routingNumber: '',
  bankAddress: ''
})

// --------------------------- ACCOUNTS ---------------------------
const { accounts, loading: accountsLoading, fetchAccounts } = useBankAccount()
const placeholder = {
  account_holder_name: 'Bank Account',
  balance: 0,
  currency: 'USD',
  account_number: null,
  id: null,
  icon: Landmark,
}

const sliderAccounts = computed(() => (accounts.value.length ? accounts.value : [placeholder]))
const activeIndex = computed(() => (accounts.value.length ? Number(activeAccount.value ?? 0) : 0))
const selectedAccount = computed(() => sliderAccounts.value[activeIndex.value] ?? placeholder)

// --------------------------- FORM LOGIC ---------------------------
const canContinue = computed(() =>
  activeAccount.value !== null &&
  form.value.amount &&
  form.value.bankName &&
  form.value.accountName &&
  form.value.accountNumber &&
  form.value.routingNumber &&
  form.value.bankAddress
)

const formatMoney = (val: number) => `$${val.toLocaleString()}`

// --------------------------- ACTIONS ---------------------------
const proceedToPreview = async () => {
  if (!canContinue.value) return
  step.value = 2
}

const proceedToPin = () => {
  step.value = 3
}

const submitWithdrawal = async () => {
  if (!transactionPin.value) {
    alert('Please enter your 4-digit transaction PIN')
    return
  }

  if (!selectedAccount.value?.id) {
    alert('Please select a valid account')
    return
  }

  pinLoading.value = true
  try {
    await verifyWithdrawalPin(transactionPin.value)

    await submitWithdrawalRequest({
      wallet_id: selectedAccount.value.id,
      amount: Number(form.value.amount),
      currency: selectedAccount.value.currency ?? 'USD',
      withdrawal_method: 'bank',
      destination: form.value.accountNumber,
    })

    alert('Withdrawal request submitted! Status: pending.')

    // Reset form
    form.value.amount = ''
    transactionPin.value = ''
    step.value = 1
    await fetchAccounts() // refresh balances

    router.push('/banking/auth-codes')
  } catch (err: any) {
    console.error(err)
    alert(err?.response?.data?.error || 'Failed to submit withdrawal request')
  } finally {
    pinLoading.value = false
  }
}

// --------------------------- LIFECYCLE ---------------------------
onMounted(async () => {
  await fetchAccounts()
})
</script>

<template>
  <div class="min-h-screen bg-black text-white relative">

    <!-- LOADER -->
    <div v-if="loading || accountsLoading" class="absolute inset-0 bg-black/80 z-50 flex items-center justify-center">
      <Loader2 class="w-12 h-12 animate-spin text-green-400"/>
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

    <!-- WITHDRAWABLE BALANCE HEADER -->
    <p class="px-4 mt-6 mb-2 text-xs text-white/60 font-medium">Withdrawable Balance</p>

    <!-- ACCOUNT CARDS SLIDER -->
    <div class="relative mb-6 px-4">
      <div class="overflow-hidden rounded-xl relative">

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
              <Landmark class="w-6 h-6 text-white/70" />
              <div>
                <p class="text-xs text-white/60">{{ acc.account_holder_name || 'Bank Account' }}</p>
                <p class="text-2xl font-bold">
                  {{ formatMoney(Number(acc.balance) || 0) }}
                  <span class="text-sm text-white/50">{{ acc.currency || 'USD' }}</span>
                </p>
                <p class="text-[10px] text-green-400">Active</p>
                <p class="text-[10px] text-white/50 mt-1">
                  Account: {{ acc.account_number?.slice(-4) || '****' }}
                </p>
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
      <div class="bg-[#151a20] rounded-xl p-4">
        <label class="text-xs mb-1 block">Withdrawal Amount (USD)</label>
        <input v-model="form.amount" type="number" class="input" placeholder="Enter amount" />
      </div>

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
    <div v-if="step === 2" class="mx-[10px] bg-[#151a20] p-5 rounded-xl space-y-4">
      <h3 class="font-semibold">Preview Withdrawal</h3>

      <div class="space-y-2 text-sm">
        <div class="row"><span>Account</span><span>{{ selectedAccount?.account_holder_name ?? 'Bank Account' }}</span></div>
        <div class="row"><span>Amount</span><span>${{ form.amount }}</span></div>
        <div class="row"><span>Bank</span><span>{{ form.bankName }}</span></div>
        <div class="row"><span>Account No.</span><span>****{{ (form.accountNumber || '').slice(-4) }}</span></div>
        <div class="row"><span>Routing</span><span>***{{ (form.routingNumber || '').slice(-3) }}</span></div>
      </div>

      <button
        class="w-full bg-green-500 text-black p-3 rounded-xl font-semibold"
        @click="proceedToPin"
      >
        Proceed to Verification →
      </button>
    </div>

    <!-- STEP 3 PIN VERIFICATION -->
    <div v-if="step === 3" class="mx-[10px] bg-[#151a20] p-5 rounded-xl space-y-4">
      <h3 class="font-semibold">Enter Transaction PIN</h3>
      <input
        type="password"
        maxlength="4"
        v-model="transactionPin"
        placeholder="****"
        class="input text-center tracking-widest text-xl"
      />
      <button
        :disabled="pinLoading || transactionPin.length !== 4"
        @click="submitWithdrawal"
        class="w-full bg-green-500 text-black p-3 rounded-xl font-semibold disabled:opacity-40"
      >
        <span v-if="pinLoading">Processing...</span>
        <span v-else>Submit Withdrawal</span>
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
  color: white;
}
.input-wrap { position: relative; }
.icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; opacity: 0.6; }
.label { font-size: 11px; color: rgba(255,255,255,0.6); margin-bottom: 4px; display: block; }
.row { display: flex; justify-content: space-between; color: rgba(255,255,255,0.7); margin-bottom:2px; }
.no-scrollbar::-webkit-scrollbar { display: none; }

@keyframes shimmer { 0% { transform: translateX(-100%) } 100% { transform: translateX(100%) } }
.animate-shimmer { animation: shimmer 2.5s linear infinite; background-size: 200% 100%; }
@keyframes float { 0%,100% { transform: translateY(0) } 50% { transform: translateY(-10px) } }
.animate-float { animation: float 4s ease-in-out infinite }
.animate-float-slow { animation: float 7s ease-in-out infinite }
</style>