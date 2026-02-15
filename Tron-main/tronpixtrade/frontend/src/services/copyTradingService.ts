import { api } from './http'

export const copyTradingService = {
  getTraders(filter = 'all', limit = 10) {
    return api.get(`/copy-trading/traders?filter=${filter}&limit=${limit}`)
  },

  followTrader(data: {
    trader_id: number
    mode: 'fixed' | 'ratio'
    fixed_amount?: number
    ratio?: number
    max_drawdown?: number
  }) {
    return api.post('/copy-trading/follow', data)
  },

  unfollowTrader(relationshipId: number) {
    return api.delete(`/copy-trading/${relationshipId}/unfollow`)
  },

  getFollowing() {
    return api.get('/copy-trading/following')
  },

  getFollowers() {
    return api.get('/copy-trading/followers')
  },
}
