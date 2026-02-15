<script setup lang="ts">
// Theme preference state
import { ref } from 'vue'

const theme = ref('dark')

const handleThemeChange = (newTheme) => {
  theme.value = newTheme
  localStorage.setItem('theme', newTheme)
  if (newTheme === 'dark') {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
}
</script>

<template>
  <div class="min-h-screen bg-black text-white p-6">
    <div class="max-w-md mx-auto">
      <h1 class="text-2xl font-bold mb-2">Appearance Settings</h1>
      <p class="text-gray-400 mb-6">Customize your app appearance</p>

      <div class="space-y-4">
        <div class="bg-[#151a20] rounded-lg p-4">
          <label class="block text-sm font-medium mb-3">Theme</label>
          <div class="space-y-2">
            <button
              @click="handleThemeChange('dark')"
              :class="[
                'w-full p-3 rounded border text-left transition',
                theme === 'dark'
                  ? 'border-green-500 bg-green-500/10'
                  : 'border-white/10 hover:border-white/20'
              ]"
            >
              🌙 Dark Mode
            </button>
            <button
              @click="handleThemeChange('light')"
              :class="[
                'w-full p-3 rounded border text-left transition',
                theme === 'light'
                  ? 'border-green-500 bg-green-500/10'
                  : 'border-white/10 hover:border-white/20'
              ]"
            >
              ☀️ Light Mode
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
