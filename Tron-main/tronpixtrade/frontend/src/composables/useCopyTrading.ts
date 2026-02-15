import { ref, onMounted } from 'vue'
import api from '@/lib/api'

const traders = ref<any[]>([])
const following = ref<number[]>([])

onMounted(async () => {
  try {
    const res = await api.get('/copy-trading/traders')
    traders.value = res.data.data ?? res.data
  } catch (e) {
    // fallback to small static list
    traders.value = [
      { id: 1, name: 'AlphaWolf', roi: 124, followers: 3200 },
      { id: 2, name: 'FXSniper', roi: 89, followers: 2100 },
    ]
  }
})

export function useCopyTrading(marginEngine: any) {
  const mirrorTrade = (trade: any) => {
    marginEngine.openPosition(
      trade.asset,
      trade.size,
      trade.leverage
    )
  }

  return { mirrorTrade, traders, following }
}