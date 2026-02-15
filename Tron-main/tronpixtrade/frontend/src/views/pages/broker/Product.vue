<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Search, Flame, Filter, ArrowLeft, X, ChevronDown, Loader2 } from 'lucide-vue-next'
import { useRouter } from 'vue-router'
import { fetchBrokerPlans, fetchUser, createInvestment } from '@/lib/api'

const router = useRouter()

/* UI STATE */
const search = ref('')
const activeFilter = ref('all')
const showFilterSheet = ref(false)
const showSortMenu = ref(false)
const sortOrder = ref('apy-desc')

const showProductModal = ref(false)
const selectedProduct = ref(null)

/* PRODUCTS */
const products = ref([])
const userBalance = ref(0)
const error = ref('')

const loadProducts = async () => {
  const staticList = [
    { id: 1, name: 'Investment Plan', apy: 36.13, min: '999.33 USD', duration: '7-Day Lock', type: 'variable', featured: true, description: 'High-yield variable investment with daily rewards.' },
    { id: 2, name: 'Stable Yield', apy: 18.5, min: '200 USD', duration: '30-Day Lock', type: 'fixed', featured: false, description: 'Low-risk fixed yield plan for stable returns.' },
    { id: 3, name: 'Growth Plan', apy: 42.9, min: '1,500 USD', duration: '14-Day Lock', type: 'variable', featured: false, description: 'Aggressive growth strategy with higher volatility.' },
  ]
  try {
    const data = await fetchBrokerPlans()
    products.value = data.length ? data : staticList
  } catch (e) {
    console.warn('Broker plans API not available, using static list', e)
    products.value = staticList
  }
}

const loadUserBalance = async () => {
  try {
    const user = await fetchUser()
    const wallet = user?.wallets?.[0] || user?.wallet
    userBalance.value = wallet?.balance || 0
  } catch (e) {
    console.warn('Could not load user balance', e)
    userBalance.value = 0
  }
}

onMounted(async () => {
  await loadProducts()
  await loadUserBalance()
})

/* INVESTMENT UI */
const showInvestSheet = ref(false)
const showConfirmSheet = ref(false)
const showSuccessScreen = ref(false)
const investAmount = ref(null)
const investLoading = ref(false)
const insufficientFunds = ref(false)

const expectedReturn = computed(() => {
  if (!investAmount.value || !selectedProduct.value?.apy) return '0.00'
  return ((investAmount.value * selectedProduct.value.apy) / 100).toFixed(2)
})

/* FILTER + SORT */
const filteredProducts = computed(() => {
  let list = products.value.filter(p => {
    if (activeFilter.value !== 'all' && p.type !== activeFilter.value) return false
    if (search.value && !p.name.toLowerCase().includes(search.value.toLowerCase())) return false
    return true
  })
  return list.sort((a, b) =>
    sortOrder.value === 'apy-desc' ? b.apy - a.apy : a.apy - b.apy
  )
})

function openProduct(item: any) {
  selectedProduct.value = item
  showProductModal.value = true
  error.value = ''
}

const validateAndInvest = () => {
  if (!investAmount.value || investAmount.value <= 0) {
    error.value = 'Please enter a valid amount'
    return
  }
  
  const minAmount = parseFloat(selectedProduct.value?.min?.replace(/[^0-9.]/g, '') || '0')
  if (investAmount.value < minAmount) {
    error.value = `Minimum investment is ${selectedProduct.value?.min}`
    return
  }
  
  if (investAmount.value > userBalance.value) {
    insufficientFunds.value = true
    error.value = 'Insufficient balance'
    return
  }
  
  insufficientFunds.value = false
  error.value = ''
  showConfirmSheet.value = true
}

