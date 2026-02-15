import { ref, computed } from 'vue'

export function useFuturesPosition() {
  const entryPrice = ref(0)
  const leverage = ref(10)
  const positionSize = ref(0)
  const markPrice = ref(0)
  const fundingRate = ref(0.0001)
  const side = ref<'long' | 'short'>('long')

  const pnl = computed(() => {
    if (!entryPrice.value || !markPrice.value) return 0

    const diff =
      side.value === 'long'
        ? markPrice.value - entryPrice.value
        : entryPrice.value - markPrice.value

    return diff * positionSize.value * leverage.value
  })

  const liquidationPrice = computed(() => {
    if (!entryPrice.value) return 0
    const factor = 1 / leverage.value
    return side.value === 'long'
      ? entryPrice.value * (1 - factor)
      : entryPrice.value * (1 + factor)
  })

  const fundingPayment = computed(() =>
    positionSize.value * markPrice.value * fundingRate.value
  )

  return {
    entryPrice,
    leverage,
    positionSize,
    markPrice,
    fundingRate,
    side,
    pnl,
    liquidationPrice,
    fundingPayment,
  }
}