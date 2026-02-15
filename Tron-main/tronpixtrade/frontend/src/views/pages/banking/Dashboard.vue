<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import {
  fetchUser,
  fetchBankAccounts,
  fetchTransactions,
  fetchPendingWithdrawals,
} from '@/lib/api'
import { fetchCardsList as fetchCards } from '@/lib/api'
import FooterLink from '@/components/banking/FooterLink.vue'
import {
  Eye,
  EyeOff,
  Wallet,
  ArrowDownLeft,
  ArrowUpRight,
  ArrowLeftRight,
  Send,
  CreditCard,
  PiggyBank,
  ShieldCheck,
  Bell,
  ChevronRight,
  Plus,
  X,
  Check,
} from 'lucide-vue-next'

// ------------------------- STATE -------------------------
const user = ref({ name: '', profile: null })
const accounts = ref([])
const activeAccount = ref(0)
const showBalance = ref(true)
const accountsLoading = ref(false)
const transactions = ref([])
const transactionsLoading = ref(false)
const cards = ref([])
const cardsLoading = ref(false)
const pendingTransaction = ref(null)
const showPendingModal = ref(false)
let sliderInterval

const today = new Date()
const greeting = computed(() => {
  const hour = today.getHours()
  if (hour < 12) return `Good morning, ${user.value.name}`
  if (hour < 18) return `Good afternoon, ${user.value.name}`
  return `Good evening, ${user.value.name}`
})

const totalNetWorth = computed(() =>
  accounts.value.reduce((sum, acc) => sum + acc.balance, 0)
)

const formatMoney = (v) =>
  showBalance.value ? v.toLocaleString() : '•••••'

const placeholderAccount = {
  name: 'Account',
  balance: 0,
  currency: 'USD',
  status: 'Active',
  minDeposit: 0,
  maxDeposit: 0,
  lastLogin: 'N/A',
  icon: Wallet,
}

const sliderAccounts = computed(() =>
  accounts.value.length ? accounts.value : [placeholderAccount]
)
const activeIndex = computed(() =>
  accounts.value.length ? Number(activeAccount.value ?? 0) : 0
)

// ------------------------- FETCH DATA -------------------------
const fetchData = async () => {
  accountsLoading.value = true
  transactionsLoading.value = true
  cardsLoading.value = true

  try {
    // 1️⃣ First check user
    const uRes = await fetchUser()
    user.value.name = uRes.name || uRes.data?.name || ''

    // 2️⃣ Only if user exists, load rest
    const [aRes, tRes, cRes, pRes] = await Promise.all([
      fetchBankAccounts(),
      fetchTransactions({ per_page: 6 }),
      fetchCards(),
      fetchPendingWithdrawals(),
    ])

    accounts.value = (aRes?.data || aRes || []).map((a) => ({
      name: a.account_name || a.account_type || 'Account',
      balance: Number(a.balance) || 0,
      currency: a.currency || 'USD',
      status: a.status === 'locked' ? 'Locked' : 'Active',
      minDeposit: a.min_deposit || 0,
      maxDeposit: a.max_deposit || 0,
      lastLogin: a.updated_at ? new Date(a.updated_at).toLocaleDateString() : 'N/A',
      icon: Wallet,
    }))

    transactions.value = (tRes?.data || tRes || []).map((tx) => ({
      type: tx.type,
      label: tx.description || (tx.type === 'credit' ? 'Credit' : 'Debit'),
      amount: Number(tx.amount),
      date: tx.created_at ? new Date(tx.created_at).toLocaleDateString() : 'N/A',
      icon: tx.type === 'credit' ? ArrowDownLeft : ArrowUpRight,
      asset: tx.asset || tx.currency || 'USD',
      status: tx.status || 'completed',
      id: tx.id,
      network: tx.network,
      address: tx.address,
      fee: tx.fee || 0,
      timestamp: tx.created_at ? new Date(tx.created_at).toLocaleTimeString() : 'N/A',
    }))

    cards.value = (cRes?.data || cRes || []).map((c) => ({
      type: c.card_type || 'Card',
      balance: Number(c.balance) || 0,
      number: c.card_number || c.number || '**** **** **** ****',
    }))

    if (pRes?.status === 200) {
      const pending = (pRes?.data || []).find((w) => w.status === 'pending')
      if (pending) {
        pendingTransaction.value = pending
        showPendingModal.value = true
      }
    }

  } catch (e: any) {
    if (e?.response?.status === 401) {
      console.log('Not authenticated → redirecting to login')
      window.location.href = '/login'
    } else {
      console.error('Dashboard load failed', e)
    }
  } finally {
    accountsLoading.value = false
    transactionsLoading.value = false
    cardsLoading.value = false
  }

  sliderInterval = window.setInterval(() => {
    activeAccount.value = accounts.value.length
      ? (activeAccount.value + 1) % accounts.value.length
      : 0
  }, 4000)
}

