<template>
  <p v-if="message" :class="['text-sm text-red-500 mt-1', customClass]">
    {{ message }}
  </p>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    default: ''
  },
  customClass: {
    type: String,
    default: ''
  }
})
</script>

<style scoped>
/* Optional: you can style for animations, spacing, or transitions */
</style>