const submitInvestment = async () => {
  if (!selectedProduct.value || !investAmount.value) return
  
  investLoading.value = true
  error.value = ''
  
  try {
    await createInvestment({
      plan_id: selectedProduct.value.id,
      amount: investAmount.value,
      currency: 'USD'
    })
    
    showSuccessScreen.value = true
    showConfirmSheet.value = false
    userBalance.value -= investAmount.value
  } catch (err: any) {
    error.value = err.response?.data?.message || err.message || 'Investment failed'
    insufficientFunds.value = err.response?.status === 422
  } finally {
    investLoading.value = false
  }
}

function resetAfterSuccess() {
  showSuccessScreen.value = false
  showConfirmSheet.value = false
  showInvestSheet.value = false
  showProductModal.value = false
  investAmount.value = null
  error.value = ''
  insufficientFunds.value = false
}

function goBack() {
  window.history.back()
}
</script>

<template>
  <section class="bg-black min-h-screen text-white pt-[56px]">
    <div class="max-w-sm mx-auto px-4 pb-24">

      <!-- HEADER -->
      <div class="fixed top-0 left-0 right-0 z-30 bg-black border-b border-white/10 px-3">
        <div class="flex items-center justify-between py-4">
          <div class="flex items-center gap-3">
            <ArrowLeft class="w-5 h-5 text-white/70" @click="goBack()" />
            <h1 class="text-[16px] font-semibold">Investments</h1>
          </div>

          <div class="flex items-center gap-4">
            <!-- SORT -->
            <div class="relative">
              <button
                class="flex items-center gap-1 text-xs text-white/70"
                @click="showSortMenu = !showSortMenu"
              >
                APY
                <ChevronDown class="w-4 h-4" />
              </button>

              <div
                v-if="showSortMenu"
                class="absolute right-0 mt-2 w-36 bg-black border border-white/10 rounded-lg text-[13px] overflow-hidden z-40"
              >
                <button class="sort-option" @click="sortOrder = 'apy-desc'; showSortMenu = false">
                  APY High → Low
                </button>
                <button class="sort-option" @click="sortOrder = 'apy-asc'; showSortMenu = false">
                  APY Low → High
                </button>
              </div>
            </div>

            <Filter class="w-5 h-5 text-white/60" />
          </div>
        </div>
      </div>

      <!-- SEARCH -->
      <div class="mt-4">
        <div class="relative">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/40" />
          <input
            v-model="search"
            placeholder="Search investment products"
            class="w-full pl-9 pr-3 py-2 rounded-xl bg-white/5 text-[13px] outline-none"
          />
        </div>
      </div>

      <!-- FILTER CHIPS -->
      <div class="flex gap-2 mt-3">
        <button class="filter-chip" :class="{ active: activeFilter === 'all' }" @click="activeFilter = 'all'">All</button>
        <button class="filter-chip" :class="{ active: activeFilter === 'fixed' }" @click="activeFilter = 'fixed'">Fixed</button>
        <button class="filter-chip" :class="{ active: activeFilter === 'variable' }" @click="activeFilter = 'variable'">Variable</button>
      </div>

<!-- FEATURED -->
<div class="mt-6">
  <h2 class="flex items-center gap-2 font-bold mb-3">
    <Flame class="w-5 h-5 text-green-400" />
    Featured Investment
  </h2>

  <div
    v-for="item in filteredProducts.filter(p => p.featured)"
    :key="item.id"
    class="bg-gradient-to-br from-black to-green-900
           rounded-xl p-4 mb-4
           border border-green-500/20
           shadow-[0_0_25px_rgba(34,197,94,0.15)]
           active:scale-[0.98] transition"
    @click="openProduct(item)"
  >
    <!-- NAME -->
    <p class="text-xs text-white/70 mb-2">
      {{ item.name }}
    </p>

    <!-- APY + CTA -->
    <div class="flex items-center justify-between mb-2">
      <div>
        <p class="text-2xl font-bold">{{ item.apy }}%</p>
        <p class="text-xs text-white/60">Estimated APY (USD)</p>
      </div>

      <button
        class="px-4 py-1.5 rounded-lg text-xs
               bg-gradient-to-r from-black to-green-600
               shadow-[0_0_12px_rgba(34,197,94,0.4)]"
      >
        Buy Plan
      </button>
    </div>

    <!-- TAGS -->
    <div class="flex gap-2 mb-2">
      <span
        class="px-2 py-0.5 rounded text-[10px]"
        :class="item.type === 'variable'
          ? 'bg-green-500/10 text-green-400'
          : 'bg-white/10 text-white/70'"
      >
        {{ item.type === 'variable' ? 'Variable Yield' : 'Fixed Yield' }}
      </span>

      <span class="px-2 py-0.5 rounded bg-white/10 text-white/70 text-[10px]">
        {{ item.duration }}
      </span>
    </div>

    <!-- FOOTNOTE -->
    <p class="text-[11px] text-white/50 leading-relaxed">
      Invest from {{ item.min }} and earn daily rewards during the lock period.
    </p>
  </div>
