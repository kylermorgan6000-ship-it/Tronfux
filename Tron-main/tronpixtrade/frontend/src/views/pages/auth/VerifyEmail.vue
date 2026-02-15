<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api, { csrf } from '@/lib/api'
import { Button } from '@/components/ui/button'
import { CheckCircle2 } from 'lucide-vue-next'

const router = useRouter()
const processing = ref(false)
const successMessage = ref('')
const errors = ref({})
const email = ref('') // optional, can prefill from registration response

const resendVerification = async () => {
  if (!email.value) {
    errors.value = { message: 'Email is required to resend verification.' }
    return
  }

  processing.value = true
  errors.value = {}
  successMessage.value = ''

  try {
    await csrf()
    const response = await api.post('/email/verification-notification', { email: email.value })
    successMessage.value = response.data.message
  } catch (err: any) {
    errors.value = { message: err.response?.data?.message || err.message }
  } finally {
    processing.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-black px-4">
    <div class="max-w-md w-full glass-soft rounded-2xl p-8 text-center space-y-6">

      <!-- Icon -->
      <div class="flex justify-center">
        <CheckCircle2 class="w-16 h-16 text-green-400 animate-[scaleIn_0.5s_ease-out]" />
      </div>

      <!-- Title -->
      <h2 class="text-2xl font-bold text-white">Verify Your Email</h2>

      <!-- Description -->
      <p class="text-sm text-white/60">
        A verification link has been sent to your email address. <br>
        Please check your inbox and click the link to verify your account.
      </p>

      <!-- Success Message -->
      <p v-if="successMessage" class="text-green-400 text-sm">{{ successMessage }}</p>

      <!-- Resend Email Button -->
      <Button class="w-full" @click="resendVerification" :disabled="processing">
        <span v-if="!processing">Resend Email</span>
        <Spinner v-else />
      </Button>

      <!-- Back to Login -->
      <Button variant="outline" class="w-full" @click="router.push('/auth/login')">
        Back to Login
      </Button>

      <p class="text-[10px] text-white/40 mt-4">
        Didn’t receive the email? Check your spam folder.
      </p>
    </div>
  </div>
</template>