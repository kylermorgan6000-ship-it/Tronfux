<script setup lang="ts">
import AdminLayout from './AdminLayout.vue'
import { ref, reactive, onMounted } from 'vue'
import { User, Key, Bell, ShieldCheck, X, RefreshCw } from 'lucide-vue-next'
import api, { fetchUser, changePassword, updateProfileUser } from '@/lib/api'

// ------------------- STATE -------------------
const profile = reactive({
  name: '',
  email: '',
  avatar: '',
})

const password = reactive({
  current: '',
  new: '',
  confirm: '',
})

const systemSettings = reactive({
  notifications: true,
  darkMode: true,
  apiAccess: false,
})

const securitySettings = reactive({
  twoFA: true,
  ipWhitelist: [] as string[],
})

const showProfileModal = ref(false)
const showPasswordModal = ref(false)
const loading = ref(false)

// ------------------- LOAD CURRENT USER -------------------
const loadProfile = async () => {
  loading.value = true
  try {
    const user = await fetchUser()
    profile.name = user.name
    profile.email = user.email
    profile.avatar = user.avatar || ''
  } catch (e) {
    console.error('Failed to fetch profile', e)
  } finally {
    loading.value = false
  }
}

onMounted(() => { void loadProfile() })

// ------------------- HELPERS -------------------
const updateProfile = async () => {
  loading.value = true
  try {
    await updateProfileUser({ name: profile.name, email: profile.email })
    showProfileModal.value = false
    alert('Profile updated successfully!')
  } catch (e) {
    console.error('Failed to update profile', e)
    alert('Profile update failed')
  } finally {
    loading.value = false
  }
}

const updatePassword = async () => {
  if (password.new !== password.confirm) {
    alert('Passwords do not match!')
    return
  }
  loading.value = true
  try {
    await changePassword(password.current, password.new, password.confirm)
    showPasswordModal.value = false
    password.current = ''
    password.new = ''
    password.confirm = ''
    alert('Password changed successfully!')
  } catch (e) {
    console.error('Failed to change password', e)
    alert('Password change failed')
  } finally {
    loading.value = false
  }
}
</script>

<template>
<AdminLayout>
<section class="p-3 text-white min-h-screen space-y-6">

  <!-- HEADER -->
  <div class="flex justify-between items-center">
    <h1 class="text-xl font-bold">Settings</h1>
  </div>

  <!-- PROFILE -->
  <div class="bg-white/5 p-4 rounded-xl border border-white/10">
    <div class="flex justify-between items-center">
      <div class="flex gap-3 items-center">
        <User class="w-6 h-6 text-green-400"/>
        <div>
          <p class="font-semibold">{{ profile.name }}</p>
          <p class="text-xs text-white/60">{{ profile.email }}</p>
        </div>
      </div>
      <button @click="showProfileModal = true" class="px-3 py-1 rounded-lg bg-green-500 text-black text-sm">Edit</button>
    </div>
  </div>

  <!-- PASSWORD -->
  <div class="bg-white/5 p-4 rounded-xl border border-white/10 flex justify-between items-center">
    <div class="flex gap-3 items-center">
      <Key class="w-6 h-6 text-green-400"/>
      <p class="font-semibold">Change Password</p>
    </div>
    <button @click="showPasswordModal = true" class="px-3 py-1 rounded-lg bg-green-500 text-black text-sm">Update</button>
  </div>

  <!-- SYSTEM SETTINGS -->
  <div class="bg-white/5 p-4 rounded-xl border border-white/10 space-y-3">
    <h2 class="font-semibold mb-2">System Settings</h2>
    <div class="flex justify-between items-center">
      <div class="flex gap-2 items-center"><Bell class="w-5 h-5 text-green-400"/> Notifications</div>
      <input type="checkbox" v-model="systemSettings.notifications" class="accent-green-400"/>
    </div>
    <div class="flex justify-between items-center">
      <div class="flex gap-2 items-center"><ShieldCheck class="w-5 h-5 text-green-400"/> API Access</div>
      <input type="checkbox" v-model="systemSettings.apiAccess" class="accent-green-400"/>
    </div>
    <div class="flex justify-between items-center">
      <div class="flex gap-2 items-center"><User class="w-5 h-5 text-green-400"/> Dark Mode</div>
      <input type="checkbox" v-model="systemSettings.darkMode" class="accent-green-400"/>
    </div>
  </div>

  <!-- SECURITY -->
  <div class="bg-white/5 p-4 rounded-xl border border-white/10 space-y-3">
    <h2 class="font-semibold mb-2">Security</h2>
    <div class="flex justify-between items-center">
      <p class="text-sm">Two-Factor Authentication</p>
      <input type="checkbox" v-model="securitySettings.twoFA" class="accent-green-400"/>
    </div>
    <div class="flex justify-between items-center">
      <p class="text-sm">IP Whitelist</p>
      <button class="px-3 py-1 rounded-lg bg-green-500 text-black text-sm">Manage</button>
    </div>
  </div>

  <!-- MODALS -->

  <!-- PROFILE MODAL -->
  <div v-if="showProfileModal" class="fixed inset-0 bg-black/50 z-[999] flex items-end sm:items-center justify-center px-3">
    <div class="bg-[#0B0E11] w-full sm:max-w-md rounded-t-2xl sm:rounded-xl p-5 space-y-3 relative">
      <button class="absolute top-3 right-3" @click="showProfileModal=false"><X class="w-5 h-5"/></button>
      <h2 class="text-lg font-semibold">Edit Profile</h2>
      <input v-model="profile.name" placeholder="Name" class="w-full p-2 rounded-lg bg-white/5 outline-none"/>
      <input v-model="profile.email" placeholder="Email" type="email" class="w-full p-2 rounded-lg bg-white/5 outline-none"/>
      <button @click="updateProfile" class="mt-3 w-full py-2 rounded-lg bg-green-500 text-black flex justify-center">
        <span v-if="!loading">Save</span>
        <RefreshCw v-else class="w-5 h-5 animate-spin"/>
      </button>
    </div>
  </div>

  <!-- PASSWORD MODAL -->
  <div v-if="showPasswordModal" class="fixed inset-0 bg-black/50 z-[999] flex items-end sm:items-center justify-center px-3">
    <div class="bg-[#0B0E11] w-full sm:max-w-md rounded-t-2xl sm:rounded-xl p-5 space-y-3 relative">
      <button class="absolute top-3 right-3" @click="showPasswordModal=false"><X class="w-5 h-5"/></button>
      <h2 class="text-lg font-semibold">Change Password</h2>
      <input v-model="password.current" placeholder="Current Password" type="password" class="w-full p-2 rounded-lg bg-white/5 outline-none"/>
      <input v-model="password.new" placeholder="New Password" type="password" class="w-full p-2 rounded-lg bg-white/5 outline-none"/>
      <input v-model="password.confirm" placeholder="Confirm Password" type="password" class="w-full p-2 rounded-lg bg-white/5 outline-none"/>
      <button @click="updatePassword" class="mt-3 w-full py-2 rounded-lg bg-green-500 text-black flex justify-center">
        <span v-if="!loading">Update</span>
        <RefreshCw v-else class="w-5 h-5 animate-spin"/>
      </button>
    </div>
  </div>

</section>
</AdminLayout>
</template>

<style scoped>
</style>