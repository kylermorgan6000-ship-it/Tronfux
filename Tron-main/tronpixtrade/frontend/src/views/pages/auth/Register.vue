<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { registerUser } from '@/lib/api' // <-- use the centralized API
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'
import AuthBase from '@/layouts/AuthLayout.vue'

const router = useRouter()
const accountType = ref('')
const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const country = ref('NG')
const processing = ref(false)
const errors = ref({})

const submitForm = async () => {
  processing.value = true
  errors.value = {}

  try {
    // 🔹 Call centralized API function
    await registerUser({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
      account_type: accountType.value,
      country: country.value,
    })

    // Redirect to verify-email page
    router.push({
      path: '/auth/verify-email',
      query: { email: email.value }
    })

  } catch (err: any) {
    // Capture validation or generic errors
    errors.value = err.response?.data?.errors || { message: err.response?.data?.message || err.message }
  } finally {
    processing.value = false
  }
}
</script>
<template>
<AuthBase
  title="Create an account"
  description="Choose your account type and enter your details"
>
  <form @submit.prevent="submitForm" class="glass-soft flex flex-col gap-6">
    <!-- ACCOUNT TYPE -->
    <div class="grid gap-4 mb-4">
      <p class="text-sm font-medium text-white/80">Select Account Type</p>

      <div class="grid grid-cols-2 gap-4">
        <!-- BANK ACCOUNT -->
        <div
          class="rounded-2xl p-4 cursor-pointer border transition-all duration-200 hover:border-green-400"
          :class="accountType === 'banking' ? 'border-green-500 bg-green-500/10' : 'border-white/20'"
          @click="accountType = 'banking'"
        >
          <div class="flex items-center gap-2 mb-2">
            <Wallet class="w-6 h-6 text-green-400" />
            <p class="font-semibold text-white">Bank Account</p>
          </div>
          <p class="text-[11px] text-white/50 mb-2">
            Access full banking features: deposits, withdrawals, transfers.
          </p>
          <ul class="text-[10px] text-white/40 space-y-1">
            <li>• Secure wallet & accounts</li>
            <li>• Savings & investment options</li>
            <li>• Bill payments & transfers</li>
          </ul>
        </div>

        <!-- BROKER ACCOUNT -->
        <div
          class="rounded-2xl p-4 cursor-pointer border transition-all duration-200 hover:border-blue-400"
          :class="accountType === 'broker' ? 'border-blue-500 bg-blue-500/10' : 'border-white/20'"
          @click="accountType = 'broker'"
        >
          <div class="flex items-center gap-2 mb-2">
            <BarChart class="w-6 h-6 text-blue-400" />
            <p class="font-semibold text-white">Broker Account</p>
          </div>
          <p class="text-[11px] text-white/50 mb-2">
            Access trading & investment platform with real-time charts.
          </p>
          <ul class="text-[10px] text-white/40 space-y-1">
            <li>• Trade stocks & crypto</li>
            <li>• Real-time analytics</li>
            <li>• Portfolio tracking</li>
          </ul>
        </div>
      </div>

      <InputError :message="errors.account_type" />
    </div>

    <!-- USER DETAILS -->
    <div class="grid gap-6">
      <!-- NAME -->
      <div class="grid gap-2">
        <Label for="name">Full Name</Label>
        <Input
          id="name"
          name="name"
          type="text"
          v-model="name"
          required
          autofocus
          autocomplete="name"
          placeholder="Full name"
        />
        <InputError :message="errors.name" />
      </div>

      <!-- EMAIL -->
      <div class="grid gap-2">
        <Label for="email">Email address</Label>
        <Input
          id="email"
          name="email"
          type="email"
          v-model="email"
          required
          autocomplete="email"
          placeholder="email@example.com"
        />
        <InputError :message="errors.email" />
      </div>

      <!-- PASSWORD -->
      <div class="grid gap-2">
        <Label for="password">Password</Label>
        <Input
          id="password"
          name="password"
          type="password"
          v-model="password"
          required
          autocomplete="new-password"
          placeholder="Password"
        />
        <InputError :message="errors.password" />
      </div>

      <!-- CONFIRM PASSWORD -->
      <div class="grid gap-2">
        <Label for="password_confirmation">Confirm password</Label>
        <Input
          id="password_confirmation"
          name="password_confirmation"
          type="password"
          v-model="password_confirmation"
          required
          autocomplete="new-password"
          placeholder="Confirm password"
        />
        <InputError :message="errors.password_confirmation" />
      </div>

      <!-- SUBMIT -->
      <Button
        type="submit"
        class="mt-2 w-full"
        :disabled="!accountType || processing"
      >
        <Spinner v-if="processing" />
        <span v-else>Create account</span>
      </Button>
    </div>

    <!-- LOGIN LINK -->
    <div class="text-center text-sm text-muted-foreground mt-2">
      Already have an account?
      <router-link to="/auth/login" class="underline underline-offset-4">Log in</router-link>
    </div>
  </form>
</AuthBase>
</template>

<style scoped>
.border-green-500, 
.border-blue-500 {
  border-width: 2px;
}
</style>