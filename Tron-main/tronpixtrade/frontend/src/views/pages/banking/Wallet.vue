<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import {
  Wallet,
  ArrowDownLeft,
  ArrowUpRight,
} from 'lucide-vue-next'
import { fetchUser, fetchWallets, fetchTransactions } from '@/lib/api'

/* -------------------------
STATE
------------------------- */
const userName = ref('')
const showBalance = ref(true)
const activeWallet = ref(0)
const sliderTimer = ref<number | null>(null)

const wallets = ref([])
const walletsLoading = ref(false)

const transactions = ref([])
const transactionsLoading = ref(false)

/* -------------------------
STATIC DATA
------------------------- */
const goals = ref([
  { title: 'Emergency Fund', progress: 70, amount: 2100, color: 'bg-green-400' },
  { title: 'Vacation', progress: 45, amount: 900, color: 'bg-blue-400' },
  { title: 'Crypto Investment', progress: 30, amount: 0.3, color: 'bg-purple-400' },
  { title: 'Education Fund', progress: 55, amount: 2750, color: 'bg-yellow-400' },
])

/* -------------------------
COMPUTED
------------------------- */
const greeting = computed(() => {
  const hour = new Date().getHours()
  if (hour < 12) return `Good morning, ${userName.value}`
  if (hour < 18) return `Good afternoon, ${userName.value}`
  return `Good evening, ${userName.value}`
})

const totalBalance = computed(() =>
  wallets.value.reduce((sum, w) => sum + w.balance, 0)
)

const formatMoney = (v: number) =>
  showBalance.value ? v.toLocaleString() : '•••••'

/* -------------------------
FETCH DATA
------------------------- */
const fetchData = async () => {
  walletsLoading.value = true
  transactionsLoading.value = true
  try {
    const [userRes, walletsRes, txRes] = await Promise.all([
      fetchUser(),
      fetchWallets(),
      fetchTransactions({ per_page: 10 }),
    ])

    userName.value = userRes?.name || ''

    wallets.value = walletsRes.map((w: any) => ({
      name: `${w.currency} Wallet`,
      balance: Number(w.balance) || 0,
      currency: w.currency,
      icon: Wallet,
    }))

    transactions.value = txRes.map((tx: any) => ({
      type: tx.type,
      label: tx.description || (tx.type === 'credit' ? 'Credit' : 'Debit'),
      amount: Number(tx.amount),
      date: tx.created_at
        ? new Date(tx.created_at).toLocaleDateString()
        : 'N/A',
      icon: tx.type === 'credit' ? ArrowDownLeft : ArrowUpRight,
    }))
  } catch (err) {
    console.error('Failed to load wallet dashboard data', err)
  } finally {
    walletsLoading.value = false
    transactionsLoading.value = false
  }
}

/* -------------------------
AUTO WALLET SLIDER
------------------------- */
const startWalletSlider = () => {
  if (sliderTimer.value) return

  sliderTimer.value = window.setInterval(() => {
    if (wallets.value.length > 1) {
      activeWallet.value = (activeWallet.value + 1) % wallets.value.length
    }
  }, 5000)
}

const stopWalletSlider = () => {
  if (sliderTimer.value) {
    clearInterval(sliderTimer.value)
    sliderTimer.value = null
  }
}

/* -------------------------
LIFECYCLE
------------------------- */
onMounted(async () => {
  await fetchData()
  startWalletSlider()
})

onUnmounted(() => {
  stopWalletSlider()
})
</script>

<template>
<!-- HEADER -->
<div class="flex items-center justify-between py-4 px-4 bg-black fixed top-0 left-0 w-full z-50 shadow-[0_1px_0_rgba(255,255,255,0.06)]">
  <div class="flex items-center gap-3">
    <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-700 flex items-center justify-center">
      <img src="https://i.pravatar.cc/150?u=wallet" alt="Profile" class="w-full h-full object-cover"/>
    </div>
    <div class="text-sm">
      <p class="text-white/80 font-medium">{{ greeting }}</p>
    </div>
  </div>
  <Bell class="w-5 h-5 text-white/70 hover:text-green-400 transition-colors duration-200" />
</div>

