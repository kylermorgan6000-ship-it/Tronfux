<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

import { copiedTradeService } from '@/services/copiedTradeService'

const history = ref([])

const loadHistory = async () => {
  try {
    const res: any = await copiedTradeService.getTradeHistory()
    // If paginated, use res.data
    history.value = Array.isArray(res) ? res : res.data || []
  } catch (err) {
    console.error('Failed loading copy trade history', err)
  }
}

/* -------------------------
DERIVED
------------------------- */
const totalPnL = computed(() =>
  history.value.reduce((sum, t) => sum + t.pnl, 0)
)

onMounted(loadHistory)
</script>

<template>
  <section class="bg-black min-h-screen text-white">
    <div class="max-w-sm mx-auto px-4 pb-16">

      <!-- HEADER -->
      <div class="py-4">
        <h1 class="text-lg font-bold">Copy Trade History</h1>
        <p class="text-xs text-white/60">
          Closed positions from copied traders
        </p>
      </div>

      <!-- SUMMARY -->
      <div class="bg-[#0b0f14] border border-white/10 rounded-xl p-3 mb-4">
        <p class="text-xs text-white/50 mb-1">Total Realized PnL</p>
        <p
          class="text-lg font-semibold"
          :class="totalPnL >= 0
            ? 'text-green-400'
            : 'text-red-400'"
        >
          {{ totalPnL.toFixed(2) }} USDT
        </p>
      </div>

      <!-- HISTORY LIST -->
      <div class="space-y-3">
        <div
          v-for="trade in history"
          :key="trade.id"
          class="bg-[#0b0f14] border border-white/10 rounded-xl p-3"
        >

          <!-- TRADER -->
          <div class="flex justify-between items-center mb-2">
            <div class="flex items-center gap-2">
              <User class="w-4 h-4 text-white/50" />
              <p class="font-semibold text-sm">
                {{ trade.copy.traderName }}
              </p>
            </div>

            <span class="text-[10px] text-white/40">
              {{ trade.closedAt }}
            </span>
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
          <div class="grid grid-cols-3 text-xs mb-2">
            <div>
              <p class="text-white/50">Trader</p>
              <p>{{ trade.traderEntry }}</p>
            </div>

            <div class="text-center">
              <p class="text-white/50">You</p>
              <p>{{ trade.userEntry }}</p>
            </div>

            <div class="text-right">
              <p class="text-white/50">Exit</p>
              <p>{{ trade.exitPrice }}</p>
            </div>
          </div>

          <!-- SIZE + PNL -->
          <div class="grid grid-cols-2 text-xs">
            <div>
              <p class="text-white/50">Size</p>
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
        v-if="history.length === 0"
        class="text-center text-white/50 text-sm mt-12"
      >
        No copy trade history yet
      </div>

    </div>
  </section>
</template>