import { ref } from 'vue'

export function useTradeForm() {
  const symbol = ref('BNBUSDT')
  const side = ref<'BUY' | 'SELL'>('BUY')
  const type = ref<'LIMIT' | 'MARKET'>('LIMIT')
  const price = ref<number | null>(null)
  const amount = ref<number | null>(null)
  const feeRate = ref(0.001)
  const availableBalance = ref(1000)

  function setAmountByPercent(percent: number) {
    amount.value = +(availableBalance.value * (percent / 100)).toFixed(6)
  }

  return {
    symbol,
    side,
    type,
    price,
    amount,
    feeRate,
    availableBalance,
    setAmountByPercent,
  }
}