<script setup lang="ts">
</script>

<template>
  <section class="bg-black py-24 px-6 lg:px-20">
    <div
      class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-14 items-center"
    >

      <!-- LEFT: Text -->
      <div class="space-y-6">
        <h2 class="text-3xl md:text-4xl font-bold text-white">
          Trade Anywhere, Anytime
        </h2>

        <p class="text-white/70 max-w-lg">
          Monitor markets, manage your portfolio, and withdraw profits
          securely from our mobile trading app — built for speed and reliability.
        </p>

        <!-- Feature bullets -->
        <ul class="space-y-3 text-white/80 text-sm">
          <li class="flex items-center gap-2">
            <span class="h-1.5 w-1.5 rounded-full bg-green-400"></span>
            Real-time Forex & Crypto prices
          </li>
          <li class="flex items-center gap-2">
            <span class="h-1.5 w-1.5 rounded-full bg-green-400"></span>
            Secure login & instant withdrawals
          </li>
          <li class="flex items-center gap-2">
            <span class="h-1.5 w-1.5 rounded-full bg-green-400"></span>
            Optimized for Android & iOS
          </li>
        </ul>

        <!-- Store badges -->
        <div class="flex gap-4 pt-6">
          <a href="/download" aria-label="Download on Google Play">
            <img
              src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg"
              alt="Get it on Google Play"
              class="h-12 hover:opacity-90 transition"
            />
          </a>

          <a href="/download" aria-label="Download on App Store">
            <img
              src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg"
              alt="Download on the App Store"
              class="h-12 hover:opacity-90 transition"
            />
          </a>
        </div>
      </div>

      <!-- RIGHT: Phone Mockup -->
      <div class="flex justify-center lg:justify-end">
        <div
          class="relative w-[260px] h-[520px] md:w-[300px] md:h-[580px] rounded-[2rem]
                 overflow-hidden shadow-[0_20px_80px_rgba(0,0,0,0.8)]"
        >
          <img
            src="https://via.placeholder.com/300x580/000000/ffffff?text=App+Mockup"
            alt="TronpixTrades App Preview"
            class="w-full h-full object-cover"
          />

          <!-- Subtle inner highlight -->
          <div
            class="pointer-events-none absolute inset-0 rounded-[2rem]
                   ring-1 ring-white/10"
          />
        </div>
      </div>

    </div>
  </section>
</template>