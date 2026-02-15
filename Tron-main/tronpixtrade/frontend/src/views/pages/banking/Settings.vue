<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import FooterLink from '@/components/FooterLink.vue'
import {
  User,
  Mail,
  Phone,
  Lock,
  Shield,
  LogOut,
  ChevronRight,
  Key,
  Bell,
  CreditCard,
  ListChecks,
} from 'lucide-vue-next'
import { fetchUser, changePassword, logout as apiLogout } from '@/lib/api'

/* Initialize router */
const router = useRouter()

/* AUTH USER */
const user = ref({
  name: '',
  kyc_status: 'not_submitted'
})

/* MODAL */
const activeModal = ref(null)

/* PASSWORD FORM */
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const passwordData = reactive({
  current: '',
  new: '',
  confirm: '',
})

/* MENU */
const menuItems = [
  { icon: Shield, key: 'security', label: 'Security Settings', description: '2FA, login history' },
  { icon: Bell, key: 'notifications', label: 'Notifications', description: 'Email and push preferences' },
  { icon: Key, key: 'api', label: 'API Keys', description: 'Manage API access' },
]

/* KYC */
const kycStatus = computed(() => user.value.kyc_status ?? 'not_submitted')

const kycText = computed(() => {
  if (kycStatus.value === 'approved') return 'Verified'
  if (kycStatus.value === 'pending') return 'KYC Pending'
  if (kycStatus.value === 'rejected') return 'KYC Rejected'
  return 'Unverified'
})

const kycBadgeClass = computed(() => {
  if (kycStatus.value === 'approved') return 'bg-green-500/20 text-green-400'
  if (kycStatus.value === 'pending') return 'bg-yellow-500/20 text-yellow-400'
  if (kycStatus.value === 'rejected') return 'bg-red-500/20 text-red-400'
  return 'bg-gray-500/20 text-gray-400'
})

/* ---------------- ACTIONS ---------------- */
const closeModal = () => {
  activeModal.value = null
  errorMessage.value = ''
  successMessage.value = ''
}

const handlePasswordChange = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  if (!passwordData.current) {
    errorMessage.value = 'Current password is required'
    return
  }

  if (passwordData.new.length < 8) {
    errorMessage.value = 'Password must be at least 8 characters'
    return
  }

  if (passwordData.new !== passwordData.confirm) {
    errorMessage.value = 'Passwords do not match'
    return
  }

  loading.value = true

  try {
    await changePassword(passwordData.current, passwordData.new, passwordData.confirm)
    successMessage.value = 'Password updated successfully'

    setTimeout(() => {
      passwordData.current = ''
      passwordData.new = ''
      passwordData.confirm = ''
      closeModal()
    }, 1000)
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Failed to update password'
  } finally {
    loading.value = false
  }
}

const handleLogout = async () => {
  try {
    await apiLogout()
    router.push('/auth/login')
  } catch (err) {
    console.error('Logout failed:', err)
  }
}

/* ---------------- INIT ---------------- */
onMounted(async () => {
  try {
    const u = await fetchUser()
    user.value = u
  } catch (err) {
    console.error('Failed to fetch user:', err)
    router.push('/auth/login')
  }
})
</script>

