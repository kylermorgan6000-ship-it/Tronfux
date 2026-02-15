import { ref, computed } from 'vue'
import { walletService } from '@/services/walletService'

export function useWallet() {
  const wallets = ref<any[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchWallets = async () => {
    try {
      loading.value = true
      error.value = null
      wallets.value = await walletService.getWallets()
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch wallets'
    } finally {
      loading.value = false
    }
  }

  const totalBalance = computed(() => {
    return wallets.value.reduce((sum, wallet) => sum + (wallet.balance || 0), 0)
  })

  return {
    wallets,
    loading,
    error,
    fetchWallets,
    totalBalance,
  }
}
