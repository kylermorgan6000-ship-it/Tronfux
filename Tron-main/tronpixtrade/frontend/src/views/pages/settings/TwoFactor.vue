<script setup lang="ts">
import { ref, onUnmounted } from 'vue'
import api, { csrf } from '@/lib/api'
import { Button } from '@/components/ui/button'
import { ShieldBan, ShieldCheck } from 'lucide-vue-next'

const twoFactorEnabled = ref(false)
const showSetupModal = ref(false)
const loading = ref(false)

const enableTwoFactor = async () => {
  loading.value = true
  try {
    await csrf()
    await api.post('/two-factor/enable')
    twoFactorEnabled.value = true
    showSetupModal.value = true
  } catch (err) {
    console.error('Failed to enable 2FA', err)
  } finally {
    loading.value = false
  }
}

const disableTwoFactor = async () => {
  loading.value = true
  try {
    await csrf()
    await api.post('/two-factor/disable')
    twoFactorEnabled.value = false
  } catch (err) {
    console.error('Failed to disable 2FA', err)
  } finally {
    loading.value = false
  }
}

onUnmounted(() => {
  // cleanup
})
</script>

<template>
  <div class="min-h-screen bg-black text-white p-6">
    <div class="max-w-md mx-auto">
      <h1 class="text-2xl font-bold mb-2">Two-Factor Authentication</h1>
      <p class="text-gray-400 mb-6">Secure your account with an additional layer of protection</p>

      <div v-if="!twoFactorEnabled" class="space-y-4">
        <p class="text-sm text-gray-400">When enabled, you'll be prompted for a secure PIN during login using a TOTP app.</p>
        <Button @click="enableTwoFactor" :disabled="loading" class="w-full">
          <ShieldCheck class="w-4 h-4 mr-2" />
          Enable 2FA
        </Button>
      </div>

      <div v-else class="space-y-4">
        <div class="bg-green-500/10 border border-green-500/30 rounded p-3">
          <p class="text-green-400 font-semibold">✓ Two-Factor Authentication Enabled</p>
        </div>
        <Button @click="disableTwoFactor" :disabled="loading" class="w-full bg-red-600 hover:bg-red-700">
          <ShieldBan class="w-4 h-4 mr-2" />
          Disable 2FA
        </Button>
      </div>
    </div>
  </div>
</template>
