import { ref } from 'vue'
import { copyTradingService } from '@/services/copyTradingService'

export function useCopyTrading() {
  const traders = ref<any[]>([])
  const following = ref<any[]>([])
  const followers = ref<any[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchTraders = async (filter = 'all', limit = 10) => {
    try {
      loading.value = true
      error.value = null
      traders.value = await copyTradingService.getTraders(filter, limit)
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch traders'
    } finally {
      loading.value = false
    }
  }

  const fetchFollowing = async () => {
    try {
      loading.value = true
      error.value = null
      following.value = await copyTradingService.getFollowing()
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch following'
    } finally {
      loading.value = false
    }
  }

  const fetchFollowers = async () => {
    try {
      loading.value = true
      error.value = null
      followers.value = await copyTradingService.getFollowers()
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch followers'
    } finally {
      loading.value = false
    }
  }

  const followTrader = async (data: any) => {
    try {
      loading.value = true
      error.value = null
      const relationship = await copyTradingService.followTrader(data)
      following.value.push(relationship)
      return relationship
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to follow trader'
      throw err
    } finally {
      loading.value = false
    }
  }

  const unfollowTrader = async (relationshipId: number) => {
    try {
      loading.value = true
      error.value = null
      await copyTradingService.unfollowTrader(relationshipId)
      following.value = following.value.filter(r => r.id !== relationshipId)
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to unfollow trader'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    traders,
    following,
    followers,
    loading,
    error,
    fetchTraders,
    fetchFollowing,
    fetchFollowers,
    followTrader,
    unfollowTrader,
  }
}