onMounted(fetchData)
onUnmounted(() => {
  if (sliderInterval) clearInterval(sliderInterval)
})
</script>


<template>
  <!-- HEADER -->
<div class="flex items-center justify-between py-4 px-4 bg-black fixed top-0 left-0 w-full z-50 shadow-[0_1px_0_rgba(255,255,255,0.06)]">
  <div class="flex items-center gap-3">
    <!-- Profile Picture -->
    <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-700 flex items-center justify-center">
      <img 
        v-if="user.profile" 
        :src="user.profile" 
        alt="Profile" 
        class="w-full h-full object-cover"
      />
      <img 
        v-else 
        src="https://i.pravatar.cc/150?u=default" 
        alt="Default Profile" 
        class="w-full h-full object-cover"
      />
    </div>

    <!-- Greeting -->
    <div class="text-sm">
      <p class="text-white/80 font-medium">{{ greeting }}</p>
    </div>
  </div>

  <!-- Notification Icon -->
  <Bell class="w-5 h-5 text-white/70 hover:text-green-400 transition-colors duration-200" />
</div>

<!-- MAIN CONTENT -->
<section class="min-h-screen bg-black text-white mt-[83px] px-4 pb-24">

  <!-- ACCOUNT CARDS -->
  <div class="relative mb-6 mt-20">
    <div class="overflow-hidden rounded-xl relative">
      
      <!-- Floating Orbs -->
      <div class="absolute w-16 h-16 bg-green-500/20 rounded-full blur-3xl animate-float top-2 left-10"></div>
      <div class="absolute w-24 h-24 bg-blue-500/10 rounded-full blur-3xl animate-float-slow bottom-2 right-8"></div>

       <div class="flex transition-transform duration-500"
         :style="{ transform: `translateX(-${Number(activeIndex) * 100}%)` }">
         <div v-for="(acc, i) in sliderAccounts" :key="i"
             class="relative min-w-full p-4 bg-gray-900/30 backdrop-blur-lg rounded-xl flex justify-between items-center overflow-hidden shadow-lg">
          
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
            <p class="text-[10px] text-white/50">Swipe →</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- TOTAL NET WORTH -->
  <div class="mb-4 px-2">
    <div class="flex items-center justify-between text-xs text-white/60 mb-1">
      <span>Total Net Worth</span>
      <button @click="showBalance = !showBalance">
        <component :is="showBalance ? Eye : EyeOff" class="w-4 h-4" />
      </button>
    </div>
    <h2 class="text-3xl font-bold">
      {{ formatMoney(totalNetWorth) }} <span class="text-sm text-white/60">USD</span>
    </h2>
    <p class="text-xs text-green-400 mt-1">+2.4% today · +8.1% this month</p>
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
  <router-link to="/banking/cards" class="action primary">
      <CreditCard class="w-5 h-5 text-white" /> Cards
    </router-link> 
  </div>

  <!-- SECONDARY ACTIONS -->
