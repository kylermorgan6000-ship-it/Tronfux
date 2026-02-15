<script setup lang="ts">
import { ref, computed } from 'vue'
import { Users, TrendingUp, Shield, PauseCircle, PlayCircle, XCircle } from 'lucide-vue-next'

/* -------------------------
MOCK DATA (API LATER)
------------------------- */
const copiedTraders = ref([
  {
    id: 1,
    name: 'AlphaWolf',
    roi: 124.5,
    pnl: 342.2,
    followers: 3421,
    status: 'active',
    config: {
      mode: 'fixed',
      fixedAmount: 500,
      maxPositions: 3,
      stopLoss: 30
    }
  },
  {
    id: 2,
    name: 'BTC_Machine',
    roi: 68.2,
    pnl: -42.5,
    followers: 1290,
    status: 'paused',
    config: {
      mode: 'ratio',
      ratio: 1,
      maxPositions: 2,
      stopLoss: 25
    }
  }
])

/* -------------------------
DERIVED STATS
------------------------- */
const totalPnL = computed(() =>
  copiedTraders.value.reduce((sum, t) => sum + t.pnl, 0)
)

const totalRiskCap = computed(() =>
  copiedTraders.value.reduce((sum, t) => {
    if (t.config.mode === 'fixed') {
      return sum + (t.config.fixedAmount || 0)
    }
    return sum
  }, 0)
)

/* -------------------------
ACTIONS
------------------------- */
function toggle(trader) {
  trader.status = trader.status === 'active' ? 'paused' : 'active'
}

function stop(trader) {
  copiedTraders.value = copiedTraders.value.filter(
    t => t.id !== trader.id
  )
}
</script>

<template>
  <section class="bg-black min-h-screen text-white">
    <div class="max-w-sm mx-auto px-4 pb-16">

      <!-- HEADER -->
      <div class="py-4">
        <h1 class="text-lg font-bold">Copy Dashboard</h1>
        <p class="text-xs text-white/60">
          Manage your copied traders
        </p>
      </div>

      <!-- STATS -->
      <div class="grid grid-cols-3 gap-2 mb-4 text-center">
        <div class="stat-card">
          <Users class="stat-icon" />
          <p class="stat-value">{{ copiedTraders.length }}</p>
          <p class="stat-label">Copied Traders</p>
        </div>

        <div class="stat-card">
          <TrendingUp class="stat-icon" />
          <p
            class="stat-value"
            :class="totalPnL >= 0 ? 'text-green-400' : 'text-red-400'"
          >
            {{ totalPnL.toFixed(2) }}
          </p>
          <p class="stat-label">Total PnL (USDT)</p>
        </div>

        <div class="stat-card">
          <Shield class="stat-icon" />
          <p class="stat-value">{{ totalRiskCap }}</p>
          <p class="stat-label">Risk Cap</p>
        </div>
      </div>

      <!-- ACTIVE COPIES -->
      <div class="space-y-3">
        <div
          v-for="trader in copiedTraders"
          :key="trader.id"
          class="bg-[#0b0f14] rounded-xl p-3 border border-white/10"
        >

          <!-- TOP -->
          <div class="flex justify-between items-center mb-2">
            <div>
              <p class="font-semibold text-sm">{{ trader.name }}</p>
              <p class="text-xs text-white/50">
                ROI {{ trader.roi }}% · {{ trader.followers }} followers
              </p>
            </div>

            <span
              class="text-xs px-2 py-0.5 rounded"
              :class="trader.status === 'active'
                ? 'bg-green-500/20 text-green-400'
                : 'bg-yellow-500/20 text-yellow-400'"
            >
              {{ trader.status }}
            </span>
          </div>

          <!-- METRICS -->
          <div class="grid grid-cols-2 text-xs mb-3">
            <div>
              <p class="text-white/50">Copy Mode</p>
              <p>
                {{ trader.config.mode === 'fixed'
                  ? trader.config.fixedAmount + ' USDT'
                  : trader.config.ratio + 'x ratio' }}
              </p>
            </div>

            <div class="text-right">
              <p class="text-white/50">PnL</p>
              <p
                :class="trader.pnl >= 0
                  ? 'text-green-400'
                  : 'text-red-400'"
              >
                {{ trader.pnl }} USDT
              </p>
            </div>
          </div>

          <!-- ACTIONS -->
          <div class="flex gap-2">
            <button
              class="flex-1 action-btn"
              @click="toggle(trader)"
            >
              <PauseCircle
                v-if="trader.status === 'active'"
                class="w-4 h-4"
              />
              <PlayCircle v-else class="w-4 h-4" />
              {{ trader.status === 'active' ? 'Pause' : 'Resume' }}
            </button>

            <button
              class="flex-1 action-btn danger"
              @click="stop(trader)"
            >
              <XCircle class="w-4 h-4" />
              Stop
            </button>
          </div>

        </div>
      </div>

      <!-- EMPTY STATE -->
      <div
        v-if="copiedTraders.length === 0"
        class="text-center text-white/50 text-sm mt-12"
      >
        You are not copying any traders yet.
      </div>

    </div>
  </section>
</template>

<style scoped>
.stat-card {
  background: #151a21;
  border-radius: 14px;
  padding: 10px 6px;
}

.stat-icon {
  width: 18px;
  height: 18px;
  margin: 0 auto 4px;
  color: #22c55e;
}

.stat-value {
  font-weight: bold;
  font-size: 14px;
}

.stat-label {
  font-size: 10px;
  color: rgba(255,255,255,0.5);
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  background: #151a21;
  border-radius: 10px;
  padding: 8px;
  font-size: 12px;
}

.action-btn.danger {
  color: #f87171;
}
</style>