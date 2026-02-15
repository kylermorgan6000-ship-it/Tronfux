<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  Users, CreditCard, BarChart2, Settings,
  FileText, CheckCircle2, DollarSign,
  UserCheck, FileCheck, LogOut,
  ChevronDown, ChevronRight, Bell
} from 'lucide-vue-next'

const router = useRouter()
const adminUser = ref({ name: 'SuperAdmin' })

const drawerOpen = ref(false)
const openMenu = ref([])

const links = [
  {
    name: 'Users',
    icon: Users,
    sub: [
      { name: 'Active Users', icon: UserCheck, path: '/admin/users/active-users' },
      { name: 'All Users', icon: Users, path: '/admin/users/all-users' },
      { name: 'KYC', icon: FileCheck, path: '/admin/users/kyc' },
      { name: 'Withdrawals', icon: DollarSign, path: '/admin/users/withdrawals' },
      { name: 'Billing Codes', icon: FileText, path: '/admin/users/billing-codes' },
    ],
  },
  {
    name: 'Bank Users',
    icon: CreditCard,
    sub: [
      { name: 'Cards', icon: CreditCard, path: '/admin/bank/cards' },
      { name: 'Active Users', icon: UserCheck, path: '/admin/bank/active-users' },
      { name: 'All Users', icon: Users, path: '/admin/bank/all-users' },
      { name: 'Credit User', icon: CreditCard, path: '/admin/bank/credit-user' },
      { name: 'KYC', icon: FileCheck, path: '/admin/bank/kyc' },
      { name: 'Withdrawals', icon: DollarSign, path: '/admin/bank/withdrawals' },
      { name: 'Billing Codes', icon: FileText, path: '/admin/bank/billing-codes' },
    ],
  },
  {
    name: 'Broker Users',
    icon: BarChart2,
    sub: [
      { name: 'All Users', icon: Users, path: '/admin/broker/all-users' },
      { name: 'Credit User', icon: BarChart2, path: '/admin/broker/credit-user' },
      { name: 'Active Trades', icon: CheckCircle2, path: '/admin/broker/active-trades' },
      { name: 'User Investments', icon: DollarSign, path: '/admin/broker/user-investments' },
      { name: 'KYC', icon: FileCheck, path: '/admin/broker/kyc' },
      { name: 'Withdrawals', icon: DollarSign, path: '/admin/broker/withdrawals' },
      { name: 'Billing Codes', icon: FileText, path: '/admin/broker/billing-codes' },
    ],
  },
  { name: 'Admins', icon: Users, path: '/admin/admins' },
  { name: 'Settings', icon: Settings, path: '/admin/settings' },
]

onMounted(() => {
  openMenu.value = links.filter(l => l.sub).map(l => l.name)
})

const toggleMenu = (name) => {
  openMenu.value = openMenu.value.includes(name)
    ? openMenu.value.filter(n => n !== name)
    : [...openMenu.value, name]
}

const navigate = (path) => {
  router.push(path)
  drawerOpen.value = false
}

watch(drawerOpen, v => {
  document.body.style.overflow = v ? 'hidden' : ''
})
</script>

<template>
  <div class="flex min-h-screen bg-[#0B0E11] text-white">

    <!-- Overlay -->
    <div
      v-if="drawerOpen"
      @click="drawerOpen = false"
      class="fixed inset-0 bg-black/40 z-[9998]"
    />

    <!-- Drawer -->
    <aside
      :class="[
        'fixed top-0 left-0 h-screen w-[260px] bg-[#0B0E11] border-r border-[#1E2329] z-[9999] transition-transform overflow-y-auto',
        drawerOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >

      <!-- SITE ICON / LOGO -->
      <div class="flex items-center justify-center py-4 border-b border-[#1E2329]">
        <img src="" alt="Site" class="w-10 h-10 rounded-md" />
      </div>

      <!-- Menu -->
      <div class="p-2 space-y-2">

        <template v-for="link in links" :key="link.name">
          <!-- Dropdown -->
          <div v-if="link.sub">
            <div
              @click="toggleMenu(link.name)"
              class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-[#1E2329] cursor-pointer"
            >
              <div class="flex items-center gap-3">
                <component :is="link.icon" class="w-5 h-5 text-green-400" />
                <span class="text-sm">{{ link.name }}</span>
              </div>

              <component
                :is="openMenu.includes(link.name) ? ChevronDown : ChevronRight"
                class="w-4 h-4 text-gray-400"
              />
            </div>

            <div v-if="openMenu.includes(link.name)" class="ml-8 mt-1 space-y-1">
              <div
                v-for="sub in link.sub"
                :key="sub.name"
                @click="navigate(sub.path)"
                class="flex items-center gap-2 text-sm px-2 py-1 rounded hover:bg-[#1A1E22] cursor-pointer"
              >
                <component :is="sub.icon" class="w-4 h-4 text-gray-400" />
                {{ sub.name }}
              </div>
            </div>
          </div>

          <!-- Single -->
          <div
            v-else
            @click="navigate(link.path)"
            class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-[#1E2329] cursor-pointer"
          >
            <component :is="link.icon" class="w-5 h-5 text-green-400" />
            <span class="text-sm">{{ link.name }}</span>
          </div>
        </template>

        <!-- Logout -->
        <div
          @click="navigate('/logout')"
          class="flex items-center gap-3 px-3 py-2 mt-3 rounded-lg hover:bg-[#1E2329] cursor-pointer"
        >
          <LogOut class="w-5 h-5 text-red-400" />
          Logout
        </div>

      </div>
    </aside>

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 bg-[#0B0E11] border-b border-[#1E2329] px-4 py-2 z-50 flex items-center justify-between">

      <!-- LEFT -->
      <div class="flex items-center gap-3">
        <button @click="drawerOpen = true" class="text-white text-lg">☰</button>

        <div class="leading-tight">
          <p class="text-xs text-gray-400">Welcome</p>
          <!-- NOT GREEN -->
          <p class="text-sm font-semibold text-white">{{ adminUser.name }}</p>
        </div>
      </div>

      <!-- RIGHT -->
      <div class="flex items-center gap-3">
        <Bell class="w-5 h-5 text-gray-300 hover:text-green-400 cursor-pointer" />
        <img
          src="https://i.pravatar.cc/40?img=3"
          class="w-8 h-8 rounded-full border border-green-400"
        />
      </div>

    </header>

    <main class="pt-14 flex-1">
      <slot />
    </main>
  </div>
</template>

<style scoped>
/* hide scrollbar but keep scroll */
aside::-webkit-scrollbar {
  width: 0px;
}
aside {
  scrollbar-width: none;
}
</style>