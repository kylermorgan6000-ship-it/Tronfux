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
import { useAdminWithdrawals } from '@/composables/useAdminWithdrawals'

// ------------------------- STATE -------------------------
const { 
  withdrawals, 
  loading, 
  actionLoading, 
  fetchWithdrawals, 
  approveWithdrawal, 
  rejectWithdrawal 
} = useAdminWithdrawals()

const selectedWithdrawal = ref<any>(null)
const showModal = ref(false)
const searchQuery = ref('')

// ------------------------- COMPUTED -----------------------
const filteredWithdrawals = computed(() => 
  withdrawals.value.filter(w =>
    (w.user?.name ?? '').toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    (w.user?.email ?? '').toLowerCase().includes(searchQuery.value.toLowerCase())
  )
)

// ------------------------- HELPERS ------------------------
const openModal = (w: any) => {
  selectedWithdrawal.value = w
  showModal.value = true
}

const closeModal = () => {
  selectedWithdrawal.value = null
  showModal.value = false
}

const handleApprove = async () => {
  if (!selectedWithdrawal.value) return
  try {
    await approveWithdrawal(selectedWithdrawal.value.id)
    selectedWithdrawal.value.status = 'approved' // local fallback
    closeModal()
  } catch (err) {
    console.error('Failed to approve withdrawal', err)
  }
}

const handleReject = async () => {
  if (!selectedWithdrawal.value) return
  try {
    await rejectWithdrawal(selectedWithdrawal.value.id)
    selectedWithdrawal.value.status = 'rejected' // local fallback
    closeModal()
  } catch (err) {
    console.error('Failed to reject withdrawal', err)
  }
}

// ------------------------- LIFECYCLE ----------------------
onMounted(async () => {
  await fetchWithdrawals()
})
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
              <p class="font-semibold">{{ w.user?.name || 'Unknown' }}</p>
              <p class="text-xs text-white/50">{{ w.user?.email || 'N/A' }}</p>
              <span
                class="text-[10px] px-2 py-0.5 rounded-full"
                :class="{
                  'bg-yellow-500 text-black': w.status==='pending',
                  'bg-green-500 text-black': w.status==='approved',
                  'bg-red-500 text-white': w.status==='rejected'
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

          <h2 class="text-lg font-semibold">{{ selectedWithdrawal.user?.name || 'User' }}</h2>
          <p class="text-xs text-white/50">{{ selectedWithdrawal.user?.email || 'N/A' }}</p>

          <div class="space-y-2">
            <p class="text-sm font-semibold">Amount</p>
            <p class="text-xs text-white/50">{{ selectedWithdrawal.currency }} {{ selectedWithdrawal.amount }}</p>

            <p class="text-sm font-semibold">Method</p>
            <p class="text-xs text-white/50">{{ selectedWithdrawal.withdrawal_method || 'N/A' }}</p>

            <p class="text-sm font-semibold">Status</p>
            <p class="text-xs text-white/50 capitalize">{{ selectedWithdrawal.status }}</p>

            <p class="text-sm font-semibold">Created At</p>
            <p class="text-xs text-white/50">{{ selectedWithdrawal.created_at || 'N/A' }}</p>
          </div>

          <div class="flex justify-between gap-3 mt-3">
            <button
              @click="handleApproveWithdrawal"
              class="flex-1 bg-green-500 text-black py-2 rounded-lg flex justify-center items-center gap-2"
            >
              <span v-if="!actionLoading">Approve</span>
              <Loader2 v-else class="w-5 h-5 animate-spin" />
            </button>

            <button
              @click="handleRejectWithdrawal"
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