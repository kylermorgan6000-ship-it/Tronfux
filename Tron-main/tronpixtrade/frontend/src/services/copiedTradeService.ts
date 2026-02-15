import { api } from './http'

export const copiedTradeService = {
  getCopiedTrades() {
    return api.get('/copied-trades')
  },

  closeTrade(tradeId: number, data: { exit_price: number; pnl: number }) {
    return api.post(`/copied-trades/${tradeId}/close`, data)
  },

  getTradeHistory(page = 1) {
    return api.get(`/copy-trade-history?page=${page}`)
  },
}