</div>

<!-- ALL PRODUCTS -->
<div class="mt-6 space-y-4">
  <div
    v-for="item in filteredProducts"
    :key="item.id"
    class="bg-gradient-to-br from-black to-green-900
           rounded-xl p-4
           border border-green-500/20
           shadow-[0_0_18px_rgba(34,197,94,0.1)]
           active:scale-[0.98] transition"
    @click="openProduct(item)"
  >
    <!-- SAME CONTENT AS FEATURED -->
    <p class="text-xs text-white/70 mb-2">
      {{ item.name }}
    </p>

    <div class="flex items-center justify-between mb-2">
      <div>
        <p class="text-2xl font-bold">{{ item.apy }}%</p>
        <p class="text-xs text-white/60">Estimated APY (USD)</p>
      </div>

      <button
        class="px-4 py-1.5 rounded-lg text-xs
               bg-green-500/20 text-green-400"
      >
        Buy Plan
      </button>
    </div>

    <div class="flex gap-2 mb-2">
      <span
        class="px-2 py-0.5 rounded text-[10px]"
        :class="item.type === 'variable'
          ? 'bg-green-500/10 text-green-400'
          : 'bg-white/10 text-white/70'"
      >
        {{ item.type === 'variable' ? 'Variable Yield' : 'Fixed Yield' }}
      </span>

      <span class="px-2 py-0.5 rounded bg-white/10 text-white/70 text-[10px]">
        {{ item.duration }}
      </span>
    </div>

    <p class="text-[11px] text-white/50">
      Invest from {{ item.min }}
    </p>
  </div>
</div>
</div>

<!-- PRODUCT MODAL -->
<div
  v-if="showProductModal && selectedProduct"
  class="fixed inset-0 bg-black/70 z-50 flex justify-end"
>
  <div
    class="w-full max-w-sm bg-black h-full p-5 animate-slide-in"
  >
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-5">
      <p class="font-semibold text-lg">
        {{ selectedProduct.name }}
      </p>
      <X class="w-5 h-5 text-white/70" @click="showProductModal = false" />
    </div>
    
    <!-- INFO / CAUTION BOX -->
<div
  class="mb-5 rounded-xl border border-green-500/30
         bg-green-500/10 px-4 py-3
         backdrop-blur-sm"
>
  <p class="text-[12px] text-green-300 leading-relaxed">
    • This investment plan uses a variable yield model based on market performance.
  </p>
  <p class="text-[12px] text-green-300 leading-relaxed">
    • Returns are calculated daily and compounded over the lock period.
  </p>
  <p class="text-[12px] text-green-300 leading-relaxed">
    • Funds cannot be withdrawn until the lock duration ends.
  </p>
  <p class="text-[12px] text-green-300 leading-relaxed">
    • Estimated returns may change depending on the invested amount and yield rate.
  </p>
