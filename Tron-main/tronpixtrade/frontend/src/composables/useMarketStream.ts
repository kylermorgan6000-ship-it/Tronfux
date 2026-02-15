import { ref, onMounted, onUnmounted } from 'vue'

export function useMarketStream(symbol = 'bnbusdt') {
  const lastPrice = ref<number>(0)
  const bids = ref<[string, string][]>([])
  const asks = ref<[string, string][]>([])
  const trades = ref<any[]>([])

  let socket: WebSocket | null = null

  onMounted(() => {
    socket = new WebSocket(
      `wss://stream.binance.com:9443/stream?streams=${symbol}@trade/${symbol}@depth5@100ms`
    )

    socket.onmessage = (e) => {
      const msg = JSON.parse(e.data)
      const data = msg.data

      // Trade updates
      if (data?.e === 'trade') {
        lastPrice.value = Number(data.p)
        trades.value.unshift(data)
        trades.value = trades.value.slice(0, 30)
      }

      // Order book updates
      if (Array.isArray(data?.b) && Array.isArray(data?.a)) {
        bids.value = data.b
        asks.value = data.a
      }
    }
  })

  onUnmounted(() => {
    socket?.close()
  })

  return { lastPrice, bids, asks, trades }
}