<div class="grid grid-cols-4 gap-3 mb-6 text-[11px] text-center">
   <!--   <Link href="/banking/cards" class="action">
      <CreditCard class="w-5 h-5 text-green-400" /> Cards
    </Link>
     <Link href="/banking/savings" class="action">
      <PiggyBank class="w-5 h-5 text-green-400" /> Savings
    </Link>
 
    <Link href="/banking/investments" class="action primary">
      <Wallet class="w-5 h-5 text-green-400" /> Investments
    </Link> -->
    <!-- <Link href="/banking/statements" class="action">
      <ChevronRight class="w-5 h-5 text-green-400" /> Statements
    </Link> -->
  </div>

  <!-- TRANSACTION STATS CARD -->
  <div class="bg-gray-800/50 rounded-xl p-4 mb-6" v-if="sliderAccounts[activeIndex]">
    <h3 class="text-sm font-semibold mb-2">Account Summary</h3>
    <div class="flex flex-col gap-2 text-[11px] text-white/70">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <Wallet class="w-4 h-4 text-white/50" />
          <p>Amount Deposited</p>
        </div>
        <p class="font-bold">{{ formatMoney(sliderAccounts[activeIndex]?.balance) }} {{ sliderAccounts[activeIndex]?.currency }}</p>
      </div>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <ArrowDownLeft class="w-4 h-4 text-white/50" />
          <p>Min Deposit</p>
        </div>
        <p class="font-bold">{{ sliderAccounts[activeIndex]?.minDeposit }} {{ sliderAccounts[activeIndex]?.currency }}</p>
      </div>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <ArrowUpRight class="w-4 h-4 text-white/50" />
          <p>Max Deposit</p>
        </div>
        <p class="font-bold">{{ sliderAccounts[activeIndex]?.maxDeposit }} {{ sliderAccounts[activeIndex]?.currency }}</p>
      </div>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <Bell class="w-4 h-4 text-white/50" />
          <p>Last Login</p>
        </div>
        <p class="font-bold">{{ sliderAccounts[activeIndex]?.lastLogin }}</p>
      </div>
    </div>
  </div>

 <!-- RECENT TRANSACTIONS -->
