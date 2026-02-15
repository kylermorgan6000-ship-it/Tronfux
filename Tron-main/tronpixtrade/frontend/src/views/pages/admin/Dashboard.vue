<script setup lang="ts">
import AdminLayout from './AdminLayout.vue'
import {
  Users,
  CreditCard,
  BarChart2,
  Wallet,
  ArrowUpRight,
  ArrowDownLeft,
  ShieldCheck,
  Activity,
  TrendingUp,
  DollarSign
} from 'lucide-vue-next'
import api, { fetchBankUsers, fetchBrokerTrades, fetchWallets } from '@/lib/api'
import { ref, onMounted } from 'vue'

// ------------------- STATE -------------------
const stats = ref([
  { label: 'Total Users', value: '0', icon: Users },
  { label: 'Active Bank Users', value: '0', icon: CreditCard },
  { label: 'Broker Trades', value: '0', icon: BarChart2 },
  { label: 'Total Wallet Balance', value: '$0', icon: Wallet },
])

const revenueStats = ref([
  { label: 'Daily Volume', value: '$0', icon: TrendingUp },
  { label: 'Fees Earned', value: '$0', icon: DollarSign },
])

const recentActions = ref([
  { name: 'Deposit', amount: '+$0', icon: ArrowDownLeft, color: 'text-green-400' },
  { name: 'Withdrawal', amount: '-$0', icon: ArrowUpRight, color: 'text-red-400' },
  { name: 'KYC Approved', amount: 'User #0', icon: ShieldCheck, color: 'text-blue-400' },
  { name: 'Trade Closed', amount: '+$0', icon: Activity, color: 'text-purple-400' },
])

// ------------------- FETCH DATA -------------------
const loadDashboard = async () => {
  try {
    // Total users & active bank users
    const usersData = await fetchBankUsers()
    const totalUsers = usersData.data?.length ?? 0
    const activeUsers = usersData.data?.filter((u: any) => u.status === 'active').length ?? 0

    // Broker trades
    const trades = await fetchBrokerTrades()
    const brokerTrades = trades.data?.length ?? 0

    // Wallets
    const wallets = await fetchWallets()
    const totalBalance = wallets.reduce((sum: number, w: any) => sum + Number(w.balance || 0), 0)

    stats.value = [
      { label: 'Total Users', value: totalUsers.toLocaleString(), icon: Users },
      { label: 'Active Bank Users', value: activeUsers.toLocaleString(), icon: CreditCard },
      { label: 'Broker Trades', value: brokerTrades.toLocaleString(), icon: BarChart2 },
      { label: 'Total Wallet Balance', value: `$${totalBalance.toLocaleString()}`, icon: Wallet },
    ]

    // Example: revenue stats can be calculated from trades or fees
    const dailyVolume = trades.data?.reduce((sum: number, t: any) => sum + Number(t.amount || 0), 0) ?? 0
    const fees = dailyVolume * 0.076 // example 7.6% fees
    revenueStats.value = [
      { label: 'Daily Volume', value: `$${dailyVolume.toLocaleString()}`, icon: TrendingUp },
      { label: 'Fees Earned', value: `$${fees.toFixed(2)}`, icon: DollarSign },
    ]

    // Example recent actions: latest transactions/trades/KYC updates
    const recentDeposits = wallets.data?.slice(-4).map((w: any) => ({
      name: 'Deposit',
      amount: `+$${w.amount ?? 0}`,
      icon: ArrowDownLeft,
      color: 'text-green-400',
    })) ?? []
    recentActions.value = recentDeposits
  } catch (err) {
    console.error('Failed to load dashboard stats', err)
  }
}

onMounted(() => { void loadDashboard() })
</script>


