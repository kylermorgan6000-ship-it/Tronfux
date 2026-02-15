<script setup lang="ts">
import { ref, computed } from 'vue'
import { X, AlertTriangle } from 'lucide-vue-next'

/* -------------------------
PROPS / EMITS
------------------------- */
const props = defineProps({
  trader: Object
})

const emit = defineEmits(['close', 'confirm'])

/* -------------------------
FORM STATE
------------------------- */
const mode = ref('fixed')

const fixedAmount = ref(100)
const ratio = ref(1)

const maxPositions = ref(3)
const stopLoss = ref(30)
const takeProfit = ref(null)

const copySide = ref('same')

/* -------------------------
VALIDATION
------------------------- */
const isValid = computed(() => {
  if (mode.value === 'fixed' && fixedAmount.value <= 0) return false
  if (mode.value === 'ratio' && ratio.value <= 0) return false
  if (maxPositions.value <= 0) return false
  if (stopLoss.value <= 0) return false
  return true
})

/* -------------------------
CONFIRM HANDLER
------------------------- */
function confirm() {
  if (!isValid.value) return

  emit('confirm', {
    traderId: props.trader.id,
    traderName: props.trader.name,

    mode: mode.value,
    fixedAmount: fixedAmount.value,
    ratio: ratio.value,

    maxPositions: maxPositions.value,
    stopLoss: stopLoss.value,
    takeProfit: takeProfit.value,

    copySide: copySide.value,
  })

  emit('close')
}
</script>

<template>
  <div class="fixed inset-0 z-50 bg-black/60">

    <!-- SHEET -->
    <div
      class="fixed bottom-0 left-0 right-0
             bg-black border-t border-white/10
             rounded-t-2xl"
    >
      <div class="max-w-sm mx-auto px-4 pb-6">

        <!-- HEADER -->
        <div class="flex justify-between items-center py-4">
          <span class="font-semibold text-[15px]">
            Copy {{ trader.name }}
          </span>

          <button @click="emit('close')">
            <X class="w-5 h-5 text-white/70" />
          </button>
        </div>

        <!-- MODE -->
        <div class="grid grid-cols-2 gap-2 mb-4">
          <button
            class="py-2 rounded-lg text-xs"
            :class="mode === 'fixed'
              ? 'bg-green-500 text-black'
              : 'bg-[#151a21] text-gray-400'"
            @click="mode = 'fixed'"
          >
            Fixed Amount
          </button>

          <button
            class="py-2 rounded-lg text-xs"
            :class="mode === 'ratio'
              ? 'bg-green-500 text-black'
              : 'bg-[#151a21] text-gray-400'"
            @click="mode = 'ratio'"
          >
            Ratio Copy
          </button>
        </div>

        <!-- FIXED -->
        <div v-if="mode === 'fixed'" class="input-box">
          <label>Amount per trade (USDT)</label>
          <input v-model.number="fixedAmount" type="number" />
        </div>

        <!-- RATIO -->
        <div v-if="mode === 'ratio'" class="input-box">
          <label>Copy ratio (1x = same size)</label>
          <input v-model.number="ratio" type="number" step="0.1" />
        </div>

        <!-- MAX POSITIONS -->
        <div class="input-box">
          <label>Max Open Positions</label>
          <input v-model.number="maxPositions" type="number" />
        </div>

        <!-- STOP LOSS -->
        <div class="input-box">
          <label>Account Stop-Loss (%)</label>
          <input v-model.number="stopLoss" type="number" />
        </div>

        <!-- TAKE PROFIT -->
        <div class="input-box">
          <label>Take-Profit (%) (optional)</label>
          <input v-model.number="takeProfit" type="number" />
        </div>

        <!-- COPY SIDE -->
        <div class="grid grid-cols-2 gap-2 mb-4">
          <button
            class="py-2 rounded-lg text-xs"
            :class="copySide === 'same'
              ? 'bg-[#1f2630]'
              : 'bg-[#151a21] text-gray-400'"
            @click="copySide = 'same'"
          >
            Same Direction
          </button>

          <button
            class="py-2 rounded-lg text-xs"
            :class="copySide === 'reverse'
              ? 'bg-[#1f2630]'
              : 'bg-[#151a21] text-gray-400'"
            @click="copySide = 'reverse'"
          >
            Reverse Copy
          </button>
        </div>

        <!-- RISK NOTICE -->
        <div class="flex gap-2 text-xs text-yellow-400 mb-4">
          <AlertTriangle class="w-4 h-4" />
          <p>
            Copy trading does not guarantee profit. Stop-loss will
            close all copied positions automatically.
          </p>
        </div>

        <!-- CONFIRM -->
        <button
          class="w-full py-3 rounded-xl font-semibold"
          :class="isValid
            ? 'bg-green-500 text-black'
            : 'bg-[#151a21] text-gray-500'"
          :disabled="!isValid"
          @click="confirm"
        >
          Confirm Copy
        </button>

      </div>
    </div>

  </div>
</template>

<style scoped>
.input-box {
  background: #151a21;
  border-radius: 12px;
  padding: 10px 12px;
  margin-bottom: 12px;
}

label {
  font-size: 11px;
  color: rgba(255,255,255,0.5);
  display: block;
  margin-bottom: 4px;
}

input {
  width: 100%;
  background: transparent;
  outline: none;
  font-size: 14px;
}
</style>