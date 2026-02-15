import { ref } from 'vue'

const fundingRate = ref(0)

export function useFundingRate() {
  const calculate = (futures: number, spot: number) => {
    fundingRate.value = ((futures - spot) / spot) * 0.01
  }

  return { fundingRate, calculate }
}