<script setup lang="ts">
import AdminLayout from '../AdminLayout.vue'
import { ref, computed, onMounted } from 'vue'
import {
  Loader2,
  Search,
  CheckCircle,
  XCircle,
  RefreshCw,
  User,
  CreditCard,
  Eye,
} from 'lucide-vue-next'
import {
  fetchBrokerWithdrawals,
  approveBrokerWithdrawal,
  rejectBrokerWithdrawal,
} from '@/lib/api'

// ------------------- TYPES -------------------
interface Withdrawal {
  id: string | number
  user?: string
  email?: string
  status?: string
  [key: string]: any
}

// ------------------- STATE -------------------
const loading = ref(true)
const actionLoading = ref(false)
const showModal = ref(false)
const selectedWithdrawal = ref<Withdrawal | null>(null)
const searchQuery = ref('')

const withdrawals = ref<Withdrawal[]>([])

// ------------------- FETCH -------------------
const loadWithdrawals = async () => {
  loading.value = true
  try {
    const data = await fetchBrokerWithdrawals()
    withdrawals.value = data?.data ?? data ?? []
  } catch (e: any) {
    console.error('Failed to load withdrawals', e)
    withdrawals.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => { void loadWithdrawals() })

// ------------------- COMPUTED -------------------
const filteredList = computed(() =>
  withdrawals.value.filter(w =>
    ((w.user ?? '').toString().toLowerCase().includes(searchQuery.value.toLowerCase())) ||
    ((w.email ?? '').toString().toLowerCase().includes(searchQuery.value.toLowerCase()))
  )
)

// ------------------- HELPERS -------------------
const openModal = (w: Withdrawal) => {
  selectedWithdrawal.value = w
  showModal.value = true
}

const approveWithdrawalHandler = async () => {
  if (!selectedWithdrawal.value) return
  actionLoading.value = true
  try {
    const res = await approveBrokerWithdrawal(selectedWithdrawal.value.id)
    selectedWithdrawal.value.status = res.status ?? 'Completed'
    await loadWithdrawals()
  } catch (e: any) {
    console.error('Approve failed', e)
  } finally {
    actionLoading.value = false
    showModal.value = false
  }
}

const rejectWithdrawalHandler = async () => {
  if (!selectedWithdrawal.value) return
  actionLoading.value = true
  try {
    const res = await rejectBrokerWithdrawal(selectedWithdrawal.value.id)
    selectedWithdrawal.value.status = res.status ?? 'Failed'
    await loadWithdrawals()
  } catch (e: any) {
    console.error('Reject failed', e)
  } finally {
    actionLoading.value = false
    showModal.value = false
  }
}
</script>

<template>
  <AdminLayout>
    <section class="p-3 text-white space-y-4 min-h-screen">

      <!-- HEADER -->
      <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold flex items-center gap-2">
          <CreditCard class="w-5 h-5 text-green-400" /> Withdrawals
        </h1>
      </div>

      <!-- SEARCH -->
      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by user or email..."
          class="w-full bg-white/5 text-white/80 p-2 rounded-lg pl-10 outline-none"
        />
        <Search class="absolute top-2.5 left-3 w-5 h-5 text-white/50" />
      </div>

      <!-- LOADER -->
      <div v-if="loading" class="flex justify-center items-center h-40">
        <Loader2 class="w-8 h-8 animate-spin text-green-400" />
      </div>

      <!-- EMPTY STATE -->
      <div v-else-if="filteredList.length === 0" class="text-center text-white/50 py-10">
        No withdrawals found.
      </div>

      <!-- LIST -->
      <div v-else class="space-y-3">
        <div
          v-for="w in filteredList"
          :key="w.id"
          class="bg-white/5 p-3 rounded-xl border border-white/10 flex justify-between items-center"
        >
          <div class="flex items-center gap-3">
            <User class="w-8 h-8 text-white/60" />
            <div>
              <p class="font-semibold">{{ w.user }}</p>
              <p class="text-xs text-white/50">{{ w.email }}</p>
              <span
                class="text-[10px] px-2 py-0.5 rounded-full"
                :class="{
                  'bg-yellow-500 text-black': w.status==='Pending',
                  'bg-green-500 text-black': w.status==='Completed',
                  'bg-red-500 text-white': w.status==='Failed'
                }"
              >
                {{ w.status }}
              </span>
            </div>
          </div>

          <div class="flex gap-2 items-center">
            <p class="text-sm text-white/60">{{ w.currency }} {{ w.amount }}</p>
            <button @click="openModal(w)" class="p-2 rounded-lg hover:bg-white/5">
              <Eye class="w-4 h-4 text-white/60" />
            </button>
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
            <XCircle class="w-5 h-5" />
          </button>

          <h2 class="text-lg font-semibold">{{ selectedWithdrawal.user }}</h2>
          <p class="text-xs text-white/50">{{ selectedWithdrawal.email }}</p>

          <div class="space-y-2">
            <p class="text-sm font-semibold">Amount</p>
            <p class="text-xs text-white/50">{{ selectedWithdrawal.currency }} {{ selectedWithdrawal.amount }}</p>

            <p class="text-sm font-semibold">Method</p>
            <p class="text-xs text-white/50">{{ selectedWithdrawal.method }}</p>

            <p class="text-sm font-semibold">Account Number</p>
            <p class="text-xs text-white/50">{{ selectedWithdrawal.accountNumber }}</p>

            <p class="text-sm font-semibold">Requested At</p>
            <p class="text-xs text-white/50">{{ selectedWithdrawal.requestedAt }}</p>
          </div>

          <div class="flex justify-between gap-3 mt-3">
            <button
              @click="approveWithdrawal"
              class="flex-1 bg-green-500 text-black py-2 rounded-lg flex justify-center items-center gap-2"
            >
              <span v-if="!actionLoading">Approve</span>
              <Loader2 v-else class="w-5 h-5 animate-spin" />
            </button>

            <button
              @click="rejectWithdrawal"
              class="flex-1 bg-red-500 text-white py-2 rounded-lg flex justify-center items-center gap-2"
            >
              <span v-if="!actionLoading">Reject</span>
              <Loader2 v-else class="w-5 h-5 animate-spin" />
            </button>
          </div>
        </div>
      </div>

    </section>
  </AdminLayout>
</template>