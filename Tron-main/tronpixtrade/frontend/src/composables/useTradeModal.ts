// src/composables/useTradeModal.ts
import { ref } from 'vue'

const isOpen = ref(false)
const activeAsset = ref<any>(null)

export function useTradeModal() {
  function openTrade(asset: any) {
    activeAsset.value = asset
    isOpen.value = true
  }

  function closeTrade() {
    isOpen.value = false
    activeAsset.value = null
  }

  return {
    isOpen,
    activeAsset,
    openTrade,
    closeTrade,
  }
}