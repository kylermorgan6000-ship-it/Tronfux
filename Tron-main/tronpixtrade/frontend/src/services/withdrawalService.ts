import { api } from './http'

export const withdrawalService = {
  getWithdrawals() {
    return api.get('/withdrawals')
  },

  getWithdrawal(withdrawalId: number) {
    return api.get(`/withdrawals/${withdrawalId}`)
  },

  createWithdrawal(data: {
    wallet_id: number
    amount: number
    currency: string
    withdrawal_method: 'bank' | 'crypto_wallet' | 'paypal'
    destination: string
  }) {
    return api.post('/withdrawals', data)
  },

  approveWithdrawal(withdrawalId: number) {
    return api.post(`/withdrawals/${withdrawalId}/approve`, {})
  },
}
