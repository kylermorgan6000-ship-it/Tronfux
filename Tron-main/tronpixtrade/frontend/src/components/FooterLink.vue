<script setup>
import { Home, Wallet, TrendingUp, Coins, Target, Settings } from "lucide-vue-next";

/* Footer items */
const footerLinks = [
  { name: 'Home', icon: Home, path: '/broker/dashboard' },
  { name: 'Wallet', icon: Wallet, path: '/broker/wallet' },
  { name: 'Trade', icon: TrendingUp, path: '/broker/trade' },
  { name: 'Markets', icon: Coins, path: '/broker/markets' },
  { name: 'Copy', icon: Target, path: '/broker/copy-trading' },
  { name: 'Settings', icon: Settings, path: '/broker/settings' },
];
</script>

<template>
  <footer class="bg-black border-t border-green-500/20 text-white fixed bottom-0 w-full z-50">
    <div class="max-w-md mx-auto flex justify-between py-2 px-4 text-[10px]">
      <router-link
        v-for="(item, i) in footerLinks"
        :key="i"
        :to="item.path"
        class="flex flex-col items-center justify-center gap-0.5 text-white hover:text-green-400"
      >
        <component :is="item.icon" class="w-4 h-4" />
        <span>{{ item.name }}</span>
      </router-link>
    </div>
  </footer>
</template>

<style scoped>
footer {
  backdrop-filter: blur(6px);
}
</style>