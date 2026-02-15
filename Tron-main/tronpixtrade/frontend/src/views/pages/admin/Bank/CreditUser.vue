<script setup lang="ts">
import AdminLayout from '../AdminLayout.vue'
import { ref } from 'vue'
import { Loader2, User, Plus, Minus } from 'lucide-vue-next'
import { creditUser, deductUser } from '@/lib/api'

// ------------------- STATE -------------------
const email = ref('')
const amount = ref<number | null>(null)
const currency = ref('USD')
const loading = ref(false)
const message = ref('')

// ------------------- ACTIONS -------------------
const handleCredit = async () => {
  if (!email.value || !amount.value) {
    message.value = 'Email and amount are required'
    return
  }

  loading.value = true
  message.value = ''

  try {
    const res = await creditUser(email.value, amount.value, currency.value)
    message.value = res.message || 'User credited successfully'
  } catch (err: any) {
    message.value = err.response?.data?.message || err.message || 'Error crediting user'
  } finally {
    loading.value = false
  }
}

const handleDeduct = async () => {
  if (!email.value || !amount.value) {
    message.value = 'Email and amount are required'
    return
  }

  loading.value = true
  message.value = ''

  try {
    const res = await deductUser(email.value, amount.value, currency.value)
    message.value = res.message || 'Amount deducted successfully'
  } catch (err: any) {
    message.value = err.response?.data?.message || err.message || 'Error deducting amount'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <AdminLayout>
    <section class="p-3 min-h-screen text-white space-y-4">
      <h1 class="text-xl font-bold flex items-center gap-2"><User class="w-5 h-5 text-green-400" /> Credit / Deduct User</h1>

      <div class="bg-white/5 p-4 rounded-xl border border-white/10 max-w-md">
        <label class="block text-xs text-white/60">User Email</label>
        <input v-model="email" class="w-full p-2 rounded mt-1 bg-transparent border border-white/10" placeholder="user@example.com" />

        <div class="mt-3">
          <label class="block text-xs text-white/60">Amount (USD)</label>
          <input v-model.number="amount" type="number" step="0.01" class="w-full p-2 rounded mt-1 bg-transparent border border-white/10" />
        </div>

        <div class="mt-3 flex gap-2">
          <button @click="handleCredit" class="flex-1 bg-green-500 text-black py-2 rounded flex justify-center items-center gap-2">
            <Plus class="w-4 h-4" />
            <span v-if="!loading">Credit</span>
            <Loader2 v-else class="w-4 h-4 animate-spin" />
          </button>

          <button @click="handleDeduct" class="flex-1 bg-red-500 text-white py-2 rounded flex justify-center items-center gap-2">
            <Minus class="w-4 h-4" />
            <span v-if="!loading">Deduct</span>
            <Loader2 v-else class="w-4 h-4 animate-spin" />
          </button>
        </div>

        <div v-if="message" class="mt-3 text-sm text-white/80">{{ message }}</div>
      </div>
    </section>
  </AdminLayout>
</template>