<div class="mb-8">
  <div class="flex items-center justify-between mb-2">
    <h3 class="font-semibold text-sm">Recent Transactions</h3>
    <router-link to="/banking/transaction-history" class="text-xs text-green-400">View all</router-link>
  </div>

  <div class="space-y-3">
    <div
      v-for="(tx, i) in transactions"
      :key="i"
      @click="selectedTx = tx"
      class="flex items-center justify-between bg-white/5 rounded-xl px-3 py-2 cursor-pointer hover:bg-white/10 transition"
    >
      <div class="flex items-center gap-3">
        <!-- TYPE ICON -->
        <div
          class="w-9 h-9 rounded-full flex items-center justify-center"
          :class="tx.type === 'credit'
            ? 'bg-green-500/20'
            : tx.type === 'debit'
            ? 'bg-red-500/20'
            : 'bg-blue-500/20'"
        >
          <component
            :is="tx.type === 'credit' ? ArrowDownLeft : tx.type === 'debit' ? ArrowUpRight : ArrowLeftRight"
            class="w-4 h-4"
          />
        </div>

        <!-- TX LABEL & DATE -->
        <div>
          <p class="font-medium text-[13px]">
            {{ tx.label }}
          </p>
          <p class="text-[11px] text-white/40">{{ tx.date }}</p>
        </div>
      </div>

      <!-- TX AMOUNT & STATUS -->
      <div class="text-right flex flex-col items-end">
        <p
          class="font-medium text-[13px]"
          :class="tx.type === 'credit' ? 'text-green-400' : 'text-red-400'"
        >
          {{ tx.type === 'credit' ? '+' : '-' }}{{ tx.amount }} {{ tx.asset || 'USD' }}
        </p>

        <span
          class="text-[10px] px-2 py-0.5 rounded-full"
          :class="tx.status === 'completed'
            ? 'bg-green-500/20 text-green-400'
            : tx.status === 'pending'
            ? 'bg-yellow-500/20 text-yellow-400'
            : 'bg-red-500/20 text-red-400'"
        >
          {{ tx.status }}
        </span>
      </div>

      <ChevronRight class="w-4 h-4 text-white/30 ml-2" />
    </div>
  </div>

  <!-- EMPTY STATE -->
  <div v-if="transactions.length === 0" class="text-center text-white/40 text-[13px] mt-10">
    No transactions found
  </div>

  <!-- TRANSACTION DETAIL SHEET -->
  <div
    v-if="selectedTx"
    class="fixed inset-0 z-50 bg-black/70 flex items-end"
    @click.self="selectedTx = null"
  >
    <div class="bg-[#0b0b0b] w-full rounded-t-2xl p-4">

      <!-- HEADER -->
      <div class="flex justify-between items-center mb-3">
        <h2 class="font-semibold text-[15px]">Transaction Details</h2>
        <X class="w-5 h-5 text-white/60" @click="selectedTx = null" />
      </div>

      <!-- STATUS ICON -->
      <div class="flex justify-center mb-4">
        <div
          class="w-14 h-14 rounded-full flex items-center justify-center"
          :class="selectedTx.status === 'completed'
            ? 'bg-green-500/20'
            : selectedTx.status === 'failed'
            ? 'bg-red-500/20'
            : 'bg-yellow-500/20'"
        >
          <Check
            v-if="selectedTx.status === 'completed'"
            class="w-7 h-7 text-green-400"
          />
          <X
            v-else-if="selectedTx.status === 'failed'"
            class="w-7 h-7 text-red-400"
          />
          <span v-else class="text-yellow-400 text-xs font-semibold">Pending</span>
        </div>
      </div>

      <!-- DETAILS -->
      <div class="space-y-3 text-[13px]">
        <div class="flex justify-between">
          <span class="text-white/50">Type</span>
          <span class="capitalize">{{ selectedTx.type }}</span>
        </div>

        <div class="flex justify-between">
          <span class="text-white/50">Amount</span>
          <span>{{ selectedTx.amount }} {{ selectedTx.asset || 'USD' }}</span>
        </div>

        <div class="flex justify-between">
          <span class="text-white/50">Status</span>
          <span class="capitalize">{{ selectedTx.status }}</span>
        </div>

        <div class="flex justify-between">
          <span class="text-white/50">Transaction ID</span>
          <span class="text-white/80">{{ selectedTx.id }}</span>
        </div>

        <div v-if="selectedTx.network" class="flex justify-between">
          <span class="text-white/50">Network</span>
          <span>{{ selectedTx.network }}</span>
        </div>

        <div v-if="selectedTx.address" class="flex justify-between">
          <span class="text-white/50">Address</span>
          <span>{{ selectedTx.address }}</span>
        </div>

        <div v-if="selectedTx.fee" class="flex justify-between">
          <span class="text-white/50">Fee</span>
          <span>{{ selectedTx.fee }} {{ selectedTx.asset || 'USD' }}</span>
        </div>

        <div class="flex justify-between">
          <span class="text-white/50">Time</span>
          <span>{{ selectedTx.timestamp }}</span>
        </div>
      </div>
    </div>
  </div>
</div>

  <!-- CARDS SECTION -->
  <div class="mb-8">
    <h3 class="font-semibold text-sm mb-3">Cards</h3>
    <div class="flex justify-center items-center min-h-[120px] bg-gray-900 rounded-xl p-4">
      <template v-if="cards.length">
        <div v-for="(card, i) in cards" :key="i" class="bg-gradient-to-r from-green-600 to-green-800 w-48 h-28 rounded-xl flex flex-col justify-between p-4 mr-3">
          <p class="text-xs">{{ card.type }}</p>
          <p class="text-lg font-bold">{{ formatMoney(card.balance) }}</p>
          <p class="text-[10px]">{{ card.number }}</p>
        </div>
      </template>
      <template v-else>
        <div class="flex flex-col items-center justify-center text-center text-white/50">
          <div class="w-12 h-12 rounded-full border-2 border-dashed border-white/50 flex items-center justify-center mb-2">
            <Plus class="w-5 h-5" />
          </div>
          <p class="text-[11px]">You have no active cards</p>
          <router-link to="/banking/card-application" class="mt-2 action primary">
            Request Card
          </router-link>
        </div>
      </template>
    </div>
  </div>
     
    <!-- MINI LOAN/STATS SECTION -->
