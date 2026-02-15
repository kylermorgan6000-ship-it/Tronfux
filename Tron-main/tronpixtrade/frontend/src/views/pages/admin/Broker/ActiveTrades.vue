<script setup lang="ts">
import AdminLayout from '../AdminLayout.vue'
import { ref, computed, onMounted } from 'vue'
import {
  Activity,
  Eye,
  X,
  RefreshCw,
  Loader2,
  CheckCircle,
  XCircle,
} from 'lucide-vue-next'
import { fetchCopiedTrades, closeCopiedTrade } from '@/lib/api'

// ------------------- STATE -------------------
const loading = ref(true)                // Page-level loading
const actionLoading = ref(false)        // Button/action loading
const showModal = ref(false)            // Modal visibility
const selectedTrade = ref<any | null>(null)
const searchQuery = ref('')
const trades = ref<any[]>([])           // Active trades list

// ------------------- FETCH TRADES -------------------
const loadTrades = async () => {
  loading.value = true
  try {
    const data = await fetchCopiedTrades()
    trades.value = Array.isArray(data) ? data : (data?.data ?? [])
  } catch (e) {
    console.error('Failed to load active trades', e)
    trades.value = []
  } finally {
    loading.value = false
  }
}

// Fetch on mount
onMounted(() => { void loadTrades() })

// ------------------- COMPUTED -------------------
const filteredTrades = computed(() =>
  trades.value.filter(t =>
    (t.user?.toLowerCase().includes(searchQuery.value.toLowerCase()) ?? false) ||
    (t.pair?.toLowerCase().includes(searchQuery.value.toLowerCase()) ?? false)
  )
)

// ------------------- HELPERS -------------------
const openModal = (trade: any) => {
  selectedTrade.value = trade
  showModal.value = true
}

const closeModal = () => {
  selectedTrade.value = null
  showModal.value = false
}

const closeTrade = async (trade: any) => {
  actionLoading.value = true
  try {
    await closeCopiedTrade(trade.id)
    trade.status = 'Closed'
    // Refresh list to reflect the latest
    await loadTrades()
  } catch (e) {
    console.error('Close trade failed', e)
    trade.status = 'Closed' // fallback local update
  } finally {
    actionLoading.value = false
  }
}
</script>

<template>
  <AdminLayout>
    <section class="p-3 text-white space-y-4 min-h-screen">

      <!-- HEADER -->
      <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold flex items-center gap-2">
          <Activity class="w-5 h-5 text-green-400" /> Active Trades
        </h1>
      </div>

      <!-- SEARCH -->
      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by user or pair..."
          class="w-full bg-white/5 text-white/80 p-2 rounded-lg pl-10 outline-none"
        />
        <Activity class="absolute top-2.5 left-3 w-5 h-5 text-white/50" />
      </div>

      <!-- LOADER -->
      <div v-if="loading" class="flex justify-center items-center h-40">
        <Loader2 class="w-8 h-8 animate-spin text-green-400" />
      </div>

      <!-- EMPTY STATE -->
      <div v-else-if="filteredTrades.length === 0" class="text-center text-white/50 py-10">
        No active trades found.
      </div>

      <!-- TRADES LIST -->
      <div v-else class="space-y-3">
        <div
          v-for="trade in filteredTrades"
          :key="trade.id"
          class="bg-white/5 p-3 rounded-xl border border-white/10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3"
        >
          <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
            <p class="font-semibold">{{ trade.user }}</p>
            <p class="text-xs text-white/50">{{ trade.pair }}</p>
            <span
              class="text-[10px] px-2 py-0.5 rounded-full"
              :class="trade.status==='Open' ? 'bg-green-500 text-black' : 'bg-red-500 text-white'"
            >
              {{ trade.status }}
            </span>
          </div>

          <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
            <p class="text-sm text-white/60">Amount: ${{ trade.amount }}</p>
            <p class="text-sm text-white/60">Leverage: {{ trade.leverage }}</p>
            <p class="text-sm font-semibold" :class="trade.pl >= 0 ? 'text-green-400' : 'text-red-400'">
              P/L: ${{ trade.pl }}
            </p>

            <div class="flex gap-2">
              <button @click="openModal(trade)" class="p-2 rounded-lg hover:bg-white/5">
                <Eye class="w-4 h-4 text-white/60" />
              </button>

              <button
                @click="closeTrade(trade)"
                class="px-2 py-1 rounded-lg bg-red-500 text-white text-xs"
              >
                <span v-if="!actionLoading">Close</span>
                <Loader2 v-else class="w-5 h-5 animate-spin" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- MODAL -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/50 z-[9999] flex items-end sm:items-center justify-center px-3"
      >
        <div class="bg-[#0B0E11] w-full sm:max-w-md rounded-t-2xl sm:rounded-xl p-4 space-y-3 relative">
          <button class="absolute top-3 right-3" @click="showModal=false">
            <X class="w-5 h-5" />
          </button>

          <h2 class="text-lg font-semibold">Trade Details</h2>

          <div class="space-y-1 text-xs text-white/60">
            <p><span class="font-semibold">User:</span> {{ selectedTrade.user }}</p>
            <p><span class="font-semibold">Pair:</span> {{ selectedTrade.pair }}</p>
            <p><span class="font-semibold">Amount:</span> ${{ selectedTrade.amount }}</p>
            <p><span class="font-semibold">Leverage:</span> {{ selectedTrade.leverage }}</p>
            <p><span class="font-semibold">P/L:</span>
              <span :class="selectedTrade.pl >= 0 ? 'text-green-400' : 'text-red-400'">
                ${{ selectedTrade.pl }}
              </span>
            </p>
            <p><span class="font-semibold">Status:</span> {{ selectedTrade.status }}</p>
            <p><span class="font-semibold">Opened At:</span> {{ selectedTrade.openedAt }}</p>
          </div>

        </div>
      </div>

    </section>
  </AdminLayout>
</template>