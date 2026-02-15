import { api } from './http'

export const depositService = {
  getDeposits() {
    return api.get('/deposits')
  },

  getDeposit(depositId: number) {
    return api.get(`/deposits/${depositId}`)
  },

  createDeposit(data: {
    wallet_id: number
    amount: number
    currency: string
    payment_method: 'card' | 'bank_transfer' | 'crypto' | 'paypal'
  }) {
    return api.post('/deposits', data)
  },
}