<div class="mb-8">
  <!-- Section Header -->
  <div class="flex items-center justify-between mb-3">
    <h3 class="font-semibold text-sm">Mini Loans & Stats</h3>
    <!-- <Link href="/banking/loans" class="text-xs text-green-400">View all</Link> -->
  </div>

  <!-- Stats Grid -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
    <!-- Loan Taken -->
    <div class="bg-gray-800/40 backdrop-blur-md rounded-xl p-3 flex items-center gap-3 relative overflow-hidden">
      <span class="absolute w-16 h-16 bg-green-500/20 rounded-full -top-4 -left-4 animate-pulse"></span>
      <CreditCard class="w-5 h-5 text-green-400 z-10" />
      <div class="flex flex-col text-[11px] z-10">
        <span class="text-white/70">Loan Taken</span>
        <span class="font-semibold text-white">$2,500</span>
        <span class="text-[10px] text-white/50 mt-1">Total loans you have taken this year</span>
      </div>
    </div>

    <!-- Loan Paid -->
    <div class="bg-gray-800/40 backdrop-blur-md rounded-xl p-3 flex items-center gap-3 relative overflow-hidden">
      <span class="absolute w-16 h-16 bg-blue-500/20 rounded-full -top-4 -right-4 animate-pulse"></span>
      <PiggyBank class="w-5 h-5 text-blue-400 z-10" />
      <div class="flex flex-col text-[11px] z-10">
        <span class="text-white/70">Loan Paid</span>
        <span class="font-semibold text-white">$1,800</span>
        <span class="text-[10px] text-white/50 mt-1">Amount you have repaid so far</span>
      </div>
    </div>

    <!-- Outstanding Balance -->
    <div class="bg-gray-800/40 backdrop-blur-md rounded-xl p-3 flex items-center gap-3 relative overflow-hidden">
      <span class="absolute w-16 h-16 bg-yellow-500/20 rounded-full -top-2 -left-3 animate-pulse"></span>
      <Wallet class="w-5 h-5 text-yellow-400 z-10" />
      <div class="flex flex-col text-[11px] z-10">
        <span class="text-white/70">Outstanding</span>
        <span class="font-semibold text-white">$700</span>
        <span class="text-[10px] text-white/50 mt-1">Remaining balance to be cleared</span>
      </div>
    </div>

    <!-- Interest Paid -->
    <div class="bg-gray-800/40 backdrop-blur-md rounded-xl p-3 flex items-center gap-3 relative overflow-hidden">
      <span class="absolute w-16 h-16 bg-red-500/20 rounded-full -top-2 -right-3 animate-pulse"></span>
      <ArrowUpRight class="w-5 h-5 text-red-400 z-10" />
      <div class="flex flex-col text-[11px] z-10">
        <span class="text-white/70">Interest Paid</span>
        <span class="font-semibold text-white">$150</span>
        <span class="text-[10px] text-white/50 mt-1">Total interest paid on all loans</span>
      </div>
    </div>

    <!-- Next Due Payment -->
    <div class="bg-gray-800/40 backdrop-blur-md rounded-xl p-3 flex items-center gap-3 relative overflow-hidden">
      <span class="absolute w-16 h-16 bg-purple-500/20 rounded-full -top-4 -left-4 animate-pulse"></span>
      <Send class="w-5 h-5 text-purple-400 z-10" />
      <div class="flex flex-col text-[11px] z-10">
        <span class="text-white/70">Next Due</span>
        <span class="font-semibold text-white">$300</span>
        <span class="text-[10px] text-white/50 mt-1">Amount due on your next installment</span>
      </div>
    </div>

    <!-- Overdue Amount -->
    <div class="bg-gray-800/40 backdrop-blur-md rounded-xl p-3 flex items-center gap-3 relative overflow-hidden">
      <span class="absolute w-16 h-16 bg-pink-500/20 rounded-full -top-4 -right-4 animate-pulse"></span>
      <ArrowDownLeft class="w-5 h-5 text-pink-400 z-10" />
      <div class="flex flex-col text-[11px] z-10">
        <span class="text-white/70">Overdue</span>
        <span class="font-semibold text-white">$50</span>
        <span class="text-[10px] text-white/50 mt-1">Amount overdue from past payments</span>
      </div>
    </div>

    <!-- Total Savings -->
    <div class="bg-gray-800/40 backdrop-blur-md rounded-xl p-3 flex items-center gap-3 relative overflow-hidden">
      <span class="absolute w-16 h-16 bg-cyan-500/20 rounded-full -top-2 -left-3 animate-pulse"></span>
      <PiggyBank class="w-5 h-5 text-cyan-400 z-10" />
      <div class="flex flex-col text-[11px] z-10">
        <span class="text-white/70">Total Savings</span>
        <span class="font-semibold text-white">$5,000</span>
        <span class="text-[10px] text-white/50 mt-1">All your savings across accounts</span>
      </div>
    </div>

    <!-- Total Loan Limit -->
    <div class="bg-gray-800/40 backdrop-blur-md rounded-xl p-3 flex items-center gap-3 relative overflow-hidden">
      <span class="absolute w-16 h-16 bg-orange-500/20 rounded-full -top-2 -right-3 animate-pulse"></span>
      <ShieldCheck class="w-5 h-5 text-orange-400 z-10" />
      <div class="flex flex-col text-[11px] z-10">
        <span class="text-white/70">Loan Limit</span>
        <span class="font-semibold text-white">$10,000</span>
        <span class="text-[10px] text-white/50 mt-1">Maximum allowable loan based on your profile</span>
      </div>
    </div>
  </div>
