<script setup>
import AppLogoIcon from './AppLogoIcon.vue';
</script>

<template>
  <div class="flex items-center gap-2">
    <!-- Logo -->
    <div class="flex size-8 items-center justify-center rounded-md bg-transparent">
      <AppLogoIcon
        class="size-4 drop-shadow-[0_0_10px_hsl(160,100%,50%)]"
      />
    </div>

    <!-- Brand name -->
    <span class="text-sm text-white font-semibold leading-tight">
      Tronpix
      <span class="text-green-400 drop-shadow-[0_0_6px_hsl(120,100%,50%)]">
        Trades
      </span>
    </span>
  </div>
</template>