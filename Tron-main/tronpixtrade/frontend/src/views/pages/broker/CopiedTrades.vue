<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { User, XCircle } from 'lucide-vue-next'

const trades = ref([])

const loadTrades = async () => {
  try {
    const res = await copiedTradeService.getCopiedTrades()
    trades.value = res || []
  } catch (err) {
    console.error('Failed loading copied trades', err)
  }
}

/* -------------------------
DERIVED
------------------------- */
const openTrades = computed(() =>
  trades.value.filter(t => t.status === 'open')
)

/* -------------------------
ACTIONS
------------------------- */
function closeTrade(trade) {
  // call API to close
  copiedTradeService.closeTrade(trade.id, { exit_price: trade.userEntry, pnl: trade.pnl }).then(() => {
    trades.value = trades.value.filter(t => t.id !== trade.id)
  }).catch((err) => {
    console.error('Failed to close trade', err)
  })
}

onMounted(loadTrades)
</script>

<template>
  <section class="bg-black min-h-screen text-white">
    <div class="max-w-sm mx-auto px-4 pb-16">

      <!-- HEADER -->
      <div class="py-4">
        <h1 class="text-lg font-bold">Copied Trades</h1>
        <p class="text-xs text-white/60">
          Live positions copied from traders
        </p>
      </div>

      <!-- POSITIONS -->
      <div class="space-y-3">
        <div
          v-for="trade in openTrades"
          :key="trade.id"
          class="bg-[#0b0f14] border border-white/10 rounded-xl p-3"
        >

          <!-- TOP ROW -->
          <div class="flex justify-between items-center mb-2">
            <div class="flex items-center gap-2">
              <User class="w-4 h-4 text-white/50" />
              <p class="font-semibold text-sm">
                {{ trade.copy.traderName }}
              </p>
            </div>

            <button
              class="text-red-400 text-xs flex items-center gap-1"
              @click="closeTrade(trade)"
            >
              <XCircle class="w-4 h-4" /> Close
            </button>
          </div>

          <!-- SYMBOL -->
          <div class="flex justify-between items-center mb-2">
            <p class="font-medium">{{ trade.symbol }}</p>

            <span
              class="text-xs px-2 py-0.5 rounded"
              :class="trade.side === 'LONG'
                ? 'bg-green-500/20 text-green-400'
                : 'bg-red-500/20 text-red-400'"
            >
              {{ trade.side }} {{ trade.leverage }}x
            </span>
          </div>

          <!-- ENTRIES -->
          <div class="grid grid-cols-2 text-xs mb-2">
            <div>
              <p class="text-white/50">Trader Entry</p>
              <p>{{ trade.traderEntry }}</p>
            </div>

            <div class="text-right">
              <p class="text-white/50">Your Entry</p>
              <p>{{ trade.userEntry }}</p>
            </div>
          </div>

          <!-- SIZE + PNL -->
          <div class="grid grid-cols-2 text-xs">
            <div>
              <p class="text-white/50">Position Size</p>
              <p>{{ trade.size }} USDT</p>
            </div>

            <div class="text-right">
              <p class="text-white/50">PnL</p>
              <p
                :class="trade.pnl >= 0
                  ? 'text-green-400'
                  : 'text-red-400'"
              >
                {{ trade.pnl }} USDT
              </p>
            </div>
          </div>

        </div>
      </div>

      <!-- EMPTY -->
      <div
        v-if="openTrades.length === 0"
        class="text-center text-white/50 text-sm mt-12"
      >
        No active copied trades
      </div>

    </div>
  </section>
</template>