</div>
     
  <!-- SECURITY STATUS -->
  <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 rounded-xl p-3 mb-3">
    <ShieldCheck class="text-green-400 w-5 h-5" />
    <div>
      <p class="text-xs font-medium">Account Secured</p>
      <p class="text-[10px] text-white/60">KYC verified · Last login today</p>
    </div>
  </div>

  <!-- PENDING TRANSACTION MODAL -->
  <div v-if="showPendingModal && pendingTransaction" class="fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="bg-gray-900 rounded-2xl p-6 max-w-sm w-full border border-green-500/30">
      <h2 class="text-lg font-bold mb-2">Pending Withdrawal</h2>
      <p class="text-sm text-white/70 mb-4">Amount: ${{ pendingTransaction.amount }}</p>
      <p class="text-xs text-white/60 mb-4">Status: <strong>{{ pendingTransaction.status }}</strong></p>
      <p class="text-xs text-white/50 mb-4">Your withdrawal is awaiting security verification. Please complete the authentication codes to proceed.</p>
      <router-link to="/banking/auth-codes" class="w-full bg-green-500 text-black p-2 rounded-lg text-sm font-semibold inline-block text-center mb-2">
        Go to Auth Codes
      </router-link>
      <button @click="showPendingModal = false" class="w-full bg-white/10 text-white p-2 rounded-lg text-sm">
        Dismiss
      </button>
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
.action:hover {
  background: rgba(34,197,94,0.1);
}
.action svg {
  width: 18px;
  height: 18px;
}
.action.primary {
  background: radial-gradient(circle at top, #22c55e, #0f2f1c);
  color: white;
}
.no-scrollbar::-webkit-scrollbar {
  display: none;
}

/* -------------------------------
ANIMATIONS FOR ACCOUNT CARDS
-------------------------------- */
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
