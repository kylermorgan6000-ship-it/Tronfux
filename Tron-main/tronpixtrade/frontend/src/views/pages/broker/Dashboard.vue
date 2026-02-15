<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import MarketTabs from '@/components/market/MarketTabs.vue'
import MarketRow from '@/components/market/MarketRow.vue'
import { useMarketData } from '@/composables/useMarketData' 
import AppLogo from '@/components/AppLogo.vue';
import FooterLink from '@/components/FooterLink.vue';
import {
  User,
  ScanLine,
  Headphones,
  MessageCircle,
  Eye,
  EyeOff,
  Wallet,
  Repeat,
  TrendingUp,
  Zap,
  Coins,
  BarChart2,
  Globe,
  Target,
  Flame
} from 'lucide-vue-next'
import { fetchWalletsList, fetchPendingWithdrawals } from '@/lib/api'

// ------------------------- MARKET STATE -------------------------
const marketType = ref('spot')
const category = ref('favorites')
const limit = ref(10)

// ------------------------- MARKET DATA -------------------------
const { data: assets, loading: isLoading } = useMarketData(
  computed(() => marketType.value),
  computed(() => category.value)
)

const featuredTradingAssets = computed(() => assets.value.slice(0, 6))
const limitedAssets = computed(() => assets.value.slice(0, limit.value))

// ------------------------- BALANCE -------------------------
const totalBalance = ref(0)
const balanceLoading = ref(true)
const showBalance = ref(true) // toggle for eye open/closed

const fetchBalance = async () => {
  balanceLoading.value = true
  try {
    const wallets = await fetchWalletsList()
    totalBalance.value = wallets.reduce((sum: number, w: any) => sum + Number(w.balance), 0)
  } catch {
    totalBalance.value = 0
  } finally {
    balanceLoading.value = false
  }
}

// ------------------------- NEWS -------------------------
const news = ref([])
const newsLoading = ref(true)

const fetchNews = async () => {
  try {
    const res = await fetch('https://api.coingecko.com/api/v3/news')
    const data = await res.json()
    news.value = data.data.slice(0, 10).map((n) => ({
      title: n.title,
      source: n.site,
      url: n.url
    }))
  } catch {
    news.value = [
      { title: 'Bitcoin volatility spikes amid market uncertainty' },
      { title: 'Ethereum gas fees drop to multi-month lows' }
    ]
  } finally {
    newsLoading.value = false
  }
}

// ------------------------- HEADER MODALS -------------------------
const showNotifications = ref(false)
const showMessages = ref(false)
const pendingTransaction = ref(null)
const showPendingModal = ref(false)
const showSupport = ref(false)

const closeAllModals = () => {
  showNotifications.value = false
  showMessages.value = false
  showSupport.value = false
}

// ------------------------- OUTSIDE CLICK MODAL CLOSE -------------------------
const handleClickOutside = (e: MouseEvent) => {
  const target = e.target as HTMLElement | null
  if (!target || !(target instanceof Element)) return

  const modal = target.closest('.modal')
  const cursor = target.closest('.cursor-pointer')

  if (!modal && !cursor) closeAllModals()
}

// ------------------------- PENDING TRANSACTIONS -------------------------
const fetchPendingTransactions = async () => {
  try {
    const withdrawals = await fetchPendingWithdrawals()
    const pending = withdrawals.find((w: any) => w.status === 'pending')
    if (pending) {
      pendingTransaction.value = pending
      showPendingModal.value = true
    }
  } catch (err) {
    console.error('Failed to load pending transactions', err)
  }
}

