import { api } from './http'

export const cardService = {
  getCards() {
    return api.get('/cards')
  },

  getCard(cardId: number) {
    return api.get(`/cards/${cardId}`)
  },

  createCard(data: {
    bank_account_id: number
    card_name: string
    card_type: 'debit' | 'credit'
    daily_limit: number
    monthly_limit: number
  }) {
    return api.post('/cards', data)
  },
}
