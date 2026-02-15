<template>
  <input
    :type="type"
    :placeholder="placeholder"
    :disabled="disabled"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    :class="[
      'w-full px-4 py-2 rounded-md border border-green-400/30',
      'bg-black text-white placeholder-gray-500',
      'focus:outline-none focus:border-green-400 focus:ring-1 focus:ring-green-400/50',
      'disabled:opacity-50 disabled:cursor-not-allowed',
      customClass
    ]"
    v-bind="$attrs"
  />
</template>

<script setup>
defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  customClass: {
    type: String,
    default: ''
  }
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
input::placeholder {
  color: rgb(107 114 128 / 0.5);
}
</style>
