import { ref } from 'vue'

const fundBalance = ref(1000000)

export function useInsuranceFund() {
  const absorb = (amount: number) => {
    fundBalance.value -= amount
  }

  const addFundingFees = (amount: number) => {
    fundBalance.value += amount
  }

  return {
    fundBalance,
    absorb,
    addFundingFees,
  }
}