<script setup lang="ts">
const modelValue = defineModel()
</script>

<template>
  <input
    type="number"
    class="w-full bg-[#0b0f14] rounded px-2 py-1 text-xs"
    :value="modelValue ?? ''"
    @input="modelValue = $event.target.value
      ? Number($event.target.value)
      : null"
  />
</template>