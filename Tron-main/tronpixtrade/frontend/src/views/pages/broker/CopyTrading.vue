<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

/* -------------------------
ROUTER
------------------------- */
// Inertia router is already imported above

/* -------------------------
STATE
------------------------- */
const activeFilter = ref('all')

const router = useRouter()

import { useCopyTrading } from '@/composables/useCopyTradingAPI'

const { traders, loading: tradersLoading, error: tradersError, fetchTraders } = useCopyTrading()

/* -------------------------
FILTERED
------------------------- */
const filteredTraders = computed(() => {
  const list = traders.value || []
  if (activeFilter.value === 'top') {
    return [...list].sort((a, b) => (b.roi || 0) - (a.roi || 0))
  }

  if (activeFilter.value === 'low-risk') {
    return list.filter((t) => (t.risk) === 'Low')
  }

  return list
})

/* -------------------------
ACTIONS
------------------------- */
function openTrader(trader) {
  router.push(`/broker/trader-profile/${trader.id}`)
}

onMounted(() => {
  fetchTraders(activeFilter.value, 20)
})
</script>

<template>
  <section class="bg-black min-h-screen text-white pt-[56px]">
    <div class="max-w-sm mx-auto px-4 pb-16">

      <!-- HEADER -->
      <div class="fixed top-0 left-0 right-0 z-30 bg-black border-b border-white/10">
        <div class="max-w-sm mx-auto px-4 py-4">
          <h1 class="text-[16px] font-semibold">Copy Trading</h1>
        </div>
      </div>

      <!-- HERO -->
      <div class="mt-4 mb-6 rounded-xl bg-gradient-to-br from-black to-green-900 p-4 border border-green-500/20">
        <p class="text-[12px] text-white/60 mb-1">
          Follow top traders and earn automatically
        </p>

        <div class="flex items-center gap-2">
          <TrendingUp class="w-5 h-5 text-green-400" />
          <span class="text-[15px] font-semibold">
            Copy professional traders in real-time
          </span>
        </div>
      </div>

      <!-- FILTERS -->
      <div class="flex items-center gap-2 mb-4">
        <button
          class="filter-btn"
          :class="activeFilter === 'all' && 'active'"
          @click="activeFilter = 'all'"
        >
          All
        </button>

        <button
          class="filter-btn"
          :class="activeFilter === 'top' && 'active'"
          @click="activeFilter = 'top'"
        >
          Top ROI
        </button>

        <button
          class="filter-btn"
          :class="activeFilter === 'low-risk' && 'active'"
          @click="activeFilter = 'low-risk'"
        >
          Low Risk
        </button>

        <Filter class="w-4 h-4 text-white/40 ml-auto" />
      </div>

      <!-- TRADER LIST -->
      <div class="space-y-3">
        <div
          v-for="trader in filteredTraders"
          :key="trader.id"
          class="bg-[#0b0b0b] rounded-xl p-4 border border-white/10 hover:border-green-500/30 transition"
          @click="openTrader(trader)"
        >
          <!-- TOP -->
          <div class="flex items-center gap-3 mb-3">
            <img :src="trader?.avatar" class="w-10 h-10 rounded-full" />

            <div class="flex-1">
              <p class="font-semibold text-[14px]">{{ trader?.name ?? 'Unknown' }}</p>
              <p class="text-[11px] text-white/40">
                7D PnL
                <span class="text-green-400">
                  +{{ trader?.pnl7d ?? 0 }}%
                </span>
              </p>
            </div>

            <ChevronRight class="w-4 h-4 text-white/40" />
          </div>

          <!-- STATS -->
          <div class="grid grid-cols-3 text-center text-[11px] mb-3">
            <div>
              <p class="text-white/40">ROI</p>
              <p class="text-green-400 font-semibold">
                +{{ trader?.roi ?? 0 }}%
              </p>
            </div>

            <div>
              <p class="text-white/40">Win Rate</p>
              <p class="font-semibold">
                {{ trader?.winRate ?? 0 }}%
              </p>
            </div>

            <div>
              <p class="text-white/40">Risk</p>
              <p
                :class="
                  trader?.risk === 'Low'
                    ? 'text-green-400'
                    : trader?.risk === 'Medium'
                    ? 'text-yellow-400'
                    : 'text-red-400'
                "
                class="font-semibold"
              >
                {{ trader?.risk ?? 'Unknown' }}
              </p>
            </div>
          </div>

          <!-- FOOTER -->
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-1 text-[11px] text-white/50">
              <Users class="w-3 h-3" />
              {{ trader.followers.toLocaleString() }} copiers
            </div>

            <button
              class="copy-btn"
              @click.stop="openTrader(trader)"
            >
              Copy
            </button>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<style scoped>
.filter-btn {
  font-size: 12px;
  padding: 6px 12px;
  border-radius: 9999px;
  background: rgba(255,255,255,0.06);
  color: rgba(255,255,255,0.7);
}

.filter-btn.active {
  background: rgba(34,197,94,0.2);
  color: #22c55e;
}

.copy-btn {
  padding: 6px 16px;
  font-size: 12px;
  border-radius: 9999px;
  background: linear-gradient(135deg, #22c55e, #15803d);
  box-shadow: 0 0 10px rgba(34,197,94,0.4);
  font-weight: 600;
}
</style>