<!-- MAIN CONTENT -->
<section class="min-h-screen bg-black text-white mt-[83px] px-4 pb-24">

  <!-- WALLET CARDS -->
  <div class="relative mb-6 mt-20">
    <div class="overflow-hidden rounded-xl relative">
      <div class="absolute w-16 h-16 bg-green-500/20 rounded-full blur-3xl animate-float top-2 left-10"></div>
      <div class="absolute w-24 h-24 bg-blue-500/10 rounded-full blur-3xl animate-float-slow bottom-2 right-8"></div>

      <div v-if="wallets.length" class="flex transition-transform duration-500" :style="{ transform: `translateX(-${Number(activeWallet)*100}%)` }">
        <div v-for="(wallet, i) in wallets" :key="i"
             class="relative min-w-full p-4 bg-gray-900/30 backdrop-blur-lg rounded-xl flex justify-between items-center overflow-hidden shadow-lg">
          
          <!-- Shimmer overlay -->
          <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-white/20 to-white/10 opacity-20 animate-shimmer rounded-xl"></div>
          
          <div class="flex items-center gap-4 relative z-10">
            <component :is="wallet.icon" class="w-6 h-6 text-white/70" />
            <div>
              <p class="text-xs text-white/60">{{ wallet.name }}</p>
              <p class="text-2xl font-bold">
                {{ formatMoney(wallet.balance) }}
                <span class="text-sm text-white/50">{{ wallet.currency }}</span>
              </p>
            </div>
          </div>
          <div class="flex flex-col items-end relative z-10">
            <ChevronRight class="w-5 h-5 text-white/50 mb-1" />
            <p class="text-[10px] text-white/50">Swipe →</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- TOTAL BALANCE -->
  <div class="mb-4 px-2">
    <div class="flex items-center justify-between text-xs text-white/60 mb-1">
      <span>Total Balance</span>
      <button @click="showBalance = !showBalance">
        <component :is="showBalance ? Eye : EyeOff" class="w-4 h-4" />
      </button>
    </div>
    <h2 class="text-3xl font-bold">
      {{ formatMoney(totalBalance) }} <span class="text-sm text-white/60">USD</span>
    </h2>
    <p class="text-xs text-green-400 mt-1">+1.8% today · +5.2% this month</p>
  </div>

  <!-- PRIMARY ACTIONS -->
  <div class="grid grid-cols-4 gap-3 mb-4 text-[11px] text-center">
    <router-link to="/banking/deposit" class="action primary">
      <ArrowDownLeft class="w-5 h-5 text-white" /> Deposit
    </router-link>
    <router-link to="/banking/withdraw" class="action primary">
      <ArrowUpRight class="w-5 h-5 text-white" /> Withdraw
    </router-link>
    <router-link to="/banking/transfer" class="action primary">
      <Send class="w-5 h-5 text-white" /> Transfer
    </router-link>
    <!-- <router-link to="/banking/pay" class="action primary">
      <Wallet class="w-5 h-5 text-white" /> Pay
    </router-link> -->
  </div>

  <!-- SECONDARY ACTIONS -->
  <div class="grid grid-cols-4 gap-3 mb-6 text-[11px] text-center">
    <router-link to="/banking/cards" class="action">
      <CreditCard class="w-5 h-5 text-green-400" /> Cards
    </router-link>
    <!-- <router-link to="/wallet/savings" class="action">
      <PiggyBank class="w-5 h-5 text-green-400" /> Savings
    </router-link> -->
    <!-- <router-link to="/wallet/stats" class="action">
      <PieChart class="w-5 h-5 text-green-400" /> Stats
    </router-link> -->
    <!-- <router-link to="/wallet/settings" class="action">
      <ShieldCheck class="w-5 h-5 text-green-400" /> Security
    </router-link> -->
  </div>

  <!-- RECENT TRANSACTIONS -->
  <div class="mb-8">
    <div class="flex items-center justify-between mb-2">
      <h3 class="font-semibold text-sm">Recent Transactions</h3>
      <router-link to="/banking/transaction-history" class="text-xs text-green-400">View all</router-link>
    </div>
    <div class="space-y-3">
      <div v-for="(tx, i) in transactions" :key="i"
           class="flex items-center justify-between bg-white/5 rounded-lg px-3 py-2">
        <div class="flex items-center gap-3">
          <component :is="tx.icon" class="w-5 h-5 text-white/70" />
          <div>
            <p class="text-xs font-medium">{{ tx.label }}</p>
            <p class="text-[10px] text-white/50">{{ tx.date }}</p>
          </div>
        </div>
        <p class="text-xs font-semibold" :class="tx.type === 'credit' ? 'text-green-400' : 'text-red-400'">
          {{ tx.type === 'credit' ? '+' : '-' }}{{ tx.amount }}
        </p>
      </div>
    </div>
  </div>

  <!-- SAVINGS / GOALS -->
  <div class="mb-8">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold text-sm">Savings & Goals</h3>
      <!-- <Link href="/banking/goals" class="text-xs text-green-400">View all</Link> -->
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
      <div v-for="(goal, i) in goals" :key="i"
           class="bg-gray-800/40 backdrop-blur-md rounded-xl p-3 flex flex-col relative overflow-hidden">
        <span :class="goal.color + ' absolute w-16 h-16 rounded-full -top-4 -left-4 animate-pulse'"></span>
        <p class="text-[10px] text-white/70">{{ goal.title }}</p>
        <p class="font-semibold text-white">{{ goal.amount }}</p>
        <div class="h-1 w-full bg-white/10 rounded-full mt-2">
          <div class="h-full bg-green-400 rounded-full" :style="{ width: goal.progress + '%' }"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- SECURITY STATUS -->
  <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 rounded-xl p-3 mb-3">
    <ShieldCheck class="text-green-400 w-5 h-5" />
    <div>
      <p class="text-xs font-medium">Wallet Secured</p>
      <p class="text-[10px] text-white/60">KYC verified · Last login today</p>
    </div>
  </div>

</section>
<FooterLink />
</template>

<style scoped>
.action {
  display: flex;
  flex-direction: column;
  gap: 4px;
  align-items: center;
  padding: 10px 0;
  border-radius: 12px;
  background: rgba(255,255,255,0.05);
  transition: all 0.2s;
}
.action:hover { background: rgba(34,197,94,0.1); }
.action svg { width: 18px; height: 18px; }
.action.primary {
  background: radial-gradient(circle at top, #22c55e, #0f2f1c);
  color: white;
}

/* WALLET CARD ANIMATIONS */
@keyframes shimmer { 0% { transform: translateX(-100%) } 100% { transform: translateX(100%) } }
.animate-shimmer { animation: shimmer 2.5s linear infinite; background-size: 200% 100%; }

@keyframes float { 0%, 100% { transform: translateY(0) } 50% { transform: translateY(-10px) } }
.animate-float { animation: float 4s ease-in-out infinite }
.animate-float-slow { animation: float 7s ease-in-out infinite }
</style>