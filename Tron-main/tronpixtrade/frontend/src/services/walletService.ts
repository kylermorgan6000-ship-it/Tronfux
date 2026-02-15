import  api  from '@/lib/api'

export const walletService = {
  getWallets() {
    return api.get('/api/wallets')
  },

  getWallet(walletId: number) {
    return api.get(`/api/wallets/${walletId}`)
  },

  getBalance(walletId: number) {
    return api.get(`/api/wallets/${walletId}/balance`)
  },
}
