<script setup lang="ts">
import PairSelector from './PairSelector.vue'
import BuySellToggle from './BuySellToggle.vue'
import OrderTypeSelect from './OrderTypeSelect.vue'
import PriceInput from './PriceInput.vue'
import AmountInput from './AmountInput.vue'
import AmountSlider from './AmountSlider.vue'
import FeeEstimate from './FeeEstimate.vue'
import SubmitOrder from './SubmitOrder.vue'
import { useTradeForm } from '@/composables/useTradeForm'

const trade = useTradeForm()
</script>

<template>
  <div class="bg-[#0b0f14] rounded-xl p-3 space-y-3">

    <PairSelector
      :model-value="trade.symbol.value"
      @update:modelValue="trade.symbol.value = $event"
    />

    <BuySellToggle
      :model-value="trade.side.value"
      @update:modelValue="trade.side.value = $event"
    />

    <OrderTypeSelect
      :model-value="trade.type.value"
      @update:modelValue="trade.type.value = $event"
    />

    <PriceInput
      v-if="trade.type.value !== 'MARKET'"
      :model-value="trade.price.value"
      @update:modelValue="trade.price.value = $event"
    />

    <AmountInput
      :model-value="trade.amount.value"
      @update:modelValue="trade.amount.value = $event"
    />

    <AmountSlider
      :balance="trade.availableBalance.value"
      @select="trade.setAmountByPercent"
    />

    <FeeEstimate
      :amount="trade.amount.value"
      :price="trade.price.value"
      :fee-rate="trade.feeRate.value"
    />

    <SubmitOrder :trade="trade" />
  </div>
</template>