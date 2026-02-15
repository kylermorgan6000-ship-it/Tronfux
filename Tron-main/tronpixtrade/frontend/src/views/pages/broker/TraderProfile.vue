<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  ArrowLeft,
  Copy,
  LineChart
} from 'lucide-vue-next'

import CopySettingsSheet from './CopySettingsSheet.vue'

const router = useRouter()

/* -------------------------
ROUTER
------------------------- */
const traderId = router.currentRoute.value.params.traderId || null

import { copyTradingService } from '@/services/copyTradingService'

const trader = ref({
  id: null,
  name: '',
  avatar: '',
  roi: 0,
  pnl: 0,
  winRate: 0,
  followers: 0,
  risk: 'Medium',
  maxDrawdown: 0,
  trades: 0,
})

const loadTrader = async () => {
  try {
    const id = traderId
    const res: any = await copyTradingService.getTraders('all', 50)
    const list = Array.isArray(res) ? res : res.data || []
    const found = list.find((t: any) => String(t.id) === String(id))
    if (found) trader.value = found
  } catch (err) {
    console.error('Failed loading trader profile', err)
  }
}

/* -------------------------
UI STATE
------------------------- */
const activeTab = ref('overview')
const showCopySheet = ref(false)

onMounted(loadTrader)
</script>

<template>
  <section class="bg-black min-h-screen text-white">

    <!-- HEADER -->
    <div class="fixed top-0 left-0 right-0 z-30 bg-black border-b border-white/10">
      <div class="max-w-sm mx-auto px-4 py-3 flex items-center gap-2">
        <ArrowLeft
          class="w-5 h-5 cursor-pointer"
          @click="router.push('/broker/copy-trading')"
        />
        <span class="text-[15px] font-semibold">Trader Profile</span>
      </div>
    </div>

    <div class="max-w-sm mx-auto px-4 pt-[60px] pb-[96px]">

      <!-- PROFILE -->
      <div class="flex items-center gap-4 mb-5">
        <img
          :src="trader?.avatar"
          class="w-14 h-14 rounded-full border border-white/10"
        />

        <div class="flex-1">
          <p class="font-semibold text-[16px]">{{ trader?.name ?? 'Unknown' }}</p>
          <p class="text-[11px] text-white/50">
            {{ (trader?.followers ?? 0).toLocaleString() }} copiers
          </p>
        </div>

        <span
          class="text-[11px] px-3 py-1 rounded-full"
          :class="
            trader?.risk === 'Low'
              ? 'bg-green-500/20 text-green-400'
              : trader?.risk === 'Medium'
              ? 'bg-yellow-500/20 text-yellow-400'
              : 'bg-red-500/20 text-red-400'
          "
        >
          {{ trader?.risk ?? 'Unknown' }} Risk
        </span>
      </div>

      <!-- STATS -->
      <div class="grid grid-cols-3 gap-2 mb-6">
        <div class="stat-box">
          <p class="label">ROI</p>
          <p class="value text-green-400">+{{ trader?.roi ?? 0 }}%</p>
        </div>

        <div class="stat-box">
          <p class="label">Win Rate</p>
          <p class="value">{{ trader?.winRate ?? 0 }}%</p>
        </div>

        <div class="stat-box">
          <p class="label">Trades</p>
          <p class="value">{{ trader?.trades ?? 0 }}</p>
        </div>
      </div>

      <!-- TABS -->
      <div class="flex border-b border-white/10 mb-4">
        <button
          class="tab-btn"
          :class="activeTab === 'overview' && 'active'"
          @click="activeTab = 'overview'"
        >
          Overview
        </button>
        <button
          class="tab-btn"
          :class="activeTab === 'performance' && 'active'"
          @click="activeTab = 'performance'"
        >
          Performance
        </button>
        <button
          class="tab-btn"
          :class="activeTab === 'history' && 'active'"
          @click="activeTab = 'history'"
        >
          History
        </button>
      </div>

      <!-- OVERVIEW -->
      <div v-if="activeTab === 'overview'" class="space-y-3 text-[12px]">
        <div class="info-row">
          <span>Total PnL</span>
          <span class="text-green-400">
            +${{ (trader?.pnl ?? 0).toLocaleString() }}
          </span>
        </div>

        <div class="info-row">
          <span>Max Drawdown</span>
          <span class="text-red-400">
            -{{ trader?.maxDrawdown ?? 0 }}%
          </span>
        </div>

        <div class="info-row">
          <span>Followers</span>
          <span>{{ (trader?.followers ?? 0).toLocaleString() }}</span>
        </div>
      </div>

      <!-- PERFORMANCE -->
      <div
        v-if="activeTab === 'performance'"
        class="h-48 flex items-center justify-center text-white/40"
      >
        <LineChart class="w-10 h-10" />
        <span class="ml-2">Performance chart coming soon</span>
      </div>

      <!-- HISTORY -->
      <div
        v-if="activeTab === 'history'"
        class="text-white/40 text-center py-10"
      >
        Trade history coming soon
      </div>

    </div>

    <!-- FLOATING COPY BUTTON -->
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-black border-t border-white/10">
      <div class="max-w-sm mx-auto px-4 py-3">
        <button
          @click="showCopySheet = true"
          class="w-full py-3 rounded-xl font-semibold flex items-center justify-center gap-2 bg-green-500"
        >
          <Copy class="w-4 h-4" />
          Copy Trader
        </button>
      </div>
    </div>

    <!-- COPY SETTINGS SHEET -->
    <CopySettingsSheet
      v-if="showCopySheet"
      :trader="trader"
      @close="showCopySheet = false"
    />

  </section>
</template>

<style scoped>
.stat-box {
  background: #0b0b0b;
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 12px;
  padding: 12px;
  text-align: center;
}

.label {
  font-size: 11px;
  color: rgba(255,255,255,0.5);
}

.value {
  font-size: 15px;
  font-weight: 600;
}

.tab-btn {
  flex: 1;
  padding: 10px 0;
  font-size: 12px;
  color: rgba(255,255,255,0.5);
}

.tab-btn.active {
  color: #22c55e;
  border-bottom: 2px solid #22c55e;
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 12px;
  background: #0b0b0b;
  border-radius: 10px;
}
</style>