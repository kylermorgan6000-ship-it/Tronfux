import { ref } from 'vue'

const balance = ref(10000)
const positions = ref<any[]>([])

export function useTradeEngine() {
  const buy = (asset: any, amount: number) => {
    if (balance.value < amount) return

    balance.value -= amount
    positions.value.push({
      asset,
      amount,
      price: asset.price
    })
  }

  const sell = (index: number, price: number) => {
    const pos = positions.value[index]
    balance.value += price
    positions.value.splice(index, 1)
  }

  return { balance, positions, buy, sell }
}