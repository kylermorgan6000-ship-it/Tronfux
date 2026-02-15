<script setup lang="ts">
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import api, { csrf } from '@/lib/api'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'
import AuthBase from '@/layouts/AuthLayout.vue'

const route = useRoute()

// Email comes from login redirect query
const email = ref(route.query.email || '')
const processing = ref(false)
const successMessage = ref('')
const errors = ref({})

const resendVerificationEmail = async () => {
  if (!email.value) {
    errors.value = { message: 'Email is required to resend verification.' }
    return
  }

  processing.value = true
  errors.value = {}
  successMessage.value = ''

  try {
    await csrf() // Ensure CSRF cookie is set

    const response = await api.post('/email/verification-notification', {
      email: email.value
    })

    successMessage.value = response.data.message

  } catch (err: any) {
    errors.value =
      err.response?.data?.errors ||
      { message: err.response?.data?.message || err.message }
  } finally {
    processing.value = false
  }
}
</script>

<template>
<AuthBase
  title="Verify Your Email"
  description="Please verify your email to access your dashboard. Check your inbox or resend the verification email."
>
  <div class="glass-soft flex flex-col gap-6 p-2 max-w-md mx-auto">
    <!-- EMAIL DISPLAY -->
    <div class="grid gap-2">
      <Label for="email">Email address</Label>
      <Input
        id="email"
        type="email"
        name="email"
        v-model="email"
        readonly
      />
      <InputError :message="errors.email || errors.message" />
    </div>

    <!-- SUCCESS MESSAGE -->
    <div v-if="successMessage" class="text-green-500 text-sm flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      {{ successMessage }}
    </div>

    <!-- RESEND BUTTON -->
    <Button
      class="mt-4 w-full"
      @click="resendVerificationEmail"
      :disabled="processing"
    >
      <Spinner v-if="processing" />
      <span v-else>Resend Verification Email</span>
    </Button>
  </div>
</AuthBase>
</template>