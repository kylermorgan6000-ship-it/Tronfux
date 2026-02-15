<script setup lang="ts">
defineProps({
  balance: Number
})

const emit = defineEmits(['select'])
</script>

<template>
  <div class="flex justify-between text-xs text-gray-400">
    <button
      v-for="p in [25, 50, 75, 100]"
      :key="p"
      @click="emit('select', p)"
    >
      {{ p }}%
    </button>
  </div>
</template>