</div>

    <!-- APY -->
    <div class="mb-4">
      <p class="text-4xl font-bold text-green-400">
        {{ selectedProduct.apy }}%
      </p>
      <p class="text-xs text-white/60">
        Estimated Annual Percentage Yield
      </p>
    </div>

    <!-- INFO GRID -->
    <div class="grid grid-cols-2 gap-3 text-sm mb-5">
      <div class="bg-white/5 p-3 rounded-lg">
        <p class="text-xs text-white/50">Minimum</p>
        <p class="font-semibold">{{ selectedProduct.min }}</p>
      </div>

      <div class="bg-white/5 p-3 rounded-lg">
        <p class="text-xs text-white/50">Duration</p>
        <p class="font-semibold">{{ selectedProduct.duration }}</p>
      </div>

      <div class="bg-white/5 p-3 rounded-lg">
        <p class="text-xs text-white/50">Yield Type</p>
        <p class="font-semibold capitalize">{{ selectedProduct.type }}</p>
      </div>

      <div class="bg-white/5 p-3 rounded-lg">
        <p class="text-xs text-white/50">Rewards</p>
        <p class="font-semibold">Daily</p>
      </div>
    </div>

    <!-- DESCRIPTION -->
    <p class="text-[13px] text-white/60 leading-relaxed mb-6">
      {{ selectedProduct.description }}
    </p>

    <!-- CTA -->
    <button
  @click="showInvestSheet = true"
  class="w-full py-3 rounded-xl
         bg-gradient-to-r from-black to-green-600
         text-white font-semibold
         shadow-[0_0_18px_rgba(34,197,94,0.4)]"
>
  Buy Investment Plan
</button>

    <p class="text-[11px] text-white/40 mt-3 text-center">
      Funds will be locked for the selected duration.
    </p>
  </div>
</div> 

<!-- INVESTMENT INPUT SHEET -->
<div
  v-if="showInvestSheet && selectedProduct"
  class="fixed inset-0 z-[60] bg-black/70 flex items-end"
>
  <div class="w-full bg-black rounded-t-2xl p-5 animate-slide-up">

    <!-- HEADER -->
    <h3 class="font-semibold text-lg mb-4">
      Investment Details
    </h3>

    <!-- PLAN SUMMARY (2 COLUMN DATA) -->
    <div class="bg-white/5 rounded-xl p-4 mb-5 text-sm">
      <div class="grid grid-cols-2 gap-y-3">

        <p class="text-white/50">Plan</p>
        <p class="font-semibold text-right">
          {{ selectedProduct.name }}
        </p>

        <p class="text-white/50">Yield Type</p>
        <p class="capitalize text-right">
          {{ selectedProduct.type }}
        </p>

        <p class="text-white/50">Duration</p>
        <p class="text-right">
          {{ selectedProduct.duration }}
        </p>

        <p class="text-white/50">Minimum</p>
        <p class="text-right font-semibold">
          {{ selectedProduct.min }}
        </p>

        <p class="text-white/50">Expected Returns</p>
        <p class="text-right text-green-400 font-semibold">
          +{{ expectedReturn }} USD
        </p>

      </div>
    </div>

    <!-- AMOUNT INPUT (FULL WIDTH BELOW) -->
    <div class="mb-5">
      <p class="text-xs text-white/50 mb-2">
        Investment Amount
      </p>

      <input
        v-model.number="investAmount"
        type="number"
        :placeholder="`Minimum ${selectedProduct.min}`"
        class="w-full px-4 py-3 rounded-xl
               bg-white/5 outline-none
               text-lg font-semibold"
      />
    </div>

    <!-- ERROR MESSAGE -->
    <div v-if="error" class="mt-3 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
      {{ error }}
    </div>

    <!-- CTA -->
    <button
      :disabled="!investAmount || insufficientFunds"
      @click="validateAndInvest"
      class="w-full py-3 rounded-xl
             bg-gradient-to-r from-black to-green-600
             text-white font-semibold
             disabled:opacity-40 mt-3"
    >
      {{ insufficientFunds ? 'Insufficient Balance' : 'Continue' }}
    </button>

    <p class="text-[11px] text-white/40 mt-3 text-center">
      Amount must meet the minimum investment requirement.
    </p>

  </div>
