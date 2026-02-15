// src/composables/useLiveTicker.ts
import { ref, onUnmounted } from 'vue'

const sockets = new Map<string, WebSocket>()

export function useLiveTicker(symbol: string) {
  const price = ref<number | null>(null)
  const change = ref<number | null>(null)

  if (!sockets.has(symbol)) {
    const ws = new WebSocket(
      `wss://stream.binance.com:9443/ws/${symbol.toLowerCase()}@ticker`
    )

    ws.onmessage = (e) => {
      const data = JSON.parse(e.data)
      price.value = Number(data.c)
      change.value = Number(data.P)
    }

    sockets.set(symbol, ws)
  }

  onUnmounted(() => {
    const ws = sockets.get(symbol)
    if (ws) {
      ws.close()
      sockets.delete(symbol)
    }
  })

  return { price, change }
}