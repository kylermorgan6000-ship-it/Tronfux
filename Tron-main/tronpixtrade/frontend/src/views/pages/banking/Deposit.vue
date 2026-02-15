<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { ArrowLeft, X, ChevronRight, CheckCircle, Landmark, CreditCard } from 'lucide-vue-next'
import { useBankAccount } from '@/composables/useBankAccount'
import FooterLink from '@/components/banking/FooterLink.vue'

// USER
const user = ref({ name: 'Anthony' })

// MODAL STATE
const showModal = ref(false)
const selectedMethod = ref(null)

// AMOUNT
const amount = ref<number | null>(null)

// ACTIVE ACCOUNT INDEX
const activeAccount = ref(0)

// LOAD ACCOUNTS FROM API
const { accounts, loading: accountsLoading, fetchAccounts } = useBankAccount()

// PAYMENT METHODS WITH CDN IMAGES
const depositMethods = [
  { name: 'Credit / Debit Card', image: 'https://upload.wikimedia.org/wikipedia/commons/0/04/Mastercard-logo.png' },
  { name: 'PayPal', image: 'https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg' },
  { name: 'Bank Transfer', image: 'https://cdn-icons-png.flaticon.com/512/2721/2721295.png' },
  { name: 'Crypto', image: 'https://cryptologos.cc/logos/bitcoin-btc-logo.png' },
]

// GREETING
const greeting = computed(() => {
  const hour = new Date().getHours()
  if (hour < 12) return 'Good morning'
  if (hour < 18) return 'Good afternoon'
  return 'Good evening'
})

// OPEN MODAL
const openMethod = (method: string) => {
  selectedMethod.value = method
  showModal.value = true
}

// CLOSE MODAL
const closeModal = () => {
  showModal.value = false
  selectedMethod.value = null
}

// FORMAT MONEY
const formatMoney = (val: number) => `$${val.toLocaleString()}`

// Slider safety
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

// FETCH ACCOUNTS ON MOUNT
onMounted(async () => {
  await fetchAccounts()
})
</script>

<template>
    <!-- HEADER -->
    <div class="sticky top-0 z-40 bg-black shadow-[0_1px_0_rgba(255,255,255,0.06)] px-4 py-3 flex items-center gap-3">
      <ArrowLeft class="w-5 h-5 text-white/70" />
      <h1 class="font-semibold text-lg">Deposit</h1>
    </div>

    <!-- INTRO -->
    <div class="px-4 mt-4">
      <p class="text-sm font-medium">Secure Deposit methods</p>
      <p class="text-xs text-white/50 mt-1">
        Choose any of the payment method to deposite into your account, if any of the payment method is unavaible please contact support.
      </p>
    </div>

    <!-- WITHDRAWABLE BALANCE HEADER -->
    <p class="px-4 mt-6 mb-2 text-xs text-white/60 font-medium">Available balance</p>

    <!-- LOADING SPINNER -->
    <div v-if="accountsLoading" class="flex justify-center items-center py-10">
      <div class="w-8 h-8 border-2 border-green-500/20 border-t-green-400 rounded-full animate-spin"></div>
    </div>

    <!-- ACCOUNT CARDS SLIDER -->
    <div v-else class="relative mb-6 px-4">
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

    <!-- AMOUNT INPUT -->
    <div class="mb-6 px-4">
      <label class="block text-[11px] text-white/60 mb-1">
        Amount to Deposit (USD)
      </label>
      <input
        v-model="amount"
        type="number"
        placeholder="Enter amount"
        class="w-full rounded-xl p-3 bg-gray-900/50 backdrop-blur-md text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-green-400"
      />
    </div>

    <!-- DEPOSIT METHODS -->
    <div class="mb-8 px-4">
      <h3 class="text-sm font-semibold mb-3">Choose Deposit Method</h3>
      <div class="overflow-x-auto flex gap-4 pb-2 no-scrollbar">
        <div
          v-for="(method, i) in depositMethods"
          :key="i"
          @click="openMethod(method.name)"
          class="min-w-[140px] relative bg-gray-800/40 backdrop-blur-md rounded-xl p-4 flex flex-col items-center gap-2 cursor-pointer hover:bg-gray-700/60 transition-all shadow-lg"
        >
          <!-- Floating overlay orb -->
          <div class="absolute w-12 h-12 bg-green-500/10 rounded-full blur-2xl top-[-10px] right-[-10px] animate-float"></div>

          <!-- CDN IMAGE -->
          <img
            :src="method.image"
            class="w-10 h-10 object-contain z-10"
            alt="method.name"
          />
          <span class="text-[11px] text-white/80 text-center">{{ method.name }}</span>
        </div>
      </div>
    </div>

    <p class="text-[10px] text-white/50 text-center">
      All deposits are processed securely. Contact support for assistance.
    </p>
  

  <!-- MODAL -->
  <transition name="fade">
    <div
      v-if="showModal"
      class="fixed inset-0 z-[60] bg-black/80 backdrop-blur-md flex items-center justify-center px-4"
    >
      <div class="bg-[#12161c] w-full max-w-md rounded-2xl p-6 relative shadow-xl">
        <button
          @click="closeModal"
          class="absolute top-4 right-4 text-white/60 hover:text-white"
        >
          <X class="w-5 h-5" />
        </button>

        <h2 class="text-lg font-semibold mb-2">
          {{ selectedMethod }}
        </h2>

        <p class="text-sm text-white/70 mb-4">
          This deposit method is currently unavailable.
        </p>

        <p class="text-[12px] text-white/50 mb-6">
          Please contact support to receive instructions on how to fund your account.
        </p>

        <button class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-500 transition font-semibold">
          Contact Support
        </button>
      </div>
    </div>
  </transition>

  <FooterLink />
</template>

<style scoped>
/* FADE MODAL */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* ORBS ANIMATION */
@keyframes float {
  0%, 100% { transform: translateY(0) }
  50% { transform: translateY(-8px) }
}
.animate-float { animation: float 4s ease-in-out infinite }
.animate-float-slow { animation: float 7s ease-in-out infinite }

/* SHIMMER ANIMATION */
@keyframes shimmer {
  0% { transform: translateX(-100%) }
  100% { transform: translateX(100%) }
}
.animate-shimmer {
  animation: shimmer 2.5s linear infinite;
  background-size: 200% 100%;
}

/* SCROLLBAR HIDDEN */
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
</style>