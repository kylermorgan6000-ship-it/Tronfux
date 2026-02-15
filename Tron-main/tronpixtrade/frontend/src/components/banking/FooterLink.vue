<script setup lang="ts">
import {
  Home,
  Wallet,
  CreditCard,
  ListChecks,
  Send,
  Settings
} from "lucide-vue-next"

/* Banking footer items */
const footerLinks = [
  { name: 'Home', icon: Home, route: '/bank/dashboard' },
  { name: 'Wallet', icon: Wallet, route: '/bank/wallet' },
  { name: 'Cards', icon: CreditCard, route: '/bank/cards' },
  { name: 'Transactions', icon: ListChecks, route: '/bank/transaction-history' },
  { name: 'Payments', icon: Send, route: '/bank/payments' },
  { name: 'Settings', icon: Settings, route: '/bank/settings' },
]
</script>

<template>
  <footer
    class="fixed mt-25 bottom-0 w-full z-50
           bg-black/80 border-t border-white/10
           backdrop-blur-md"
  >
    <div
      class="max-w-md mx-auto
             flex justify-between
             py-2 px-4
             text-[10px]"
    >
      <router-link
        v-for="(item, i) in footerLinks"
        :key="i"
        :to="item.route"
        class="flex flex-col items-center gap-0.5
               text-white/70 hover:text-green-400
               transition"
      >
        <component :is="item.icon" class="w-4 h-4" />
        <span>{{ item.name }}</span>
      </router-link>
    </div>
  </footer>
</template>

<style scoped>
footer {
  backdrop-filter: blur(8px);
}
</style>