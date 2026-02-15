import { api } from './http'

export const adminService = {
  // Withdrawals
  getWithdrawals: async (page = 1) => {
    const response = await api.get(`/admin/withdrawals?page=${page}`)
    return response.data
  },

  approveWithdrawal: async (withdrawalId: number) => {
    const response = await api.post(`/admin/withdrawals/${withdrawalId}/approve`, {})
    return response.data
  },

  rejectWithdrawal: async (withdrawalId: number) => {
    const response = await api.post(`/admin/withdrawals/${withdrawalId}/reject`, {})
    return response.data
  },

  // Deposits
  getDeposits: async (page = 1) => {
    const response = await api.get(`/admin/deposits?page=${page}`)
    return response.data
  },
}
