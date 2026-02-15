import { api } from './http'

export const bankAccountService = {
  getBankAccounts() {
    return api.get('/bank-accounts')
  },

  getBankAccount(accountId: number) {
    return api.get(`/bank-accounts/${accountId}`)
  },

  createBankAccount(data: {
    account_name: string
    account_type: 'checking' | 'savings' | 'investment'
    currency: string
  }) {
    return api.post('/bank-accounts', data)
  },

  getBalance(accountId: number) {
    return api.get(`/bank-accounts/${accountId}/balance`)
  },

  getTransactionHistory(accountId: number, page = 1) {
    return api.get(`/bank-accounts/${accountId}/transactions?page=${page}`)
  },
}