</div>

<!-- CONFIRMATION SHEET -->
<div
  v-if="showConfirmSheet && selectedProduct"
  class="fixed inset-0 z-[70] bg-black/80 flex items-end"
>
  <div class="w-full bg-black rounded-t-2xl p-5 animate-slide-up">

    <!-- HEADER -->
    <h3 class="font-semibold text-lg mb-4">
      Confirm Investment
    </h3>

    <!-- SUMMARY CARD -->
    <div class="bg-white/5 rounded-xl p-4 mb-5 text-sm">
      <div class="grid grid-cols-2 gap-y-3">

        <p class="text-white/50">Investment Plan</p>
        <p class="font-semibold text-right">
          {{ selectedProduct.name }}
        </p>

        <p class="text-white/50">Amount</p>
        <p class="font-semibold text-right">
          {{ investAmount }} USD
        </p>

        <p class="text-white/50">Yield Type</p>
        <p class="capitalize text-right">
          {{ selectedProduct.type }}
        </p>

        <p class="text-white/50">Duration</p>
        <p class="text-right">
          {{ selectedProduct.duration }}
        </p>

        <p class="text-white/50">Expected Returns</p>
        <p class="text-right text-green-400 font-semibold">
          +{{ expectedReturn }} USD
        </p>

      </div>
    </div>

    <!-- ERROR MESSAGE -->
    <div v-if="error" class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
      {{ error }}
    </div>

    <!-- CTA -->
    <button
      :disabled="investLoading"
      @click="submitInvestment"
      class="w-full py-3 rounded-xl
             bg-gradient-to-r from-black to-green-600
             text-white font-semibold
             shadow-[0_0_18px_rgba(34,197,94,0.4)]
             disabled:opacity-40 flex items-center justify-center gap-2"
    >
      <Loader2 v-if="investLoading" class="w-4 h-4 animate-spin" />
      <span>{{ investLoading ? 'Processing' : 'Confirm Investment' }}</span>
    </button>

    <p class="text-[11px] text-white/40 mt-3 text-center">
      This action cannot be undone once confirmed.
    </p>

  </div>
</div>
<!-- SUCCESS SCREEN -->
<div
  v-if="showSuccessScreen"
  class="fixed inset-0 z-[80] bg-black flex flex-col items-center justify-center px-6"
>
  <div
    class="w-20 h-20 rounded-full bg-green-500/20
           flex items-center justify-center mb-6"
  >
    <Flame class="w-10 h-10 text-green-400" />
  </div>

  <h2 class="text-xl font-bold mb-2">
    Investment Successful
  </h2>

  <p class="text-sm text-white/60 text-center mb-6">
    Your investment has been locked successfully and will begin earning rewards.
  </p>

  <button
    @click="resetAfterSuccess"
    class="w-full py-3 rounded-xl bg-green-500/20 text-green-400 font-semibold hover:bg-green-500/30 transition"
  >
    Continue Trading →
  </button>
</div>

  </section>
</template>

<style scoped>
.filter-chip {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 11px;
  background: rgba(255,255,255,0.08);
}

.filter-chip.active {
  background: rgba(34,197,94,0.25);
  color: #22c55e;
  font-weight: 600;
}

.sort-option {
  width: 100%;
  padding: 10px 12px;
  text-align: left;
}

.animate-slide-in {
  animation: slideIn 0.25s ease-out;
}

@keyframes slideIn {
  from { transform: translateX(100%); }
  to { transform: translateX(0); }
}

.animate-slide-up {
  animation: slideUp 0.25s ease-out;
}

@keyframes slideUp {
  from { transform: translateY(100%); }
  to { transform: translateY(0); }
}
</style>