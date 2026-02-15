<script setup lang="ts">
import { ref } from 'vue'
import api from '@/lib/api'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const processing = ref(false)
const errors = ref({})
const message = ref('')

const form = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const handleSubmit = async () => {
  errors.value = {}
  message.value = ''
  processing.value = true

  try {
    await api.post('/user/password', form.value)
    message.value = 'Password updated successfully'
    form.value = {
      current_password: '',
      password: '',
      password_confirmation: ''
    }
  } catch (err) {
    errors.value = err.response?.data?.errors || { general: err.response?.data?.message || err.message }
  } finally {
    processing.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-black text-white p-6">
    <div class="max-w-md mx-auto">
      <h1 class="text-2xl font-bold mb-2">Update Password</h1>
      <p class="text-gray-400 mb-6">Ensure your account is using a long, random password to stay secure</p>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <Label for="current_password">Current password</Label>
          <Input
            id="current_password"
            v-model="form.current_password"
            type="password"
            placeholder="Current password"
            autocomplete="current-password"
          />
          <InputError :message="errors.current_password" />
        </div>

        <div>
          <Label for="password">New password</Label>
          <Input
            id="password"
            v-model="form.password"
            type="password"
            placeholder="New password"
            autocomplete="new-password"
          />
          <InputError :message="errors.password" />
        </div>

        <div>
          <Label for="password_confirmation">Confirm password</Label>
          <Input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            placeholder="Confirm password"
            autocomplete="new-password"
          />
          <InputError :message="errors.password_confirmation" />
        </div>

        <div class="flex items-center gap-4">
          <Button type="submit" :disabled="processing">Save password</Button>
          <transition enter-active-class="transition ease-in-out" leave-active-class="transition ease-in-out">
            <p v-show="message" class="text-sm text-green-400">{{ message }}</p>
          </transition>
        </div>
      </form>
    </div>
  </div>
</template>
