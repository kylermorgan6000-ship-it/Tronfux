<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import {
  ArrowDownLeft,
  ArrowUpRight,
  ArrowLeftRight
} from 'lucide-vue-next'
import { fetchTransactions } from '@/lib/api'

const activeTab = ref('all')
const selectedTx = ref(null)
const transactions = ref([])
const loading = ref(false)
const error = ref('')

/* -------------------------
FETCH TRANSACTIONS
------------------------- */
const loadTransactions = async (type?: string) => {
  loading.value = true
  error.value = ''
  try {
    const params: any = {}
    if (type && type !== 'all') params.type = type

    const data = await fetchTransactions(params)
    transactions.value = data.map((tx: any) => ({
      id: tx.id,
      type: tx.type,
      asset: tx.asset || tx.currency,
      amount: Number(tx.amount),
      status: tx.status,
      timestamp: tx.created_at,
      network: tx.network ?? undefined,
      address: tx.address ?? undefined,
      fee: tx.fee ?? undefined,
    }))
  } catch (err: any) {
    console.error(err)
    error.value = 'Failed to load transactions'
  } finally {
    loading.value = false
  }
}

// Initial fetch
onMounted(() => loadTransactions(activeTab.value))

// Refetch whenever tab changes
watch(activeTab, (newTab) => loadTransactions(newTab))

/* -------------------------
COMPUTED
------------------------- */
const filteredTxs = computed(() => {
  if (activeTab.value === 'all') return transactions.value
  return transactions.value.filter(t => t.type === activeTab.value)
})

const iconMap = {
  deposit: ArrowDownLeft,
  withdraw: ArrowUpRight,
  transfer: ArrowLeftRight,
}
</script>

<template>
  <section class="bg-black min-h-screen text-white pt-[56px]">
    <div class="max-w-sm mx-auto px-4 pb-10">

      <!-- HEADER -->
      <div
        class="fixed top-0 left-0 right-0 z-30 bg-black border-b border-white/10 px-3"
      >
        <div class="flex items-center gap-3 py-4">
          <ArrowLeft class="w-5 h-5 text-white/70" />
          <h1 class="text-[16px] font-semibold">Transaction History</h1>
        </div>
      </div>

      <!-- TABS -->
      <div class="flex mt-[10px] gap-2 mb-4"> 
 
        <button
          v-for="t in ['all','deposit','withdraw','transfer']"
          :key="t"
          @click="activeTab = t"
          class="px-3 py-1.5 rounded-full text-[12px]"
          :class="activeTab === t
            ? 'bg-green-500/20 text-green-400'
            : 'bg-white/5 text-white/60'"
        >
          {{ t === 'all' ? 'All' : t.charAt(0).toUpperCase() + t.slice(1) }}
        </button>
      </div>
      
           <!-- NOTICE -->
<p class="text-[11px] mt-[15px] text-white/40 mb-3 leading-relaxed">
  Transactions may take time to update depending on network conditions.
  Only records from the last 90 days are shown here. Older transactions
  will be archived and can be accessed via account export.
</p>

      <!-- LIST -->
      <div class="space-y-3">
        <div
          v-for="tx in filteredTxs"
          :key="tx.id"
          @click="selectedTx = tx"
          class="flex items-center justify-between  rounded-xl  py-3"
        >
          <div class="flex items-center gap-3">
            <div
              class="w-9 h-9 rounded-full flex items-center justify-center"
              :class="tx.type === 'deposit'
                ? 'bg-green-500/20'
                : tx.type === 'withdraw'
                ? 'bg-red-500/20'
                : 'bg-blue-500/20'"
            >
              <component :is="iconMap[tx.type]" class="w-4 h-4" />
            </div>

            <div>
              <p class="font-medium text-[13px]">
                {{ tx.type.charAt(0).toUpperCase() + tx.type.slice(1) }}
              </p>
              <p class="text-[11px] text-white/40">
                {{ tx.timestamp }}
              </p>
            </div>
          </div>

          <div class="text-right">
            <p
              class="font-medium text-[13px]"
              :class="tx.type === 'deposit'
                ? 'text-green-400'
                : 'text-red-400'"
            >
              {{ tx.type === 'deposit' ? '+' : '-' }}{{ tx.amount }} {{ tx.asset }}
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

      <!-- EMPTY -->
      <div
        v-if="filteredTxs.length === 0"
        class="text-center text-white/40 text-[13px] mt-10"
      >
        No transactions found
      </div>

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
            <span
              v-else
              class="text-yellow-400 text-xs font-semibold"
            >
              Pending
            </span>
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
            <span>{{ selectedTx.amount }} {{ selectedTx.asset }}</span>
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

          <div v-if="selectedTx.fee" class="flex justify-between">
            <span class="text-white/50">Fee</span>
            <span>{{ selectedTx.fee }} USDT</span>
          </div>

          <div class="flex justify-between">
            <span class="text-white/50">Time</span>
            <span>{{ selectedTx.timestamp }}</span>
          </div>
        </div>

      </div>
    </div>
  </section>
</template>