<template>
  <div class="bg-[#0f1215] min-h-screen text-white pb-24">

    <!-- TOP PROFILE HEADER (OPAY STYLE BUT DARK) -->
    <div class="px-4 pt-6 pb-4 relative">

      <div class="flex items-start justify-between">
        <!-- LEFT -->
        <div class="flex items-center gap-3">
          <div class="h-14 w-14 rounded-full bg-white/10 flex items-center justify-center text-xl font-bold text-green-400">
            {{ user.name?.charAt(0) ?? '?' }}
          </div>

          <div>
            <p class="text-lg font-semibold">
              Hi, {{ user.name }}
            </p>

            <button
              @click="router.push('/banking/kyc')"
              class="mt-1 inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px]
                     bg-white/10 border border-white/10 text-white/80 hover:bg-white/15 transition"
            >
              <Shield class="w-4 h-4 text-green-400" />
              Upgrade to Tier 3
            </button>
          </div>
        </div>

        <!-- RIGHT ICON -->
        <button
          @click="activeModal = 'security'"
          class="h-11 w-11 rounded-xl bg-white/5 border border-white/10
                 flex items-center justify-center hover:bg-white/10 transition"
        >
          <Shield class="w-6 h-6 text-green-400" />
        </button>
      </div>

      <!-- TOTAL BALANCE SECTION -->
      <div class="mt-6">
        <div class="flex items-center gap-2 text-white/60 text-sm">
          <span>Total Balance</span>
          <button class="opacity-70 hover:opacity-100 transition">
            👁️
          </button>
        </div>

        <p class="text-3xl font-bold mt-1 tracking-wider">
          ****
        </p>

        <div class="mt-3 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10">
          <span class="text-sm text-white/70">Interest Credited Today</span>
          <span class="text-green-400 font-semibold">****</span>
        </div>
      </div>
    </div>

    <!-- SAFETY TIP BANNER -->
    <div class="px-4">
      <div class="rounded-2xl bg-gradient-to-r from-green-600 to-emerald-700 p-4 flex items-center justify-between">
        <div>
          <p class="font-semibold text-white text-lg leading-tight">
            4 Safety Tips
          </p>
          <p class="text-white/90 text-sm">
            Make your account more secure.
          </p>
        </div>

        <button
          @click="activeModal = 'security'"
          class="px-5 py-2 rounded-full bg-white text-green-700 font-semibold text-sm shadow hover:scale-[1.02] active:scale-[0.98] transition"
        >
          View
        </button>
      </div>
    </div>

    <!-- MENU LIST -->
    <div class="px-4 mt-5">
      <div class="bg-[#151a20] rounded-2xl overflow-hidden border border-white/5">

        <!-- Transaction History -->
        <button
          @click="router.push('/banking/transaction-history')"
          class="w-full flex items-center justify-between px-4 py-4 hover:bg-white/5 transition"
        >
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-white/5 flex items-center justify-center">
              <ListChecks class="w-5 h-5 text-green-400" />
            </div>
            <div class="text-left">
              <p class="font-medium">Transaction History</p>
            </div>
          </div>
          <ChevronRight class="text-white/40" />
        </button>

        <div class="h-[1px] bg-white/5" />

        <!-- Account Limits -->
        <button
          @click="router.push('/settings/appearance')"
          class="w-full flex items-center justify-between px-4 py-4 hover:bg-white/5 transition"
        >
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-white/5 flex items-center justify-center">
              <Shield class="w-5 h-5 text-green-400" />
            </div>
            <div class="text-left">
              <p class="font-medium">Account Limits</p>
              <p class="text-sm text-white/50">View your transaction limits</p>
            </div>
          </div>
          <ChevronRight class="text-white/40" />
        </button>

        <div class="h-[1px] bg-white/5" />

        <!-- Bank Card / Account -->
        <button
          @click="router.push('/banking/cards')"
          class="w-full flex items-center justify-between px-4 py-4 hover:bg-white/5 transition"
        >
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-white/5 flex items-center justify-center">
              <CreditCard class="w-5 h-5 text-green-400" />
            </div>
            <div class="text-left">
              <p class="font-medium">Bank Card/Account</p>
              <p class="text-sm text-white/50">Add payment option</p>
            </div>
          </div>
          <ChevronRight class="text-white/40" />
        </button>

        <div class="h-[1px] bg-white/5" />

        <!-- KYC -->
        <button
          @click="router.push('/banking/kyc')"
          class="w-full flex items-center justify-between px-4 py-4 hover:bg-white/5 transition"
        >
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-white/5 flex items-center justify-center">
              <Shield class="w-5 h-5 text-yellow-400" />
            </div>
            <div class="text-left">
              <p class="font-medium">KYC Verification</p>
              <p class="text-sm text-white/50">Status: {{ kycText }}</p>
            </div>
          </div>

          <span class="text-[11px] px-3 py-1 rounded-full" :class="kycBadgeClass">
            {{ kycText }}
          </span>
        </button>

      </div>
    </div>

    <!-- SECURITY SECTION -->
    <div class="px-4 mt-6">
      <div class="bg-[#151a20] rounded-2xl overflow-hidden border border-white/5">

        <!-- Change Password -->
        <button
          @click="activeModal = 'password'"
          class="w-full flex items-center justify-between px-4 py-4 hover:bg-white/5 transition"
        >
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-white/5 flex items-center justify-center">
              <Lock class="w-5 h-5 text-green-400" />
            </div>
            <div class="text-left">
              <p class="font-medium">Change Password</p>
              <p class="text-sm text-white/50">Update your login password</p>
            </div>
          </div>
          <ChevronRight class="text-white/40" />
        </button>

        <div class="h-[1px] bg-white/5" />

        <!-- Notifications -->
        <button
          @click="activeModal = 'notifications'"
          class="w-full flex items-center justify-between px-4 py-4 hover:bg-white/5 transition"
        >
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-white/5 flex items-center justify-center">
              <Bell class="w-5 h-5 text-green-400" />
            </div>
            <div class="text-left">
              <p class="font-medium">Notifications</p>
              <p class="text-sm text-white/50">Email and push preferences</p>
            </div>
          </div>
          <ChevronRight class="text-white/40" />
        </button>

        <div class="h-[1px] bg-white/5" />

        <!-- API Keys -->
        <button
          @click="activeModal = 'api'"
          class="w-full flex items-center justify-between px-4 py-4 hover:bg-white/5 transition"
        >
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-white/5 flex items-center justify-center">
              <Key class="w-5 h-5 text-green-400" />
            </div>
            <div class="text-left">
              <p class="font-medium">API Keys</p>
              <p class="text-sm text-white/50">Manage API access</p>
            </div>
          </div>
          <ChevronRight class="text-white/40" />
        </button>

      </div>
    </div>

    <!-- LOGOUT -->
    <div class="px-4 mt-6">
      <div class="bg-[#151a20] rounded-2xl border border-white/5 overflow-hidden">
        <button
          @click="handleLogout"
          class="w-full flex items-center justify-between px-4 py-4 hover:bg-red-500/10 transition"
        >
          <div class="flex items-center gap-3 text-red-400">
            <div class="h-10 w-10 rounded-full bg-red-500/10 flex items-center justify-center">
              <LogOut class="w-5 h-5" />
            </div>
            <span class="font-medium">Logout</span>
          </div>
          <ChevronRight class="text-white/40" />
        </button>
      </div>
    </div>

    <!-- FULL PAGE MODAL (UNCHANGED) -->
    <div v-if="activeModal" class="fixed inset-0 z-50 bg-[#0b0f14] animate-slide-up">
      <div class="flex items-center gap-3 p-4 border-b border-white/10">
        <button @click="closeModal">←</button>
        <h2 class="font-semibold">
          {{ activeModal === 'password' ? 'Change Password' :
             activeModal === 'security' ? 'Security Settings' :
             activeModal === 'notifications' ? 'Notifications' :
             'API Keys' }}
        </h2>
      </div>

      <div class="p-5 space-y-4" v-if="activeModal === 'password'">
        <input v-model="passwordData.current" type="password" placeholder="Current password" class="w-full p-3 bg-white/5 rounded-lg" />
        <input v-model="passwordData.new" type="password" placeholder="New password" class="w-full p-3 bg-white/5 rounded-lg" />
        <input v-model="passwordData.confirm" type="password" placeholder="Confirm password" class="w-full p-3 bg-white/5 rounded-lg" />

        <p v-if="errorMessage" class="text-red-400 text-sm">{{ errorMessage }}</p>
        <p v-if="successMessage" class="text-green-400 text-sm">{{ successMessage }}</p>

        <button
          @click="handlePasswordChange"
          class="w-full p-3 bg-green-500 text-black rounded-lg"
        >
          {{ loading ? 'Updating…' : 'Update Password' }}
        </button>
      </div>
    </div>

    <FooterLink />
  </div>
</template>

<style scoped>
@keyframes slide-up {
  from { transform: translateY(100%); }
  to { transform: translateY(0); }
}
.animate-slide-up {
  animation: slide-up 0.25s ease-out;
}
</style>