<template>
  <AdminLayout>
    <section class="relative p-4 space-y-6 text-white overflow-hidden">

      <!-- BACKGROUND CHART -->
      <svg
        viewBox="0 0 1000 300"
        class="absolute top-20 left-0 w-full opacity-10 animate-chart"
      >
        <polyline
          fill="none"
          stroke="#22c55e"
          stroke-width="3"
          points="0,200 150,150 300,180 450,100 600,120 750,80 900,100"
        />
      </svg>

      <!-- FLOATING ORBS -->
      <div class="orb orb-green"></div>
      <div class="orb orb-blue"></div>
      <div class="orb orb-purple"></div>

      <!-- HEADER -->
      <div class="relative z-10">
        <h1 class="text-2xl font-bold">Admin Overview</h1>
        <p class="text-white/60 text-sm">
          Unified control of Banking & Trading systems
        </p>
      </div>

      <!-- STATS -->
      <div class="relative z-10 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div
          v-for="s in stats"
          :key="s.label"
          class="relative bg-gray-900/40 backdrop-blur-xl rounded-xl p-4 overflow-hidden border border-white/5 shadow-[0_0_40px_rgba(34,197,94,0.12)] hover:scale-[1.02] transition"
        >
          <div class="absolute w-24 h-24 bg-green-500/20 blur-3xl -top-6 -left-6"></div>

          <div class="flex items-center gap-3 relative z-10">
            <component :is="s.icon" class="w-6 h-6 text-green-400" />
            <div>
              <p class="text-xs text-white/60">{{ s.label }}</p>
              <p class="text-xl font-bold">{{ s.value }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- REVENUE -->
      <div class="relative z-10 grid grid-cols-2 gap-4">
        <div
          v-for="r in revenueStats"
          :key="r.label"
          class="relative bg-gray-900/40 backdrop-blur-xl rounded-xl p-4 border border-white/5"
        >
          <div class="absolute w-20 h-20 bg-blue-500/20 blur-3xl -top-4 -right-4"></div>

          <div class="flex items-center gap-3 relative z-10">
            <component :is="r.icon" class="w-5 h-5 text-blue-400" />
            <div>
              <p class="text-xs text-white/60">{{ r.label }}</p>
              <p class="text-lg font-bold">{{ r.value }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- ACTIVITY -->
      <div class="relative z-10 bg-gray-900/40 backdrop-blur-xl rounded-xl p-4 border border-white/5">
        <h3 class="text-sm font-semibold mb-3">Recent Activity</h3>

        <div class="space-y-3">
          <div
            v-for="a in recentActions"
            :key="a.name"
            class="flex items-center justify-between bg-white/5 rounded-lg px-3 py-2 hover:bg-white/10 transition"
          >
            <div class="flex items-center gap-3">
              <component :is="a.icon" :class="['w-5 h-5', a.color]" />
              <span class="text-sm">{{ a.name }}</span>
            </div>
            <span class="text-sm font-semibold" :class="a.color">
              {{ a.amount }}
            </span>
          </div>
        </div>
      </div>

      <!-- QUICK LINKS -->
      <div class="relative z-10 grid grid-cols-2 md:grid-cols-4 gap-3">
        <button class="admin-btn">Manage Users</button>
        <button class="admin-btn">Bank Accounts</button>
        <button class="admin-btn">Broker Trades</button>
        <button class="admin-btn">Withdrawals</button>
      </div>

    </section>
  </AdminLayout>
</template>

<style scoped>
.admin-btn {
  padding: 10px;
  border-radius: 12px;
  background: radial-gradient(circle at top, #22c55e, #0f2f1c);
  color: white;
  font-size: 12px;
  transition: all 0.2s;
}
.admin-btn:hover {
  box-shadow: 0 0 20px rgba(34,197,94,0.4);
  transform: translateY(-1px);
}

/* FLOATING ORBS */
.orb {
  position: absolute;
  border-radius: 9999px;
  filter: blur(60px);
  opacity: 0.3;
  animation: float 10s ease-in-out infinite;
}
.orb-green {
  width: 200px;
  height: 200px;
  background: #22c55e;
  top: 20%;
  left: -80px;
}
.orb-blue {
  width: 180px;
  height: 180px;
  background: #3b82f6;
  bottom: 10%;
  right: -80px;
}
.orb-purple {
  width: 150px;
  height: 150px;
  background: #a855f7;
  top: 60%;
  right: 30%;
}

@keyframes float {
  0%,100% { transform: translateY(0) }
  50% { transform: translateY(-30px) }
}

/* BACKGROUND CHART ANIMATION */
.animate-chart {
  animation: chartMove 20s linear infinite;
}

@keyframes chartMove {
  0% { transform: translateX(0) }
  100% { transform: translateX(-200px) }
}
</style>