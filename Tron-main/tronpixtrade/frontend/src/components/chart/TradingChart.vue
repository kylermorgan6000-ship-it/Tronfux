<script setup lang="ts">
import { onMounted, onBeforeUnmount, ref, watch } from 'vue'

const props = defineProps()

const containerId = 'tv_chart_container'
const widget = ref(null)

function loadTradingViewScript() {
  return new Promise((resolve) => {
    if (window.TradingView) {
      resolve()
      return
    }

    const script = document.createElement('script')
    script.src = 'https://s3.tradingview.com/tv.js'
    script.onload = () => resolve()
    document.head.appendChild(script)
  })
}

async function initChart() {
  await loadTradingViewScript()

  if (!window.TradingView) return

  widget.value = new window.TradingView.widget({
    container_id: containerId,
    autosize: true,
    symbol: (props.symbol ?? 'BNBUSDT').toUpperCase(),
    interval: '15',
    timezone: 'Etc/UTC',
    theme: 'dark',
    style: '1',
    locale: 'en',
    toolbar_bg: '#0b0f14',
    hide_top_toolbar: false,
    hide_legend: false,
    allow_symbol_change: true,
    save_image: false,
  })
}

onMounted(() => {
  initChart()
})

onBeforeUnmount(() => {
  if (widget.value && widget.value.remove) {
    widget.value.remove()
    widget.value = null
  }
})

watch(
  () => props.symbol,
  (s) => {
    if (widget.value && s) {
      widget.value.setSymbol(s.toUpperCase(), '15')
    }
  }
)
</script>

<template>
  <div
    :id="containerId"
    class="w-full h-[390px] bg-black rounded-xl overflow-hidden bg-[#0b0f14]"
  />
</template>