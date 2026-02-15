import { ref } from 'vue'
import { withdrawalService } from '@/services/withdrawalService'

export function useWithdrawal() {
  const withdrawals = ref<any[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchWithdrawals = async () => {
    try {
      loading.value = true
      error.value = null
      withdrawals.value = await withdrawalService.getWithdrawals()
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch withdrawals'
    } finally {
      loading.value = false
    }
  }

  const createWithdrawal = async (data: any) => {
    try {
      loading.value = true
      error.value = null
      const withdrawal = await withdrawalService.createWithdrawal(data)
      withdrawals.value.unshift(withdrawal)
      return withdrawal
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to create withdrawal'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    withdrawals,
    loading,
    error,
    fetchWithdrawals,
    createWithdrawal,
  }
}
