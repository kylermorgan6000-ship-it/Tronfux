<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { ArrowLeft, ShieldCheck, ArrowDownLeft, ArrowUpRight, Banknote, Wallet, Landmark } from 'lucide-vue-next'
import { useRouter } from 'vue-router'
import FooterLink from '@/components/banking/FooterLink.vue'
import { fetchBankAccounts, fetchTransactions } from '@/lib/api' // <- API calls

/* Initialize router */
const router = useRouter()

/* ---------------- STATE ---------------- */
const accounts = ref<any[]>([])
const stats = ref<any[]>([])
const loading = ref(true)
const error = ref<string | null>(null)

/* ---------------- HELPERS ---------------- */
const formatMoney = (val: number) => `$${val.toLocaleString()}`

/* ---------------- FETCH DATA ---------------- */
const loadData = async () => {
  loading.value = true
  try {
    const [accountData, transactionData] = await Promise.all([
      fetchBankAccounts(),
      fetchTransactions()
    ])

    // Map accounts
    accounts.value = (accountData || []).map(acc => ({
      bank: acc.bank_name || 'Bank',
      account: acc.account_number ? '********' + acc.account_number.slice(-2) : '**********',
      type: acc.account_type || 'USD Checking',
      amount: formatMoney(Number(acc.balance) || 0)
    }))

    // Calculate stats
    const totalDeposited = (transactionData || [])
      .filter(tx => tx.type === 'credit')
      .reduce((sum, tx) => sum + Number(tx.amount), 0)

    const totalSpent = (transactionData || [])
      .filter(tx => tx.type === 'debit')
      .reduce((sum, tx) => sum + Number(tx.amount), 0)

    const netBalance = totalDeposited - totalSpent

    stats.value = [
      { label: 'Total Deposited', value: formatMoney(totalDeposited), icon: Banknote, color: 'text-green-400' },
      { label: 'Total Received', value: formatMoney(totalDeposited), icon: ArrowDownLeft, color: 'text-blue-400' },
      { label: 'Spending Out', value: formatMoney(totalSpent), icon: ArrowUpRight, color: 'text-red-400' },
      { label: 'Net Balance', value: formatMoney(netBalance), icon: Wallet, color: 'text-white' }
    ]

  } catch (err: any) {
    console.error(err)
    error.value = err.message || 'Failed to load dashboard'
  } finally {
    loading.value = false
  }
}

/* ---------------- INIT ---------------- */
onMounted(loadData)
</script>

<template>
  <!-- HEADER -->
  <div class="sticky top-0 z-40 bg-[#0f1215] px-4 py-4 shadow-md flex items-center gap-3">
    <button @click="() => router.back()" class="text-white/70 hover:text-white">
      <ArrowLeft class="w-5 h-5" />
    </button>
    <h1 class="text-sm font-semibold">Payments</h1>
  </div>

  <section class="min-h-screen bg-[#0f1215] text-white px-4 pb-28 pt-4">

    <!-- TOP SECURITY STRIP -->
    <div class="mb-5 flex items-center gap-2 text-[11px] text-white/50">
      <ShieldCheck class="w-4 h-4 text-green-400" />
      <span>All payment data is encrypted and monitored</span>
    </div>

    <!-- MAIN STATS GRID -->
    <div class="grid grid-cols-2 gap-3 mb-6">
      <div
        v-for="stat in stats"
        :key="stat.label"
        class="bg-gray-900/50 rounded-2xl p-4 space-y-2
               hover:bg-gray-800/60 transition cursor-pointer"
      >
        <div class="flex justify-between items-center">
          <component :is="stat.icon" class="w-5 h-5" :class="stat.color" />
          <span class="text-[10px] text-white/40">This year</span>
        </div>
        <p class="text-[11px] text-white/50">{{ stat.label }}</p>
        <p class="text-lg font-bold">{{ stat.value }}</p>
      </div>
    </div>

    <!-- IN vs OUT -->
    <div class="bg-gray-900/40 rounded-2xl p-4 mb-6">
      <p class="text-sm font-semibold mb-3">Money Flow</p>

      <div class="space-y-3">
        <div class="flex justify-between items-center text-[11px]">
          <div class="flex items-center gap-2">
            <ArrowDownLeft class="w-4 h-4 text-green-400" />
            <span>Incoming</span>
          </div>
          <span class="font-semibold">$9,340.00</span>
        </div>

        <div class="w-full h-1.5 bg-gray-800 rounded-full overflow-hidden">
          <div class="h-full bg-green-500 w-[70%]" />
        </div>

        <div class="flex justify-between items-center text-[11px]">
          <div class="flex items-center gap-2">
            <ArrowUpRight class="w-4 h-4 text-red-400" />
            <span>Outgoing</span>
          </div>
          <span class="font-semibold">$6,120.50</span>
        </div>

        <div class="w-full h-1.5 bg-gray-800 rounded-full overflow-hidden">
          <div class="h-full bg-red-500 w-[46%]" />
        </div>
      </div>
    </div>

    <!-- DESTINATION ACCOUNTS -->
    <div class="mb-6">
      <p class="text-sm font-semibold mb-3">Linked Accounts</p>

      <div class="space-y-3">
        <div
          v-for="acc in accounts"
          :key="acc.account"
          class="bg-gray-900/50 rounded-2xl p-4
                 flex justify-between items-center
                 hover:bg-gray-800/60 transition cursor-pointer"
        >
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
              <Landmark class="w-5 h-5 text-green-400" />
            </div>
            <div class="text-[11px]">
              <p class="font-semibold">{{ acc.bank }}</p>
              <p class="text-white/50">
                {{ acc.type }} · {{ acc.account }}
              </p>
            </div>
          </div>

          <p class="text-sm font-semibold">{{ acc.amount }}</p>
        </div>
      </div>
    </div>

    <!-- MINI INSIGHTS -->
    <div class="grid grid-cols-2 gap-3 text-[11px]">
      <div class="bg-gray-900/40 rounded-xl p-3">
        <p class="text-white/50">Most used bank</p>
        <p class="font-semibold mt-1">Chase Bank</p>
      </div>

      <div class="bg-gray-900/40 rounded-xl p-3">
        <p class="text-white/50">Avg. transaction</p>
        <p class="font-semibold mt-1">$286.40</p>
      </div>
    </div>

  </section>

  <FooterLink />
</template>