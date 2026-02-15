import { ref, computed } from 'vue'
import { transactionService } from '@/services/transactionService'

export interface Transaction {
  id: string
  type: 'deposit' | 'withdraw' | 'transfer'
  asset: string
  amount: number
  status: 'pending' | 'completed' | 'failed'
  timestamp: string
  network?: string
  address?: string
  fee?: number
}

export function useTransaction() {
  const transactions = ref<Transaction[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const activeTab = ref<'all' | 'deposit' | 'withdraw' | 'transfer'>('all')

  const fetchTransactions = async () => {
    loading.value = true
    error.value = null
    try {
      const data = await transactionService.getTransactions()
      // Map API response to transaction format
      transactions.value = (data || []).map((t: any) => ({
        id: t.id || `TX${Date.now()}`,
        type: t.type || 'transfer',
        asset: t.asset || 'USDT',
        amount: Number(t.amount) || 0,
        status: t.status || 'pending',
        timestamp: t.created_at || new Date().toISOString(),
        network: t.network,
        address: t.address,
        fee: t.fee,
      }))
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch transactions'
      console.error('Error fetching transactions:', err)
      // Fallback to empty array
      transactions.value = []
    } finally {
      loading.value = false
    }
  }

  const filteredTransactions = computed(() => {
    if (activeTab.value === 'all') return transactions.value
    return transactions.value.filter(t => t.type === activeTab.value)
  })

  return {
    transactions,
    filteredTransactions,
    loading,
    error,
    activeTab,
    fetchTransactions,
  }
}
