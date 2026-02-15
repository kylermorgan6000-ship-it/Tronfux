import { ref } from 'vue'
import { fetchBankAccountsList } from '@/lib/api'

export function useBankAccount() {
  const accounts = ref<any[]>([])
  const loading = ref(false)

  const fetchAccounts = async () => {
    loading.value = true
    try {
      const data = await fetchBankAccountsList()
      accounts.value = data
    } catch (err) {
      console.error('Failed to fetch accounts:', err)
    } finally {
      loading.value = false
    }
  }

  return { accounts, loading, fetchAccounts }
}