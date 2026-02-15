import { computed } from 'vue'

export function usePnL(position: any, currentPrice: number) {
  const pnl = computed(() => {
    return ((currentPrice - position.entry) / position.entry) * 100 * position.leverage
  })

  const pnlValue = computed(() => {
    return (currentPrice - position.entry) * position.size
  })

  return { pnl, pnlValue }
}