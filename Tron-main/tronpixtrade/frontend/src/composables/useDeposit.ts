import { ref } from 'vue'
import { depositService } from '@/services/depositService'

export function useDeposit() {
  const deposits = ref<any[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchDeposits = async () => {
    try {
      loading.value = true
      error.value = null
      deposits.value = await depositService.getDeposits()
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch deposits'
    } finally {
      loading.value = false
    }
  }

  const createDeposit = async (data: any) => {
    try {
      loading.value = true
      error.value = null
      const deposit = await depositService.createDeposit(data)
      deposits.value.unshift(deposit)
      return deposit
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to create deposit'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    deposits,
    loading,
    error,
    fetchDeposits,
    createDeposit,
  }
}
