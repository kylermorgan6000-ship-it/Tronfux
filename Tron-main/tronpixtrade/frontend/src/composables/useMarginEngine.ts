import { ref } from 'vue'

export type MarginMode = 'isolated' | 'cross'

const balance = ref(10000)
const marginMode = ref<MarginMode>('isolated')
const positions = ref<any[]>([])

export function useMarginEngine() {
  const openPosition = (asset: any, size: number, leverage: number) => {
    const margin = size / leverage
    if (balance.value < margin) return false

    balance.value -= margin

    positions.value.push({
      asset,
      size,
      leverage,
      entry: asset.price,
      margin,
      liquidated: false,
    })

    return true
  }

  return {
    balance,
    marginMode,
    positions,
  }
}