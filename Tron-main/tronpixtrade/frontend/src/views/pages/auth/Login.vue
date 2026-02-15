<script setup lang="ts">  
import { ref } from 'vue'  
import { useRouter } from 'vue-router'  
import { login } from '@/lib/api' // <-- import login from API module  
import InputError from '@/components/InputError.vue'  
import { Button } from '@/components/ui/button'  
import { Checkbox } from '@/components/ui/checkbox'  
import { Input } from '@/components/ui/input'  
import { Label } from '@/components/ui/label'  
import { Spinner } from '@/components/ui/spinner'  
import AuthBase from '@/layouts/AuthLayout.vue'  
  
const router = useRouter()  
  
const email = ref('')  
const password = ref('')  
const remember = ref(false)  
const processing = ref(false)  
const errors = ref({})  
  
const submitForm = async () => {
  processing.value = true
  errors.value = {}

  try {
    const user = await login(email.value, password.value)

    // Check email verification correctly
    if (!user.email_verified_at) {
      router.push({
        path: '/auth/verify-email-returning',
        query: { email: user.email }
      })
    } else {
      // Redirect based on account type
      if (user.account_type === 'broker') router.push('/broker/dashboard')
      else if (user.account_type === 'banking') router.push('/banking/dashboard')
      else router.push('/admin/dashboard')
    }

  } catch (err: any) {
    errors.value =
      err.response?.data?.errors || { message: err.response?.data?.message || err.message }
  } finally {
    processing.value = false
  }
}
</script>  

<template>
<AuthBase
  title="Log in to your account"
  description="Enter your email and password below to log in"
>
  <form @submit.prevent="submitForm" class="glass-soft flex flex-col gap-6">
    <div class="grid gap-6">
      <!-- EMAIL -->
      <div class="grid gap-2">
        <Label for="email">Email address</Label>
        <Input
          id="email"
          type="email"
          name="email"
          v-model="email"
          required
          autofocus
          autocomplete="email"
          placeholder="email@example.com"
        />
        <InputError :message="errors.email || errors.message" />
      </div>

      <!-- PASSWORD -->
      <div class="grid gap-2">
        <div class="flex items-center justify-between">
          <Label for="password">Password</Label>
          <router-link to="/auth/forgot-password" class="text-sm text-primary hover:underline">
            Forgot password?
          </router-link>
        </div>
        <Input
          id="password"
          type="password"
          name="password"
          v-model="password"
          required
          autocomplete="current-password"
          placeholder="Password"
        />
        <InputError :message="errors.password" />
      </div>

      <!-- REMEMBER ME -->
      <div class="flex items-center space-x-3">
        <Checkbox id="remember" name="remember" v-model="remember" />
        <Label for="remember">Remember me</Label>
      </div>

      <!-- SUBMIT -->
      <Button
        type="submit"
        class="mt-4 w-full"
        :disabled="processing"
      >
        <Spinner v-if="processing" />
        <span v-else>Log in</span>
      </Button>
    </div>

    <!-- REGISTER LINK -->
    <div class="text-center text-sm text-muted-foreground mt-2">
      Don't have an account?
      <router-link to="/auth/register" class="text-primary hover:underline">Sign up</router-link>
    </div>
  </form>
</AuthBase>
</template>