// ------------------------- LIFECYCLE -------------------------
onMounted(() => {
  fetchBalance()
  fetchNews()
  fetchPendingTransactions()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
<section class="bg-black min-h-screen text-white">
  <div class="max-w-sm mx-auto px-4 pb-10">

    <!-- HEADER -->
    <div class="flex items-center justify-between py-4">
      <div class="flex gap-4">
        <User class="icon-white cursor-pointer" @click="showNotifications = !showNotifications" />
        <ScanLine class="icon-white" />
      </div>

      <div class="flex text-xs overflow-hidden">
        <AppLogo />
      </div>

      <div class="flex gap-4">
        <Headphones class="icon-white cursor-pointer" @click="showSupport = !showSupport"/>
        <div class="relative">
          <MessageCircle class="icon-white cursor-pointer" @click="showMessages = !showMessages" />
          <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-green-400"></span>
        </div>
      </div>
    </div>

    <!-- HEADER MODALS -->
    <div v-if="showNotifications" class="modal top-14 right-0">
      <p class="p-4 text-black bg-white rounded shadow-lg">Notifications content here</p>
    </div>

    <div v-if="showMessages" class="modal top-14 right-0">
      <p class="p-4 text-black bg-white rounded shadow-lg">Messages content here</p>
    </div>

    <div v-if="showSupport" class="modal top-14 right-0">
      <p class="p-4 text-black bg-white rounded shadow-lg">Support chat or contact info here</p>
    </div>

    <!-- TOTAL ASSET -->
<div class="mb-6">
  <div class="flex items-center gap-1 text-white/60 text-xs mb-1">
    <span>Total Asset</span>
    <span class="cursor-pointer" @click="showBalance = !showBalance">
      <component :is="showBalance ? Eye : EyeOff" class="w-3 h-3 text-white" />
    </span>
  </div>

  <div class="flex items-center justify-between">
    <h2 class="text-2xl font-bold">
      {{ balanceLoading ? '...' : showBalance ? totalBalance.toFixed(2) : '••••' }}
      <span class="text-sm text-white/60">USD</span>
    </h2>
<router-link :to="{ name: 'broker-wallet' }" class="px-4 py-1.5 rounded-full text-sm bg-gradient-to-r from-black to-green-600">
      Deposit
    </router-link>
  </div>
</div>

<!-- HEADER MODALS -->
<div v-if="showNotifications" class="modal top-14 right-0">
  <p class="p-4 text-black bg-white rounded shadow-lg">Notifications content here</p>
</div>

<div v-if="showMessages" class="modal top-14 right-0">
  <p class="p-4 text-black bg-white rounded shadow-lg">Messages content here</p>
</div>

<div v-if="showSupport" class="modal top-14 right-0">
  <p class="p-4 text-black bg-white rounded shadow-lg">Support chat or contact info here</p>
</div>

<!-- FEATURE LINKS -->
<div class="grid grid-cols-4 gap-y-6 text-center text-[11px] mb-6">
    <router-link to="/broker/wallet" class="feature-item primary">
      <div class="feature-circle primary"><Wallet class="feature-icon primary" /></div>
      My Wallet
    </router-link>

    <router-link to="/broker/withdraw" class="feature-item primary">
      <div class="feature-circle primary"><Repeat class="feature-icon primary" /></div>
      Withdraw
    </router-link>

    <router-link to="/broker/trade-view" class="feature-item primary">
      <div class="feature-circle primary"><TrendingUp class="feature-icon primary" /></div>
      Trade
    </router-link>

    <router-link to="/broker/markets" class="feature-item primary">
      <div class="feature-circle primary"><Zap class="feature-icon primary" /></div>
      Quick Buy
    </router-link>

    <router-link to="/broker/spot" class="feature-item">
      <div class="feature-circle"><Coins class="feature-icon" /></div>
      Spot
    </router-link>

    <router-link to="/broker/futures" class="feature-item">
      <div class="feature-circle"><BarChart2 class="feature-icon" /></div>
      Futures
    </router-link>

    <router-link to="/broker/forex" class="feature-item">
      <div class="feature-circle"><Globe class="feature-icon" /></div>
      Forex
    </router-link>

    <router-link to="/broker/copy-trading" class="feature-item">
      <div class="feature-circle"><Target class="feature-icon" /></div>
      Copy Trade
    </router-link>
  </div>
  
    <!-- FEATURED INVESTMENT -->
    <div class="text-white py-3 flex items-center gap-2 font-bold">
      <Flame class="w-5 h-5 fire-icon" /> Featured Investment
    </div>
    <div class="bg-gradient-to-br from-black to-green-900 rounded-xl p-4 mb-4 border border-green-500/20 shadow-[0_0_25px_rgba(34,197,94,0.15)]">
      <p class="text-xs text-white/70 mb-2">Investment Plan</p>
      <div class="flex items-center justify-between mb-2">
        <div>
          <p class="text-2xl font-bold">36.13%</p>
          <p class="text-xs text-white/60">Estimated APY (USD)</p>
        </div>
          <router-link to="/broker/product" class="">
        <button class="px-4 py-1.5 rounded-lg text-xs bg-gradient-to-r from-black to-green-600 shadow-[0_0_12px_rgba(34,197,94,0.4)]">Buy Plan</button>
        </router-link>
      </div>
      <div class="flex gap-2 mb-2">
        <span class="px-2 py-0.5 rounded bg-green-500/10 text-green-400 text-[10px]">Variable Yield</span>
        <span class="px-2 py-0.5 rounded bg-white/10 text-white/70 text-[10px]">7-Day Lock</span>
      </div>
      <p class="text-[11px] text-white/50 leading-relaxed">Invest from 999.33 USD and earn daily rewards during the lock period.</p>
    </div>

    <!-- MARKET -->
    <div class="py-3">
      <h2 class="flex items-center gap-2 font-bold mb-2"><Flame class="w-5 h-5 fire-icon" /> Others are Trading</h2>
      <div class="grid grid-cols-[1.6fr_1fr_0.8fr_1fr] text-[11px] text-white/50 px-1 mb-2">
        <span>Pair</span>
        <span class="text-right">Price</span>
        <span class="text-right">24h %</span>
        <span class="text-right">Volume</span>
      </div>
      <div v-if="isLoading" class="text-xs text-white/40">Loading market…</div>
      <div v-else class="space-y-2">
        <MarketRow v-for="asset in featuredTradingAssets" :key="asset.symbol" :asset="asset" />
      </div>
    </div>

    <!-- NEWS -->
    <div class="relative overflow-hidden bg-white/5 rounded-lg py-2 px-2 mb-4">
      <div class="flex gap-10 whitespace-nowrap animate-marquee">
        <div v-for="(item, i) in news" :key="i" class="text-xs text-white/70">
          🔊 {{ item.title }}
        </div>
      </div>
    </div>

    <!-- MARKET TABS -->
    <MarketTabs
      :marketType="marketType"
      :category="category"
      @changeMarket="marketType = $event"
      @changeCategory="category = $event"
      @changeLimit="limit = $event"
    />

    <div v-if="isLoading" class="text-xs text-white/40 mt-2">Loading market…</div>
    <div v-else class="space-y-1 mt-2">
      <MarketRow v-for="asset in limitedAssets" :key="asset.symbol" :asset="asset" />
    </div>

    <!-- PENDING TRANSACTION MODAL -->
    <div v-if="showPendingModal && pendingTransaction" class="fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
      <div class="bg-gray-900 rounded-2xl p-6 max-w-sm w-full border border-green-500/30">
        <h2 class="text-lg font-bold mb-2">Pending Withdrawal</h2>
        <p class="text-sm text-white/70 mb-4">Amount: ${{ pendingTransaction.amount }}</p>
        <p class="text-xs text-white/60 mb-4">Status: <strong>{{ pendingTransaction.status }}</strong></p>
        <p class="text-xs text-white/50 mb-4">Your withdrawal is awaiting security verification. Please complete the authentication codes to proceed.</p>
        <router-link to="/broker/auth-codes" class="w-full bg-green-500 text-black p-2 rounded-lg text-sm font-semibold inline-block text-center mb-2">
          Go to Auth Codes
        </router-link>
        <button @click="showPendingModal = false" class="w-full bg-white/10 text-white p-2 rounded-lg text-sm">
          Dismiss
        </button>
      </div>
    </div>

  </div>
  <FooterLink />
</section>
</template>

<style scoped>
.icon-white { width: 20px; height: 20px; color: #fff; }
.fire-icon { color: #f97316; }

@keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
.animate-marquee { animation: marquee 35s linear infinite; }

.feature-item { display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer; }
.feature-circle { width: 48px; height: 48px; border-radius: 9999px; border: 1px solid rgba(34,197,94,0.3); display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.35); transition: all 0.2s ease; }
.feature-circle.primary { background: radial-gradient(circle at top, #22c55e, #0f2f1c); border: none; box-shadow: 0 0 16px rgba(34,197,94,0.6); }
.feature-icon { width: 20px; height: 20px; color: #22c55e; }
.feature-icon.primary { color: white; }
.feature-item:active .feature-circle { transform: scale(0.94); }

/* MODAL */
.modal { position: absolute; width: 220px; z-index: 50; background: white; color: black; border-radius: 0.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
</style>