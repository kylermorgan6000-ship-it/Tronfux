<script setup lang="ts">
import { ArrowLeft } from 'lucide-vue-next'
import { useRouter } from 'vue-router'

const router = useRouter()
</script>

<template>
  <div class="min-h-screen bg-black text-white">
    <!-- Header -->
    <div class="sticky top-0 z-40 bg-black shadow-sm px-4 py-4 flex items-center gap-3 border-b border-white/10">
      <button @click="router.back()" class="text-white/70 hover:text-white">
        <ArrowLeft class="w-5 h-5" />
      </button>
      <h1 class="font-semibold text-lg">Terms of Service</h1>
    </div>

    <!-- Content -->
    <div class="max-w-3xl mx-auto px-4 py-12 space-y-8">
      <div>
        <h2 class="text-2xl font-bold mb-4">Terms of Service</h2>
        <p class="text-white/60 text-sm mb-6">Effective Date: January 1, 2026</p>
      </div>

      <section class="space-y-4">
        <h3 class="text-xl font-semibold">1. Agreement to Terms</h3>
        <p class="text-white/70 leading-relaxed">
          By accessing and using TronpixTrades, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.
        </p>
      </section>

      <section class="space-y-4">
        <h3 class="text-xl font-semibold">2. Use License</h3>
        <p class="text-white/70 leading-relaxed">
          Permission is granted to temporarily download one copy of the materials (information or software) on TronpixTrades for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title.
        </p>
      </section>

      <section class="space-y-4">
        <h3 class="text-xl font-semibold">3. Disclaimer</h3>
        <p class="text-white/70 leading-relaxed">
          The materials on TronpixTrades are provided on an 'as is' basis. TronpixTrades makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.
        </p>
      </section>

      <section class="space-y-4">
        <h3 class="text-xl font-semibold">4. Limitations</h3>
        <p class="text-white/70 leading-relaxed">
          In no event shall TronpixTrades or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on TronpixTrades.
        </p>
      </section>

      <section class="space-y-4">
        <h3 class="text-xl font-semibold">5. Trading Risk Disclaimer</h3>
        <p class="text-white/70 leading-relaxed">
          Trading on financial markets involves substantial risk of loss. You acknowledge and accept that you are solely responsible for your investment decisions. Past performance does not guarantee future results.
        </p>
      </section>

      <section class="space-y-4">
        <h3 class="text-xl font-semibold">6. Account Termination</h3>
        <p class="text-white/70 leading-relaxed">
          TronpixTrades reserves the right, in its sole discretion, to terminate your account and access to the service at any time, for any reason, with or without notice.
        </p>
      </section>

      <section class="space-y-4">
        <h3 class="text-xl font-semibold">7. Contact</h3>
        <p class="text-white/70 leading-relaxed">
          If you have any questions regarding these Terms of Service, please contact us at support@tronpixtrades.com.
        </p>
      </section>
    </div>
  </div>
</template>
