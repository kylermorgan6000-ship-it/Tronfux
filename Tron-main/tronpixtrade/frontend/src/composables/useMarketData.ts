import { ref, watch, onUnmounted, type Ref } from 'vue'
import { getSpotMarkets } from '@/services/coingecko'
import {
  getBinanceSpotMarkets,
  getBinanceFuturesMarkets,
} from '@/services/binance'
import { getForexMarkets } from '@/services/forex'

export function useMarketData(
  marketType: Ref<'spot' | 'futures' | 'forex'>,
  category: Ref<string>
) {
  const data = ref<any[]>([])
  const loading = ref(true)
  let timer: any

  const fetchData = async () => {
    loading.value = true
    try {
      if (marketType.value === 'forex') {
        data.value = await getForexMarkets()
      }

      else if (marketType.value === 'futures') {
        data.value = await getBinanceFuturesMarkets()
      }

      else {
        // spot
        data.value = await getSpotMarkets(category.value)
      }
    } catch (err) {
      console.error('Market fetch failed, falling back to Binance spot', err)
      data.value = await getBinanceSpotMarkets()
    } finally {
      loading.value = false
    }
  }

  watch([marketType, category], fetchData, { immediate: true })

  timer = setInterval(fetchData, 10_000)

  onUnmounted(() => clearInterval(timer))

  return {
    data,
    loading,
  }
}