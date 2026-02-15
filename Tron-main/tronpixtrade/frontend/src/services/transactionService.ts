import { api } from './http'

export const transactionService = {
  getTransactions(type?: string, currency?: string) {
    let query = '/transactions'
    const params = new URLSearchParams()
    
    if (type) params.append('type', type)
    if (currency) params.append('currency', currency)
    
    if (params.toString()) {
      query += '?' + params.toString()
    }
    
    return api.get(query)
  },

  getTransaction(transactionId: number) {
    return api.get(`/transactions/${transactionId}`)
  },
}
