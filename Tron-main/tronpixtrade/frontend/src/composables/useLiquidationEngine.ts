import { ref } from 'vue'

const maintenanceMarginRate = 0.005 // 0.5%

export function useLiquidationEngine() {
  const maintenanceMarginRate = 0.005

  const shouldLiquidate = (pos: any, price: number) => {
    const pnl =
      (price - pos.entry) *
      (pos.side === 'long' ? 1 : -1) *
      pos.size

    return pos.margin + pnl <= pos.size * maintenanceMarginRate
  }

  return { shouldLiquidate }
}