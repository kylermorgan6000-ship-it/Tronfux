import { ref } from 'vue'
import api from '@/lib/api'

export interface Withdrawal {
  id: number
  user: {
    id: number
    name: string
    email: string
  }
  amount: number
  currency: string
  status: string
  created_at: string
  updated_at?: string
  [key: string]: any
}

export function useAdminWithdrawals() {
  const withdrawals = ref<Withdrawal[]>([])
  const loading = ref(false)
  const actionLoading = ref(false)
  const error = ref<string | null>(null)
  const currentPage = ref(1)
  const totalPages = ref(1)

  // ------------------------- FETCH -------------------------
  const fetchWithdrawals = async (page = 1) => {
    try {
      loading.value = true
      error.value = null
      const res = await api.get('/withdrawals', { params: { page } })
      const data = res.data
      withdrawals.value = data.data || []
      currentPage.value = data.current_page || 1
      totalPages.value = data.last_page || 1
    } catch (err: any) {
      console.error('Error fetching admin withdrawals:', err)
      error.value = err.response?.data?.message || err.message || 'Failed to fetch withdrawals'
      withdrawals.value = []
    } finally {
      loading.value = false
    }
  }

  // ------------------------- APPROVE -------------------------
  const approveWithdrawal = async (withdrawalId: number) => {
    try {
      actionLoading.value = true
      error.value = null
      const res = await api.put(`/withdrawals/${withdrawalId}/status`, { status: 'approved' })
      const updated: Withdrawal = res.data
      const index = withdrawals.value.findIndex(w => w.id === withdrawalId)
      if (index >= 0) withdrawals.value[index] = updated
      return updated
    } catch (err: any) {
      error.value = err.response?.data?.message || err.message || 'Failed to approve withdrawal'
      throw err
    } finally {
      actionLoading.value = false
    }
  }

  // ------------------------- REJECT -------------------------
  const rejectWithdrawal = async (withdrawalId: number) => {
    try {
      actionLoading.value = true
      error.value = null
      const res = await api.put(`/withdrawals/${withdrawalId}/status`, { status: 'rejected' })
      const updated: Withdrawal = res.data
      const index = withdrawals.value.findIndex(w => w.id === withdrawalId)
      if (index >= 0) withdrawals.value[index] = updated
      return updated
    } catch (err: any) {
      error.value = err.response?.data?.message || err.message || 'Failed to reject withdrawal'
      throw err
    } finally {
      actionLoading.value = false
    }
  }

  return {
    withdrawals,
    loading,
    actionLoading,
    error,
    currentPage,
    totalPages,
    fetchWithdrawals,
    approveWithdrawal,
    rejectWithdrawal,
  }
}