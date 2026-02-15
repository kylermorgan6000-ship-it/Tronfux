import { api } from './http'

export const loanService = {
  getLoans() {
    return api.get('/loans')
  },

  getLoan(loanId: number) {
    return api.get(`/loans/${loanId}`)
  },

  createLoan(data: {
    bank_account_id: number
    amount: number
    currency: string
    interest_rate: number
    term_months: number
  }) {
    return api.post('/loans', data)
  },

  calculatePayment(loanId: number) {
    return api.post(`/loans/${loanId}/calculate-payment`, {})
  },
}
