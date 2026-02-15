<script setup lang="ts">
defineProps({ trades: Array })
</script>

<template>
  <div class="bg-[#0b0f14] rounded-xl p-2 text-xs">
    <div class="text-gray-400 mb-1">Recent Trades</div>

    <div
      v-for="t in trades"
      :key="t.t"
      :class="t.m ? 'text-red-400' : 'text-green-400'"
      class="flex justify-between"
    >
      <span>{{ t.p }}</span>
      <span>{{ t.q }}</span>
    </div>
  </div>
</template>